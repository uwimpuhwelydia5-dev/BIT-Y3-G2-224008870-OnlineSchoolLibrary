

<?php
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "librarian") {
    header("Location: login.php");
    exit;
}

$username = htmlspecialchars($_SESSION["username"], ENT_QUOTES, 'UTF-8');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Librarian Dashboard - Online School Library</title>

    <link rel="stylesheet" href="css/style.css">

    <style>
        /* Center dashboard welcome section */
        .dashboard .dashboard-header {
            text-align: center !important;
            width: 100%;
            margin-left: auto;
            margin-right: auto;
        }

        .dashboard .dashboard-header h1,
        .dashboard .dashboard-header h2,
        .dashboard .dashboard-header p {
            text-align: center !important;
        }

        /* Light purple headings */
        .dashboard .dashboard-header h1,
        .dashboard .dashboard-header h2,
        .dashboard > h2,
        .dashboard .service-card h3 {
            color: #5565f7 !important;
        }
    </style>
</head>

<body>

<div class="dashboard">

    <div class="dashboard-header">

        <h1>Online School Library</h1>

        <h2>Librarian Dashboard</h2>

        <p>Welcome, <?= $username ?>!</p>

        <p>Manage books, borrowing records and student library services.</p>

    </div>

    <h2>Library Management</h2>

    <div class="book-container">

        <div class="service-card">
            <div class="book-icon">📚</div>
            <h3>Manage E-books</h3>
            <p>View, search, edit, upload PDF files and delete available books.</p>
            <a class="login-btn" href="mvc-books.php">Manage E-books</a>
        </div>

        <div class="service-card">
            <div class="book-icon">➕</div>
            <h3>Add New E-book</h3>
            <p>Add new books and upload their PDF documents.</p>
            <a class="login-btn" href="mvc-add-book.php">Add E-book</a>
        </div>

        <div class="service-card">
            <div class="book-icon">🔄</div>
            <h3>Manage Book Returns</h3>
            <p>View borrowed books and process student returns.</p>
            <a class="login-btn" href="all-borrowings.php">Manage Returns</a>
        </div>

        <div class="service-card">
            <div class="book-icon">📋</div>
            <h3>Borrowing Records</h3>
            <p>View borrowing history and student borrowing information.</p>
            <a class="login-btn" href="all-borrowings.php">View Borrowing Records</a>
        </div>

        <div class="service-card">
            <div class="book-icon">🚪</div>
            <h3>Logout</h3>
            <p>Securely leave your librarian account.</p>
            <a class="login-btn" href="logout.php">Logout</a>
        </div>

    </div>

</div>

</body>
</html>
