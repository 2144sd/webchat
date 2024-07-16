<?php
session_start();
include 'database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Check if username already exists
    $checkSql = "SELECT COUNT(*) FROM users WHERE username = :username";
    $checkStmt = $pdo->prepare($checkSql);
    $checkStmt->bindParam(':username', $username);
    $checkStmt->execute();
    $userExists = $checkStmt->fetchColumn();

    if ($userExists > 0) {
        echo "<p style='color: red;'>Username already taken! Please choose another.</p>";
    } else {
        // Insert new user
        $sql = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);

        if ($stmt->execute()) {
            // Get the inserted user's ID
            $userId = $pdo->lastInsertId();
            // Store user info in session
            $_SESSION['user_id'] = $userId;
            $_SESSION['username'] = $username;

            // Redirect to dashboard
            header("Location: dashboard.php");
            exit();
        } else {
            echo "<p style='color: red;'>Signup failed!</p>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Signup</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }
        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 3px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            border: none;
            color: #fff;
            font-size: 16px;
            border-radius: 3px;
            cursor: pointer;
        }
        button:hover {
            background-color: #218838;
        }
        p {
            text-align: center;
            margin-top: 10px;
            color: #555;
        }
        .error {
            color: red;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Signup</h2>
        <?php if (isset($errorMessage)): ?>
            <p class="error"><?php echo htmlspecialchars($errorMessage); ?></p>
        <?php endif; ?>
        <form method="POST" action="">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" required><br>
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required><br>
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required><br>
            <button type="submit">Signup</button>
        </form>
        <p>Already have an account? <a href="login.php">Login here</a>.</p>
    </div>
</body>
</html>
