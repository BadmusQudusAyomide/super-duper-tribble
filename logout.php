<?php
session_start();
session_unset(); // Unset all session variables
session_destroy(); // Destroy the session

// Redirect to login page based on the user type
if (isset($_SESSION['matric_no'])) {
    header("Location: login.php"); // Redirect to student login
} elseif (isset($_SESSION['admin_username'])) {
    header("Location: admin_login.php"); // Redirect to admin login
} else {
    header("Location: login.php"); // Default to student login if unsure
}

exit;
?>