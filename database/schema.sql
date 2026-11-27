
-- =========================================
-- ONLINE SCHOOL LIBRARY E-BOOK SYSTEM
-- Database: online_library_db
-- =========================================

-- TABLE 1: USERS
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('student', 'librarian') NOT NULL DEFAULT 'student',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;


-- TABLE 2: EBOOKS
CREATE TABLE ebooks (
    ebook_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    author VARCHAR(150) NOT NULL,
    category VARCHAR(100) NOT NULL,
    file_url VARCHAR(255) NOT NULL,
    copies INT NOT NULL DEFAULT 1
) ENGINE=InnoDB;


-- TABLE 3: BORROWINGS
CREATE TABLE borrowings (
    borrow_id INT AUTO_INCREMENT PRIMARY KEY,
    ebook_id INT NOT NULL,
    user_id INT NOT NULL,
    borrowed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    due_date DATE NOT NULL,
    returned_at DATETIME DEFAULT NULL,

    CONSTRAINT fk_borrowing_ebook
        FOREIGN KEY (ebook_id)
        REFERENCES ebooks(ebook_id),

    CONSTRAINT fk_borrowing_user
        FOREIGN KEY (user_id)
        REFERENCES users(user_id),

    INDEX idx_borrowing_user (user_id),
    INDEX idx_borrowing_ebook (ebook_id)
) ENGINE=InnoDB;


-- TABLE 4: READING_LOGS
CREATE TABLE reading_logs (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    borrow_id INT NOT NULL UNIQUE,
    pages_read INT NOT NULL DEFAULT 0,
    last_read_at DATETIME DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_reading_borrowing
        FOREIGN KEY (borrow_id)
        REFERENCES borrowings(borrow_id)
        ON DELETE CASCADE
) ENGINE=InnoDB;
