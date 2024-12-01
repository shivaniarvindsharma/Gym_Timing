<?php
include('../includes/db_connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $equipment_id = intval($_POST['equipment_id']);

    try {

        $stmt = $pdo->prepare("DELETE FROM equipment WHERE equipment_id = ?");
        if ($stmt->execute([$equipment_id])) {
            $message = "Equipment deleted successfully";
        } else {
            $message = "Error deleting equipment.";
        }
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
    }
    header("Location: ../admin/admin_dashboard.php?message=" . urlencode($message));
    exit();
}
