<?php
include('../includes/db_connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $equipment_name = htmlspecialchars(trim($_POST['equipment_name']));
    $equipment_type = htmlspecialchars(trim($_POST['equipment_type']));

    try {

        $stmt = $pdo->prepare("INSERT INTO equipment (equipment_name, equipment_type) VALUES (?, ?)");
        if ($stmt->execute([$equipment_name, $equipment_type])) {
            $message = "Equipment added successfully";
        } else {
            $message = "Error adding equipment.";
        }
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
    }

    header("Location: ../admin/admin_dashboard.php?message=" . urlencode($message));
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/style.css">
    <title>Add Equipment</title>
</head>

<body>
    <?php include('../includes/header.php'); ?>

    <h1>Add Equipment</h1>

    <form action="add_equipment.php" method="POST">
        <input type="text" name="equipment_name" placeholder="Equipment Name" required>
        <input type="text" name="equipment_type" placeholder="Equipment Type" required>
        <button type="submit" class="btn-add">Add Equipment</button>
    </form>

    <?php include('../includes/footer.php'); ?>
</body>

</html>