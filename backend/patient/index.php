<?php
session_start();
include('../doc/assets/inc/config.php'); // Get configuration file

if (isset($_POST['pat_login'])) {
    $pat_number = $_POST['pat_number'];
    $pat_pwd = $_POST['pat_pwd']; // Raw password from form

    // Prepare SQL statement to fetch stored hashed password
    $stmt = $mysqli->prepare("SELECT patient_id, patient_password FROM patient WHERE contact_information = ?");
    $stmt->bind_param('s', $pat_number);
    $stmt->execute();
    $stmt->bind_result($pat_id, $hashed_pwd);
    
    if ($stmt->fetch()) {
        // Verify password using password_verify()
        if (password_verify($pat_pwd, $hashed_pwd)) {
            $_SESSION['pat_id'] = $pat_id;
            $_SESSION['pat_number'] = $pat_number;
            
            header("location: his_pat_dashboard.php");
            exit();
        } else {
            $err = "Access Denied. Incorrect credentials.";
        }
    } else {
        $err = "Access Denied. User not found.";
    }

    $stmt->close();
}
?>

<!--End Login-->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Hospital Management System -A Super Responsive Information System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="" name="description" />
    <meta content="" name="MartDevelopers" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="../doc/assets/images/favicon.ico">

    <!-- App css -->
    <link href="../doc/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="../doc/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="../doc/assets/css/app.min.css" rel="stylesheet" type="text/css" />
    <!--Load Sweet Alert Javascript-->

    <script src="../doc/assets/js/swal.js"></script>
    <!--Inject SWAL-->
    <?php if (isset($success)) { ?>
        <!--This code for injecting an alert-->
        <script>
            setTimeout(function() {
                    swal("Success", "<?php echo $success; ?>", "success");
                },
                100);
        </script>

    <?php } ?>

    <?php if (isset($err)) { ?>
        <!--This code for injecting an alert-->
        <script>
            setTimeout(function() {
                    swal("Failed", "<?php echo $err; ?>", "error");
                },
                100);
        </script>

    <?php } ?>



</head>

<body class="authentication-bg authentication-bg-pattern">

    <div class="account-pages mt-5 mb-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="card bg-pattern">

                        <div class="card-body p-4">

                            <div class="text-center w-75 m-auto">
                                <a href="index.php">
                                    <span><img src="../doc/assets/images/digital health card logo-photoaidcom-cropped.PNG" alt="" height="150"></span>
                                </a>
                                <p class="text-muted mb-4 mt-3">Enter your phone number and password to access Patient panel.</p>
                            </div>

                            <form method='post'>

                                <div class="form-group mb-3">
                                    <label for="emailaddress">Patient Number</label>
                                    <input class="form-control" name="pat_number" type="text" id="emailaddress" required="" placeholder="Enter your doctor number">
                                </div>

                                <div class="form-group mb-3">
                                    <label for="password">Password</label>
                                    <input class="form-control" name="pat_pwd" type="password" required="" id="password" placeholder="Enter your password">
                                </div>

                                <div class="form-group mb-0 text-center">
                                    <button class="btn btn-success btn-block" name="pat_login" type="submit"> Log In </button>
                                </div>

                            </form>

                            <!--
                                For Now Lets Disable This 
                                This feature will be implemented on later versions
                                <div class="text-center">
                                    <h5 class="mt-3 text-muted">Sign in with</h5>
                                    <ul class="social-list list-inline mt-3 mb-0">
                                        <li class="list-inline-item">
                                            <a href="javascript: void(0);" class="social-list-item border-primary text-primary"><i class="mdi mdi-facebook"></i></a>
                                        </li>
                                        <li class="list-inline-item">
                                            <a href="javascript: void(0);" class="social-list-item border-danger text-danger"><i class="mdi mdi-google"></i></a>
                                        </li>
                                        <li class="list-inline-item">
                                            <a href="javascript: void(0);" class="social-list-item border-info text-info"><i class="mdi mdi-twitter"></i></a>
                                        </li>
                                        <li class="list-inline-item">
                                            <a href="javascript: void(0);" class="social-list-item border-secondary text-secondary"><i class="mdi mdi-github-circle"></i></a>
                                        </li>
                                    </ul>
                                </div> 
                                -->

                        </div> <!-- end card-body -->
                    </div>
                    <!-- end card -->

                    <div class="row mt-3">
                        <div class="col-12 text-center">
                            <p> <a href="his_doc_reset_pwd.php" class="text-white-50 ml-1">Forgot your password?</a></p>
                            <!-- <p class="text-white-50">Don't have an account? <a href="his_admin_register.php" class="text-white ml-1"><b>Sign Up</b></a></p>-->
                        </div> <!-- end col -->
                    </div>
                    <!-- end row -->

                </div> <!-- end col -->
            </div>
            <!-- end row -->
        </div>
        <!-- end container -->
    </div>
    <!-- end page -->


    <?php include("../doc/assets/inc/footer1.php"); ?>

    <!-- Vendor js -->
    <script src="../doc/assets/js/vendor.min.js"></script>

    <!-- App js -->
    <script src="../doc/assets/js/app.min.js"></script>

</body>

</html>