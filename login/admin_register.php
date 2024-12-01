<?php

session_start();

if (!isset($_SESSION['admin_id'])) {

    header("Location: ../login/admin_login.php");
    exit();
}
include('../includes/db_connection.php');

$successMessage = "";
$errorMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];


    if (!preg_match('/[A-Z]/', $password) || !preg_match('/\d/', $password) || !preg_match('/[\W_]/', $password) || strlen($password) < 8) {
        $errorMessage = "Password must contain at least one uppercase letter, one symbol, one number, and be at least 8 characters long.";
    } else {
        try {

            $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM admin WHERE username = ?");
            $checkStmt->bindParam(1, $username);
            $checkStmt->execute();
            $usernameExists = $checkStmt->fetchColumn();

            if ($usernameExists) {
                $errorMessage = "Username already exists. Please use a different username.";
            } else {

                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);


                $stmt = $pdo->prepare("INSERT INTO admin (username, password, email) VALUES (?, ?, ?)");
                $stmt->bindParam(1, $username);
                $stmt->bindParam(2, $hashedPassword);
                $stmt->bindParam(3, $email);


                if ($stmt->execute()) {
                    $successMessage = "Admin added successfully!";

                    echo "<script>setTimeout(function() { window.location.href = '../admin_dashboard/members.php'; }, 2000);</script>";
                } else {
                    $errorMessage = "Error: Could not register admin.";
                }
            }
        } catch (PDOException $e) {
            $errorMessage = "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/admin-form-styles.css">
    <title>Admin Registration</title>
</head>

<body>
    <div class="container">
        <h1>Admin Registration</h1>

        <?php if (!empty($successMessage)): ?>
            <div class="success-message" id="message" style="color: green; font-weight: bold;"><?php echo $successMessage; ?></div>
        <?php endif; ?>
        <?php if (!empty($errorMessage)): ?>
            <div class="error-message" id="error-message" style="color: red; font-weight: bold;"><?php echo $errorMessage; ?></div>
        <?php endif; ?>
        <form id="registrationForm" action="admin_register.php" method="POST" onsubmit="return validatePassword();" novalidate>
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password : (must contain atleast 1 captital , 1 small , 1 number , 1 symbol and atleast 8 characters)</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Sign Up</button>
        </form>
    </div>
</body>

<script>
    let errorTimeout;

    function validatePassword() {
        const password = document.getElementById('password').value;
        const errorMessage = document.getElementById('error-message');
        const passwordRegex = /^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;

        if (!passwordRegex.test(password)) {

            errorMessage.textContent = "Password must contain at least one uppercase letter, one number, one symbol, and be at least 8 characters long.";
            errorMessage.style.display = 'block';

            clearTimeout(errorTimeout);

            errorTimeout = setTimeout(() => {
                errorMessage.style.display = 'none';
            }, 5000);

            return false;
        }
        return true;
    }

    document.addEventListener('DOMContentLoaded', () => {
        const successMessage = document.querySelector('.success-message');
        if (successMessage) {
            setTimeout(() => {
                window.location.href = '../admin_dashboard/members.php';
            }, 2000);
        }
    });
</script>

</html>