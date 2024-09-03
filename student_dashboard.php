<?php
session_start();

// Check if the student is logged in
if (!isset($_SESSION['matric_no'])) {
    header("Location: login.php");
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

// Get the student's matric number from session
$student_matric_no = $_SESSION['matric_no'];

// Fetch classes that are currently active
$classes_sql = "SELECT classes.id, courses.name AS course_name, venues.name AS venue_name, venues.latitude, venues.longitude 
                FROM classes
                JOIN courses ON classes.course_id = courses.id
                JOIN venues ON classes.venue_id = venues.id
                WHERE classes.status = 'active'";

$classes_result = $conn->query($classes_sql);

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .sidebar {
            width: 250px;
            height: 100vh;
            background-color: #343a40;
            position: fixed;
            top: 0;
            left: 0;
            padding-top: 20px;
            transition: left 0.3s ease-in-out;
        }

        .sidebar.show {
            left: 0;
        }

        .sidebar.hide {
            left: -250px;
        }

        .sidebar a {
            padding: 10px 15px;
            font-size: 18px;
            color: white;
            display: block;
            text-decoration: none;
        }

        .sidebar a:hover {
            background-color: #007bff;
            color: white;
        }

        .content {
            margin-left: 250px;
            padding: 20px;
            width: 100%;
        }

        .burger {
            position: fixed;
            top: 10px;
            left: 10px;
            z-index: 1;
            cursor: pointer;
        }

        .burger span {
            display: block;
            width: 30px;
            height: 2px;
            background-color: #333;
            margin-bottom: 5px;
            transition: transform 0.3s ease-in-out;
        }

        .burger.active span:first-child {
            transform: rotate(45deg) translate(5px, 5px);
        }

        .burger.active span:nth-child(2) {
            opacity: 0;
        }

        .burger.active span:last-child {
            transform: rotate(-45deg) translate(5px, -5px);
        }
        .content {
    margin-left: 250px;
    padding: 20px;
    width: 100%;
    height: 100vh; /* add this line */
    overflow-y: hidden; /* add this line */
}

table {
    table-layout: fixed; /* add this line */
    width: 100%; /* add this line */
}
table th, table td {
    padding: 10px;
    font-size: 14px;
}

table th {
    background-color: #f0f0f0;
}
    </style>
</head>

<body>
    <div class="burger" id="burger">
        <span></span>
        <span></span>
        <span></span>
    </div>

    <div class="sidebar hide" id="sidebar">
        <h4 class="text-white text-center">Student Dashboard</h4>
        <a href="student_dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
        <a href="student_courses.php"><i class="fas fa-book">My Courses</i></a>
        <a href="student_attendance.php"><i class="fas fa-calendar-check"></i> Attendance</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="content">
    <h2>Current Classes</h2>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Course</th>
                <th>Venue</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($classes_result->num_rows > 0): ?>
                    <?php while ($row = $classes_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['course_name']; ?></td>
                            <td><?php echo $row['venue_name']; ?></td>
                            <td>
                                <button class="btn btn-primary"
                                    onclick="markAttendance(<?php echo $row['id']; ?>, <?php echo $row['latitude']; ?>, <?php echo $row['longitude']; ?>)">
                                    Mark Attendance
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="text-center">No active classes found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <script>
        const burger = document.getElementById('burger');
        const sidebar = document.getElementById('sidebar');

        burger.addEventListener('click', () => {
            sidebar.classList.toggle('show');
            sidebar.classList.toggle('hide');
            burger.classList.toggle('active');
        });
    </script>
    <script>
        function markAttendance(classId, venueLat, venueLng) {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function (position) {
                    let studentLat = position.coords.latitude;
                    let studentLng = position.coords.longitude;

                    // Calculate distance between student and venue
                    let distance = getDistanceFromLatLonInMeters(studentLat, studentLng, venueLat, venueLng);

                    if (distance <= 10000000000000) {
                        // Proceed to capture face and mark attendance
                        captureFace(classId);
                    } else {
                        alert("You are not within the required range to mark attendance.");
                    }
                });
            } else {
                alert("Geolocation is not supported by this browser.");
            }
        }

        function getDistanceFromLatLonInMeters(lat1, lon1, lat2, lon2) {
            const R = 6371000; // Radius of the earth in meters
            const dLat = deg2rad(lat2 - lat1);
            const dLon = deg2rad(lon2 - lon1);
            const a =
                Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) *
                Math.sin(dLon / 2) * Math.sin(dLon / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            const distance = R * c; // Distance in meters
            return distance;
        }

        function deg2rad(deg) {
            return deg * (Math.PI / 180);
        }

        function captureFace(classId) {
            const video = document.createElement('video');

            navigator.mediaDevices.getUserMedia({ video: true })
                .then(stream => {
                    video.srcObject = stream;
                    video.play();

                    const canvas = document.createElement('canvas');
                    document.body.appendChild(canvas);

                    const context = canvas.getContext('2d');

                    // Capture a frame from the video
                    video.addEventListener('canplay', () => {
                        context.drawImage(video, 0, 0, canvas.width, canvas.height);

                        // Stop the video stream
                        stream.getTracks().forEach(track => track.stop());

                        // Convert the canvas to a data URL (base64 image)
                        const faceImageData = canvas.toDataURL('image/png');

                        // Send the image to the server for face recognition
                        let xhr = new XMLHttpRequest();
                        xhr.open("POST", "mark_attendance.php", true);
                        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                        xhr.onload = function () {
                            alert(this.responseText);
                        };
                        xhr.send("class_id=" + classId + "&face_image=" + encodeURIComponent(faceImageData));

                        // Clean up the canvas
                        document.body.removeChild(canvas);
                    });
                })
                .catch(err => {
                    console.error("Error accessing webcam: " + err);
                    alert("Could not access your webcam. Please ensure it is connected and working.");
                });
        }
    </script>
</body>

</html>