<?php
session_start();
include('assets/inc/config.php');
include('assets/inc/checklogin.php');
check_login();
$aid = $_SESSION['ad_id'];

// Process the update form submission
if (isset($_POST['update_emp'])) {
    // Capture and sanitize form data
    $first_name = $_POST['first_name'];
    $last_name  = $_POST['last_name'];
    $email      = $_POST['email'];
    // Note: Using sha1(md5(...)) for password hashing is not recommended.
    // Consider using password_hash() for stronger security.
    $password   = sha1(md5($_POST['password']));
    // The unique record identifier comes from the GET parameter 'id'
    $id         = intval($_GET['ad_id']);

    // Update query for the doctor table
    $query = "UPDATE doctor SET first_name = ?, last_name = ?, email = ?, password = ? WHERE id = ?";
    if ($stmt = $mysqli->prepare($query)) {
        $stmt->bind_param('ssssi', $first_name, $last_name, $email, $password, $id);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            $success = "Employee Details Updated";
        } else {
            $err = "Please Try Again Or Try Later";
        }
        $stmt->close();
    } else {
        $err = "Database Error: Unable to prepare statement";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<!-- Head -->
<?php include('assets/inc/head.php'); ?>

<body>
    <!-- Begin page -->
    <div id="wrapper">
        <!-- Topbar Start -->
        <?php include('assets/inc/nav.php'); ?>
        <!-- End Topbar -->

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
                                        <li class="breadcrumb-item"><a href="javascript: void(0);">Doctors</a></li>
                                        <li class="breadcrumb-item active">Manage Doctors</li>
                                    </ol>
                                </div>
                                <h4 class="page-title">Update Doctors Details</h4>
                            </div>
                        </div>
                    </div>
                    <!-- end page title -->

                    <?php
                    // Retrieve the current employee details using the GET parameter 'id'
                    $id = intval($_GET['ad_id']);
                    $ret = "SELECT * FROM doctor WHERE id = ?";
                    if ($stmt = $mysqli->prepare($ret)) {
                        $stmt->bind_param('i', $id);
                        $stmt->execute();
                        $res = $stmt->get_result();
                        // Display the update form for the retrieved record
                        while ($row = $res->fetch_object()) {
                    ?>
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <h4 class="header-title">Fill all fields</h4>
                                            <!-- Update Employee Form -->
                                            <form method="post">
                                                <div class="form-row">
                                                    <div class="form-group col-md-6">
                                                        <label for="inputFirstName" class="col-form-label">First Name</label>
                                                        <input type="text" required="required" value="<?php echo $row->first_name; ?>" name="first_name" class="form-control" id="inputFirstName">
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <label for="inputLastName" class="col-form-label">Last Name</label>
                                                        <input type="text" required="required" value="<?php echo $row->last_name; ?>" name="last_name" class="form-control" id="inputLastName">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="inputEmail" class="col-form-label">Email</label>
                                                    <input type="email" required="required" value="<?php echo $row->email; ?>" name="email" class="form-control" id="inputEmail">
                                                </div>
                                                <div class="form-group">
                                                    <label for="inputPassword" class="col-form-label">Password</label>
                                                    <input type="password" required="required" name="password" class="form-control" id="inputPassword">
                                                </div>
                                                <button type="submit" name="update_emp" class="ladda-button btn btn-success" data-style="expand-right">Update Employee</button>
                                            </form>
                                            <!-- End Update Employee Form -->
                                        </div> <!-- end card-body -->
                                    </div> <!-- end card -->
                                </div> <!-- end col -->
                            </div>
                            <!-- end row -->
                    <?php
                        }
                        $stmt->close();
                    } else {
                        echo "<div class='alert alert-danger'>Unable to retrieve record.</div>";
                    }
                    ?>
                </div> <!-- end container-fluid -->
            </div> <!-- end content -->

            <!-- Footer Start -->
            <?php include('assets/inc/footer.php'); ?>
            <!-- End Footer -->
        </div>
        <!-- ============================================================== -->
        <!-- End Page Content -->
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