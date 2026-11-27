
<?php
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "librarian") {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../config/database.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    exit("Invalid request.");
}

$ebook_id = filter_input(INPUT_POST, "ebook_id", FILTER_VALIDATE_INT);

if (!$ebook_id) {
    exit("Invalid book ID.");
}

try {
    $stmt = $pdo->prepare(
        "DELETE FROM ebooks WHERE ebook_id = :id"
    );

    $stmt->execute([":id" => $ebook_id]);

    header("Location: books.php");
    exit;

} catch (PDOException $e) {
    exit("This book cannot be deleted because it may be linked to borrowing records.");
}
