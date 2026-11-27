
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

// Load model first, then controller
require_once __DIR__ . '/../app/models/Ebook.php';
require_once __DIR__ . '/../app/controllers/EbookController.php';

// Create model and controller
$ebookModel = new Ebook($pdo);
$controller = new EbookController($ebookModel);

// Validate book ID
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    exit("Invalid book ID.");
}

// Find book
$book = $controller->show($id);

if (!$book) {
    exit("E-book not found.");
}

// Create CSRF token
if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

$csrf_token = $_SESSION["csrf_token"];

$message = "";
$error = false;
$old = [];

// Process update form
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (
        !isset($_POST["csrf_token"]) ||
        !hash_equals($_SESSION["csrf_token"], $_POST["csrf_token"])
    ) {
        http_response_code(403);
        exit("Invalid security token. Please refresh the page.");
    }

    $old = [
        "title" => trim($_POST["title"] ?? ""),
        "author" => trim($_POST["author"] ?? ""),
        "category" => trim($_POST["category"] ?? ""),
        "file_url" => trim($_POST["file_url"] ?? ""),
        "copies" => $_POST["copies"] ?? ""
    ];

    $copies = filter_var($old["copies"], FILTER_VALIDATE_INT);

    if (
        $old["title"] === "" ||
        $old["author"] === "" ||
        $old["category"] === "" ||
        $old["file_url"] === ""
    ) {
        $message = "Please fill in all fields.";
        $error = true;

    } elseif (
        strlen($old["title"]) > 200 ||
        strlen($old["author"]) > 150 ||
        strlen($old["category"]) > 100 ||
        strlen($old["file_url"]) > 255
    ) {
        $message = "One or more fields are too long.";
        $error = true;

    } elseif ($copies === false || $copies < 0) {
        $message = "Enter a valid number of copies.";
        $error = true;

    } else {
        try {
            $controller->update(
                $id,
                $old["title"],
                $old["author"],
                $old["category"],
                $old["file_url"],
                $copies
            );

            $_SESSION["csrf_token"] = bin2hex(random_bytes(32));

            header("Location: mvc-books.php?updated=1");
            exit;

        } catch (PDOException $e) {
            error_log($e->getMessage());
            $message = "Unable to update the e-book.";
            $error = true;
        }
    }
}

// Load edit view
require __DIR__ . '/../app/views/ebooks/edit.php';
?>
