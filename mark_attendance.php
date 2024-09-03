<?php
session_start();

if (!isset($_SESSION['matric_no'])) {
    echo "Session expired. Please log in again.";
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "student_attendance_system";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$student_matric_no = $_SESSION['matric_no'];
$class_id = $_POST['class_id'];
$face_image = $_POST['face_image'];

$face_image = str_replace('data:image/png;base64,', '', $face_image);
$face_image = str_replace(' ', '+', $face_image);
$face_image_data = base64_decode($face_image);

$image_path = 'temp_face_image.png';
file_put_contents($image_path, $face_image_data);

// Fetch face encoding from the database
$stmt = $conn->prepare("SELECT face_encoding FROM students WHERE matric_no = ?");
$stmt->bind_param("s", $student_matric_no);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $face_encoding_str = $row['face_encoding'];

    // ...

    // Write face encoding data to a temporary file
    $face_encoding_file = tempnam(sys_get_temp_dir(), 'face_encoding_');
    file_put_contents($face_encoding_file, $face_encoding_str);

    // Execute the Python script
    $command = "python3 recognize_face.py " . escapeshellarg($image_path) . " " . escapeshellarg($face_encoding_file);
    $output = shell_exec($command);

    // Remove the temporary file
    unlink($face_encoding_file);

    if (trim($output) == 'recognized') {
        $stmt = $conn->prepare("INSERT INTO attendance (student_matric_no, class_id, marked_at) VALUES (?, ?, NOW())");
        $stmt->bind_param("ss", $student_matric_no, $class_id);
        if ($stmt->execute()) {
            echo "Attendance marked successfully.";
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        echo "Face not recognized. Attendance not marked.";
    }
} else {
    echo "Student face encoding not found.";
}

unlink($image_path);
$conn->close();
?>