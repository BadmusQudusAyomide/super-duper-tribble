<?php
session_start(); // Start the session

$servername = "sql211.ezyro.com";
$username = "ezyro_37064834"; // Change this to your database username
$password = "e5a7a28ca2f3"; // Change this to your database password
$dbname = "ezyro_37064834_student_attendance";
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
    <title>Ilaro: &mdash;Federal Polytechnic Ilaro | FPI</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    
    <link href="https://fonts.googleapis.com/css?family=Muli:300,400,700,900" rel="stylesheet">
    <link rel="stylesheet" href="fonts/icomoon/style.css">

    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/jquery-ui.css">
    <link rel="stylesheet" href="css/owl.carousel.min.css">
    <link rel="stylesheet" href="css/owl.theme.default.min.css">
    <link rel="stylesheet" href="css/owl.theme.default.min.css">

    <link rel="stylesheet" href="css/jquery.fancybox.min.css">

    <link rel="stylesheet" href="css/bootstrap-datepicker.css">

    <link rel="stylesheet" href="fonts/flaticon/font/flaticon.css">

    <link rel="stylesheet" href="css/aos.css">

    <link rel="stylesheet" href="css/style.css">
    
  </head>
  <body data-spy="scroll" data-target=".site-navbar-target" data-offset="300">
  
  <div class="site-wrap">

    <div class="site-mobile-menu site-navbar-target">
      <div class="site-mobile-menu-header">
        <div class="site-mobile-menu-close mt-3">
          <span class="icon-close2 js-menu-toggle"></span>
        </div>
      </div>
      <div class="site-mobile-menu-body"></div>
    </div>
   
    
    <header class="site-navbar py-4 js-sticky-header site-navbar-target" role="banner">
      
      <div class="container-fluid">
        <div class="d-flex align-items-center">
          <div class="site-logo mr-auto w-25"><a href="index.html">Welcome to FIP</a></div>

          <div class="mx-auto text-center">
            <nav class="site-navigation position-relative text-right" role="navigation">
              <ul class="site-menu main-menu js-clone-nav mx-auto d-none d-lg-block  m-0 p-0">
                <li><a href="#home-section" class="nav-link">Home</a></li>
                <li><a href="#courses-section" class="nav-link">Courses</a></li>
                <li><a href="#programs-section" class="nav-link">Programs</a></li>
                <li><a href="#teachers-section" class="nav-link">Teachers</a></li>
              </ul>
            </nav>
          </div>

          <div class="ml-auto w-25">
            <nav class="site-navigation position-relative text-right" role="navigation">
              <ul class="site-menu main-menu site-menu-dark js-clone-nav mr-auto d-none d-lg-block m-0 p-0">
                <li class="cta"><a href="#contact-section" class="nav-link"><span>Contact Us</span></a></li>
              </ul>
            </nav>
            <a href="#" class="d-inline-block d-lg-none site-menu-toggle js-menu-toggle text-black float-right"><span class="icon-menu h3"></span></a>
          </div>
        </div>
      </div>
      
    </header>

    <div class="intro-section" id="home-section">
      
      <div class="slide-1" style="background-image: url('images/4.jpg');" data-stellar-background-ratio="0.5">
        <div class="container">
          <div class="row align-items-center">
            <div class="col-12">
              <div class="row align-items-center">
                <div class="col-lg-6 mb-4">
                  <h1  data-aos="fade-up" data-aos-delay="100">Mark Your Attendance</h1>
                  <p class="mb-4"  data-aos="fade-up" data-aos-delay="200">This is a student attendace system using Face recognition and Geo location </p>
                  <p data-aos="fade-up" data-aos-delay="300"><a href="#" class="btn btn-success py-3 px-5 btn-pill">Go to Dashboard</a></p>

                </div>

                <div class="col-lg-5 ml-auto" data-aos="fade-up" data-aos-delay="500">
                  <?php if (isset($error)) { ?>
            <div class="error"><?= $error ?></div>
        <?php } ?>
                   <form action="login.php" method="post" class="form-box">
                                    <h3 class="h4 text-black mb-4">Login</h3>
                                    <div class="form-group">
                                        <input type="text" name="matric_no" class="form-control" placeholder="Matric No" required>
                                    </div>
                                    <div class="form-group">
                                        <input type="password" name="password" class="form-control" placeholder="Password" required>
                                    </div>
                                    <div class="form-group">
                                        <input type="submit" class="btn btn-success btn-pill" value="Login">
                                    </div>
                                </form>

                </div>
              </div>
            </div>
            
          </div>
        </div>
      </div>
    </div>



    <div class="site-section pb-0">

      <div class="future-blobs">
        <div class="blob_2">
          <img src="images/blob_2.svg" alt="Image">
        </div>
        <div class="blob_1">
          <img src="images/blob_1.svg" alt="Image">
        </div>
      </div>
      <div class="container">
        <div class="row mb-5 justify-content-center" data-aos="fade-up" data-aos-delay="">
          <div class="col-lg-7 text-center">
            <h2 class="section-title">Features of the Student Attendace System</h2>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-4 ml-auto align-self-start"  data-aos="fade-up" data-aos-delay="100">

            <div class="p-4 rounded bg-white why-choose-us-box">

              <div class="d-flex align-items-center custom-icon-wrap custom-icon-light mb-3">
                <div class="mr-3"><span class="custom-icon-inner"><span class="icon icon-graduation-cap"></span></span></div>
                <div><h3 class="m-0">Real Time Geo Location</h3></div>
              </div>

              <div class="d-flex align-items-center custom-icon-wrap custom-icon-light mb-3">
                <div class="mr-3"><span class="custom-icon-inner"><span class="icon icon-university"></span></span></div>
                <div><h3 class="m-0">Real time Face Recognition</h3></div>
              </div>

              <div class="d-flex align-items-center custom-icon-wrap custom-icon-light mb-3">
                <div class="mr-3"><span class="custom-icon-inner"><span class="icon icon-graduation-cap"></span></span></div>
                <div><h3 class="m-0">Printing of individual course for leacturers</h3></div>
              </div>

              <div class="d-flex align-items-center custom-icon-wrap custom-icon-light mb-3">
                <div class="mr-3"><span class="custom-icon-inner"><span class="icon icon-university"></span></span></div>
                <div><h3 class="m-0">Printing of individual course for Student</h3></div>
              </div>

              <div class="d-flex align-items-center custom-icon-wrap custom-icon-light mb-3">
                <div class="mr-3"><span class="custom-icon-inner"><span class="icon icon-graduation-cap"></span></span></div>
                <div><h3 class="m-0">Assesing Past Attendance </h3></div>
              </div>


            </div>


          </div>
          <div class="col-lg-7 align-self-end"  data-aos="fade-left" data-aos-delay="200">
            <img src="images/person_transparent.png" alt="Image" class="img-fluid">
          </div>
        </div>
      </div>
    </div>

    
     
    <footer class="footer-section bg-white">
      <div class="container">
        <div class="row">
          <div class="col-md-4">
            <h3>About Ilaro Federal Polythecnic</h3>
            <p>The Federal Polytechnic, Ilaro was established by Decree No. 33 of July 25, 1979. The Polytechnic opened to students on November 15, 1979 on a temporary site provided by its host community, the ancient town of Ilaro, Ogun State. The first site of the Polytechnic was the compound of the Anglican Grammar School, Ilaro about half a kilometre from Ilaro township junction. The Polytechnic was on this temporary site till 1983 when it moved to its permanent site along Ilaro/Oja-Odan Road, about three kilometres from Ilaro Township.</p>
          </div>

          <div class="col-md-3 ml-auto">
            <h3>Links</h3>
            <ul class="list-unstyled footer-links">
              <li><a href="#">Home</a></li>
              <li><a href="https://federalpolyilaro.edu.ng/">School Website</a></li>
            </ul>
          </div>

          <div class="col-md-4">
            <h3>Subscribe</h3>
            <p>Subsribe to ilaro newsletter </p>
            <form action="#" class="footer-subscribe">
              <div class="d-flex mb-5">
                <input type="text" class="form-control rounded-0" placeholder="Email">
                <input type="submit" class="btn btn-success rounded-0" value="Subscribe">
              </div>
            </form>
          </div>

        </div>

        
    </footer>

  
    
  </div> <!-- .site-wrap -->

  <script src="jquery-3.3.1.min.js"></script>
  <script src="jquery-migrate-3.0.1.min.js"></script>
  <script src="jquery-ui.js"></script>
  <script src="popper.min.js"></script>
  <script src="bootstrap.min.js"></script>
  <script src="owl.carousel.min.js"></script>
  <script src="jquery.stellar.min.js"></script>
  <script src="jquery.countdown.min.js"></script>
  <script src="bootstrap-datepicker.min.js"></script>
  <script src="jquery.easing.1.3.js"></script>
  <script src="aos.js"></script>
  <script src="jquery.fancybox.min.js"></script>
  <script src="jquery.sticky.js"></script>

  
  <script src="main.js"></script>
    
  </body>
</html>