<?php
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $matric_no = $_POST['matric_no'];
    $department = $_POST['department'];
    $faculty = $_POST['faculty'];
    $level = $_POST['level'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Use bcrypt to hash passwords
    $face_encoding = $_POST['face_encoding'];

    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO students (name, matric_no, department, faculty, level, password, face_encoding) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $name, $matric_no, $department, $faculty, $level, $password, $face_encoding);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "Student registered successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
}

$conn->close();
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 500px;
            margin: 40px auto;
            padding: 20px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-control {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
        }

        .video-container {
            margin: 20px 0;
        }

        video {
            width: 100%;
            height: 240px;
            border: 1px solid #ddd;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Student Registration</h2>
        <form method="post" action="register.php" onsubmit="return captureFace();">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" name="name" id="name" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="matric_no">Matric No:</label>
                <input type="text" name="matric_no" id="matric_no" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="department">Department:</label>
                <input type="text" name="department" id="department" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="faculty">Faculty:</label>
                <input type="text" name="faculty" id="faculty" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="level">Level:</label>
                <input type="text" name="level" id="level" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>

            <div class="video-container">
                <video id="video" width="320" height="240" autoplay></video>
            </div>

            <input type="hidden" name="face_encoding" id="face_encoding">

            <button type="submit" class="btn btn-primary">Register</button>
        </form>
    </div>

    <script>
        // JavaScript to handle webcam access and face capture
        const video = document.getElementById('video');

        // Access the webcam
        // ...

        function captureFace() {
            const canvas = document.createElement('canvas');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0);

            // Convert the image to base64 (this can be further processed to extract face encoding)
            const faceImage = canvas.toDataURL('image/jpeg');

            // For simplicity, we'll use the base64 image as the "face encoding"
            document.getElementById('face_encoding').value = faceImage;

            return true; // Allow form submission
        }

        // Handle webcam access errors
        navigator.mediaDevices.getUserMedia({ video: true })
            .then(stream => {
                video.srcObject = stream;
            })
            .catch(err => {
                console.error("Error accessing webcam: ", err);
                alert("Error accessing webcam. Please try again.");
            });
    </script>
</body>

</html>