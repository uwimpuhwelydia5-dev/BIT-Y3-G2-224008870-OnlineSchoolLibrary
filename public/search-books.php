
<?php
header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . '/../config/database.php';

try {
    $search = trim($_GET["q"] ?? "");

    if ($search === "") {
        echo json_encode([]);
        exit;
    }

    $stmt = $pdo->prepare(
        "SELECT ebook_id, title, author, category, copies
         FROM ebooks
         WHERE title LIKE :search
            OR author LIKE :search
            OR category LIKE :search
         ORDER BY title
         LIMIT 20"
    );

    $stmt->execute([
        ":search" => "%" . $search . "%"
    ]);

    $books = $stmt->fetchAll();

    echo json_encode($books);

} catch (PDOException $e) {
    error_log($e->getMessage());

    http_response_code(500);

    echo json_encode([
        "error" => "Unable to search books."
    ]);
}
