
<?php
session_start();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/models/Ebook.php';
require_once __DIR__ . '/../app/controllers/EbookController.php';

// Only librarians can return books
if (
    !isset($_SESSION['user_id']) ||
    $_SESSION['role'] !== 'librarian'
) {
    header("Location: login.php");
    exit;
}

// Create model and controller
$ebookModel = new Ebook($pdo);
$controller = new EbookController($ebookModel);

// Check POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: all-borrowings.php");
    exit;
}

// Verify CSRF token
if (
    !isset($_POST['csrf_token']) ||
    !isset($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
) {
    exit("Invalid security token.");
}

// Validate borrowing ID
$borrow_id = filter_input(
    INPUT_POST,
    'borrow_id',
    FILTER_VALIDATE_INT
);

if (!$borrow_id || $borrow_id < 1) {
    exit("Invalid borrowing ID.");
}

try {
    $success = $controller->returnBook($borrow_id);

    if ($success) {
        header("Location: all-borrowings.php?message=returned");
        exit;
    }

    header("Location: all-borrowings.php?message=notfound");
    exit;

} catch (PDOException $e) {
    error_log($e->getMessage());
    exit("Return failed. Please try again.");
}
?>
