<?php
include('../includes/db_connection.php');
$successMessage = "";
$errorMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $roll_number = $_POST['roll_number'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $dob = $_POST['dob'];

    if (!preg_match('/^[a-zA-Z0-9._%+-]+@iitg\.ac\.in$/', $email)) {
        $errorMessage = "Email must end with @iitg.ac.in";
    } elseif (!preg_match('/[A-Z]/', $password) || !preg_match('/\d/', $password) || !preg_match('/[\W_]/', $password) || strlen($password) < 8) {
        $errorMessage = "Password must contain at least one uppercase letter, one number, one symbol, and be at least 8 characters long.";
    } else {

        $password = password_hash($password, PASSWORD_BCRYPT);

        try {

            $stmt = $pdo->prepare("INSERT INTO students (first_name, last_name, roll_number, email, password, dob) 
               VALUES (:first_name, :last_name, :roll_number, :email, :password, :dob)");

            $stmt->bindParam(':first_name', $first_name);
            $stmt->bindParam(':last_name', $last_name);
            $stmt->bindParam(':roll_number', $roll_number);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $password);
            $stmt->bindParam(':dob', $dob);


            if ($stmt->execute()) {
                $successMessage = "Student registered successfully!";
            } else {
                $errorMessage = "Error: Could not register student.";
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
    <link rel="stylesheet" href="../assets/form-styles.css">
    <title>Student Registration</title>
</head>

<body>
    <div class="container">
        <h1>Student Registration</h1>

        <?php if (!empty($successMessage)): ?>
            <div class="success-message" id="message" style="color: green; font-weight: bold;"><?php echo $successMessage; ?></div>
        <?php endif; ?>
        <?php if (!empty($errorMessage)): ?>
            <div class="error-message" id="error-message" style="color: red; font-weight: bold;"><?php echo $errorMessage; ?></div>
        <?php endif; ?>

        <form action="student_register.php" method="POST" onsubmit="return validateForm();">
            <label for="first_name">First Name:</label>
            <input type="text" id="first_name" name="first_name" required>

            <label for="last_name">Last Name:</label>
            <input type="text" id="last_name" name="last_name" required>

            <label for="roll_number">Roll Number:</label>
            <input type="text" id="roll_number" name="roll_number" required>

            <label for="dob">Date of Birth:</label>
            <input type="date" id="dob" name="dob" required max="<?php echo date('Y-m-d'); ?>">

            <label for="email">Email (must end with @iitg.ac.in):</label>
            <input type="email" id="email" name="email" required
                pattern="^[a-zA-Z0-9._%+-]+@iitg\.ac\.in$"
                title="Email must end with @iitg.ac.in">

            <label for="password">Password : (must contain atleast 1 captital , 1 small , 1 number , 1 symbol and atleast 8 characters)</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Register</button>
        </form>
    </div>

    <script>
        function validateForm() {
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            const errorMessage = document.getElementById('error-message');
            errorMessage.style.display = 'none';

            let errorText = "";

            const emailPattern = /^[a-zA-Z0-9._%+-]+@iitg\.ac\.in$/;
            if (!emailPattern.test(email)) {
                errorText += "Email must end with @iitg.ac.in. ";
            }

            const passwordPattern = /^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;
            if (!passwordPattern.test(password)) {
                errorText += "Password must contain at least one uppercase letter, one number, one symbol, and be at least 8 characters long.";
            }
            if (errorText) {
                errorMessage.textContent = errorText;
                errorMessage.style.display = 'block';
                setTimeout(() => {
                    errorMessage.style.display = 'none';
                }, 5000);
                return false;
            }
            return true;
        }

        document.addEventListener('DOMContentLoaded', () => {
            const errorMessage = document.querySelector('.error-message');
            if (errorMessage && errorMessage.textContent.trim() !== "") {
                setTimeout(() => {
                    errorMessage.style.display = 'none';
                }, 5000);
            }

            const successMessage = document.querySelector('.success-message');
            if (successMessage) {
                setTimeout(() => {
                    window.location.href = '../login/student_login.php';
                }, 2000);
            }
        });
    </script>

</body>

</html>