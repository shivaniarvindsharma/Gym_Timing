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

$feedback_sort_column = 'feedback_id';
$feedback_sort_order = 'ASC';

if (isset($_GET['feedback_sort_column']) && isset($_GET['feedback_sort_order'])) {
    $feedback_sort_column = htmlspecialchars($_GET['feedback_sort_column']);
    $feedback_sort_order = htmlspecialchars($_GET['feedback_sort_order']);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['feedback_id']) && isset($_POST['roll_number'])) {
    $feedback_id = $_POST['feedback_id'];
    $roll_number = $_POST['roll_number'];

    try {
        $stmt = $pdo->prepare("DELETE FROM feedback WHERE feedback_id = ? AND roll_number = ?");
        $stmt->execute([$feedback_id, $roll_number]);

        header("Location: feedback.php?message=" . urlencode("Feedback deleted successfully."));
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
            <li><a href="../admin_dashboard/slots.php" class="btn">Slots</a></li>
            <li><a href="../admin_dashboard/equipment.php" class="btn">Equipment</a></li>
            <li><a href="../admin_dashboard/feedback.php" class="cbtn">Feedback</a></li>
            <li><a href="../admin_dashboard/groups.php" class="btn">Groups</a></li>
            <li><a href="../admin_dashboard/members.php" class="btn">Admins</a></li>
        </ul>
    </nav>

    <h1>Feedback</h1>

    <?php if ($message): ?>
        <p id="message" style="color: green; font-weight: bold;"><?php echo $message; ?></p>
    <?php endif; ?>

    <h2 style="margin-left:150px;color: black;background-color:#50bdae;width:13%">Student Feedback</h2>
    <table>
        <tr>
            <th><a href="?feedback_sort_column=feedback_id&feedback_sort_order=<?php echo $feedback_sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">Feedback ID</a></th>
            <th><a href="?feedback_sort_column=roll_number&feedback_sort_order=<?php echo $feedback_sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">Roll Number</a></th>
            <th><a href="?feedback_sort_column=student_name&feedback_sort_order=<?php echo $feedback_sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">Student Name</a></th>
            <th><a href="?feedback_sort_column=content&feedback_sort_order=<?php echo $feedback_sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">Feedback</a></th>
            <th><a href="?feedback_sort_column=created_at&feedback_sort_order=<?php echo $feedback_sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">Date Submitted</a></th>
            <th>Actions</th>
        </tr>
        <?php

        $stmt = $pdo->prepare("
        SELECT f.feedback_id, f.content, f.created_at, st.roll_number, CONCAT(st.first_name, ' ', st.last_name) AS student_name
        FROM feedback f
        JOIN students st ON f.roll_number = st.roll_number
        ORDER BY $feedback_sort_column $feedback_sort_order
    ");
        $stmt->execute();
        $feedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($feedbacks)): ?>
            <tr>
                <td colspan="6" style="color: red">No feedback found.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($feedbacks as $feedback): ?>
                <tr>
                    <td><?php echo htmlspecialchars($feedback['feedback_id']); ?></td>
                    <td><?php echo htmlspecialchars($feedback['roll_number']); ?></td>
                    <td><?php echo htmlspecialchars($feedback['student_name']); ?></td>
                    <td><?php echo htmlspecialchars($feedback['content']); ?></td>
                    <td><?php echo htmlspecialchars($feedback['created_at']); ?></td>
                    <td>
                        <form action="feedback.php" method="POST" onsubmit="return confirmDelete();">
                            <input type="hidden" name="feedback_id" value="<?php echo htmlspecialchars($feedback['feedback_id']); ?>">
                            <input type="hidden" name="roll_number" value="<?php echo htmlspecialchars($feedback['roll_number']); ?>">
                            <button type="submit" class="btn-delete">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>

    <script>
        function confirmDelete() {
            return confirm("Are you sure you want to delete this feedback?");
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

    <script src="../assets/script.js"></script>
</body>
</html>
