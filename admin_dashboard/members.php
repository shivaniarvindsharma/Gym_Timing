<?php

session_start();
include '../includes/db_connection.php';
if (!isset($_SESSION['admin_id'])) {

    header("Location: ../login/admin_login.php");
    exit();
}

$message = '';
if (isset($_GET['message'])) {
    $message = htmlspecialchars($_GET['message']);
}


$admin_sort_column = 'admin_id';
$admin_sort_order = 'ASC';


if (isset($_GET['admin_sort_column']) && isset($_GET['admin_sort_order'])) {
    $admin_sort_column = htmlspecialchars($_GET['admin_sort_column']);
    $admin_sort_order = htmlspecialchars($_GET['admin_sort_order']);
}

try {

    $stmt = $pdo->prepare("
        SELECT admin_id, username, email
        FROM admin
        ORDER BY $admin_sort_column $admin_sort_order
    ");
    $stmt->execute();
    $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching admin members: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/dashboard.css">
    <title>Admin Members</title>
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
            <li><a href="../admin_dashboard/members.php" class="cbtn">Admins</a></li>
        </ul>
    </nav>
    <h1> Admins </h1>



    <?php if ($message): ?>
        <p style="color: green; font-weight: bold;"><?php echo $message; ?></p>
    <?php endif; ?>



    <h2 style="margin-left:150px;color: black;background-color:#50bdae;width:14%">Add New Groups</h2>
    <li><a style="background-color: #da6161; margin-left:120px" href="../login/admin_register.php" class="btn">Add Admin</a></li>
    <h2 style="margin-left:150px;color: black;background-color:#50bdae;;width:10%">View Admins</h2>
    <table>
        <tr>
            <th><a href="?admin_sort_column=admin_id&admin_sort_order=<?php echo $admin_sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">Admin ID</a></th>
            <th><a href="?admin_sort_column=username&admin_sort_order=<?php echo $admin_sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">Username</a></th>
            <th><a href="?admin_sort_column=email&admin_sort_order=<?php echo $admin_sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">Email</a></th>
        </tr>

        <?php if (empty($admins)): ?>
            <tr>
                <td colspan="3" style="color: red">No admin members found.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($admins as $admin): ?>
                <tr>
                    <td><?php echo htmlspecialchars($admin['admin_id']); ?></td>
                    <td><?php echo htmlspecialchars($admin['username']); ?></td>
                    <td><?php echo htmlspecialchars($admin['email']); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>

    <script src="../assets/script.js"></script>
</body>

</html>
