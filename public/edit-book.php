
<?php
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "librarian") {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../config/database.php';

$message = "";
$ebook_id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$ebook_id) {
    exit("Invalid book ID.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST["title"] ?? "");
    $author = trim($_POST["author"] ?? "");
    $category = trim($_POST["category"] ?? "");
    $file_url = trim($_POST["file_url"] ?? "");
    $copies = filter_var($_POST["copies"] ?? null, FILTER_VALIDATE_INT);

    if ($title === "" || $author === "" || $category === "" ||
        $file_url === "" || $copies === false || $copies < 0) {
        $message = "Please enter valid information in all fields.";
    } else {
        $sql = "UPDATE ebooks
                SET title = :title, author = :author,
                    category = :category, file_url = :file_url,
                    copies = :copies
                WHERE ebook_id = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ":title" => $title,
            ":author" => $author,
            ":category" => $category,
            ":file_url" => $file_url,
            ":copies" => $copies,
            ":id" => $ebook_id
        ]);

        header("Location: books.php");
        exit;
    }
}

$stmt = $pdo->prepare("SELECT * FROM ebooks WHERE ebook_id = :id");
$stmt->execute([":id" => $ebook_id]);
$book = $stmt->fetch();

if (!$book) {
    exit("Book not found.");
}

function escape($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit E-book</title>
</head>
<body>

    <h1>Edit E-book</h1>

    <p><?= escape($message) ?></p>

    <form method="POST">
        <label>Book Title:</label><br>
        <input type="text" name="title" value="<?= escape($book['title']) ?>" required><br><br>

        <label>Author:</label><br>
        <input type="text" name="author" value="<?= escape($book['author']) ?>" required><br><br>

        <label>Category:</label><br>
        <input type="text" name="category" value="<?= escape($book['category']) ?>" required><br><br>

        <label>File URL:</label><br>
        <input type="text" name="file_url" value="<?= escape($book['file_url']) ?>" required><br><br>

        <label>Number of Copies:</label><br>
        <input type="number" name="copies" min="0" value="<?= escape($book['copies']) ?>" required><br><br>

        <button type="submit">Update E-book</button>
    </form>

    <p><a class="login-btn" href="books.php">Back to Books</a></p>

</body>
</html>
