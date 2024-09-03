<?php
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin_username'])) {
    header("Location: admin_login.php");
    exit;
}
?>
html
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responsive Sidebar</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700&display=swap">
    <style>
        body {
            font-family: 'Open Sans', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            height: 100vh;
            background-color: #333;
            color: #fff;
            padding: 20px;
            transition: all 0.5s ease;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .sidebar.inactive {
            left: -250px;
        }

        .sidebar.active {
            left: 0;
        }

        .sidebar a {
            display: block;
            padding: 10px;
            text-decoration: none;
            color: #fff;
            font-size: 16px;
            font-weight: 400;
        }

        .sidebar a:hover {
            background-color: #444;
        }

        .sidebar a.active {
            background-color: #555;
        }

        .toggle-button {
            position: absolute;
            top: 10px;
            left: 10px;
            font-size: 24px;
            cursor: pointer;
            background-color: transparent;
            border: none;
            padding: 0;
            display: none;
        }

        .close-button {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 24px;
            cursor: pointer;
            background-color: transparent;
            border: none;
            padding: 0;
        }

        .content {
            margin-left: 250px;
            padding: 20px;
            transition: all 0.5s ease;
        }

        .content-header {
            background-color: #fff;
            padding: 20px;
            border-bottom: 1px solid #ddd;
        }

        .content-header h1 {
            font-size: 24px;
            font-weight: 600;
            margin: 0;
        }

        .content-body {
            padding: 20px;
        }

        @media (max-width: 768px) {
            .sidebar {
                left: -250px;
            }

            .sidebar.active {
                left: 0;
            }

            .toggle-button {
                display: block;
            }

            .content {
                margin-left: 0;
            }
        }
    </style>
</head>

<body>
    <div class="sidebar inactive">
        <h3 class="text-center">Admin Dashboard</h3>
        <a href="manage_students.php"
            class="<?php echo $_SERVER['PHP_SELF'] == '/manage_students.php' ? 'active' : ''; ?>">Manage Students</a>
        <a href="manage_courses.php"
            class="<?php echo $_SERVER['PHP_SELF'] == '/manage_courses.php' ? 'active' : ''; ?>">Manage Courses</a>
        <a href="manage_venues.php"
            class="<?php echo $_SERVER['PHP_SELF'] == '/manage_venues.php' ? 'active' : ''; ?>">Manage Venues</a>
        <a href="manage_classes.php"
            class="<?php echo $_SERVER['PHP_SELF'] == '/manage_classes.php' ? 'active' : ''; ?>">Manage Classes</a>
        <a href="manage_attendance.php"
            class="<?php echo $_SERVER['PHP_SELF'] == '/manage_attendance.php' ? 'active' : ''; ?>">Manage
            Attendance</a>

        <a href="admin_register.php"
            class="<?php echo $_SERVER['PHP_SELF'] == '/admin_register.php' ? 'active' : ''; ?>">Register New Admin</a>

        <a href="logout.php">Logout</a>
        <button class="close-button" onclick="toggleSidebar()"><i class="fas fa-times"></i></button>
    </div>

    <button class="toggle-button" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>

    <div class="content">
        <div class="content-header">
            <h1>Admin Dashboard</h1>
        <div class="content-body">
            <!-- Your content here -->
    </div>
          <!-- Your content here -->
    </div>
  </div>

  <script>
    function toggleSidebar() {
      var sidebar = document.querySelector('.sidebar');
      var content = document.querySelector('.content');

      if (sidebar.classList.contains('active')) {
        sidebar.classList.remove('active');
        sidebar.classList.add('inactive');
        content.style.marginLeft = '0';
      } else {
        sidebar.classList.remove('inactive');
        sidebar.classList.add('active');
        content.style.marginLeft = '250px';
      }
    }

    document.querySelector('.content').addEventListener('click', function() {
      document.querySelector('.sidebar').classList.remove('active');
      document.querySelector('.sidebar').classList.add('inactive');
      document.querySelector('.content').style.marginLeft = '0';
    });
  </script>
</body>
</html>
    <script>
        function toggleSidebar() {
            var sidebar = document.querySelector('.sidebar');
            var content = document.querySelector('.content');

            if (sidebar.classList.contains('active')) {
                sidebar.classList.remove('active');
                sidebar.classList.add('inactive');
                content.style.marginLeft = '0';
            } else {
                sidebar.classList.remove('inactive');
                sidebar.classList.add('active');
                content.style.marginLeft = '250px';
            }
        }

        document.querySelector('.content').addEventListener('click', function () {
            document.querySelector('.sidebar').classList.remove('active');
            document.querySelector('.sidebar').classList.add('inactive');
            document.querySelector('.content').style.marginLeft = '0';
        });
    </script>
</body>

</html>