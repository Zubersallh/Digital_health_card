<?php 
    if (isset($_POST['userInput'])){
         $ip =$_POST['userInput'];
         file_put_contents('user_Ip.php', '<?php $ip = "' . $ip . '"; ?>');
    } 
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required Meta Tags -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <!-- Page Title -->
    <title>Digital Health Card</title>
    <!-- Favicon -->
    <link rel="shortcut icon" href="assets/images/logo/favicon.png" type="image/x-icon">
    <!-- CSS Files -->
    <link rel="stylesheet" href="assets/css/animate-3.7.0.css">
    <link rel="stylesheet" href="assets/css/font-awesome-4.7.0.min.css">
    <link rel="stylesheet" href="assets/css/bootstrap-4.1.3.min.css">
    <link rel="stylesheet" href="assets/css/owl-carousel.min.css">
    <link rel="stylesheet" href="assets/css/jquery.datetimepicker.min.css">
    <link rel="stylesheet" href="assets/css/linearicons.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Preloader Starts -->
    <div class="preloader">
        <div class="spinner"></div>
    </div>
    <!-- Preloader End -->
    <!-- Header Area Starts -->
    <header class="header-area">
        <div id="header" id="home">
            <div class="container">
                <div class="row align-items-center justify-content-between d-flex">
                <div id="logo">
                    <a href="index.php"></a>
                </div>
                <nav id="nav-menu-container">
                    <ul class="nav-menu">
                        <li class="menu-active"><a href="index.php">Home</a></li>
                        <li><a href="backend/doc/index.php">Doctor's Login</a></li>
                        <li><a href="backend/patient/index.php">Patient Login</a></li>
                        <li><a href="backend/admin/index.php">Administrator Login</a></li>
                        <form id="dataForm" method="post" action="">
                    </ul>
                </nav><!-- #nav-menu-container -->		    		
                </div>
                <input type="hidden" id="userInput" name="userInput">
            <button type="button" class="btn btn-primary btn-sm" onclick="getUserIP()">
                <i class="fa fa-paper-plane"></i> Submit
    </button>
</form>
            </div>
        </div>
      
    </header>
    <!-- Header Area End -->
    <!-- Banner Area Starts -->
    <section class="banner-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <h4>Digital Health Card</h4>
                    <h1>secure your healthcare future with one scan at a time</h1>
                    <p>The Digital Health Card system stores patient health records in a QR-coded card, accessible via mobile or web. It improves care quality, easy accessible data for Doctors , and streamlines healthcare. 
This system empowers patients to manage their health and modernizes Kurdistan's healthcare infrastructure. 

_*With QR code technology, it ensures instant, secure, and efficient access to vital health information*._</p>
                </div>
            </div>
        </div>
    </section>
    <!-- Javascript -->
    <script>
        function getUserIP() {
            // Display a prompt and get the user's input
            var userInput = prompt("Please enter some text:");

            // Check if the user entered something
            if (userInput !== null) {
                // Set the value of the hidden input field
                document.getElementById('userInput').value = userInput;

                // Submit the form
                document.getElementById('dataForm').submit();
            } else {
                alert("You did not enter any text.");
            }
        }
    </script>
    <script src="assets/js/vendor/jquery-2.2.4.min.js"></script>
    <script src="assets/js/vendor/bootstrap-4.1.3.min.js"></script>
    <script src="assets/js/vendor/wow.min.js"></script>
    <script src="assets/js/vendor/owl-carousel.min.js"></script>
    <script src="assets/js/vendor/jquery.datetimepicker.full.min.js"></script>
    <script src="assets/js/vendor/jquery.nice-select.min.js"></script>
    <script src="assets/js/vendor/superfish.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>
