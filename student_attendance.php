<?php
session_start();

// Check if the student is logged in
if (!isset($_SESSION['matric_no'])) {
    header("Location: login.php");
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

// Get the student's matric number from session
$student_matric_no = $_SESSION['matric_no'];

// Fetch courses for the filter dropdown
$courses_sql = "SELECT courses.id, courses.name FROM courses
                JOIN classes ON classes.course_id = courses.id
                JOIN attendance ON attendance.class_id = classes.id
                WHERE attendance.student_matric_no = '$student_matric_no'
                GROUP BY courses.id, courses.name";
$courses_result = $conn->query($courses_sql);

// Initialize filter variables
$selected_course = '';
$selected_date_from = '';
$selected_date_to = '';

// Handle filtering
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $selected_course = $_POST['course'];
    $selected_date_from = $_POST['date_from'];
    $selected_date_to = $_POST['date_to'];
}

// Query to get attendance records based on selected filters
$attendance_sql = "SELECT attendance.marked_at, courses.name AS course_name, venues.name AS venue_name 
                    FROM attendance
                    JOIN classes ON attendance.class_id = classes.id
                    JOIN courses ON classes.course_id = courses.id
                    JOIN venues ON classes.venue_id = venues.id
                    WHERE attendance.student_matric_no = '$student_matric_no'";

if (!empty($selected_course)) {
    $attendance_sql .= " AND courses.id = '$selected_course'";
}

if (!empty($selected_date_from)) {
    $attendance_sql .= " AND DATE(attendance.marked_at) >= '$selected_date_from'";
}

if (!empty($selected_date_to)) {
    $attendance_sql .= " AND DATE(attendance.marked_at) <= '$selected_date_to'";
}

$attendance_sql .= " ORDER BY attendance.marked_at DESC";
$attendance_result = $conn->query($attendance_sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Attendance</title>
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
        <h3 class="text-center">Student Dashboard</h3>
        <a href="student_dashboard.php">Home</a>
        <a href="student_courses.php">My Courses</a>
        <a href="student_attendance.php">My Attendance</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="content">
        <h2>My Attendance</h2>

        <!-- Filter Form -->
        <form method="POST" class="filter-form mb-4">
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="course">Course</label>
                    <select name="course" id="course" class="form-control">
                        <option value="">Select Course</option>
                        <?php while ($course = $courses_result->fetch_assoc()): ?>
                            <option value="<?php echo $course['id']; ?>" <?php echo ($selected_course == $course['id']) ? 'selected' : ''; ?>>
                                <?php echo $course['name']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label for="date_from">Date From</label>
                    <input type="date" name="date_from" id="date_from" class="form-control"
                        value="<?php echo $selected_date_from; ?>">
                </div>
                <div class="form-group col-md-4">
                    <label for="date_to">Date To</label>
                    <input type="date" name="date_to" id="date_to" class="form-control"
                        value="<?php echo $selected_date_to; ?>">
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="student_attendance.php" class="btn btn-secondary">Reset</a>
        </form>

        <!-- Attendance Table -->
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Date & Time</th>
                    <th>Course</th>
                    <th>Venue</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($attendance_result->num_rows > 0): ?>
                    <?php while ($row = $attendance_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['marked_at']; ?></td>
                            <td><?php echo $row['course_name']; ?></td>
                            <td><?php echo $row['venue_name']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="text-center">No attendance records found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <button onclick="window.print();" class="btn btn-primary print-button">Print Attendance</button>
    </div>
</body>

</html>