
<?php
session_start();

if (
    !isset($_SESSION["user_id"]) ||
    !isset($_SESSION["role"]) ||
    $_SESSION["role"] !== "librarian"
) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/models/Ebook.php';
require_once __DIR__ . '/../app/controllers/EbookController.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: mvc-books.php");
    exit;
}

// Check CSRF token
if (
    !isset($_POST["csrf_token"]) ||
    !isset($_SESSION["csrf_token"]) ||
    !hash_equals($_SESSION["csrf_token"], $_POST["csrf_token"])
) {
    exit("Invalid request. Please try again.");
}

$ebookId = filter_input(INPUT_POST, "ebook_id", FILTER_VALIDATE_INT);

if (!$ebookId || !isset($_FILES["pdf_file"])) {
    exit("Please select a book and PDF file.");
}

$file = $_FILES["pdf_file"];

if ($file["error"] !== UPLOAD_ERR_OK) {
    exit("PDF upload failed. Please try again.");
}

if ($file["size"] > 10 * 1024 * 1024) {
    exit("The PDF must not exceed 10 MB.");
}

// Confirm that the uploaded file is a PDF
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mimeType = $finfo->file($file["tmp_name"]);

$handle = fopen($file["tmp_name"], "rb");
$signature = fread($handle, 5);
fclose($handle);

if ($mimeType !== "application/pdf" || $signature !== "%PDF-") {
    exit("Please upload a valid PDF file.");
}

// Confirm that the book exists
$stmt = $pdo->prepare(
    "SELECT ebook_id FROM ebooks WHERE ebook_id = ?"
);
$stmt->execute([$ebookId]);

if (!$stmt->fetch()) {
    exit("Book not found.");
}

// Generate a unique filename
$newFileName = bin2hex(random_bytes(16)) . ".pdf";

$uploadDirectory = __DIR__ . "/uploads/ebooks/";

if (!is_dir($uploadDirectory)) {
    mkdir($uploadDirectory, 0755, true);
}

$destination = $uploadDirectory . $newFileName;

if (!move_uploaded_file($file["tmp_name"], $destination)) {
    exit("Could not save the PDF file.");
}

$filePath = "uploads/ebooks/" . $newFileName;

// Update only the PDF path for the existing book
try {
    $stmt = $pdo->prepare(
        "UPDATE ebooks SET file_url = ? WHERE ebook_id = ?"
    );

    $stmt->execute([$filePath, $ebookId]);

    header("Location: mvc-books.php?message=pdf-uploaded");
    exit;

} catch (PDOException $e) {
    if (file_exists($destination)) {
        unlink($destination);
    }

    error_log($e->getMessage());
    exit("Could not update the book PDF.");
}
