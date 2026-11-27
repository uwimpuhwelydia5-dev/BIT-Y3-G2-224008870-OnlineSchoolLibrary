<?php

require_once __DIR__ . '/../config/database.php';

$stmt = $pdo->query("SELECT file_url FROM ebooks ORDER BY ebook_id DESC LIMIT 1");

$book = $stmt->fetch();

if ($book) {
    $fileUrl = trim($book['file_url']);
    $filePath = __DIR__ . '/' . $fileUrl;

    echo "Database path: " . htmlspecialchars($fileUrl) . "<br>";

    echo "Full file path: " . htmlspecialchars($filePath) . "<br>";

    if (file_exists($filePath)) {
        echo "PDF file exists!";
    } else {
        echo "PDF file NOT FOUND!";
    }
} else {
    echo "No books found.";
}

?>