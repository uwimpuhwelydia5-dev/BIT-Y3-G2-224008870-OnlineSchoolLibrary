

<?php
session_start();

// Librarian access only
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "librarian") {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../config/database.php';

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$csrf_token = $_SESSION['csrf_token'];

// Get all borrowing records
$sql = "SELECT
            b.borrow_id,
            u.username,
            e.title,
            e.author,
            b.borrowed_at,
            b.due_date,
            b.returned_at
        FROM borrowings b
        JOIN users u ON b.user_id = u.user_id
        JOIN ebooks e ON b.ebook_id = e.ebook_id
        ORDER BY b.borrowed_at DESC";

$stmt = $pdo->query($sql);
$records = $stmt->fetchAll();

function escape($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Borrowing Records</title>
    <link rel="stylesheet" href="css/style.css">

    <style>
        .return-button {
            background: #1769aa;
            color: white;
            border: none;
            padding: 10px 16px;
            border-radius: 5px;
            cursor: pointer;
        }

        .return-button:hover {
            background: #104c7d;
        }

        .returned {
            color: green;
            font-weight: bold;
        }

        .borrowed {
            color: orange;
            font-weight: bold;
        }

        .message {
            padding: 12px;
            margin: 15px 0;
            background: #e5f5e9;
            color: #176b35;
            border-radius: 5px;
        }

        .error-message {
            padding: 12px;
            margin: 15px 0;
            background: #ffe8e8;
            color: #a00000;
            border-radius: 5px;
        }
    </style>
</head>

<body>

    <h1>All Borrowing Records</h1>

    <?php if (isset($_GET['message']) && $_GET['message'] === 'returned'): ?>
        <p class="message">Book returned successfully!</p>

    <?php elseif (isset($_GET['message']) && $_GET['message'] === 'notfound'): ?>
        <p class="error-message">Borrowing record not found or already returned.</p>
    <?php endif; ?>

    <?php if (count($records) > 0): ?>

        <?php foreach ($records as $record): ?>

            <div class="book-card">

                <h3><?= escape($record["title"]) ?></h3>

                <p>Author: <?= escape($record["author"]) ?></p>

                <p>Student: <?= escape($record["username"]) ?></p>

                <p>Borrowed on: <?= escape($record["borrowed_at"]) ?></p>

                <p>Due date: <?= escape($record["due_date"]) ?></p>

                <p>
                    Status:

                    <?php if ($record["returned_at"]): ?>
                        <span class="returned">Returned</span>
                    <?php else: ?>
                        <span class="borrowed">Borrowed</span>
                    <?php endif; ?>
                </p>

                <?php if (!$record["returned_at"]): ?>

                    <form method="POST" action="mvc-return-book.php">

                        <input
                            type="hidden"
                            name="borrow_id"
                            value="<?= (int)$record["borrow_id"] ?>"
                        >

                        <input
                            type="hidden"
                            name="csrf_token"
                            value="<?= escape($csrf_token) ?>"
                        >

                        <button type="submit" class="return-button">
                            Return Book
                        </button>

                    </form>

                <?php endif; ?>

            </div>

        <?php endforeach; ?>

    <?php else: ?>

        <p>No borrowing records found.</p>

    <?php endif; ?>

    <p>
        <a class="login-btn" href="librarian.php">Back to Librarian Dashboard</a>
    </p>

</body>
</html>
