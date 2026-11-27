

<?php
session_start();

if (
    !isset($_SESSION["user_id"]) ||
    !isset($_SESSION["role"]) ||
    $_SESSION["role"] !== "librarian"
) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../config/database.php';

if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

$stmt = $pdo->query(
    "SELECT ebook_id, title, author, category, copies
     FROM ebooks
     ORDER BY ebook_id DESC"
);

$books = $stmt->fetchAll();

function escape($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Manage E-books</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
            background-color: #f4f7fb;
        }

        h1 {
            color: #1e3a8a;
        }

        input {
            width: 90%;
            max-width: 450px;
            padding: 12px;
            margin: 15px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #1e3a8a;
            color: white;
        }

        a {
            color: #1d4ed8;
            text-decoration: none;
        }

        button {
            padding: 7px 12px;
            cursor: pointer;
            border: none;
            border-radius: 4px;
            background-color: #dc2626;
            color: white;
        }

        .actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        #searchResults {
            margin: 10px 0 20px;
            background-color: white;
            padding: 10px;
            border-radius: 5px;
        }

        .search-result {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        .search-result h3 {
            color: #1e3a8a;
            margin-bottom: 5px;
        }
    </style>
</head>

<body>

    <h1>Manage E-books</h1>

    <p>View, search, edit and delete available books.</p>

    <p>
        <a class="login-btn" href="librarian.php">Back to Librarian Dashboard</a> |
        <a class="login-btn" href="add-book.php">Add New E-book</a>
    </p>

    <h2>Search Books</h2>

    <input
        type="text"
        id="bookSearch"
        placeholder="Search by title, author or category..."
        autocomplete="off"
    >

    <div id="searchResults"></div>

    <h2>Available E-books</h2>

    <?php if (count($books) > 0): ?>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Category</th>
                    <th>Available Copies</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>

                <?php foreach ($books as $book): ?>

                    <tr>
                        <td><?= escape($book["ebook_id"]) ?></td>
                        <td><?= escape($book["title"]) ?></td>
                        <td><?= escape($book["author"]) ?></td>
                        <td><?= escape($book["category"]) ?></td>
                        <td><?= escape($book["copies"]) ?></td>

                        <td>
                            <div class="actions">

                                <a href="edit-book.php?id=<?= escape($book["ebook_id"]) ?>">
                                    Edit
                                </a>

                                <form
                                    method="POST"
                                    action="delete-book.php"
                                    onsubmit="return confirm('Are you sure you want to delete this book?');"
                                >

                                    <input
                                        type="hidden"
                                        name="ebook_id"
                                        value="<?= escape($book["ebook_id"]) ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="csrf_token"
                                        value="<?= escape($_SESSION["csrf_token"]) ?>"
                                    >

                                    <button type="submit">Delete</button>

                                </form>

                            </div>
                        </td>
                    </tr>

                <?php endforeach; ?>

            </tbody>
        </table>

    <?php else: ?>

        <p>No books are currently available.</p>

    <?php endif; ?>

    <p>
        <a class="login-btn" href="librarian.php">Back to Dashboard</a>
    </p>

    <!-- Correct JavaScript file location -->
    <script src="/Online-library/public/js/javascript.js"></script>

</body>
</html>
