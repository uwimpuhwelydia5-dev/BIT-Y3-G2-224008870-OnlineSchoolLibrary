

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

// Process borrowing form
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Verify CSRF token
    if (
        !isset($_POST["csrf_token"]) ||
        !hash_equals($_SESSION["csrf_token"], $_POST["csrf_token"])
    ) {
        http_response_code(403);
        exit("Invalid security token. Please refresh the page.");
    }

    $ebook_id = filter_input(INPUT_POST, "ebook_id", FILTER_VALIDATE_INT);
    $due_date = $_POST["due_date"] ?? "";

    if (!$ebook_id || !$due_date) {
        $message = "Please select a valid book and due date.";
    } elseif ($due_date <= date("Y-m-d")) {
        $message = "The due date must be in the future.";
    } else {

        try {
            $pdo->beginTransaction();

            // Check book availability
            $stmt = $pdo->prepare(
                "SELECT copies FROM ebooks
                 WHERE ebook_id = :id FOR UPDATE"
            );

            $stmt->execute([":id" => $ebook_id]);
            $book = $stmt->fetch();

            if (!$book || $book["copies"] < 1) {
                throw new Exception("This book is not available.");
            }

            // Save borrowing record
            $stmt = $pdo->prepare(
                "INSERT INTO borrowings
                 (ebook_id, user_id, due_date)
                 VALUES (:ebook_id, :user_id, :due_date)"
            );

            $stmt->execute([
                ":ebook_id" => $ebook_id,
                ":user_id" => $_SESSION["user_id"],
                ":due_date" => $due_date
            ]);

            $borrow_id = $pdo->lastInsertId();

            // Create reading progress record
            $stmt = $pdo->prepare(
                "INSERT INTO reading_logs (borrow_id, pages_read)
                 VALUES (:borrow_id, 0)"
            );

            $stmt->execute([
                ":borrow_id" => $borrow_id
            ]);

            // Reduce available copies
            $stmt = $pdo->prepare(
                "UPDATE ebooks
                 SET copies = copies - 1
                 WHERE ebook_id = :id"
            );

            $stmt->execute([":id" => $ebook_id]);

            $pdo->commit();

            $message = "Book borrowed successfully!";

            // Generate a fresh token after submission
            $_SESSION["csrf_token"] = bin2hex(random_bytes(32));

        } catch (Exception $e) {

            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            $message = $e->getMessage();
        }
    }
}

// Get available books from database
$stmt = $pdo->query(
    "SELECT ebook_id, title, author, copies
     FROM ebooks
     WHERE copies > 0
     ORDER BY title"
);

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

    <title>Borrow an E-book</title>
</head>

<body>

    <h1>Borrow an E-book</h1>

    <p><?= escape($message) ?></p>

    <form method="POST">

        <!-- CSRF security token -->
        <input
            type="hidden"
            name="csrf_token"
            value="<?= escape($_SESSION["csrf_token"]) ?>"
        >

        <label>Select an E-book:</label><br>

        <select name="ebook_id" required>
            <option value="">Choose a book</option>

            <?php foreach ($books as $book): ?>
                <option value="<?= escape($book["ebook_id"]) ?>">
                    <?= escape($book["title"]) ?>
                    - <?= escape($book["author"]) ?>
                    (Available: <?= escape($book["copies"]) ?>)
                </option>
            <?php endforeach; ?>

        </select>

        <br><br>

        <label>Due Date:</label><br>

        <input
            type="date"
            name="due_date"
            min="<?= date('Y-m-d', strtotime('+1 day')) ?>"
            required
        >

        <br><br>

        <button type="submit">Borrow Book</button>

    </form>

    <p>
        <a class="login-btn" href="dashboard.php">Back to Dashboard</a>
    </p>

</body>
</html>
