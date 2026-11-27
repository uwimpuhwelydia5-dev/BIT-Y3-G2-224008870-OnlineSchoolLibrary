

<?php
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "student") {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../config/database.php';

// Create CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$csrf_token = $_SESSION['csrf_token'];

// Get borrowing history and reading progress
$sql = "SELECT
            b.borrow_id,
            e.title,
            e.author,
            b.borrowed_at,
            b.due_date,
            b.returned_at,
            r.pages_read
        FROM borrowings b
        JOIN ebooks e ON b.ebook_id = e.ebook_id
        LEFT JOIN reading_logs r ON b.borrow_id = r.borrow_id
        WHERE b.user_id = :user_id
        ORDER BY b.borrow_id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([":user_id" => $_SESSION["user_id"]]);
$borrowings = $stmt->fetchAll();

function escape($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Borrowing History</title>
    <link rel="stylesheet" href="css/style.css">

    <style>
        .progress-form {
            margin-top: 15px;
        }

        .progress-form input {
            padding: 9px;
            width: 120px;
            margin-right: 8px;
        }

        .progress-form button {
            padding: 9px 15px;
            background: #1769aa;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .message {
            background: #e5f5e9;
            color: #176b35;
            padding: 12px;
            border-radius: 5px;
            margin: 15px 0;
        }
    </style>
</head>

<body>

    <h1>My Borrowing History</h1>

    <?php if (isset($_GET['message']) && $_GET['message'] === 'progress-updated'): ?>
        <p class="message">Reading progress updated successfully!</p>
    <?php endif; ?>

    <?php if (count($borrowings) > 0): ?>

        <?php foreach ($borrowings as $book): ?>

            <div class="book-card">

                <h3><?= escape($book["title"]) ?></h3>

                <p>Author: <?= escape($book["author"]) ?></p>

                <p>Borrowed on: <?= escape($book["borrowed_at"]) ?></p>

                <p>Due date: <?= escape($book["due_date"]) ?></p>

                <p>
                    Pages read:
                    <?= escape($book["pages_read"] ?? 0) ?>
                </p>

                <p>
                    Status:
                    <?= $book["returned_at"] ? "Returned" : "Borrowed" ?>
                </p>

                <?php if (!$book["returned_at"]): ?>

                    <form
                        class="progress-form"
                        method="POST"
                        action="mvc-reading-progress.php"
                    >

                        <input
                            type="hidden"
                            name="borrow_id"
                            value="<?= (int)$book["borrow_id"] ?>"
                        >

                        <input
                            type="hidden"
                            name="csrf_token"
                            value="<?= escape($csrf_token) ?>"
                        >

                        <label>Update pages read:</label>

                        <input
                            type="number"
                            name="pages_read"
                            min="0"
                            value="<?= (int)($book["pages_read"] ?? 0) ?>"
                            required
                        >

                        <button type="submit">
                            Update Progress
                        </button>

                    </form>

                <?php endif; ?>

            </div>

        <?php endforeach; ?>

    <?php else: ?>

        <p>You have not borrowed any books yet.</p>

    <?php endif; ?>

    <p><a class="login-btn" href="dashboard.php">Back to Dashboard</a></p>

</body>
</html>
