



<?php
function escapeStudentBook($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available E-books</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
            background: #f4f7fb;
        }

        h2 {
            color: #1e3a5f;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background: #1e3a5f;
            color: white;
        }

        button {
            background: #1769aa;
            color: white;
            border: none;
            padding: 8px 14px;
            cursor: pointer;
            border-radius: 4px;
        }

        .read-link {
            display: inline-block;
            background: #15803d;
            color: white;
            padding: 8px 14px;
            text-decoration: none;
            border-radius: 4px;
            margin-bottom: 5px;
        }

        .unavailable {
            color: red;
        }

        .debug {
            background: #fff3cd;
            padding: 10px;
            margin: 10px 0;
            overflow-wrap: anywhere;
        }
    </style>
</head>

<body>

<h2>Available E-books</h2>

<p>Select a book you would like to read or borrow.</p>

<table>
    <thead>
        <tr>
            <th>Title</th>
            <th>Author</th>
            <th>Category</th>
            <th>Available Copies</th>
            <th>Actions</th>
        </tr>
    </thead>

    <tbody>

        <?php foreach ($books as $book): ?>

            <?php
            $fileUrl = trim($book['file_url'] ?? '');

            $pdfPath = dirname(__DIR__, 3) . '/public/' . $fileUrl;

            $isUploadedPdf = is_file($pdfPath);
            ?>

            <tr>

                <td><?= escapeStudentBook($book['title']) ?></td>

                <td><?= escapeStudentBook($book['author']) ?></td>

                <td><?= escapeStudentBook($book['category']) ?></td>

                <td><?= escapeStudentBook($book['copies']) ?></td>

                <td>

                    <div class="debug">
                        <strong>DEBUG:</strong>
                        <?= escapeStudentBook($fileUrl) ?><br>

                        <strong>File path:</strong>
                        <?= escapeStudentBook($pdfPath) ?><br>

                        <strong>File exists:</strong>
                        <?= $isUploadedPdf ? 'YES' : 'NO' ?>
                    </div>

                    <?php if ($isUploadedPdf): ?>

                        <a class="read-link"
                           href="/Online-library/public/<?= escapeStudentBook($fileUrl) ?>"
                           target="_blank"
                           rel="noopener noreferrer">
                            Read PDF
                        </a>

                    <?php else: ?>

                        <span class="unavailable">
                            PDF not available
                        </span>

                    <?php endif; ?>

                    <?php if ($book['copies'] > 0): ?>

                        <form method="POST"
                              action="/Online-library/public/mvc-borrow-book.php">

                            <input type="hidden"
                                   name="ebook_id"
                                   value="<?= (int)$book['ebook_id'] ?>">

                            <input type="hidden"
                                   name="csrf_token"
                                   value="<?= escapeStudentBook($csrf_token) ?>">

                            <button type="submit">Borrow</button>

                        </form>

                    <?php else: ?>

                        <span class="unavailable">Unavailable</span>

                    <?php endif; ?>

                </td>

            </tr>

        <?php endforeach; ?>

    </tbody>
</table>

<p>
    <a class="login-btn" href="/Online-library/public/dashboard.php"> Back to Dashboard </a>
</p>

</body>
</html>
