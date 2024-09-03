<?php
session_start(); // Start the session

$servername = "localhost";
$username = "root"; // Change this to your database username
$password = ""; // Change this to your database password
$dbname = "student_attendance_system";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $matric_no = $_POST['matric_no'];
    $password = $_POST['password'];

    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM students WHERE matric_no=?");
    $stmt->bind_param("s", $matric_no);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $hashed_password = $row['password'];

        // Verify the password using password_verify
        if (password_verify($password, $hashed_password)) {
            $_SESSION['matric_no'] = $matric_no; // Store student's matric_no in session
            header("Location: student_dashboard.php"); // Redirect to student dashboard
            exit;
        } else {
            echo "Invalid matric number or password!";
        }
    } else {
        echo "Invalid matric number or password!";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
        }

        .container {
            max-width: 400px;
            margin: 40px auto;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ddd;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .form-control {
            height: 40px;
            padding: 10px;
            font-size: 16px;
        }

        .btn {
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
        }

        .error {
            color: #red;
            font-size: 14px;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2 class="text-center">Student Login</h2>
        <?php if (isset($error)) { ?>
            <div class="error"><?= $error ?></div>
        <?php } ?>
        <form method="post" action="login.php">
            <div class="form-group">
                <label for="matric_no">Matric No:</label>
                <input type="text" id="matric_no" name="matric_no" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Login</button>
        </form>
    </div>
</body>

</html>