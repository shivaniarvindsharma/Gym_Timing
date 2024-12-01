<?php
session_start();

date_default_timezone_set('Asia/Kolkata');

if (!isset($_SESSION['roll_number'])) {

    header("Location: ../login/student_login.php");
    exit();
}

include('../includes/db_connection.php');

$roll_number = $_SESSION['roll_number'];

$current_date = date('Y-m-d');
$current_time = date('H:i:s');


$sql = "SELECT s.slot_id, s.slot_time,e.equipment_id, s.booking_date, e.equipment_name
        FROM slots s
        JOIN equipment e ON s.equipment_id = e.equipment_id
        WHERE s.roll_number = :roll_number
        AND (s.booking_date > :current_date OR (s.booking_date = :current_date AND s.slot_time > :current_time))
        AND s.is_booked = TRUE";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':roll_number' => $roll_number,
    ':current_date' => $current_date,
    ':current_time' => $current_time,
]);

$booked_slots = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/dashboard.css"> 
    <title>Slot Booking</title>
    <style>
        .error-message {
            color: red;
            margin: 10px 0;
            display: none;
        }

        .success-message {
            color: green;
            margin: 10px 0;
            display: none;
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <ul>

            <li><a style="background-color: #da6161;" href="slot_booking_form.php" class="btn">Dashboard</a></li>
            <li><a href="fitness_groups.php" class="btn">Groups</a></li>

            <li><a href="feedback.php" class="btn">Give Feedback</a></li>
            <li><a href="profile.php" class="btn">Profile</a></li>
        </ul>
    </nav>

    <script>
        document.addEventListener('DOMContentLoaded', () => {

            const errorMessage = document.getElementById('error-message');
            if (errorMessage) {
                errorMessage.style.display = 'block';
                setTimeout(() => {
                    errorMessage.style.display = 'none';
                }, 8000);
            }
            const successMessage = document.getElementById('success-message');
            if (successMessage) {
                successMessage.style.display = 'block';
                setTimeout(() => {
                    successMessage.style.display = 'none';
                }, 3000);
            }
        });
    </script>

    <h1>Book a Gym Slot</h1>

    <div class="container1">
        <form action="book_slot.php" method="POST">
            <?php
            if (isset($_GET['success'])) {
                echo "<p class='success-message' id='success-message'>" . htmlspecialchars($_GET['success']) . "</p>";
            }

            if (isset($_GET['error'])) {

                $error_message = nl2br(htmlspecialchars(htmlspecialchars_decode($_GET['error'])));
                echo "<p class='error-message' id='error-message'>$error_message</p>";
            }
            ?>

            <?php

            $today = date('Y-m-d');
            $maxDate = date('Y-m-d', strtotime('+7 days'));
            ?>

            <label for="date">Select Date:</label>
            <input type="date" id="date" name="date" min="<?php echo $today; ?>" max="<?php echo $maxDate; ?>" required>

            <label for="equipment">Choose Equipment:</label>
            <select id="equipment" name="equipment_id" required>
                <?php

                $stmt = $pdo->query("SELECT equipment_id, equipment_name FROM equipment");
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value=\"{$row['equipment_id']}\">{$row['equipment_name']}</option>";
                }
                ?>
            </select>

            <label for="preferred_slot">Preferred Time Slot:</label>
            <select id="preferred_slot" name="preferred_slot" required>
                <?php

                for ($h = 5; $h <= 21; $h++) {
                    for ($m = 0; $m < 60; $m += 30) {
                        $time = sprintf('%02d:%02d', $h, $m);
                        echo "<option value=\"$time\">$time</option>";
                    }
                }
                ?>
            </select>

            <button type="submit">Book Slot</button>
        </form>
    </div>

    <h2 style="margin-left:150px;color: white;background-color:black;width:18%">Your Booked Gym Slots</h2>

    <table>
        <thead>
            <tr>
                <th>Booking Date</th>
                <th>Slot Time</th>
                <th>Equipment ID</th>
                <th>Equipment Name</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($booked_slots)): ?>
                <?php foreach ($booked_slots as $slot): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($slot['booking_date']); ?></td>
                        <td><?php echo htmlspecialchars($slot['slot_time']); ?></td>
                        <td><?php echo htmlspecialchars($slot['equipment_id']); ?></td>
                        <td><?php echo htmlspecialchars($slot['equipment_name']); ?></td>
                        <td>
                            <form action="delete_slot.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this slot?');">
                                <input type="hidden" name="slot_id" value="<?php echo $slot['slot_id']; ?>">
                                <button type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="color: red;">No booked slots found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>

</html>
