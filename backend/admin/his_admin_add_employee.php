<?php
session_start();
include('assets/inc/config.php');

if (isset($_POST['add_doc'])) {
    // Capture and sanitize form data
    $first_name = $_POST['first_name'];
    $last_name  = $_POST['last_name'];
    $email      = $_POST['email'];
    // Note: Using sha1(md5(...)) for password hashing is not recommended.
    // Consider using password_hash() for stronger security.
    $password   = sha1(md5($_POST['password']));

    // SQL to insert captured values into the doctor table
    $query = "INSERT INTO doctor (first_name, last_name, email, password) VALUES (?, ?, ?, ?)";
    $stmt  = $mysqli->prepare($query);
    $rc    = $stmt->bind_param('ssss', $first_name, $last_name, $email, $password);
    $stmt->execute();

    if ($stmt) {
        $success = "Doctor Details Added";
    } else {
        $err = "Please Try Again Or Try Later";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<!--Head-->
<?php include('assets/inc/head.php'); ?>

<body>
    <!-- Begin page -->
    <div id="wrapper">
        <!-- Topbar Start -->
        <?php include("assets/inc/nav.php"); ?>
        <!-- end Topbar -->

        <!-- ========== Left Sidebar Start ========== -->
        <?php include('assets/inc/sidebar.php'); ?>
        <!-- Left Sidebar End -->

        <!-- ============================================================== -->
        <!-- Start Page Content here -->
        <!-- ============================================================== -->
        <div class="content-page">
            <div class="content">
                <!-- Start Content-->
                <div class="container-fluid">
                    <!-- start page title -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box">
                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="his_admin_dashboard.php">Dashboard</a></li>
                                        <li class="breadcrumb-item"><a href="javascript: void(0);">Doctor</a></li>
                                        <li class="breadcrumb-item active">Add Doctor</li>
                                    </ol>
                                </div>
                                <h4 class="page-title">Add Doctor Details</h4>
                            </div>
                        </div>
                    </div>
                    <!-- end page title -->

                    <!-- Form row -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="header-title">Fill all fields</h4>
                                    <!--Add Doctor Form-->
                                    <form method="post">
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label for="inputFirstName" class="col-form-label">First Name</label>
                                                <input type="text" required="required" name="first_name" class="form-control" id="inputFirstName">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="inputLastName" class="col-form-label">Last Name</label>
                                                <input type="text" required="required" name="last_name" class="form-control" id="inputLastName">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="inputEmail" class="col-form-label">Email</label>
                                            <input type="email" required="required" name="email" class="form-control" id="inputEmail">
                                        </div>

                                        <div class="form-group">
                                            <label for="inputPassword" class="col-form-label">Password</label>
                                            <input type="password" required="required" name="password" class="form-control" id="inputPassword">
                                        </div>

                                        <button type="submit" name="add_doc" class="ladda-button btn btn-success" data-style="expand-right">Add Doctor</button>
                                    </form>
                                    <!--End Doctor Form-->
                                </div> <!-- end card-body -->
                            </div> <!-- end card-->
                        </div> <!-- end col -->
                    </div>
                    <!-- end row -->
                </div> <!-- container -->
            </div> <!-- content -->

            <!-- Footer Start -->
            <?php include('assets/inc/footer.php'); ?>
            <!-- end Footer -->
        </div>
        <!-- ============================================================== -->
        <!-- End Page content -->
        <!-- ============================================================== -->
    </div>
    <!-- END wrapper -->

    <!-- Right bar overlay-->
    <div class="rightbar-overlay"></div>

    <!-- Vendor js -->
    <script src="assets/js/vendor.min.js"></script>
    <!-- App js-->
    <script src="assets/js/app.min.js"></script>
    <!-- Loading buttons js -->
    <script src="assets/libs/ladda/spin.js"></script>
    <script src="assets/libs/ladda/ladda.js"></script>
    <!-- Buttons init js-->
    <script src="assets/js/pages/loading-btn.init.js"></script>
</body>

</html>