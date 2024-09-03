<?php
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin_username'])) {
    header("Location: admin_login.php");
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "student_attendance_system";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle start class
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['start_class'])) {
    $course_id = $_POST['course_id'];
    $venue_id = $_POST['venue_id'];

    // Start the selected class
    $sql = "INSERT INTO classes (course_id, venue_id, status) VALUES ('$course_id', '$venue_id', 'active')";
    if ($conn->query($sql) === TRUE) {
        echo "Class started successfully.";
    } else {
        echo "Error starting class: " . $conn->error;
    }
}

// Handle stop class
if (isset($_GET['stop_class'])) {
    $class_id = $_GET['stop_class'];
    $conn->query("UPDATE classes SET status = 'inactive' WHERE id=$class_id");
}

// Retrieve active classes
$sql = "SELECT classes.id, courses.name AS course_name, venues.name AS venue_name 
        FROM classes 
        JOIN courses ON classes.course_id = courses.id 
        JOIN venues ON classes.venue_id = venues.id 
        WHERE classes.status = 'active'";
$active_classes = $conn->query($sql);

// Retrieve courses and venues for the dropdowns
$courses = $conn->query("SELECT * FROM courses");
$venues = $conn->query("SELECT * FROM venues");

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Classes</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

<body>
    <div class="container">
        <h2>Manage Classes</h2>
        <button class="btn btn-primary" data-toggle="modal" data-target="#startClassModal">Start Class</button>
        <table class="table table-bordered table-striped mt-4">
            <thead>
                <tr>
                    <th>Course</th>
                    <th>Venue</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $active_classes->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['course_name']; ?></td>
                        <td><?php echo $row['venue_name']; ?></td>
                        <td>
                            <a href="manage_classes.php?stop_class=<?php echo $row['id']; ?>" class="btn btn-danger">Stop Class</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Start Class Modal -->
    <div class="modal fade" id="startClassModal" tabindex="-1" role="dialog" aria-labelledby="startClassModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="startClassModalLabel">Start Class</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="manage_classes.php">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="course_id">Select Course</label>
                            <select class="form-control" id="course_id" name="course_id" required>
                                <?php while ($course = $courses->fetch_assoc()): ?>
                                    <option value="<?php echo $course['id']; ?>"><?php echo $course['name']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="venue_id">Select Venue</label>
                            <select class="form-control" id="venue_id" name="venue_id" required>
                                <?php while ($venue = $venues->fetch_assoc()): ?>
                                    <option value="<?php echo $venue['id']; ?>"><?php echo $venue['name']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" name="start_class" class="btn btn-primary">Start Class</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Include jQuery and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
