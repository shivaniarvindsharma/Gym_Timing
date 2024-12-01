<?php

session_start();
include('../includes/db_connection.php');
if (!isset($_SESSION['admin_id'])) {

    header("Location: ../login/admin_login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_equipment'])) {
        $equipment_id = trim($_POST['equipment_id']);
        $equipment_name = trim($_POST['equipment_name']);
        $equipment_type = trim($_POST['equipment_type']);


        if (!empty($equipment_id) && !empty($equipment_name) && !empty($equipment_type)) {

            $stmt = $pdo->prepare("INSERT INTO equipment (equipment_id, equipment_name, equipment_type) VALUES (:equipment_id, :equipment_name, :equipment_type)");
            $stmt->bindParam(':equipment_id', $equipment_id);
            $stmt->bindParam(':equipment_name', $equipment_name);
            $stmt->bindParam(':equipment_type', $equipment_type);


            if ($stmt->execute()) {

                header("Location: equipment.php?message=Equipment added successfully");
                exit;
            } else {

                $errorInfo = $stmt->errorInfo();
                echo "Error adding equipment: " . htmlspecialchars($errorInfo[2]);
            }
        } else {
            echo "Please fill in all fields.";
        }
    } elseif (isset($_POST['delete_equipment'])) {
        $equipment_id = trim($_POST['equipment_id']);


        if (!empty($equipment_id)) {

            $stmt = $pdo->prepare("DELETE FROM equipment WHERE equipment_id = :equipment_id");
            $stmt->bindParam(':equipment_id', $equipment_id);


            if ($stmt->execute()) {

                header("Location: equipment.php?message=Equipment deleted successfully");
                exit;
            } else {

                $errorInfo = $stmt->errorInfo();
                echo "Error deleting equipment: " . htmlspecialchars($errorInfo[2]);
            }
        } else {
            echo "Invalid equipment ID.";
        }
    }
}

$message = '';
if (isset($_GET['message'])) {
    $message = htmlspecialchars($_GET['message']);
}

$equipment_sort_column = 'equipment_id';
$equipment_sort_order = 'ASC';

if (isset($_GET['equipment_sort_column']) && isset($_GET['equipment_sort_order'])) {
    $equipment_sort_column = htmlspecialchars($_GET['equipment_sort_column']);
    $equipment_sort_order = htmlspecialchars($_GET['equipment_sort_order']);
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
            <li><a href="../admin_dashboard/equipment.php" class="cbtn">Equipment</a></li>
            <li><a href="../admin_dashboard/feedback.php" class="btn">Feedback</a></li>
            <li><a href="../admin_dashboard/groups.php" class="btn">Groups</a></li>
            <li><a href="../admin_dashboard/members.php" class="btn">Admins</a></li>
        </ul>
    </nav>

    <h1>Equipment</h1>

    <h2 style="margin-left:150px;color: black;background-color:#50bdae;;width:10%">All Equipment</h2>

    <div class="container1">
        <form action="equipment.php" method="POST">
            <?php if ($message): ?>
                <p id="message" style="color: green; font-weight: bold;"><?php echo $message; ?></p>
            <?php endif; ?>
            <input type="hidden" name="add_equipment" value="1">


            <label for="equipment_id">Equipment ID:</label>
            <input type="text" name="equipment_id" required>

            <label for="equipment_name">Equipment Name:</label>
            <input type="text" name="equipment_name" required>

            <label for="equipment_type">Equipment Type:</label>
            <input type="text" name="equipment_type" required>

            <button type="submit" class="btn-add">Add Equipment</button>
        </form>
    </div>

    <table>
        <tr>
            <th><a href="?equipment_sort_column=equipment_id&equipment_sort_order=<?php echo $equipment_sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">ID</a></th>
            <th><a href="?equipment_sort_column=equipment_name&equipment_sort_order=<?php echo $equipment_sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">Name</a></th>
            <th><a href="?equipment_sort_column=equipment_type&equipment_sort_order=<?php echo $equipment_sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">Type</a></th>
            <th>Action</th>
        </tr>
        <?php

        $stmt = $pdo->prepare("SELECT * FROM equipment ORDER BY $equipment_sort_column $equipment_sort_order");
        $stmt->execute();
        $equipments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($equipments)): ?>
            <tr>
                <td colspan="4" style="color: red">No equipment found.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($equipments as $equipment): ?>
                <tr>
                    <td><?php echo htmlspecialchars($equipment['equipment_id']); ?></td>
                    <td><?php echo htmlspecialchars($equipment['equipment_name']); ?></td>
                    <td><?php echo htmlspecialchars($equipment['equipment_type']); ?></td>
                    <td>
                        <form action="equipment.php" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this equipment?');">
                            <input type="hidden" name="delete_equipment" value="1">
                            <input type="hidden" name="equipment_id" value="<?php echo htmlspecialchars($equipment['equipment_id']); ?>">
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
                messageElement.style.display = 'none';
            }, 2000);
        }
    </script>
</body>

</html>