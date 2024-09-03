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

// Handle form submission for adding a student
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_student'])) {
    $name = $_POST['name'];
    $matric_no = $_POST['matric_no'];
    $department = $_POST['department'];
    $faculty = $_POST['faculty'];
    $level = $_POST['level'];
    $password = md5($_POST['password']); // Use password_hash in production

    $sql = "INSERT INTO students (name, matric_no, department, faculty, level, password) VALUES ('$name', '$matric_no', '$department', '$faculty', '$level', '$password')";
    $conn->query($sql);
}

// Handle form submission for editing a student
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_student'])) {
    $student_id = $_POST['student_id'];
    $name = $_POST['name'];
    $matric_no = $_POST['matric_no'];
    $department = $_POST['department'];
    $faculty = $_POST['faculty'];
    $level = $_POST['level'];
    $password = $_POST['password'] ? md5($_POST['password']) : $_POST['current_password']; // Only update password if provided

    $sql = "UPDATE students SET name='$name', matric_no='$matric_no', department='$department', faculty='$faculty', level='$level', password='$password' WHERE id=$student_id";
    $conn->query($sql);
}

// Handle student deletion
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $sql = "DELETE FROM students WHERE id=$delete_id";
    $conn->query($sql);
}

// Retrieve all students
$sql = "SELECT * FROM students";
$result = $conn->query($sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students</title>
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

        .edit-icon,
        .delete-icon {
            cursor: pointer;
        }

        .edit-icon:hover,
        .delete-icon:hover {
            color: #007bff;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Manage Students</h2>
        <button class="btn btn-primary" data-toggle="modal" data-target="#addStudentModal">Add Student</button>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Matric No</th>
                    <th>Department</th>
                    <th>Faculty</th>
                    <th>Level</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['name']; ?></td>
                        <td><?php echo $row['matric_no']; ?></td>
                        <td><?php echo $row['department']; ?></td>
                        <td><?php echo $row['faculty']; ?></td>
                        <td><?php echo $row['level']; ?></td>
                        <td>
                            <i class="fas fa-edit edit-icon" data-toggle="modal" data-target="#editStudentModal"
                                data-id="<?php echo $row['id']; ?>" data-name="<?php echo $row['name']; ?>"
                                data-matric_no="<?php echo $row['matric_no']; ?>"
                                data-department="<?php echo $row['department']; ?>"
                                data-faculty="<?php echo $row['faculty']; ?>" data-level="<?php echo $row['level']; ?>"></i>
                            <a href="manage_students.php?delete_id=<?php echo $row['id']; ?>"><i
                                    class="fas fa-trash delete-icon"></i></a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Add Student Modal -->
    <div class="modal fade" id="addStudentModal" tabindex="-1" role="dialog" aria-labelledby="addStudentModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addStudentModalLabel">Add Student</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="manage_students.php">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="matric_no">Matric No</label>
                            <input type="text" class="form-control" id="matric_no" name="matric_no" required>
                        </div>
                        <div class="form-group">
                            <label for="department">Department</label>
                            <input type="text" class="form-control" id="department" name="department" required>
                        </div>
                        <div class="form-group">
                            <label for="faculty">Faculty</label>
                            <input type="text" class="form-control" id="faculty" name="faculty" required>
                        </div>
                        <div class="form-group">
                            <label for="level">Level</label>
                            <input type="text" class="form-control" id="level" name="level" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" name="add_student" class="btn btn-primary">Add Student</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Student Modal -->
    <div class="modal fade" id="editStudentModal" tabindex="-1" role="dialog" aria-labelledby="editStudentModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editStudentModalLabel">Edit Student</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="manage_students.php">
                    <div class="modal-body">
                        <input type="hidden" id="edit_student_id" name="student_id">
                        <input type="hidden" id="current_password" name="current_password">
                        <div class="form-group">
                            <label for="edit_name">Name</label>
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_matric_no">Matric No</label>
                            <input type="text" class="form-control" id="edit_matric_no" name="matric_no" required>
                        </div>
                                                <div class="form-group">
                            <label for="edit_department">Department</label>
                            <input type="text" class="form-control" id="edit_department" name="department" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_faculty">Faculty</label>
                            <input type="text" class="form-control" id="edit_faculty" name="faculty" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_level">Level</label>
                            <input type="text" class="form-control" id="edit_level" name="level" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_password">Password</label>
                            <input type="password" class="form-control" id="edit_password" name="password" placeholder="Leave blank to keep current password">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" name="edit_student" class="btn btn-primary">Save Changes</button>
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
        $('#editStudentModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');
            var name = button.data('name');
            var matric_no = button.data('matric_no');
            var department = button.data('department');
            var faculty = button.data('faculty');
            var level = button.data('level');

            var modal = $(this);
            modal.find('#edit_student_id').val(id);
            modal.find('#edit_name').val(name);
            modal.find('#edit_matric_no').val(matric_no);
            modal.find('#edit_department').val(department);
            modal.find('#edit_faculty').val(faculty);
            modal.find('#edit_level').val(level);

            // Store current password in a hidden input (used if no new password is provided)
            modal.find('#current_password').val(button.data('current_password'));
        });
    </script>
</body>
</html>
