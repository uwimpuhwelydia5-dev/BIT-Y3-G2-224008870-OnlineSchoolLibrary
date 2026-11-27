

<?php
session_start();

// Create CSRF security token
if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

// Allow students only
if (
    !isset($_SESSION["user_id"]) ||
    !isset($_SESSION["role"]) ||
    $_SESSION["role"] !== "student"
) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../config/database.php';

$message = "";

// Process reading progress form
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Verify CSRF token
    if (
        !isset($_POST["csrf_token"]) ||
        !hash_equals($_SESSION["csrf_token"], $_POST["csrf_token"])
    ) {
        http_response_code(403);
        exit("Invalid security token. Please refresh the page.");
    }

    $borrow_id = filter_input(INPUT_POST, "borrow_id", FILTER_VALIDATE_INT);
    $pages_read = filter_input(INPUT_POST, "pages_read", FILTER_VALIDATE_INT);

    if (!$borrow_id || $pages_read === false || $pages_read === null || $pages_read < 0) {
        $message = "Please select a book and enter a valid number of pages.";
    } else {
        try {
            // Check that the borrowing belongs to this student
            // and that the book has not been returned
            $stmt = $pdo->prepare(
                "SELECT borrow_id
                 FROM borrowings
                 WHERE borrow_id = :borrow_id
                 AND user_id = :user_id
                 AND returned_at IS NULL"
            );

            $stmt->execute([
                ":borrow_id" => $borrow_id,
                ":user_id" => $_SESSION["user_id"]
            ]);

            $borrowing = $stmt->fetch();

            if (!$borrowing) {
                $message = "Invalid borrowing record or the book has already been returned.";
            } else {
                // Update reading progress
                $stmt = $pdo->prepare(
                    "UPDATE reading_logs
                     SET pages_read = :pages_read,
                         last_read_at = NOW()
                     WHERE borrow_id = :borrow_id"
                );

                $stmt->execute([
                    ":pages_read" => $pages_read,
                    ":borrow_id" => $borrow_id
                ]);

                if ($stmt->rowCount() === 0) {
                    // Check whether the record exists even if the value did not change
                    $check = $pdo->prepare(
                        "SELECT log_id FROM reading_logs
                         WHERE borrow_id = :borrow_id"
                    );
                    $check->execute([":borrow_id" => $borrow_id]);

                    if (!$check->fetch()) {
                        $message = "Reading progress record was not found.";
                    } else {
                        $message = "Reading progress updated successfully!";
                    }
                } else {
                    $message = "Reading progress updated successfully!";
                }

                // Generate a fresh token
                $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $message = "Unable to update reading progress. Please try again.";
        }
    }
}

// Get active borrowings belonging to the logged-in student
$stmt = $pdo->prepare(
    "SELECT
        b.borrow_id,
        e.title,
        e.author,
        r.pages_read,
        r.last_read_at
     FROM borrowings b
     INNER JOIN ebooks e ON b.ebook_id = e.ebook_id
     INNER JOIN reading_logs r ON b.borrow_id = r.borrow_id
     WHERE b.user_id = :user_id
     AND b.returned_at IS NULL
     ORDER BY b.borrowed_at DESC"
);

$stmt->execute([
    ":user_id" => $_SESSION["user_id"]
]);

$books = $stmt->fetchAll();

// Escape output safely
function escape($value) {
    return htmlspecialchars(
        (string)$value,
        ENT_QUOTES,
        'UTF-8'
    );
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Reading Progress</title>
</head>

<body>

    <h1>My Reading Progress</h1>

    <p><?= escape($message) ?></p>

    <?php if (count($books) > 0): ?>

        <h2>Update Your Reading Progress</h2>

        <form method="POST">

            <!-- CSRF security token -->
            <input
                type="hidden"
                name="csrf_token"
                value="<?= escape($_SESSION["csrf_token"]) ?>"
            >

            <label>Select Your Borrowed Book:</label><br>

            <select name="borrow_id" required>
                <option value="">Choose a book</option>

                <?php foreach ($books as $book): ?>
                    <option value="<?= escape($book["borrow_id"]) ?>">
                        <?= escape($book["title"]) ?>
                        - <?= escape($book["author"]) ?>
                    </option>
                <?php endforeach; ?>

            </select>

            <br><br>

            <label>Number of Pages Read:</label><br>

            <input
                type="number"
                name="pages_read"
                min="0"
                step="1"
                required
            >

            <br><br>

            <button type="submit">Update Progress</button>

        </form>

        <h2>Current Reading Records</h2>

        <table border="1" cellpadding="8">
            <tr>
                <th>Book Title</th>
                <th>Author</th>
                <th>Pages Read</th>
                <th>Last Updated</th>
            </tr>

            <?php foreach ($books as $book): ?>
                <tr>
                    <td><?= escape($book["title"]) ?></td>
                    <td><?= escape($book["author"]) ?></td>
                    <td><?= escape($book["pages_read"]) ?></td>
                    <td><?= escape($book["last_read_at"]) ?></td>
                </tr>
            <?php endforeach; ?>

        </table>

    <?php else: ?>

        <p>You have no active borrowed books.</p>

    <?php endif; ?>

    <p>
        <a class="login-btn" href="dashboard.php">Back to Dashboard</a>
    </p>

</body>
</html>
