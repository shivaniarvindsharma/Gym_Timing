<?php
session_start();

if (!isset($_SESSION['roll_number'])) {

    header("Location: ../login/student_login.php");
    exit();
}

include('../includes/db_connection.php');

if (isset($_POST['slot_id'])) {

    $slot_id = $_POST['slot_id'];
    $sql = "DELETE FROM slots WHERE slot_id = :slot_id AND roll_number = :roll_number";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':slot_id' => $slot_id,
        ':roll_number' => $_SESSION['roll_number']
    ]);
    header("Location: slot_booking_form.php");
    exit();
} else {
    header("Location: slot_booking_form.php");
    exit();
}
