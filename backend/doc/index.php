<?php
session_start();
include('assets/inc/config.php');

if (isset($_POST['doc_login'])) {

    $doc_email = $_POST['doc_email'];
    $doc_pwd   = sha1(md5($_POST['doc_pwd']));


    if ($stmt = $mysqli->prepare("SELECT id, email, password FROM doctor WHERE email = ? AND password = ?")) {
        $stmt->bind_param('ss', $doc_email, $doc_pwd);
        $stmt->execute();
        $stmt->store_result();


        if ($stmt->num_rows > 0) {

            $stmt->bind_result($doc_id, $email, $password);
            $stmt->fetch();


            $_SESSION['doc_id']    = $doc_id;
            $_SESSION['doc_email'] = $email;


            header("Location: his_doc_dashboard.php");
            exit;
        } else {

            $err = "Access Denied. Please check your credentials.";
        }
        $stmt->close();
    } else {
        die("Database error: " . $mysqli->error);
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Digital Health Card</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="" name="description" />
    <meta content="MartDevelopers" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="shortcut icon" href="assets/images/favicon.ico">


    <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" />


    <script src="assets/js/swal.js"></script>


    <?php if (isset($success)) { ?>
        <script>
            setTimeout(function() {
                swal("Success", "<?php echo $success; ?>", "success");
            }, 100);
        </script>
    <?php } ?>

    <?php if (isset($err)) { ?>
        <script>
            setTimeout(function() {
                swal("Failed", "<?php echo $err; ?>", "error");
            }, 100);
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
                                    <span><img src="assets/images/logo-dark.png" alt="" height="22"></span>
                                </a>
                                <p class="text-muted mb-4 mt-3">Enter your email address and password to access the Doctor panel.</p>
                            </div>

                            <form method="post">
                                <div class="form-group mb-3">
                                    <label for="emailaddress">Email Address</label>
                                    <input class="form-control" name="doc_email" type="text" id="emailaddress" required placeholder="Enter your email address">
                                </div>
                                <div class="form-group mb-3">
                                    <label for="password">Password</label>
                                    <input class="form-control" name="doc_pwd" type="password" required id="password" placeholder="Enter your password">
                                </div>
                                <div class="form-group mb-0 text-center">
                                    <button class="btn btn-success btn-block" name="doc_login" type="submit">Log In</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12 text-center">
                            <p><a href="his_doc_reset_pwd.php" class="text-white-50 ml-1">Forgot your password?</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include("assets/inc/footer1.php"); ?>


    <script src="assets/js/vendor.min.js"></script>

    <script src="assets/js/app.min.js"></script>
</body>

</html>