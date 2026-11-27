
<?php
session_start();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/models/Ebook.php';
require_once __DIR__ . '/../app/controllers/EbookController.php';

// Allow students only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit;
}

// Create model and controller
$ebookModel = new Ebook($pdo);
$controller = new EbookController($ebookModel);

// Get all available books
$books = $controller->index();

// Create CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$csrf_token = $_SESSION['csrf_token'];
?>

<?php require_once __DIR__ . '/../app/views/ebooks/student-books.php'; ?>
