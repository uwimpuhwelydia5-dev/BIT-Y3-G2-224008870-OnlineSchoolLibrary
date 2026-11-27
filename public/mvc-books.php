


<?php
session_start();

// Check librarian access
if (
    !isset($_SESSION["user_id"]) ||
    !isset($_SESSION["role"]) ||
    $_SESSION["role"] !== "librarian"
) {
    header("Location: login.php");
    exit;
}

// Connect to database
require_once __DIR__ . '/../config/database.php';

// Load the Ebook model FIRST
require_once __DIR__ . '/../app/models/Ebook.php';

// Then load the controller
require_once __DIR__ . '/../app/controllers/EbookController.php';

// Create model and controller
$ebookModel = new Ebook($pdo);
$controller = new EbookController($ebookModel);

// Create CSRF token
if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

$csrf_token = $_SESSION["csrf_token"];

// Search keyword
$keyword = trim($_GET["q"] ?? "");

// Get books
if ($keyword !== "") {
    $books = $controller->search($keyword);
} else {
    $books = $controller->index();
}

// Load View
require __DIR__ . '/../app/views/ebooks/index.php';
?>
