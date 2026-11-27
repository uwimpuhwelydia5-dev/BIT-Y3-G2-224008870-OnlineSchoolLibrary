





<?php

class Ebook
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    // 1. Get all books, including PDF file paths
    public function getAll()
    {
        $sql = "SELECT ebook_id, title, author, category, file_url, copies
                FROM ebooks
                ORDER BY ebook_id DESC";

        $stmt = $this->pdo->query($sql);

        return $stmt->fetchAll();
    }

    // 2. Search books, including PDF file paths
    public function search($keyword)
    {
        $sql = "SELECT ebook_id, title, author, category, file_url, copies
                FROM ebooks
                WHERE title LIKE :title
                OR author LIKE :author
                OR category LIKE :category
                ORDER BY title";

        $stmt = $this->pdo->prepare($sql);

        $search = '%' . $keyword . '%';

        $stmt->execute([
            ':title' => $search,
            ':author' => $search,
            ':category' => $search
        ]);

        return $stmt->fetchAll();
    }

    // 3. Find one book
    public function findById($id)
    {
        $sql = "SELECT * FROM ebooks WHERE ebook_id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);

        return $stmt->fetch();
    }

    // 4. Add a new book
    public function create($title, $author, $category, $file_url, $copies)
    {
        $sql = "INSERT INTO ebooks
                (title, author, category, file_url, copies)
                VALUES
                (:title, :author, :category, :file_url, :copies)";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':title' => $title,
            ':author' => $author,
            ':category' => $category,
            ':file_url' => $file_url,
            ':copies' => $copies
        ]);
    }

    // 5. Update a book
    public function update($id, $title, $author, $category, $file_url, $copies)
    {
        $sql = "UPDATE ebooks
                SET title = :title,
                    author = :author,
                    category = :category,
                    file_url = :file_url,
                    copies = :copies
                WHERE ebook_id = :id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':id' => $id,
            ':title' => $title,
            ':author' => $author,
            ':category' => $category,
            ':file_url' => $file_url,
            ':copies' => $copies
        ]);
    }

    // 6. Delete a book
    public function delete($id)
    {
        $sql = "DELETE FROM ebooks WHERE ebook_id = :id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':id' => $id
        ]);
    }

    // 7. Borrow a book
    public function borrow($ebook_id, $user_id)
    {
        try {
            $this->pdo->beginTransaction();

            $sql = "SELECT copies
                    FROM ebooks
                    WHERE ebook_id = :ebook_id
                    FOR UPDATE";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':ebook_id' => $ebook_id]);

            $book = $stmt->fetch();

            if (!$book || $book['copies'] < 1) {
                $this->pdo->rollBack();
                return false;
            }

            $sql = "UPDATE ebooks
                    SET copies = copies - 1
                    WHERE ebook_id = :ebook_id";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':ebook_id' => $ebook_id]);

            $sql = "INSERT INTO borrowings
                    (ebook_id, user_id, borrowed_at, due_date)
                    VALUES
                    (:ebook_id, :user_id, NOW(),
                    DATE_ADD(CURDATE(), INTERVAL 14 DAY))";

            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([
                ':ebook_id' => $ebook_id,
                ':user_id' => $user_id
            ]);

            $borrow_id = $this->pdo->lastInsertId();

            $sql = "INSERT INTO reading_logs
                    (borrow_id, pages_read, last_read_at)
                    VALUES
                    (:borrow_id, 0, NOW())";

            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([
                ':borrow_id' => $borrow_id
            ]);

            $this->pdo->commit();

            return true;

        } catch (PDOException $e) {

            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            error_log($e->getMessage());
            throw $e;
        }
    }

    // 8. Return a borrowed book
    public function returnBook($borrow_id)
    {
        try {
            $this->pdo->beginTransaction();

            $sql = "SELECT ebook_id
                    FROM borrowings
                    WHERE borrow_id = :borrow_id
                    AND returned_at IS NULL
                    FOR UPDATE";

            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([
                ':borrow_id' => $borrow_id
            ]);

            $borrowing = $stmt->fetch();

            if (!$borrowing) {
                $this->pdo->rollBack();
                return false;
            }

            $sql = "UPDATE borrowings
                    SET returned_at = NOW()
                    WHERE borrow_id = :borrow_id";

            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([
                ':borrow_id' => $borrow_id
            ]);

            $sql = "UPDATE ebooks
                    SET copies = copies + 1
                    WHERE ebook_id = :ebook_id";

            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([
                ':ebook_id' => $borrowing['ebook_id']
            ]);

            $this->pdo->commit();

            return true;

        } catch (PDOException $e) {

            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            error_log($e->getMessage());
            throw $e;
        }
    }

    // 9. Update student reading progress
    public function updateReadingProgress($borrow_id, $user_id, $pages_read)
    {
        $sql = "SELECT borrow_id
                FROM borrowings
                WHERE borrow_id = :borrow_id
                AND user_id = :user_id
                AND returned_at IS NULL";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':borrow_id' => $borrow_id,
            ':user_id' => $user_id
        ]);

        if (!$stmt->fetch()) {
            return false;
        }

        $sql = "UPDATE reading_logs
                SET pages_read = :pages_read,
                    last_read_at = NOW()
                WHERE borrow_id = :borrow_id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':pages_read' => $pages_read,
            ':borrow_id' => $borrow_id
        ]);
    }
}

?>
