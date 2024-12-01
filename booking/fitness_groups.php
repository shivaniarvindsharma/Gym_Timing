<?php
session_start();
include('../includes/db_connection.php');

if (!isset($_SESSION['roll_number'])) {

    header("Location: ../login/student_login.php");
    exit();
}

$roll_number = $_SESSION['roll_number'];

if (isset($_POST['join_group'])) {
    $group_id = $_POST['group_id'];
    $checkQuery = "SELECT * FROM members WHERE group_id = :group_id AND roll_number = :roll_number";
    $stmt = $pdo->prepare($checkQuery);
    $stmt->execute([':group_id' => $group_id, ':roll_number' => $roll_number]);

    if ($stmt->rowCount() == 0) {
        $joinQuery = "INSERT INTO members (group_id, roll_number) VALUES (:group_id, :roll_number)";
        $stmt = $pdo->prepare($joinQuery);
        $stmt->execute([':group_id' => $group_id, ':roll_number' => $roll_number]);
    }
} elseif (isset($_POST['leave_group'])) {
    $group_id = $_POST['group_id'];

    $checkQuery = "SELECT * FROM members WHERE group_id = :group_id AND roll_number = :roll_number";
    $stmt = $pdo->prepare($checkQuery);
    $stmt->execute([':group_id' => $group_id, ':roll_number' => $roll_number]);

    if ($stmt->rowCount() > 0) {

        $leaveQuery = "DELETE FROM members WHERE group_id = :group_id AND roll_number = :roll_number";
        $stmt = $pdo->prepare($leaveQuery);
        $stmt->execute([':group_id' => $group_id, ':roll_number' => $roll_number]);
    }
}

$sql = "SELECT group_id, group_name, group_description FROM fitness_groups";
$stmt = $pdo->query($sql);
$groups = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join Fitness Group</title>
    <link rel="stylesheet" href="../assets/dashboard.css">
    <style>
        .join-btn {
            background-color: #50bdae;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
        }

        .leave-btn {
            background-color: #d83333;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
        }

        .join-btn:hover,
        .leave-btn:hover {
            opacity: 0.8;
        }
    </style>
    <script>
        function confirmleave(event) {
            if (!confirm("Are you sure you want to leave this group?")) {
                event.preventDefault();
            }
        }
    </script>
</head>

<body>
    <nav class="navbar">
        <ul>
            <li><a href="slot_booking_form.php" class="btn">Dashboard</a></li>
            <li><a style="background-color: #da6161;" href="fitness_groups.php" class="btn">Groups</a></li>
            <li><a href="feedback.php" class="btn">Give Feedback</a></li>
 <li><a href="profile.php" class="btn">Profile</a></li>
        </ul>
    </nav>

    <h1>Join a Fitness Group</h1>

    <h2 style="margin-left:150px;color: black;background-color:#50bdae;width:18%">Available Fitness Groups</h2>

    <table>
        <thead>
            <tr>
                <th>Group Name</th>
                <th>Description</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($groups as $group): ?>
                <tr>
                    <td><?php echo htmlspecialchars($group['group_name']); ?></td>
                    <td><?php echo htmlspecialchars($group['group_description']) ?: 'No description available'; ?></td>
                    <td>
                        <?php
                        $checkQuery = "SELECT * FROM members WHERE group_id = :group_id AND roll_number = :roll_number";
                        $stmt = $pdo->prepare($checkQuery);
                        $stmt->execute([':group_id' => $group['group_id'], ':roll_number' => $roll_number]);
                        if ($stmt->rowCount() == 0): ?>
                            <form method="POST" action="">
                                <input type="hidden" name="group_id" value="<?php echo $group['group_id']; ?>">
                                <button type="submit" name="join_group" class="join-btn">Join</button>
                            </form>
                        <?php else: ?>
                            <form method="POST" action="">
                                <input type="hidden" name="group_id" value="<?php echo $group['group_id']; ?>">
                                <button type="submit" name="leave_group" class="leave-btn" onclick="confirmleave(event)">leave</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>

</html>
