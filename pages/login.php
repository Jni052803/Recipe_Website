<?php
session_start();
require_once '../server/config.php'; 

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (!empty($username) && !empty($password)) {
        // Check if the user exists in the database
        $stmt = $conn->prepare("SELECT user_id, password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($user_id, $hashed_password);
            $stmt->fetch();

            if (password_verify($password, $hashed_password)) {
                // Password is correct, start session
                $_SESSION['user_id'] = $user_id;
                $_SESSION['username'] = $username;
                header("Location: index.php"); // Redirect to homepage
                exit();
            } else {
                $error = "Incorrect password!";
            }
        } else {
            $error = "No user found with that username!";
        }

        $stmt->close();
    } else {
        $error = "Please fill in both fields!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../css/style.css"> 
</head>
<body>
    <header>
        <h1>Login</h1>
    </header>

    <main class="login-form">
        <form action="login.php" method="POST" class="form-container">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <?php if ($error): ?>
                <p class="error-message"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <button type="submit" class="button">Login</button>
        </form>

        <p>Don't have an account? <a href="signup.php">Sign Up here</a>.</p>
    </main>
</body>
</html>


