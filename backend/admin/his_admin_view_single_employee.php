<?php
session_start();
include('assets/inc/config.php');
include('assets/inc/checklogin.php');
check_login();
$aid = $_SESSION['ad_id'];
?>
<!DOCTYPE html>
<html lang="en">
<!-- Head -->
<?php include('assets/inc/head.php'); ?>

<body>
    <!-- Begin page -->
    <div id="wrapper">
        <!-- Topbar Start -->
        <?php include("assets/inc/nav.php"); ?>
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
                    <!-- Page Title and Breadcrumbs -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box">
                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="his_admin_dashboard.php">Dashboard</a></li>
                                        <li class="breadcrumb-item"><a href="his_admin_view_doctors.php">Doctor</a></li>
                                        <li class="breadcrumb-item active">View Doctor</li>
                                    </ol>
                                </div>
                                <h4 class="page-title">Doctor Profile</h4>
                            </div>
                        </div>
                    </div>
                    <!-- End Page Title -->

                    <?php
                    // Check if a doctor id is provided via the GET parameter "ad_id"
                    if (isset($_GET['ad_id'])) {
                        $doctor_id = intval($_GET['ad_id']);
                        $ret = "SELECT * FROM doctor WHERE id = ?";
                        if ($stmt = $mysqli->prepare($ret)) {
                            $stmt->bind_param('i', $doctor_id);
                            $stmt->execute();
                            $res = $stmt->get_result();

                            if ($res->num_rows > 0) {
                                $row = $res->fetch_object();
                    ?>
                                <!-- Doctor Details Table -->
                                <div class="row">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-striped mb-0">
                                                        <thead>
                                                            <tr>
                                                                <th>First Name</th>
                                                                <th>Last Name</th>
                                                                <th>Email</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td><?php echo $row->first_name; ?></td>
                                                                <td><?php echo $row->last_name; ?></td>
                                                                <td><?php echo $row->email; ?></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div> <!-- end card-body -->
                                        </div> <!-- end card -->
                                    </div> <!-- end col-12 -->
                                </div> <!-- end row -->
                    <?php
                            } else {
                                echo "<div class='alert alert-danger'>No doctor found with that ID.</div>";
                            }
                        } else {
                            echo "<div class='alert alert-danger'>Database error: Unable to prepare statement.</div>";
                        }
                    } else {
                        echo "<div class='alert alert-danger'>No doctor ID specified.</div>";
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
</body>

</html>