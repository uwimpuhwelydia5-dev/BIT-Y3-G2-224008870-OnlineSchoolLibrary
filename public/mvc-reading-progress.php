
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

// Accept POST requests only
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: my-borrowings.php");
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

// Validate pages read
$pages_read = filter_input(
    INPUT_POST,
    'pages_read',
    FILTER_VALIDATE_INT
);

if (
    !$borrow_id ||
    $borrow_id < 1 ||
    $pages_read === false ||
    $pages_read === null ||
    $pages_read < 0
) {
    exit("Please enter valid reading progress.");
}

try {
    $success = $controller->updateReadingProgress(
        $borrow_id,
        $_SESSION['user_id'],
        $pages_read
    );

    if ($success) {
        header("Location: my-borrowings.php?message=progress-updated");
        exit;
    }

    exit("Reading progress could not be updated. Check that the borrowing is active.");

} catch (PDOException $e) {
    error_log($e->getMessage());
    exit("An error occurred while updating reading progress.");
}
?>
