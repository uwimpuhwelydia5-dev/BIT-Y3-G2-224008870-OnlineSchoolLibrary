
# DATABASE DOCUMENTATION

## Project: Online School Library Management System

### 1. Database Overview

Database name: online-library

The database stores information about students, librarians, E-books, borrowing records, and reading progress.

### 2. Database Tables

The system contains four main tables:

#### A. Users Table

Stores information about registered students and librarians.

| Field | Description |
|---|---|
| user_id | Unique user identification |
| username | User login name |
| password | Hashed user password |
| role | Student or librarian |

#### B. Ebooks Table

Stores information about available electronic books.

| Field | Description |
|---|---|
| ebook_id | Unique book identification |
| title | Book title |
| author | Book author |
| category | Book category |
| file_url | Location of the E-book file |
| copies | Available copies |

#### C. Borrowings Table

Stores information about books borrowed by students.

| Field | Description |
|---|---|
| borrow_id | Unique borrowing identification |
| ebook_id | Reference to the borrowed book |
| user_id | Reference to the student |
| borrowed_at | Date the book was borrowed |
| due_date | Expected return date |
| returned_at | Actual return date |

#### D. Reading Logs Table

Stores information about student reading progress.

| Field | Description |
|---|---|
| log_id | Unique reading log identification |
| borrow_id | Reference to the borrowing record |
| pages_read | Number of pages read |
| last_read_at | Last reading update date |

### 3. Database Relationships

- One user can have many borrowing records.
- One E-book can have many borrowing records.
- Each borrowing record belongs to one user and one E-book.
- Each borrowing record has one reading log.

### 4. Primary Keys

- users: user_id
- ebooks: ebook_id
- borrowings: borrow_id
- reading_logs: log_id

### 5. Foreign Keys

- borrowings.user_id references users.user_id.
- borrowings.ebook_id references ebooks.ebook_id.
- reading_logs.borrow_id references borrowings.borrow_id.

### 6. Conclusion

The online-library database organizes information into related tables. It supports user management, E-book management, borrowing, returning, and reading progress tracking.
