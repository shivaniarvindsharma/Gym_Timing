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

$group_sort_column = 'group_id';
$group_sort_order = 'ASC';

$allowed_columns = ['group_id', 'group_name'];
$allowed_orders = ['ASC', 'DESC'];

if (isset($_GET['group_sort_column']) && isset($_GET['group_sort_order'])) {
    $group_sort_column = in_array($_GET['group_sort_column'], $allowed_columns) ? $_GET['group_sort_column'] : 'group_id';
    $group_sort_order = in_array($_GET['group_sort_order'], $allowed_orders) ? $_GET['group_sort_order'] : 'ASC';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_group'])) {
    $group_id = htmlspecialchars($_POST['group_id']);
    $group_name = htmlspecialchars($_POST['group_name']);
    $group_description = htmlspecialchars($_POST['group_description']);

    try {

        $insert_stmt = $pdo->prepare("INSERT INTO fitness_groups (group_id, group_name, group_description) VALUES (:group_id, :group_name, :group_description)");
        $insert_stmt->execute([
            ':group_id' => $group_id,
            ':group_name' => $group_name,
            ':group_description' => $group_description
        ]);

        header("Location: add_group.php?message=" . urlencode("Group added successfully."));
        exit();
    } catch (PDOException $e) {
        $message = "Error adding group: " . htmlspecialchars($e->getMessage());
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_group'])) {
    $group_id = htmlspecialchars($_POST['group_id']);

    try {

        $delete_stmt = $pdo->prepare("DELETE FROM fitness_groups WHERE group_id = :group_id");
        $delete_stmt->execute([':group_id' => $group_id]);

        header("Location: add_group.php?message=" . urlencode("Group deleted successfully."));
        exit();
    } catch (PDOException $e) {
        $message = "Error deleting group: " . htmlspecialchars($e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/dashboard.css">
    <title>Admin Dashboard - Groups</title>
</head>

<body>
    <nav class="navbar">
        <ul>
            <li><a href="../admin_dashboard/admin_dashboard.php" class="btn">Dashboard</a></li>
            <li><a href="../admin_dashboard/students.php" class="btn">Students</a></li>
            <li><a href="../admin_dashboard/slots.php" class="btn">Slots</a></li>
            <li><a href="../admin_dashboard/equipment.php" class="btn">Equipment</a></li>
            <li><a href="../admin_dashboard/feedback.php" class="btn">Feedback</a></li>
            <li><a href="../admin_dashboard/groups.php" class="btn">Groups</a></li>
            <li><a href="../admin_dashboard/members.php" class="btn">Members</a></li>
        </ul>
    </nav>

    <h2 style="margin-left:150px;color: black;background-color:#50bdae;width:10%">All Groups</h2>

    <div class="container1">
        <form action="add_group.php?group_sort_column=<?php echo htmlspecialchars($group_sort_column); ?>&group_sort_order=<?php echo htmlspecialchars($group_sort_order); ?>" method="POST">
            <?php if ($message): ?>
                <p id="message" style="color: green; font-weight: bold;"><?php echo $message; ?></p>
            <?php endif; ?>
            <input type="hidden" name="add_group" value="1">

            <label for="group_id">Group ID:</label>
            <input type="text" name="group_id" required placeholder="Enter Group ID">

            <label for="group_name">Group Name:</label>
            <input type="text" name="group_name" required placeholder="Enter Group Name">

            <label for="group_description">Group Description:</label>
            <input type="text" id="content" name="group_description" required placeholder="Enter Group Description"></textarea>

            <button type="submit" class="btn-add">Add Group</button>
        </form>
    </div>

    <table>
        <tr>
            <th><a href="?group_sort_column=group_id&group_sort_order=<?php echo $group_sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">ID <?php echo $group_sort_column === 'group_id' ? ($group_sort_order === 'ASC' ? '▲' : '▼') : ''; ?></a></th>
            <th><a href="?group_sort_column=group_name&group_sort_order=<?php echo $group_sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">Name <?php echo $group_sort_column === 'group_name' ? ($group_sort_order === 'ASC' ? '▲' : '▼') : ''; ?></a></th>
            <th>Description</th>
            <th>Action</th>
        </tr>
        <?php

        $stmt = $pdo->prepare("SELECT * FROM fitness_groups ORDER BY $group_sort_column $group_sort_order");
        $stmt->execute();
        $groups = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($groups)): ?>
            <tr>
                <td colspan="4" style="color: red">No groups found.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($groups as $group): ?>
                <tr>
                    <td><?php echo htmlspecialchars($group['group_id']); ?></td>
                    <td><?php echo htmlspecialchars($group['group_name']); ?></td>
                    <td><?php echo htmlspecialchars($group['group_description']); ?></td>
                    <td>
                        <form action="add_group.php" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this group?');">
                            <input type="hidden" name="delete_group" value="1">
                            <input type="hidden" name="group_id" value="<?php echo htmlspecialchars($group['group_id']); ?>">
                            <button type="submit" class="btn-delete">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>

    <script>
        const messageElement = document.getElementById('message');
        if (messageElement) {
            messageElement.style.display = 'block';
            setTimeout(() => {
                messageElement.style.transition = 'opacity 0.5s';
                messageElement.style.opacity = '0';
                setTimeout(() => messageElement.style.display = 'none', 500);
            }, 2000);
        }
    </script>
</body>

</html>