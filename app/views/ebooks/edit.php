
<?php
function escapeEditValue($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit E-book</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f7fb;
            margin: 30px;
        }

        h1 {
            color: #1e3a8a;
        }

        form {
            background: white;
            padding: 25px;
            max-width: 500px;
            border-radius: 8px;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        input {
            display: block;
            width: 95%;
            box-sizing: border-box;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            margin-top: 20px;
            padding: 12px 20px;
            background: #1e3a8a;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .message {
            color: green;
        }

        .error {
            color: red;
        }
    </style>
</head>

<body>

<h1>Edit E-book</h1>

<?php if (!empty($message)): ?>
    <p class="<?= !empty($error) ? 'error' : 'message' ?>">
        <?= escapeEditValue($message) ?>
    </p>
<?php endif; ?>

<form method="POST" action="mvc-edit-book.php?id=<?= (int)$book['ebook_id'] ?>">

    <input type="hidden" name="csrf_token"
           value="<?= escapeEditValue($csrf_token) ?>">

    <label for="title">Book Title</label>
    <input type="text" id="title" name="title"
           value="<?= escapeEditValue($old['title'] ?? $book['title']) ?>"
           required maxlength="200">

    <label for="author">Author</label>
    <input type="text" id="author" name="author"
           value="<?= escapeEditValue($old['author'] ?? $book['author']) ?>"
           required maxlength="150">

    <label for="category">Category</label>
    <input type="text" id="category" name="category"
           value="<?= escapeEditValue($old['category'] ?? $book['category']) ?>"
           required maxlength="100">

    <label for="file_url">PDF File Path</label>
    <input type="text" id="file_url" name="file_url"
           value="<?= escapeEditValue($old['file_url'] ?? $book['file_url']) ?>"
           required maxlength="255">

    <label for="copies">Available Copies</label>
    <input type="number" id="copies" name="copies"
           value="<?= escapeEditValue($old['copies'] ?? $book['copies']) ?>"
           min="0" required>

    <button type="submit">Update E-book</button>

</form>

<p><a class="login-btn" href="mvc-books.php">Back to E-books</a></p>
<p><a class="login-btn" href="librarian.php">Librarian Dashboard</a></p>

</body>
</html>
