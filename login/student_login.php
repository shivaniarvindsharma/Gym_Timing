<?php
session_start();
include('../includes/db_connection.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $roll_number = $_POST['roll_number'];
    $password = $_POST['password'];

    try {

        if (empty($roll_number) || empty($password)) {
            header("Location: student_login.php?error=" . urlencode("Roll number and password are required."));
            exit();
        }

        $stmt = $pdo->prepare("SELECT roll_number, first_name, last_name, password FROM students WHERE roll_number = ?");
        $stmt->execute([$roll_number]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {

            session_regenerate_id(true);

            $_SESSION['roll_number'] = $user['roll_number'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['last_name'] = $user['last_name'];

            header("Location: ../booking/slot_booking_form.php");
            exit();
        } else {

            header("Location: student_login.php?error=" . urlencode("Invalid roll number or password. Please try again."));
            exit();
        }
    } catch (PDOException $e) {

        echo "Database Error: " . htmlspecialchars($e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/form-styles.css">
    <title>Student Login</title>
    <style>
        .error-message {
            color: red;
            margin: 10px 0;
            display: none;
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <ul>
            <li><a href="../index.php" class="btn">Home</a></li>
            <li><a href="../about.php" class="btn">About Us</a></li>
            <li><a href="../gallery.php" class="btn">Gallery</a></li>
            <li><a href="../contact.php" class="btn">Contact</a></li>
        </ul>
    </nav>

    <div class="container">
        <div class="image-container"></div>
        <div class="form-container">
            <h1>Student Login</h1>
            <?php

            if (isset($_GET['error'])) {
                echo "<p class='error-message' id='error-message'>" . htmlspecialchars($_GET['error']) . "</p>";
            }
            ?>
            <form action="student_login.php" method="POST">
                <label for="roll_number">Roll Number:</label>
                <input type="text" id="roll_number" name="roll_number" required>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
                <button type="submit">Login</button>
                <p>Don't have an account? <a href="student_register.php">Sign up here</a>.</p>
            </form>
        </div>
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