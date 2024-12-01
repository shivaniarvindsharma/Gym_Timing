<?php
session_start();
include('../includes/db_connection.php');
if (!isset($_SESSION['admin_id'])) {

    header("Location: ../login/admin_login.php");
    exit();
}

$message = '';
if (isset($_GET['message'])) {
    $message = htmlspecialchars($_GET['message']);
}

$slot_sort_column = 'combined_datetime';
$slot_sort_order = 'ASC';

if (isset($_GET['slot_sort_column']) && isset($_GET['slot_sort_order'])) {

    $allowed_columns = ['slot_id', 'student_name', 'roll_number', 'combined_datetime', 'equipment_id', 'equipment_name'];
    $allowed_orders = ['ASC', 'DESC'];


    $slot_sort_column = in_array($_GET['slot_sort_column'], $allowed_columns) ? $_GET['slot_sort_column'] : 'combined_datetime';
    $slot_sort_order = in_array($_GET['slot_sort_order'], $allowed_orders) ? $_GET['slot_sort_order'] : 'ASC';
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['slot_id'])) {
    $slot_id = $_POST['slot_id'];

    try {

        $stmt = $pdo->prepare("DELETE FROM slots WHERE slot_id = ?");
        $stmt->execute([$slot_id]);


        header("Location: slots.php?message=" . urlencode("Slot deleted successfully."));
        exit();
    } catch (PDOException $e) {

        echo "Error: " . htmlspecialchars($e->getMessage());
    }
}
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
            <li><a href="../admin_dashboard/admin_dashboard.php" class="btn">Dashboard</a></li>
            <li><a href="../admin_dashboard/students.php" class="btn">Students</a></li>
            <li><a href="../admin_dashboard/slots.php" class="cbtn">Slots</a></li>
            <li><a href="../admin_dashboard/equipment.php" class="btn">Equipment</a></li>
            <li><a href="../admin_dashboard/feedback.php" class="btn">Feedback</a></li>
            <li><a href="../admin_dashboard/groups.php" class="btn">Groups</a></li>
            <li><a href="../admin_dashboard/members.php" class="btn">Admins</a></li>
        </ul>
    </nav>

    <h1>Slots</h1>

    <?php if ($message): ?>
        <p id="message" style="color: green; font-weight: bold;"><?php echo $message; ?></p>
    <?php endif; ?>

    <h2 style="margin-left:150px;color: black;background-color:#50bdae;width:10%">Booked Slots</h2>
    <table>
        <tr>
            <th><a href="?slot_sort_column=slot_id&slot_sort_order=<?php echo $slot_sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">Slot ID</a></th>
            <th><a href="?slot_sort_column=student_name&slot_sort_order=<?php echo $slot_sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">Student Name</a></th>
            <th><a href="?slot_sort_column=roll_number&slot_sort_order=<?php echo $slot_sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">Roll Number</a></th>
            <th><a href="?slot_sort_column=combined_datetime&slot_sort_order=<?php echo $slot_sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">Combined DateTime</a></th>
            <th><a href="?slot_sort_column=equipment_id&slot_sort_order=<?php echo $slot_sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">Equipment ID</a></th>
            <th><a href="?slot_sort_column=equipment_name&slot_sort_order=<?php echo $slot_sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">Equipment Name</a></th>
            <th>Actions</th>
        </tr>

        <?php

        date_default_timezone_set('Asia/Kolkata');
        $currentDateTime = date('Y-m-d H:i:s');
        $stmt = $pdo->prepare("
        SELECT s.slot_id, CONCAT(st.first_name, ' ', st.last_name) AS student_name, st.roll_number, s.slot_time, s.booking_date, e.equipment_id, e.equipment_name,
        CONCAT(s.booking_date, ' ', s.slot_time) AS combined_datetime
        FROM slots s
        JOIN students st ON s.roll_number = st.roll_number
        JOIN equipment e ON s.equipment_id = e.equipment_id
        WHERE s.is_booked = 1
        AND CONCAT(s.booking_date, ' ', s.slot_time) > ?
        ORDER BY $slot_sort_column $slot_sort_order
    ");
        $stmt->execute([$currentDateTime]);
        $bookedSlots = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($bookedSlots)): ?>
            <tr>
                <td colspan="7" style="color: red">No booked slots found after the current date and time.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($bookedSlots as $slot): ?>
                <tr>
                    <td><?php echo htmlspecialchars($slot['slot_id']); ?></td>
                    <td><?php echo htmlspecialchars($slot['student_name']); ?></td>
                    <td><?php echo htmlspecialchars($slot['roll_number']); ?></td>
                    <td><?php echo htmlspecialchars($slot['combined_datetime']); ?></td>
                    <td><?php echo htmlspecialchars($slot['equipment_id']); ?></td>
                    <td><?php echo htmlspecialchars($slot['equipment_name']); ?></td>
                    <td>
                        <form action="slots.php" method="POST" onsubmit="return confirmDelete();">
                            <input type="hidden" name="slot_id" value="<?php echo htmlspecialchars($slot['slot_id']); ?>">
                            <button type="submit" class="btn-delete">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>

    <script>
        function confirmDelete() {
            return confirm("Are you sure you want to delete this slot?");
        }
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