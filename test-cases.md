
# SOFTWARE TESTING DOCUMENT

## Project: Online School Library Management System

### 1. Introduction

This document describes the test cases used to evaluate the functionality of the Online School Library Management System.

### 2. Software Test Cases

| Test ID | Feature | Test Description | Expected Result | Actual Result | Status |
|---|---|---|---|---|---|
| TC01 | Student Registration | Register a new student | Student account is created | Successful | Pass |
| TC02 | Student Login | Log in using valid student credentials | Student dashboard opens | Successful | Pass |
| TC03 | Librarian Login | Log in using librarian credentials | Librarian dashboard opens | Successful | Pass |
| TC04 | Add E-book | Add a new E-book | Book is saved in database | Successful | Pass |
| TC05 | Edit E-book | Update existing book information | Book information is updated | Successful | Pass |
| TC06 | Delete E-book | Delete a test book | Book is removed from the list | Successful | Pass |
| TC07 | Search E-books | Search using a book title | Matching books appear | Successful | Pass |
| TC08 | Borrow E-book | Student borrows an available book | Borrowing record is created and copies decrease | Successful | Pass |
| TC09 | Return E-book | Librarian returns a borrowed book | Return is recorded and copies increase | Successful | Pass |
| TC10 | Reading Progress | Student updates pages read | Reading progress is updated | Successful | Pass |
| TC11 | Borrowing History | Student views borrowing history | Borrowed books and due dates appear | Successful | Pass |
| TC12 | Logout | User logs out | User returns to login page | Successful | Pass |
| TC13 | Access Control | Student attempts to access librarian page | Access is denied | Successful | Pass |

### 3. Conclusion

The testing process demonstrated that the main functions of the Online School Library Management System worked successfully during the tests. Student and librarian activities, book management, borrowing, returning, and reading progress were tested.
