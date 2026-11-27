

<?php
function escapeBookValue($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add E-book - MVC</title>

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
        }

        input {
            width: 95%;
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
            margin-bottom: 15px;
            color: #166534;
        }

        .error {
            color: #dc2626;
        }
    </style>
</head>

<body>

    <h1>Add a New E-book</h1>

    <?php if (!empty($message)): ?>
        <p class="<?= !empty($error) ? 'error' : 'message' ?>">
            <?= escapeBookValue($message) ?>
        </p>
    <?php endif; ?>

    <form method="POST"
          action="mvc-add-book.php"
          enctype="multipart/form-data">

        <input
            type="hidden"
            name="csrf_token"
            value="<?= escapeBookValue($csrf_token) ?>"
        >

        <label>Book Title</label>
        <input type="text" name="title"
               value="<?= escapeBookValue($old['title'] ?? '') ?>"
               required maxlength="200">

        <label>Author</label>
        <input type="text" name="author"
               value="<?= escapeBookValue($old['author'] ?? '') ?>"
               required maxlength="150">

        <label>Category</label>
        <input type="text" name="category"
               value="<?= escapeBookValue($old['category'] ?? '') ?>"
               required maxlength="100">

        <label>Select PDF Book</label>
        <input type="file"
               name="pdf_file"
               accept=".pdf,application/pdf"
               required>

        <label>Available Copies</label>
        <input type="number" name="copies"
               value="<?= escapeBookValue($old['copies'] ?? '1') ?>"
               min="0" required>

        <button type="submit">Upload and Add E-book</button>

    </form>

    <p><a class="login-btn" href="mvc-books.php">View E-books</a></p>
    <p><a class="login-btn" href="librarian.php">Back to Librarian Dashboard</a></p>

</body>
</html>
