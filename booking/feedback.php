<?php
session_start();

if (!isset($_SESSION['roll_number'])) {
    header("Location: ../login/student_login.php");
    exit();
}

include('../includes/db_connection.php');

$message = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $feedback_id = $_POST['feedback_id'];
    $content = $_POST['content'];
    $roll_number = $_SESSION['roll_number'];

    // Check if the feedback ID already exists for this student
    $checkSql = "SELECT feedback_id FROM feedback WHERE feedback_id = :feedback_id AND roll_number = :roll_number";
    $checkStmt = $pdo->prepare($checkSql);
    $checkStmt->bindParam(':feedback_id', $feedback_id);
    $checkStmt->bindParam(':roll_number', $roll_number);
    $checkStmt->execute();

    if ($checkStmt->rowCount() > 0) {
        // Feedback ID already exists for this student
        $error = "This Feedback ID has already been used. Please use a unique Feedback ID.";
    } else {
        // Insert feedback content into the database
        $sql = "INSERT INTO feedback (feedback_id, content, created_at, roll_number) VALUES (:feedback_id, :content, NOW(), :roll_number)";
        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(':feedback_id', $feedback_id);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':roll_number', $roll_number);

        if ($stmt->execute()) {
            $message = "Feedback submitted successfully! Thank you for your feedback.";
        } else {
            $error = "Error submitting feedback.";
        }
    }
}

// Retrieve existing feedback for the logged-in student along with student name
$sql = "SELECT f.feedback_id, f.content, f.created_at, s.roll_number, s.first_name, s.last_name
        FROM feedback f
        JOIN students s ON f.roll_number = s.roll_number
        WHERE f.roll_number = :roll_number
        ORDER BY f.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':roll_number', $_SESSION['roll_number']);
$stmt->execute();
$feedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/dashboard.css">
    <title>Feedback Form</title>
    <style>
        .message, .error {
            margin: 10px 0;
        }
        .message {
            color: green;
            display: none;
        }
        .error {
            color: red;
        }
        table {
            width: 80%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #50bdae;
            color: white;
        }
        .no-feedback {
            color: red;
            text-align: center;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <ul>
            <li><a href="slot_booking_form.php" class="btn">Dashboard</a></li>
            <li><a href="fitness_groups.php" class="btn">Groups</a></li>
            <li><a style="background-color: #da6161;" href="feedback.php" class="btn">Give Feedback</a></li>
 <li><a href="profile.php" class="btn">Profile</a></li>
        </ul>
    </nav>
    <h1>Feedback Form</h1>
    <div class="container1">
        <?php if (!empty($message)): ?>
            <div class="message" id="message"><?php echo $message; ?></div>
        <?php elseif (!empty($error)): ?>
            <div class="error" id="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="feedback.php" method="POST">
            <label for="feedback_id">Feedback ID:</label>
            <input type="text" id="feedback_id" name="feedback_id" required>

            <label for="content">Your Feedback:</label>
            <textarea id="content" name="content" rows="4" required></textarea>
            <button type="submit">Submit Feedback</button>
        </form>
    </div>

    <!-- Display submitted feedbacks in a table format -->
    <h2 style="margin-left:150px;color: black;background-color:#50bdae;width:13%">Student Feedback</h2>
    <table>
        <tr>
            <th>Feedback ID</th>
            <th>Feedback</th>
            <th>Date Submitted</th>
        </tr>
        <?php if (empty($feedbacks)): ?>
            <tr>
                <td colspan="5" class="no-feedback">No feedback found.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($feedbacks as $feedback): ?>
                <tr>
                    <td><?php echo htmlspecialchars($feedback['feedback_id']); ?></td>
                    <td><?php echo htmlspecialchars($feedback['content']); ?></td>
                    <td><?php echo htmlspecialchars($feedback['created_at']); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>

    <script>
        const message = document.getElementById('message');
        const error = document.getElementById('error');
        if (message) {
            message.style.display = 'block';
            setTimeout(() => {
                message.style.display = 'none';
            }, 3000);
        }
        if (error) {
            error.style.display = 'block';
            setTimeout(() => {
                error.style.display = 'none';
            }, 5000);
        }
    </script>
</body>
</html>
