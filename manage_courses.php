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

// Handle form submission for adding a course
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_course'])) {
    $course_name = $_POST['course_name'];
    $course_code = $_POST['course_code'];

    $sql = "INSERT INTO courses (name, code) VALUES ('$course_name', '$course_code')";
    $conn->query($sql);
}

// Handle form submission for editing a course
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_course'])) {
    $course_id = $_POST['course_id'];
    $course_name = $_POST['course_name'];
    $course_code = $_POST['course_code'];

    $sql = "UPDATE courses SET course_name='$course_name', course_code='$course_code' WHERE id=$course_id";
    $conn->query($sql);
}

// Handle course deletion
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $sql = "DELETE FROM courses WHERE id=$delete_id";
    $conn->query($sql);
}

// Retrieve all courses
$sql = "SELECT * FROM courses";
$result = $conn->query($sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Courses</title>
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .container {
            margin-top: 50px;
        }
        .table {
            margin-top: 20px;
        }
        .modal-header {
            background-color: #007bff;
            color: white;
        }
        .edit-icon, .delete-icon {
            cursor: pointer;
        }
        .edit-icon:hover, .delete-icon:hover {
            color: #007bff;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Manage Courses</h2>
        <button class="btn btn-primary" data-toggle="modal" data-target="#addCourseModal">Add Course</button>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Course Name</th>
                    <th>Course Code</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['code']; ?></td>
                    <td>
                        <i class="fas fa-edit edit-icon" data-toggle="modal" data-target="#editCourseModal"
                            data-id="<?php echo $row['id']; ?>"
                            data-name="<?php echo $row['name']; ?>"
                            data-code="<?php echo $row['code']; ?>"></i>
                        <a href="manage_courses.php?delete_id=<?php echo $row['id']; ?>"><i class="fas fa-trash delete-icon"></i></a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Add Course Modal -->
    <div class="modal fade" id="addCourseModal" tabindex="-1" role="dialog" aria-labelledby="addCourseModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCourseModalLabel">Add Course</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="manage_courses.php">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="course_name">Course Name</label>
                            <input type="text" class="form-control" id="course_name" name="course_name" required>
                        </div>
                        <div class="form-group">
                            <label for="course_code">Course Code</label>
                            <input type="text" class="form-control" id="course_code" name="course_code" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" name="add_course" class="btn btn-primary">Add Course</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Course Modal -->
    <div class="modal fade" id="editCourseModal" tabindex="-1" role="dialog" aria-labelledby="editCourseModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCourseModalLabel">Edit Course</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="manage_courses.php">
                    <div class="modal-body">
                        <input type="hidden" id="edit_course_id" name="course_id">
                        <div class="form-group">
                            <label for="edit_course_name">Course Name</label>
                            <input type="text" class="form-control" id="edit_course_name" name="course_name" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_course_code">Course Code</label>
                            <input type="text" class="form-control" id="edit_course_code" name="course_code" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" name="edit_course" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Include jQuery and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        // Pass data to the edit modal
        $('#editCourseModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');
            var name = button.data('name');
            var code = button.data('code');

            var modal = $(this);
            modal.find('#edit_course_id').val(id);
            modal.find('#edit_course_name').val(name);
            modal.find('#edit_course_code').val(code);
        });
    </script>
</body>
</html>
