

<?php
require_once __DIR__ . '/../config/database.php';

$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($username === "" || $email === "" || $password === "") {
        $message = "Please fill in all fields.";
        $messageType = "error";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
        $messageType = "error";
    } elseif (strlen($password) < 8) {
        $message = "Password must contain at least 8 characters.";
        $messageType = "error";
    } else {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        try {
            $sql = "INSERT INTO users (username, email, password_hash)
                    VALUES (:username, :email, :password_hash)";

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                ":username" => $username,
                ":email" => $email,
                ":password_hash" => $passwordHash
            ]);

            $message = "Registration successful! You can now log in.";
            $messageType = "success";

        } catch (PDOException $e) {
            if ($e->getCode() === "23000") {
                $message = "This email address is already registered.";
            } else {
                $message = "Registration failed. Please try again.";
            }

            $messageType = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Student Registration - Online School Library</title>

    <link rel="stylesheet" href="css/style.css">

    <style>
        body {
            background: linear-gradient(135deg, #dbeafe, #eff6ff);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .register-container {
            width: 100%;
            max-width: 450px;
            background: #ffffff;
            padding: 40px 35px;
            border-radius: 16px;
            box-shadow: 0 8px 25px rgba(30, 64, 175, 0.15);
        }

        .register-icon {
            text-align: center;
            font-size: 55px;
            margin-bottom: 10px;
        }

        .register-container h1 {
            text-align: center;
            color: #2563eb;
            margin-bottom: 8px;
        }

        .register-description {
            text-align: center;
            color: #64748b;
            margin-bottom: 28px;
        }

        .register-container label {
            display: block;
            font-weight: bold;
            color: #334155;
            margin-bottom: 7px;
        }

        .register-container input {
            width: 100%;
            padding: 13px;
            border: 1px solid #bfdbfe;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 15px;
        }

        .register-container input:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 5px rgba(37, 99, 235, 0.3);
        }

        .register-container button {
            width: 100%;
            padding: 13px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        .register-container button:hover {
            background: #1d4ed8;
        }

        .register-message {
            padding: 12px;
            border-radius: 7px;
            text-align: center;
            margin-bottom: 18px;
        }

        .register-message.success {
            background: #dcfce7;
            color: #166534;
        }

        .register-message.error {
            background: #fee2e2;
            color: #b91c1c;
        }

        .login-link {
            text-align: center;
            margin-top: 22px;
            color: #64748b;
        }

        .login-link a,
        .back-home a {
            color: #2563eb;
            font-weight: bold;
            text-decoration: none;
        }

        .login-link a:hover,
        .back-home a:hover {
            text-decoration: underline;
        }

        .back-home {
            text-align: center;
            margin-top: 15px;
        }
    </style>
</head>

<body>

    <div class="register-container">

        <div class="register-icon">📚</div>

        <h1>Create Account</h1>

        <p class="register-description">
            Join the Online School Library
        </p>

        <?php if (!empty($message)): ?>
            <div class="register-message <?= $messageType ?>">
                <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">

            <label for="username">Username</label>
            <input
                type="text"
                id="username"
                name="username"
                placeholder="Enter your username"
                required
            >

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
                placeholder="Create a password (minimum 8 characters)"
                minlength="8"
                required
            >

            <button type="submit">Create Account</button>

        </form>

        <p class="login-link">
            Already registered?
            <a class="login-btn" href="login.php">Login here</a>
        </p>

        <p class="back-home">
            <a class="login-btn" href="index.html">← Back to Home</a>
        </p>

    </div>

</body>
</html>
