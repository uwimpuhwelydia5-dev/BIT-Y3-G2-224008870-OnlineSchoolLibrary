



<?php
function escapeValue($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage E-books - MVC</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f7fb;
            margin: 30px;
        }

        h1 {
            color: #1e3a8a;
        }

        .container {
            background: white;
            padding: 25px;
            border-radius: 8px;
            overflow-x: auto;
        }

        input[type="text"] {
            padding: 10px;
            width: 250px;
            margin-bottom: 15px;
        }

        input[type="file"] {
            width: 180px;
            margin: 5px 0;
        }

        button {
            padding: 10px 15px;
            background: #1e3a8a;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background: #1e3a8a;
            color: white;
        }

        tr:nth-child(even) {
            background: #f2f2f2;
        }

        a {
            color: #1e40af;
            margin-right: 8px;
        }

        .success {
            color: green;
            font-weight: bold;
        }

        .error {
            color: red;
            font-weight: bold;
        }

        .delete-button {
            background: #dc2626;
            padding: 8px 12px;
        }

        .upload-button {
            background: #15803d;
            padding: 8px 12px;
        }

        .upload-form {
            min-width: 220px;
        }
    </style>
</head>

<body>

<h1>Manage E-books (MVC)</h1>

<div class="container">

    <?php if (isset($_GET['updated']) && $_GET['updated'] === '1'): ?>
        <p class="success">E-book updated successfully!</p>
    <?php endif; ?>

    <?php if (isset($_GET['deleted']) && $_GET['deleted'] === '1'): ?>
        <p class="success">E-book deleted successfully!</p>
    <?php endif; ?>

    <?php if (isset($_GET['delete_error'])): ?>
        <p class="error">
            Unable to delete this book. It may have borrowing records.
        </p>
    <?php endif; ?>

    <?php if (isset($_GET['message']) && $_GET['message'] === 'pdf-uploaded'): ?>
        <p class="success">PDF uploaded successfully!</p>
    <?php endif; ?>

    <h2>Search Books</h2>

    <form method="GET" action="mvc-books.php">
        <input
            type="text"
            name="q"
            placeholder="Search by title, author or category"
            value="<?= escapeValue($keyword ?? '') ?>"
        >

        <button type="submit">Search</button>
        <a href="mvc-books.php">Clear Search</a>
    </form>

    <h2>Available E-books</h2>

    <p>
        <a href="mvc-add-book.php">Add New E-book</a>
    </p>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Author</th>
                <th>Category</th>
                <th>Available Copies</th>
                <th>Upload PDF</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>

        <?php if (!empty($books)): ?>

            <?php foreach ($books as $book): ?>
                <tr>
                    <td><?= escapeValue($book['ebook_id']) ?></td>
                    <td><?= escapeValue($book['title']) ?></td>
                    <td><?= escapeValue($book['author']) ?></td>
                    <td><?= escapeValue($book['category']) ?></td>
                    <td><?= escapeValue($book['copies']) ?></td>

                    <td>
                        <form
                            method="POST"
                            action="mvc-upload-pdf.php"
                            enctype="multipart/form-data"
                            class="upload-form"
                        >
                            <input
                                type="hidden"
                                name="ebook_id"
                                value="<?= (int)$book['ebook_id'] ?>"
                            >

                            <input
                                type="hidden"
                                name="csrf_token"
                                value="<?= escapeValue($csrf_token) ?>"
                            >

                            <input
                                type="file"
                                name="pdf_file"
                                accept=".pdf,application/pdf"
                                required
                            >

                            <button type="submit" class="upload-button">
                                Upload PDF
                            </button>
                        </form>
                    </td>

                    <td>
                        <a href="mvc-edit-book.php?id=<?= (int)$book['ebook_id'] ?>">
                            Edit
                        </a>

                        <form
                            method="POST"
                            action="mvc-delete-book.php"
                            style="display:inline;"
                            onsubmit="return confirm('Are you sure you want to delete this book?');"
                        >
                            <input
                                type="hidden"
                                name="ebook_id"
                                value="<?= (int)$book['ebook_id'] ?>"
                            >

                            <input
                                type="hidden"
                                name="csrf_token"
                                value="<?= escapeValue($csrf_token) ?>"
                            >

                            <button type="submit" class="delete-button">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>

        <?php else: ?>
            <tr>
                <td colspan="7">No books found.</td>
            </tr>
        <?php endif; ?>

        </tbody>
    </table>

</div>

<p>
    <a class="login-btn" href="librarian.php">Back to Librarian Dashboard</a>
</p>

</body>
</html>
