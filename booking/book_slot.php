<?php
session_start();
include('../includes/db_connection.php');


if (!isset($_SESSION['roll_number'])) {
    header("Location: ../login/student_login.php");
    exit();
}

date_default_timezone_set('Asia/Kolkata');
$error_message = '';
$success_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['date'], $_POST['equipment_id'], $_POST['preferred_slot'])) {
        $error_message = "Missing booking information. Please provide a date, equipment, and preferred slot.";
    } else {
        $roll_number = $_SESSION['roll_number'];
        $equipment_id = $_POST['equipment_id'];
        $preferred_slot = $_POST['preferred_slot'];
        $booking_date = $_POST['date'];

        if (empty($equipment_id)) {
            $error_message = "Equipment cannot be null. Please select a valid equipment.";
        } else {
            $current_datetime = new DateTime();
            $booking_datetime = new DateTime("$booking_date $preferred_slot");

            if ($booking_datetime <= $current_datetime) {
                $error_message = "You cannot book a slot in the past. Please select a future date and time.";
            } else {
                try {

                    $check_student_stmt = $pdo->prepare("
                        SELECT slot_id 
                        FROM slots 
                        WHERE booking_date = ? 
                        AND slot_time = ? 
                        AND roll_number = ?
                    ");
                    $check_student_stmt->execute([$booking_date, $preferred_slot, $roll_number]);
                    $existing_booking = $check_student_stmt->fetch(PDO::FETCH_ASSOC);

                    if ($existing_booking) {
                        $error_message = "You have already booked this slot on the selected date. Please choose a different slot.";
                    } else {

                        $check_equipment_stmt = $pdo->prepare("
                            SELECT slot_id 
                            FROM slots 
                            WHERE booking_date = ? 
                            AND slot_time = ? 
                            AND equipment_id = ?
                        ");
                        $check_equipment_stmt->execute([$booking_date, $preferred_slot, $equipment_id]);
                        $slot_booked = $check_equipment_stmt->fetch(PDO::FETCH_ASSOC);

                        if ($slot_booked) {

                            $available_slots = [];
                            $start_time = new DateTime();
                            $end_time = new DateTime('21:00');

                            if ($booking_date === $current_datetime->format('Y-m-d')) {
                                $start_time->modify('+' . (30 - $start_time->format('i') % 30) . ' minutes');
                            } else {
                                $start_time = new DateTime('05:00');
                            }

                            while (count($available_slots) < 5 && $start_time <= $end_time) {
                                $next_slot_formatted = $start_time->format('H:i');

                                $check_available_stmt = $pdo->prepare("
                                    SELECT slot_time 
                                    FROM slots 
                                    WHERE booking_date = ? 
                                    AND slot_time = ? 
                                    AND equipment_id = ?
                                ");
                                $check_available_stmt->execute([$booking_date, $next_slot_formatted, $equipment_id]);
                                $slot_booked = $check_available_stmt->fetch(PDO::FETCH_ASSOC);

                                if (!$slot_booked) {
                                    $available_slots[] = $next_slot_formatted;
                                }

                                $start_time->modify('+30 minutes');
                            }

                            if (empty($available_slots)) {
                                $error_message = "No available slots for this equipment on the selected date.";
                            } else {
                                $error_message = "The selected slot is already booked. Please choose one of the following available slots: ";
                                foreach ($available_slots as $slot) {
                                    $error_message .= htmlspecialchars($slot) . " , ";
                                }
                                $error_message .= "so on.";
                            }
                        } else {

                            $insert_stmt = $pdo->prepare("
                                INSERT INTO slots (booking_date, slot_time, equipment_id, roll_number, is_booked) 
                                VALUES (?, ?, ?, ?, TRUE)
                            ");
                            $insert_stmt->execute([$booking_date, $preferred_slot, $equipment_id, $roll_number]);

                            $success_message = "Slot booked successfully!";
                        }
                    }
                } catch (PDOException $e) {
                    $error_message = "Error checking or booking slot: " . $e->getMessage();
                }
            }
        }
    }
}

if (!empty($error_message)) {
    header("Location: slot_booking_form.php?error=" . urlencode($error_message));
    exit();
}

if (!empty($success_message)) {
    header("Location: slot_booking_form.php?success=" . urlencode(htmlspecialchars($success_message)));
    exit();
}
