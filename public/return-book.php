
<?php
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "librarian") {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../config/database.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
        if (
        !isset($_POST["csrf_token"]) ||
        !hash_equals($_SESSION["csrf_token"], $_POST["csrf_token"])
    ) {
        http_response_code(403);
        exit("Invalid security token. Please refresh the page.");
    }

    $borrow_id = filter_input(INPUT_POST, "borrow_id", FILTER_VALIDATE_INT);

    if (!$borrow_id) {
        $message = "Invalid borrowing record.";
    } else {
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare(
                "SELECT ebook_id FROM borrowings
                 WHERE borrow_id = :id AND returned_at IS NULL
                 FOR UPDATE"
            );

            $stmt->execute([":id" => $borrow_id]);
            $borrowing = $stmt->fetch();

            if (!$borrowing) {
                throw new Exception("This book has already been returned or the record does not exist.");
            }

            $stmt = $pdo->prepare(
                "UPDATE borrowings
                 SET returned_at = NOW()
                 WHERE borrow_id = :id"
            );

            $stmt->execute([":id" => $borrow_id]);

            $stmt = $pdo->prepare(
                "UPDATE ebooks
                 SET copies = copies + 1
                 WHERE ebook_id = :ebook_id"
            );

            $stmt->execute([":ebook_id" => $borrowing["ebook_id"]]);

            $pdo->commit();

            $message = "Book returned successfully!";

        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            $message = "Unable to process the return. Please check the borrowing record.";
        }
    }
}

$sql = "SELECT b.borrow_id, e.title, u.username, b.due_date
        FROM borrowings b
        JOIN ebooks e ON b.ebook_id = e.ebook_id
        JOIN users u ON b.user_id = u.user_id
        WHERE b.returned_at IS NULL
        ORDER BY b.borrowed_at DESC";

$borrowings = $pdo->query($sql)->fetchAll();

function escape($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Return Books</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <h1>Manage Book Returns</h1>

    <p><?= escape($message) ?></p>

    <?php if (count($borrowings) > 0): ?>

        <?php foreach ($borrowings as $book): ?>
            <div class="book-card">
                <h3><?= escape($book["title"]) ?></h3>
                <p>Student: <?= escape($book["username"]) ?></p>
                <p>Due date: <?= escape($book["due_date"]) ?></p>

                <form method="POST">
    <input type="hidden" name="borrow_id"
           value="<?= (int)$book["borrow_id"] ?>">

    <input type="hidden" name="csrf_token"
           value="<?= htmlspecialchars($_SESSION["csrf_token"], ENT_QUOTES, 'UTF-8') ?>">

    <button type="submit">Confirm Return</button>
</form>
            </div>
        <?php endforeach; ?>

    <?php else: ?>

        <p>There are no books waiting to be returned.</p>

    <?php endif; ?>

    <p><a class="login-btn" href="librarian.php">Back to Librarian Dashboard</a></p>

</body>
</html>
