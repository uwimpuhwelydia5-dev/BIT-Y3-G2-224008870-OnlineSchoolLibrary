

<?php

session_start();

// Only librarians can delete books
if (
    !isset($_SESSION["user_id"]) ||
    !isset($_SESSION["role"]) ||
    $_SESSION["role"] !== "librarian"
) {
    header("Location: login.php");
    exit;
}

// Only allow POST requests
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: mvc-books.php");
    exit;
}

// Check CSRF security token
if (
    !isset($_POST["csrf_token"]) ||
    !hash_equals(
        $_SESSION["csrf_token"] ?? "",
        $_POST["csrf_token"]
    )
) {
    http_response_code(403);
    exit("Invalid security token.");
}

// Validate book ID
$id = filter_var(
    $_POST["ebook_id"] ?? null,
    FILTER_VALIDATE_INT
);

if (!$id || $id < 1) {
    header("Location: mvc-books.php?delete_error=1");
    exit;
}

// Load database, model, and controller
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/models/Ebook.php';
require_once __DIR__ . '/../app/controllers/EbookController.php';

// Create model and controller
$ebookModel = new Ebook($pdo);
$controller = new EbookController($ebookModel);

try {

    $deleted = $controller->delete($id);

    if ($deleted) {
        $_SESSION["csrf_token"] = bin2hex(random_bytes(32));

        header("Location: mvc-books.php?deleted=1");
        exit;
    }

    header("Location: mvc-books.php?delete_error=1");
    exit;

} catch (PDOException $e) {

    error_log($e->getMessage());

    header("Location: mvc-books.php?delete_error=1");
    exit;
}

?>
