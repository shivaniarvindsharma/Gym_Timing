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

date_default_timezone_set('Asia/Kolkata');
$currentDate = date('Y-m-d');
$currentDateTime = date('Y-m-d H:i:s');

// Query for booked slots
$stmt = $pdo->prepare("
    SELECT s.slot_id, st.first_name AS student_first_name, st.last_name AS student_last_name, st.roll_number, s.slot_time, s.booking_date, e.equipment_id, e.equipment_name,
    CONCAT(s.booking_date, ' ', s.slot_time) AS combined_datetime
    FROM slots s
    JOIN students st ON s.roll_number = st.roll_number
    JOIN equipment e ON s.equipment_id = e.equipment_id
    WHERE s.is_booked = 1
    AND s.booking_date = ? AND CONCAT(s.booking_date, ' ', s.slot_time) > ?
    ORDER BY combined_datetime ASC
");
$stmt->execute([$currentDate, $currentDateTime]);
$bookedSlots = $stmt->fetchAll(PDO::FETCH_ASSOC);


$stmt = $pdo->prepare("
    SELECT f.feedback_id, f.content, f.created_at, st.roll_number, st.first_name AS student_first_name, st.last_name AS student_last_name
    FROM feedback f
    JOIN students st ON f.roll_number = st.roll_number
    ORDER BY f.created_at DESC
    LIMIT 5
");
$stmt->execute();
$feedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/dashboard.css">
    <title>Admin Dashboard</title>
</head>
<body>
    <nav class="navbar">
        <ul>
            <li><a href="../admin_dashboard/admin_dashboard.php" class="cbtn">Dashboard</a></li>
            <li><a href="../admin_dashboard/students.php" class="btn">Students</a></li>
            <li><a href="../admin_dashboard/slots.php" class="btn">Slots</a></li>
            <li><a href="../admin_dashboard/equipment.php" class="btn">Equipment</a></li>
            <li><a href="../admin_dashboard/feedback.php" class="btn">Feedback</a></li>
            <li><a href="../admin_dashboard/groups.php" class="btn">Groups</a></li>
            <li><a href="../admin_dashboard/members.php" class="btn">Admins</a></li>
        </ul>
    </nav>

    <h1>Admin Dashboard</h1>

    <?php if ($message): ?>
        <p style="color: green; font-weight: bold;"><?php echo $message; ?></p>
    <?php endif; ?>

    <h2 style="margin-left:150px;color: black;background-color:#50bdae;width:8%">Booked Slots</h2>
    <table>
        <tr>
            <th>Slot ID</th>
            <th>Student Name</th>
            <th>Roll Number</th>
            <th>Combined DateTime</th>
            <th>Equipment ID</th>
            <th>Equipment Name</th>
        </tr>
        <?php if (empty($bookedSlots)): ?>
            <tr>
                <td colspan="6" style="color: red">No booked slots found for today.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($bookedSlots as $slot): ?>
                <tr>
                    <td><?php echo htmlspecialchars($slot['slot_id']); ?></td>
                    <td><?php echo htmlspecialchars($slot['student_first_name'] . ' ' . $slot['student_last_name']); ?></td>
                    <td><?php echo htmlspecialchars($slot['roll_number']); ?></td>
                    <td><?php echo htmlspecialchars($slot['combined_datetime']); ?></td>
                    <td><?php echo htmlspecialchars($slot['equipment_id']); ?></td>
                    <td><?php echo htmlspecialchars($slot['equipment_name']); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>

    <h2 style="margin-left:150px;color: black;background-color:#50bdae;width:13%">Student Feedback</h2>
    <table>
        <tr>
            <th>Feedback ID</th>
            <th>Roll Number</th>
            <th>Student Name</th>
            <th>Feedback</th>
            <th>Date Submitted</th>
        </tr>
        <?php if (empty($feedbacks)): ?>
            <tr>
                <td colspan="5" style="color: red">No feedback found.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($feedbacks as $feedback): ?>
                <tr>
                    <td><?php echo htmlspecialchars($feedback['feedback_id']); ?></td>
                    <td><?php echo htmlspecialchars($feedback['roll_number']); ?></td>
                    <td><?php echo htmlspecialchars($feedback['student_first_name'] . ' ' . $feedback['student_last_name']); ?></td>
                    <td><?php echo htmlspecialchars($feedback['content']); ?></td>
                    <td><?php echo htmlspecialchars($feedback['created_at']); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>

    <script src="../assets/script.js"></script>
</body>
</html>
