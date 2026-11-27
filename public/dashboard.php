


<?php
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "student") {
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

    <title>Student Dashboard - Online School Library</title>

    <link rel="stylesheet" href="css/style.css">

    <style>
        /* Center student dashboard welcome section */
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
            color: rgb(85, 112, 247) !important;
        }
    </style>
</head>

<body>

<div class="dashboard">

    <div class="dashboard-header">

        <h1>Online School Library</h1>

        <h2>Welcome, <?= $username ?>!</h2>

        <p>You are successfully logged in as a student.</p>

    </div>

    <h2>Student Services</h2>

    <div class="book-container">

        <div class="service-card">
            <div class="book-icon">📚</div>
            <h3>Browse E-books</h3>
            <p>View available books and borrow your preferred e-books.</p>
            <a class="login-btn" href="mvc-student-books.php">Browse E-books</a>
        </div>

        <div class="service-card">
            <div class="book-icon">📖</div>
            <h3>My Borrowing History</h3>
            <p>View your borrowed and returned books.</p>
            <a class="login-btn" href="my-borrowings.php">View Borrowing History</a>
        </div>

        <div class="service-card">
            <div class="book-icon">📊</div>
            <h3>Reading Progress</h3>
            <p>Record and update the pages you have read.</p>
            <a class="login-btn" href="my-borrowings.php">Update Reading Progress</a>
        </div>

        <div class="service-card">
            <div class="book-icon">🚪</div>
            <h3>Logout</h3>
            <p>Securely leave your student account.</p>
            <a class="login-btn" href="logout.php">Logout</a>
        </div>

    </div>

</div>

</body>
</html>
