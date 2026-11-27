
<?php
require_once __DIR__ . '/../config/database.php';

$query = trim($_GET['q'] ?? '');
$books = [];

if ($query !== '') {
    $stmt = $pdo->prepare(
        "SELECT ebook_id, title, author, category, copies
         FROM ebooks
         WHERE title LIKE :search
            OR author LIKE :search
            OR category LIKE :search
         ORDER BY title ASC"
    );

    $stmt->execute([
        'search' => '%' . $query . '%'
    ]);

    $books = $stmt->fetchAll();
}

function escape($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results - Online School Library</title>
</head>

<body>

    <h1>Search Results</h1>

    <form method="GET" action="home-search.php">
        <input
            type="text"
            name="q"
            placeholder="Search books..."
            value="<?= escape($query) ?>"
        >
        <button type="submit">Search</button>
    </form>

    <p>
        <a class="login-btn" href="index.html">Back to Home</a>
    </p>

    <?php if ($query === ''): ?>

        <p>Please enter a search term.</p>

    <?php elseif (count($books) > 0): ?>

        <h2>Books matching: <?= escape($query) ?></h2>

        <?php foreach ($books as $book): ?>
            <div>
                <h3><?= escape($book['title']) ?></h3>
                <p>Author: <?= escape($book['author']) ?></p>
                <p>Category: <?= escape($book['category']) ?></p>
                <p>Available copies: <?= escape($book['copies']) ?></p>
            </div>
        <?php endforeach; ?>

    <?php else: ?>

        <p>No books found.</p>

    <?php endif; ?>

</body>
</html>
