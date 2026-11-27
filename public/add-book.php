
<?php
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "librarian") {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../config/database.php';

$message = "";

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
        $sql = "INSERT INTO ebooks (title, author, category, file_url, copies)
                VALUES (:title, :author, :category, :file_url, :copies)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ":title" => $title,
            ":author" => $author,
            ":category" => $category,
            ":file_url" => $file_url,
            ":copies" => $copies
        ]);

        $message = "E-book added successfully!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add E-book</title>
</head>
<body>

    <h1>Add a New E-book</h1>

    <p><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>

    <form method="POST">
        <label>Book Title:</label><br>
        <input type="text" name="title" required><br><br>

        <label>Author:</label><br>
        <input type="text" name="author" required><br><br>

        <label>Category:</label><br>
        <input type="text" name="category" required><br><br>

        <label>File URL:</label><br>
        <input type="text" name="file_url" required><br><br>

        <label>Number of Copies:</label><br>
        <input type="number" name="copies" min="0" required><br><br>

        <button type="submit">Add E-book</button>
    </form>

    <p><a class="login-btn" href="librarian.php">Back to Dashboard</a></p>

</body>
</html>
