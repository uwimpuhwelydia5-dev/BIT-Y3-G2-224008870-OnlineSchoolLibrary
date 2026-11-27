
<?php
session_start();
require_once __DIR__ . '/../config/database.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([":email" => $email]);

    $user = $stmt->fetch();

    if ($user && password_verify($password, $user["password_hash"])) {

        session_regenerate_id(true);

        $_SESSION["user_id"] = $user["user_id"];
        $_SESSION["username"] = $user["username"];
        $_SESSION["role"] = $user["role"];

        if ($user["role"] === "librarian") {
            header("Location: librarian.php");
        } else {
            header("Location: dashboard.php");
        }

        exit;

    } else {
        $message = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login - Online School Library</title>

    <link rel="stylesheet" href="css/style.css">

    <style>
        body {
            background: linear-gradient(135deg, #f3e8ff, #ede9fe);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            width: 100%;
            max-width: 420px;
            background: #ffffff;
            padding: 40px 35px;
            border-radius: 16px;
            box-shadow: 0 8px 25px rgba(88, 28, 135, 0.15);
        }

        .login-icon {
            text-align: center;
            font-size: 55px;
            margin-bottom: 10px;
        }

        .login-container h1 {
            text-align: center;
            color: rgb(85, 85, 247);
            margin-bottom: 8px;
        }

        .login-description {
            text-align: center;
            color: #64748b;
            margin-bottom: 28px;
        }

        .login-container label {
            display: block;
            font-weight: bold;
            color: #334155;
            margin-bottom: 7px;
        }

        .login-container input {
            width: 100%;
            padding: 13px;
            border: 1px solid #d8b4fe;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 15px;
        }

        .login-container input:focus {
            outline: none;
            border-color: rgb(88, 85, 247);
            box-shadow: 0 0 5px rgba(168, 85, 247, 0.3);
        }

        .login-container button {
            width: 100%;
            padding: 13px;
            background: rgb(85, 93, 247);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        .login-container button:hover {
            background: rgb(72, 51, 234);
        }

        .login-message {
            background: #fee2e2;
            color: #b91c1c;
            padding: 10px;
            border-radius: 7px;
            text-align: center;
            margin-bottom: 18px;
        }

        .register-link {
            text-align: center;
            margin-top: 22px;
            color: #64748b;
        }

        .register-link a {
            color: #9333ea;
            font-weight: bold;
            text-decoration: none;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        .back-home {
            text-align: center;
            margin-top: 15px;
        }

        .back-home a {
            color: #5b33ea;
            text-decoration: none;
        }
    </style>
</head>

<body>

    <div class="login-container">

        <div class="login-icon">📚</div>

        <h1>Welcome Back!</h1>

        <p class="login-description">
            Online School Library System
        </p>

        <?php if (!empty($message)): ?>
            <div class="login-message">
                <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">

            <label for="email">Email Address</label>
            <input
                type="email"
                id="email"
                name="email"
                placeholder="Enter your email"
                required
            >

            <label for="password">Password</label>
            <input
                type="password"
                id="password"
                name="password"
                placeholder="Enter your password"
                required
            >

            <button type="submit">Login</button>

        </form>

        <p class="register-link">
            Don't have an account?
            <a href="register.php">Register here</a>
        </p>

        <p class="back-home">
            <a  class="login-btn" href="index.html">← Back to Home</a>
        </p>

    </div>

</body>
</html>
