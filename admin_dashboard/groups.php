<?php
session_start();
include('../includes/db_connection.php');
if (!isset($_SESSION['admin_id'])) {

    header("Location: ../login/admin_login.php");
    exit();
}

$message = '';

try {
    $stmt = $pdo->prepare("SELECT group_id, group_name FROM fitness_groups");
    $stmt->execute();
    $groups = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching groups: " . $e->getMessage());
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_member'])) {
    $roll_number = htmlspecialchars(trim($_POST['roll_number']));
    $group_id = htmlspecialchars(trim($_POST['group_id']));


    if (!empty($roll_number) && !empty($group_id)) {
        try {

            $stmt = $pdo->prepare("INSERT INTO members (roll_number, group_id) VALUES (:roll_number, :group_id)");
            $stmt->execute([':roll_number' => $roll_number, ':group_id' => $group_id]);

            $message = "Member added successfully";
        } catch (PDOException $e) {
            $message = "Error adding member: " . $e->getMessage();
        }
    } else {
        $message = "Please fill in all fields.";
    }
}


$students = [];
$group_name = '';
$group_id_display = '';
if (isset($_POST['group_id']) && !empty($_POST['group_id'])) {
    $group_id = htmlspecialchars($_POST['group_id']);

    try {

        $groupStmt = $pdo->prepare("SELECT group_name FROM fitness_groups WHERE group_id = :group_id");
        $groupStmt->execute([':group_id' => $group_id]);
        $group = $groupStmt->fetch(PDO::FETCH_ASSOC);
        $group_name = $group['group_name'];
        $group_id_display = $group_id;


        $stmt = $pdo->prepare("SELECT st.roll_number, st.first_name, st.last_name, st.email, st.dob, st.registration_date
                               FROM students st
                               JOIN members m ON st.roll_number = m.roll_number
                               WHERE m.group_id = :group_id");
        $stmt->execute([':group_id' => $group_id]);
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Error fetching students: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/dashboard.css">
    <title>Group Members</title>
</head>

<body>
    <nav class="navbar">
        <ul>
            <li><a href="../admin_dashboard/admin_dashboard.php" class="btn">Dashboard</a></li>
            <li><a href="../admin_dashboard/students.php" class="btn">Students</a></li>
            <li><a href="../admin_dashboard/slots.php" class="btn">Slots</a></li>
            <li><a href="../admin_dashboard/equipment.php" class="btn">Equipment</a></li>
            <li><a href="../admin_dashboard/feedback.php" class="btn">Feedback</a></li>
            <li><a href="../admin_dashboard/groups.php" class="cbtn">Groups</a></li>
            <li><a href="../admin_dashboard/members.php" class="btn">Admins</a></li>
        </ul>
    </nav>

    <h1>Groups</h1>
    <h2 style="margin-left:150px;color: black;background-color:#50bdae;width:14%">Add New Groups</h2>
    <li><a style="background-color: #da6161; margin-left:120px" href="../admin_dashboard/add_group.php" class="btn">Add Groups</a></li>


    <?php if ($message): ?>
        <p id="message" style="color: green; font-weight: bold;"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>


    <div class="group-container">
        <h3>Select Group to View Members</h3>

        <form action="groups.php" method="POST">
            <label for="group_id">Select Group:</label>
            <select name="group_id" id="group_id" class="select-group" required>
                <option value="" disabled selected>Select a Group</option>
                <?php foreach ($groups as $group): ?>
                    <option value="<?php echo htmlspecialchars($group['group_id']); ?>"><?php echo htmlspecialchars($group['group_name']); ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn">View Members</button>
        </form>
    </div>


    <?php if (!empty($students)): ?>
        <div class="group-container1">
            <h3 style="text-align:center;font-size:25px;padding-top:30px;color: #036e6c;">Group: <?php echo htmlspecialchars($group_name); ?> (ID: <?php echo htmlspecialchars($group_id_display); ?>) - Student Details</h3>
            <table class="group-table">
                <thead>
                    <tr>
                        <th>Roll Number</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Date of Birth</th>
                        <th>Age</th>
                        <th>Registration Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $student): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($student['roll_number']); ?></td>
                            <td><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($student['email']); ?></td>
                            <td><?php echo htmlspecialchars($student['dob']); ?></td>
                            <td><?php echo htmlspecialchars(date_diff(date_create($student['dob']), date_create('today'))->y); ?> years</td>
                            <td><?php echo htmlspecialchars($student['registration_date']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php elseif (isset($_POST['group_id']) && empty($students)): ?>
        <p style="color: red; text-align: center;">No students found for the selected group.</p>
    <?php endif; ?>

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