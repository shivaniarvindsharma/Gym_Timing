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

// Fetch the student's details from the database using the roll number from session
$roll_number = $_SESSION['roll_number'];
$sql = "SELECT * FROM students WHERE roll_number = :roll_number";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':roll_number', $roll_number, PDO::PARAM_INT);
$stmt->execute();
$student = $stmt->fetch(PDO::FETCH_ASSOC);

// Calculate age if date of birth is available
$age = null;
if ($student && !empty($student['dob'])) {
    $dob = new DateTime($student['dob']);
    $today = new DateTime();
    $age = $today->diff($dob)->y;  // Calculate age in years
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile</title>
    <link rel="stylesheet" href="../assets/dashboard.css">
</head>
<body>
    <nav class="navbar">
        <ul>
            <li><a href="slot_booking_form.php" class="btn">Dashboard</a></li>
            <li><a href="fitness_groups.php" class="btn">Groups</a></li>
            <li><a href="feedback.php" class="btn">Give Feedback</a></li>
            <li><a style="background-color: #da6161;" href="profile.php" class="btn">Profile</a></li>
        </ul>
    </nav>
    
    <h1>Student Profile</h1>

    <li><a style="background-color: #da6161; margin-left:120px" href="update.php" class="btn">Update Profile</a></li>

    <div class="container1">
        <?php if ($student): ?>
            <table>
                <tr>
                    <th>Roll Number</th>
                    <td><?php echo htmlspecialchars($student['roll_number']); ?></td>
                </tr>
                <tr>
                    <th>First Name</th>
                    <td><?php echo htmlspecialchars($student['first_name']); ?></td>
                </tr>
                <tr>
                    <th>Last Name</th>
                    <td><?php echo htmlspecialchars($student['last_name']); ?></td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td><?php echo htmlspecialchars($student['email']); ?></td>
                </tr>
                <tr>
                    <th>Date of Birth</th>
                    <td><?php echo htmlspecialchars($student['dob']); ?></td>
                </tr>
                <tr>
                    <th>Age</th>
                    <td><?php echo $age !== null ? htmlspecialchars($age) . " years" : "N/A"; ?></td>
                </tr>
                <tr>
                    <th>Registration Date</th>
                    <td><?php echo htmlspecialchars($student['registration_date']); ?></td>
                </tr>
            </table>
        <?php else: ?>
            <p>No student found.</p>
        <?php endif; ?>
    </div>
</body>
</html>

