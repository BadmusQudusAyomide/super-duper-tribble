<?php
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin_username'])) {
    header("Location: admin_login.php");
    exit;
}

$servername = "localhost";
$username = "root"; // Change this if needed
$password = ""; // Change this if needed
$dbname = "student_attendance_system";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch departments for the filter dropdown
$departments_sql = "SELECT DISTINCT department FROM students";
$departments_result = $conn->query($departments_sql);

// Initialize filter variables
$selected_department = '';
$selected_date = '';

// Handle filtering
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $selected_department = $_POST['department'];
    $selected_date = $_POST['date'];
}

// Query to get attendance records based on selected filters
$attendance_sql = "SELECT attendance.marked_at, students.name AS student_name, students.matric_no, students.department, courses.name AS course_name, venues.name AS venue_name 
                    FROM attendance
                    JOIN students ON attendance.student_matric_no = students.matric_no
                    JOIN classes ON attendance.class_id = classes.id
                    JOIN courses ON classes.course_id = courses.id
                    JOIN venues ON classes.venue_id = venues.id
                    WHERE 1=1";

if (!empty($selected_department)) {
    $attendance_sql .= " AND students.department = '$selected_department'";
}

if (!empty($selected_date)) {
    $attendance_sql .= " AND DATE(attendance.marked_at) = '$selected_date'";
}

$attendance_sql .= " ORDER BY students.department, attendance.marked_at DESC";
$attendance_result = $conn->query($attendance_sql);

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Attendance</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .sidebar {
            width: 250px;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background-color: #343a40;
            color: white;
            padding-top: 20px;
            transition: width 0.5s;
        }

        .sidebar a {
            padding: 10px 15px;
            text-decoration: none;
            display: block;
            color: white;
        }

        .sidebar a:hover {
            background-color: #007bff;
        }

        .content {
            margin-left: 260px;
            padding: 20px;
            transition: margin 0.5s;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
                padding-bottom: 20px;
            }

            .content {
                margin-left: 0;
            }
        }

        @media print {

            .sidebar,
            .filter-form,
            .print-button {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="sidebar">
        <h3 class="text-center">Admin Dashboard</h3>
        <a href="admin_dashboard.php">Home</a>
        <a href="manage_students.php">Manage Students</a>
        <a href="manage_courses.php">Manage Courses</a>
        <a href="manage_venues.php">Manage Venues</a>
        <a href="manage_classes.php">Manage Classes</a>
        <a href="manage_attendance.php">Manage Attendance</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="content">
        <h2>Manage Attendance</h2>

        <!-- Filter Form -->
        <form method="POST" class="filter-form mb-4">
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="department">Department</label>
                    <select name="department" id="department" class="form-control">
                        <option value="">Select Department</option>
                        <?php while ($dept = $departments_result->fetch_assoc()): ?>
                            <option value="<?php echo $dept['department']; ?>" <?php echo ($selected_department == $dept['department']) ? 'selected' : ''; ?>>
                                <?php echo $dept['department']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label for="date">Date</label>
                    <input type="date" name="date" id="date" class="form-control" value="<?php echo $selected_date; ?>">
                </div>
                <div class="form-group col-md-4 align-self-end">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="manage_attendance.php" class="btn btn-secondary">Reset</a>
                </div>
            </div>
        </form>

        <!-- Attendance Table -->
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Date & Time</th>
                    <th>Student Name</th>
                    <th>Matric No</th>
                    <th>Department</th>
                    <th>Course</th>
                    <th>Venue</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($attendance_result->num_rows > 0): ?>
                    <?php while ($row = $attendance_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['marked_at']; ?></td>
                            <td><?php echo $row['student_name']; ?></td>
                            <td><?php echo $row['matric_no']; ?></td>
                            <td><?php echo $row['department']; ?></td>
                            <td><?php echo $row['course_name']; ?></td>
                            <td><?php echo $row['venue_name']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">No attendance records found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <button onclick="window.print();" class="btn btn-primary print-button">Print Attendance</button>
    </div>
</body>

</html>