<?php
session_start();

include('../includes/db_connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $defaultUsername = 'Admin';
    $defaultPassword = 'Admin@123';

    if ($username === $defaultUsername && $password === $defaultPassword) {
        $_SESSION['admin_id'] = 0;
        $_SESSION['username'] = $defaultUsername;

        header("Location: ../admin_dashboard/admin_dashboard.php");
        exit();
    }

    try {

        $stmt = $pdo->prepare("SELECT admin_id, username, password FROM admin WHERE username = ?");
        $stmt->execute([$username]);

        if ($stmt->rowCount() > 0) {
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            if (password_verify($password, $admin['password'])) {
                $_SESSION['admin_id'] = $admin['admin_id'];
                $_SESSION['username'] = $admin['username'];

                header("Location: ../admin_dashboard/admin_dashboard.php");
                exit();
            } else {
                $error = "Invalid username or password. Please try again.";
            }
        } else {
            $error = "Invalid username or password. Please try again.";
        }
    } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
    $pdo = null;
}


if (isset($error)) {
    header("Location: admin_login.php?error=" . urlencode($error));
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/admin-form-styles.css">
    <title>Admin Login</title>
    <style>
        .error-message {
            color: red;
            margin: 10px 0;
            display: none;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Admin Login</h1>
        <?php

        if (isset($_GET['error'])) {
            echo "<p class='error-message' id='error-message'>" . htmlspecialchars($_GET['error']) . "</p>";
        }
        ?>
        <form action="admin_login.php" method="POST">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit">Login</button>

        </form>
    </div>

    <script>
        const errorMessage = document.getElementById('error-message');
        if (errorMessage) {
            errorMessage.style.display = 'block';
            setTimeout(() => {
                errorMessage.style.display = 'none';
            }, 2000);
        }
    </script>
</body>

</html>