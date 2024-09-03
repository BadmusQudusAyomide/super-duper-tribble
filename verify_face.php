<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "student_attendance_system";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$student_matric_no = $_SESSION['matric_no'];

// Fetch the student's face encoding from the database
$sql = "SELECT face_encoding FROM students WHERE matric_no = '$student_matric_no'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $stored_face_encoding = json_decode($row['face_encoding'], true);

    // Capture the current face using the Python script
    $current_face_encoding_json = shell_exec("python capture_face.py");
    $current_face_encoding = json_decode($current_face_encoding_json, true);

    if ($current_face_encoding) {
        // Compare the stored face encoding with the current face encoding
        $matches = shell_exec("python compare_faces.py '" . json_encode($stored_face_encoding) . "' '" . json_encode($current_face_encoding) . "'");

        if (trim($matches) == "True") {
            echo "Face matched";
        } else {
            echo "Face does not match";
        }
    } else {
        echo "No face detected";
    }
} else {
    echo "Student not found.";
}

$conn->close();
?>