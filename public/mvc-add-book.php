


<?php
session_start();

// Only librarians can add books
if (
    !isset($_SESSION["user_id"]) ||
    !isset($_SESSION["role"]) ||
    $_SESSION["role"] !== "librarian"
) {
    header("Location: login.php");
    exit;
}

// Load database, model, and controller
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/models/Ebook.php';
require_once __DIR__ . '/../app/controllers/EbookController.php';

$ebookModel = new Ebook($pdo);
$controller = new EbookController($ebookModel);

// Create CSRF token
if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

$message = "";
$error = false;
$old = [];

if (isset($_GET["added"]) && $_GET["added"] === "1") {
    $message = "E-book uploaded and added successfully!";
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $old = [
        "title" => trim($_POST["title"] ?? ""),
        "author" => trim($_POST["author"] ?? ""),
        "category" => trim($_POST["category"] ?? ""),
        "copies" => $_POST["copies"] ?? ""
    ];

    // Check CSRF token
    if (
        !isset($_POST["csrf_token"]) ||
        !hash_equals($_SESSION["csrf_token"], $_POST["csrf_token"])
    ) {
        http_response_code(403);
        exit("Invalid security token. Please refresh the page.");
    }

    $copies = filter_var($old["copies"], FILTER_VALIDATE_INT);

    // Validate text fields
    if (
        $old["title"] === "" ||
        $old["author"] === "" ||
        $old["category"] === ""
    ) {
        $message = "Please fill in all book information.";
        $error = true;

    } elseif (
        strlen($old["title"]) > 200 ||
        strlen($old["author"]) > 150 ||
        strlen($old["category"]) > 100
    ) {
        $message = "One or more fields exceed the allowed length.";
        $error = true;

    } elseif ($copies === false || $copies < 0) {
        $message = "Please enter a valid number of copies.";
        $error = true;

    } elseif (
        !isset($_FILES["pdf_file"]) ||
        $_FILES["pdf_file"]["error"] !== UPLOAD_ERR_OK
    ) {
        $message = "Please select a valid PDF file.";
        $error = true;

    } else {

        $pdf = $_FILES["pdf_file"];

        // Maximum PDF size: 10 MB
        $maxSize = 10 * 1024 * 1024;

        if ($pdf["size"] > $maxSize) {
            $message = "PDF file must not exceed 10 MB.";
            $error = true;

        } else {

            // Check actual file type
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->file($pdf["tmp_name"]);

            $fileHandle = fopen($pdf["tmp_name"], "rb");
            $fileHeader = fread($fileHandle, 5);
            fclose($fileHandle);

            if (
                $mimeType !== "application/pdf" ||
                $fileHeader !== "%PDF-"
            ) {
                $message = "Only genuine PDF files are allowed.";
                $error = true;

            } else {

                $uploadDirectory = __DIR__ . "/uploads/ebooks/";

                if (!is_dir($uploadDirectory)) {
                    $message = "Upload folder was not found.";
                    $error = true;

                } else {

                    // Generate a safe random filename
                    $newFileName = bin2hex(random_bytes(16)) . ".pdf";

                    $destination = $uploadDirectory . $newFileName;

                    if (move_uploaded_file($pdf["tmp_name"], $destination)) {

                        $fileUrl = "uploads/ebooks/" . $newFileName;

                        try {

                            $controller->create(
                                $old["title"],
                                $old["author"],
                                $old["category"],
                                $fileUrl,
                                $copies
                            );

                            // Renew CSRF token
                            $_SESSION["csrf_token"] = bin2hex(random_bytes(32));

                            header("Location: mvc-add-book.php?added=1");
                            exit;

                        } catch (PDOException $e) {

                            // Remove uploaded file if database save fails
                            if (file_exists($destination)) {
                                unlink($destination);
                            }

                            error_log($e->getMessage());

                            $message = "Unable to add the book. Please try again.";
                            $error = true;
                        }

                    } else {
                        $message = "Unable to upload the PDF file.";
                        $error = true;
                    }
                }
            }
        }
    }
}

$csrf_token = $_SESSION["csrf_token"];

// Load the View
require __DIR__ . '/../app/views/ebooks/create.php';
?>
