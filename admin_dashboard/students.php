<?php
session_start();


if (!isset($_SESSION['admin_id'])) {

    header("Location: ../login/admin_login.php");
    exit();
}

include('../includes/db_connection.php');


$message = '';
if (isset($_GET['message'])) {
    $message = htmlspecialchars($_GET['message']);
}


$student_sort_column = 'roll_number';
$student_sort_order = 'ASC';


if (isset($_GET['student_sort_column']) && isset($_GET['student_sort_order'])) {

    $allowed_columns = ['roll_number', 'first_name', 'last_name', 'email', 'dob', 'registration_date', 'age'];
    $allowed_orders = ['ASC', 'DESC'];


    $student_sort_column = in_array($_GET['student_sort_column'], $allowed_columns) ? $_GET['student_sort_column'] : 'roll_number';
    $student_sort_order = in_array($_GET['student_sort_order'], $allowed_orders) ? $_GET['student_sort_order'] : 'ASC';
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_roll_number'])) {
    $roll_number_to_delete = $_POST['delete_roll_number'];

    try {
        $stmt = $pdo->prepare("DELETE FROM students WHERE roll_number = :roll_number");
        $stmt->bindParam(':roll_number', $roll_number_to_delete);

        if ($stmt->execute()) {
            header("Location: students.php?message=Student deleted successfully!");
            exit();
        } else {
            $message = "Error: Could not delete student.";
        }
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/dashboard.css">
    <title>Student Details</title>
</head>

<body>
    <nav class="navbar">
        <ul>
            <li><a href="../admin_dashboard/admin_dashboard.php" class="btn">Dashboard</a></li>
            <li><a href="../admin_dashboard/students.php" class="cbtn">Students</a></li>
            <li><a href="../admin_dashboard/slots.php" class="btn">Slots</a></li>
            <li><a href="../admin_dashboard/equipment.php" class="btn">Equipment</a></li>
            <li><a href="../admin_dashboard/feedback.php" class="btn">Feedback</a></li>
            <li><a href="../admin_dashboard/groups.php" class="btn">Groups</a></li>
            <li><a href="../admin_dashboard/members.php" class="btn">Admins</a></li>
        </ul>
    </nav>

    <h1>Students</h1>

    <?php if ($message): ?>
        <p style="color: green; font-weight: bold;" id="message" class="success-message"><?php echo $message; ?></p>
    <?php endif; ?>

    <h2 style="margin-left:150px;color: black;background-color:#50bdae;width:11%">Student Details</h2>

    <table>
        <tr>
            <th><a href="?student_sort_column=roll_number&student_sort_order=<?php echo $student_sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">Roll Number</a></th>
            <th><a href="?student_sort_column=first_name&student_sort_order=<?php echo $student_sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">First Name</a></th>
            <th><a href="?student_sort_column=last_name&student_sort_order=<?php echo $student_sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">Last Name</a></th>
            <th><a href="?student_sort_column=email&student_sort_order=<?php echo $student_sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">Email</a></th>
            <th><a href="?student_sort_column=dob&student_sort_order=<?php echo $student_sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">Date of Birth</a></th>
            <th><a href="?student_sort_column=age&student_sort_order=<?php echo $student_sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">Age</a></th>
            <th><a href="?student_sort_column=registration_date&student_sort_order=<?php echo $student_sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">Registration Date</a></th>
            <th>Action</th>
        </tr>
        <?php

        $stmt = $pdo->prepare("
        SELECT roll_number, first_name, last_name, email, dob, registration_date,
        FLOOR(DATEDIFF(CURRENT_DATE, dob) / 365.25) AS age
        FROM students
        ORDER BY $student_sort_column $student_sort_order
    ");
        $stmt->execute();
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($students)): ?>
            <tr>
                <td colspan="8" style="color: red">No student details found.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($students as $student): ?>
                <tr>
                    <td><?php echo htmlspecialchars($student['roll_number']); ?></td>
                    <td><?php echo htmlspecialchars($student['first_name']); ?></td>
                    <td><?php echo htmlspecialchars($student['last_name']); ?></td>
                    <td><?php echo htmlspecialchars($student['email']); ?></td>
                    <td><?php echo htmlspecialchars($student['dob']); ?></td>
                    <td><?php echo htmlspecialchars($student['age']); ?></td>
                    <td><?php echo htmlspecialchars($student['registration_date']); ?></td>
                    <td>
                        <form action="students.php" method="POST" style="display:inline;">
                            <input type="hidden" name="delete_roll_number" value="<?php echo htmlspecialchars($student['roll_number']); ?>">
                            <button type="submit" class="delete-btn" onclick="return confirm('Are you sure you want to delete this student?');">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const message = document.getElementById("message");
            if (message) {
                setTimeout(() => {
                    message.style.display = "none";
                }, 2000);
            }
        });
    </script>

</body>

</html>