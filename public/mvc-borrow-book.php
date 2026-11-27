
<?php
session_start();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/models/Ebook.php';
require_once __DIR__ . '/../app/controllers/EbookController.php';

// Only students can borrow books
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit;
}

$ebookModel = new Ebook($pdo);
$controller = new EbookController($ebookModel);

// Create CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Process borrowing request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (
        !isset($_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        exit("Invalid security token.");
    }

    $ebook_id = filter_input(INPUT_POST, 'ebook_id', FILTER_VALIDATE_INT);

    if (!$ebook_id || $ebook_id < 1) {
        exit("Invalid book selection.");
    }

    try {
        $success = $controller->borrow(
            $ebook_id,
            $_SESSION['user_id']
        );

        if ($success) {
            header("Location: mvc-student-books.php?message=success");
            exit;
        }

        header("Location: mvc-student-books.php?message=unavailable");
        exit;

    } catch (PDOException $e) {
        error_log($e->getMessage());
        exit("Borrowing failed. Please try again.");
    }
}

header("Location: mvc-student-books.php");
exit;
?>
