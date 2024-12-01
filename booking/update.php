<?php
session_start();

date_default_timezone_set('Asia/Kolkata');

// Check if the user is logged in
if (!isset($_SESSION['roll_number'])) {
    header("Location: ../login/student_login.php");
    exit();
}

// Include database connection file
include('../includes/db_connection.php');

// Fetch the roll number from session
$roll_number = $_SESSION['roll_number'];

// Retrieve current student details
$sql = "SELECT * FROM students WHERE roll_number = :roll_number";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':roll_number', $roll_number, PDO::PARAM_INT);
$stmt->execute();
$student = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle form submission for updating details
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = !empty($_POST['first_name']) ? $_POST['first_name'] : $student['first_name'];
    $last_name = !empty($_POST['last_name']) ? $_POST['last_name'] : $student['last_name'];
    $dob = !empty($_POST['dob']) ? $_POST['dob'] : $student['dob'];

    // Update query to change only the provided fields (excluding email)
    $sql_update = "UPDATE students SET first_name = :first_name, last_name = :last_name, dob = :dob WHERE roll_number = :roll_number";
    $stmt_update = $pdo->prepare($sql_update);
    $stmt_update->bindParam(':first_name', $first_name);
    $stmt_update->bindParam(':last_name', $last_name);
    $stmt_update->bindParam(':dob', $dob);
    $stmt_update->bindParam(':roll_number', $roll_number, PDO::PARAM_INT);

    if ($stmt_update->execute()) {
        $success_message = "Profile updated successfully.";
    } else {
        $error_message = "Failed to update profile. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile</title>
    <link rel="stylesheet" href="../assets/dashboard.css">
</head>
<body>
    <nav class="navbar">
        <ul>
            <li><a href="slot_booking_form.php" class="btn">Dashboard</a></li>
            <li><a href="fitness_groups.php" class="btn">Groups</a></li>
            <li><a href="feedback.php" class="btn">Give Feedback</a></li>
 <li><a href="profile.php" class="btn">Profile</a></li>
        </ul>
    </nav>
    <h1>Update Profile</h1>

    <?php if (isset($success_message)): ?>
        <p class="success-message"><?php echo htmlspecialchars($success_message); ?></p>
    <?php endif; ?>

    <?php if (isset($error_message)): ?>
        <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
    <?php endif; ?>

    <form style="width:400px; padding-left:750px; padding-right:750px;" action="update.php" method="POST">
        <label for="first_name">First Name:</label>
        <input type="text" id="first_name" name="first_name" placeholder="<?php echo htmlspecialchars($student['first_name']); ?>">

        <label for="last_name">Last Name:</label>
        <input type="text" id="last_name" name="last_name" placeholder="<?php echo htmlspecialchars($student['last_name']); ?>">

        <label for="dob">Date of Birth:</label>
        <input type="date" id="dob" name="dob" value="<?php echo htmlspecialchars($student['dob']); ?>">

        <button type="submit">Update Profile</button>
    </form>
</body>
</html>

