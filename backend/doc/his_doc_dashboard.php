<?php
session_start();
include('assets/inc/config.php');
include('assets/inc/checklogin.php');
check_login();

// We assume you've stored the logged-in doctor’s ID in the session:
$doc_id = $_SESSION['doc_id'];

// 1) Fetch the doctor's info from the new 'doctor' table (optional if you want to display doc's details):
$query = "SELECT first_name, last_name, email FROM doctor WHERE id = ?";
$stmt  = $mysqli->prepare($query);
$stmt->bind_param('i', $doc_id);
$stmt->execute();
$result = $stmt->get_result();
$doc    = $result->fetch_object();

?>
<!DOCTYPE html>
<html lang="en">
<?php include("assets/inc/head.php"); ?>

<body>
    <!-- Begin page -->
    <div id="wrapper">
        <!-- Topbar Start -->
        <?php include('assets/inc/nav.php'); ?>
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
                                <h4 class="page-title">Digital Health Card</h4>
                            </div>
                        </div>
                    </div>
                    <!-- end page title -->

                    <!-- Optionally display the Doctor's name or email here -->
                    <!-- Example: -->
                    <?php if (!empty($doc)): ?>
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    Logged in as
                                    <strong>
                                        <?php
                                        echo htmlspecialchars($doc->first_name . ' ' . $doc->last_name);
                                        ?>
                                    </strong>
                                    (<?php echo htmlspecialchars($doc->email); ?>)
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="row">
                        <!--Start Patients-->
                        <div class="col-md-6 col-xl-4">
                            <div class="widget-rounded-circle card-box">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="avatar-lg rounded-circle bg-soft-danger border-danger border">
                                            <i class="fab fa-accessible-icon font-22 avatar-title text-danger"></i>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-right">
                                            <?php
                                            // Count total patients
                                            $result = "SELECT COUNT(*) FROM patient";
                                            $stmt   = $mysqli->prepare($result);
                                            $stmt->execute();
                                            $stmt->bind_result($patient);
                                            $stmt->fetch();
                                            $stmt->close();
                                            ?>
                                            <h3 class="text-dark mt-1">
                                                <span data-plugin="counterup"><?php echo $patient; ?></span>
                                            </h3>
                                            <p class="text-muted mb-1 text-truncate">Patients</p>
                                        </div>
                                    </div>
                                </div> <!-- end row-->
                            </div> <!-- end widget-rounded-circle-->
                        </div> <!-- end col-->

                        <!-- My Profile widget -->
                        <div class="col-md-6 col-xl-6">
                            <a href="his_doc_account.php">
                                <div class="widget-rounded-circle card-box">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="avatar-lg rounded-circle bg-soft-danger border-danger border">
                                                <i class="fas fa-user-tag font-22 avatar-title text-danger"></i>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-right">
                                                <!-- You can display the doc name here if you like -->
                                                <h3 class="text-dark mt-1">
                                                    <!-- Example: 
                                                         echo htmlspecialchars($doc->first_name);
                                                    -->
                                                </h3>
                                                <p class="text-muted mb-1 text-truncate">My Profile</p>
                                            </div>
                                        </div>
                                    </div> <!-- end row-->
                                </div>
                            </a> <!-- end widget-rounded-circle-->
                        </div>
                        <!-- end col-->
                    </div>
                    <!-- end row -->

                    <!-- List of Patients -->
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="card-box">
                                <h4 class="header-title mb-3">Patients</h4>
                                <div class="table-responsive">
                                    <table class="table table-borderless table-hover table-centered m-0">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Name</th>
                                                <th>Address</th>
                                                <th>Mobile Phone</th>
                                                <th>Emergency contact</th>
                                                <th>Blood type</th>
                                                <th>Date Of Birth</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <?php
                                        // Retrieve up to 100 random patients
                                        $ret = "SELECT * FROM patient ORDER BY RAND() LIMIT 100";
                                        $stmt = $mysqli->prepare($ret);
                                        $stmt->execute();
                                        $res  = $stmt->get_result();
                                        while ($row = $res->fetch_object()) {
                                        ?>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <?php echo htmlspecialchars($row->first_name . ' ' . $row->last_name); ?>
                                                    </td>
                                                    <td>
                                                        <?php echo htmlspecialchars($row->address); ?>
                                                    </td>
                                                    <td>
                                                        <?php echo htmlspecialchars($row->contact_information); ?>
                                                    </td>
                                                    <td>
                                                        <?php echo htmlspecialchars($row->emergency_contact_detail); ?>
                                                    </td>
                                                    <td>
                                                        <?php echo htmlspecialchars($row->blood_type); ?>
                                                    </td>
                                                    <td>
                                                        <?php echo htmlspecialchars($row->date_of_birth); ?>
                                                    </td>
                                                    <td>
                                                        <a href="his_doc_view_single_patient.php?patient_id=<?php echo $row->patient_id; ?>&&pat_phone=<?php echo htmlspecialchars($row->contact_information); ?>&&pat_name=<?php echo htmlspecialchars($row->first_name . '_' . $row->last_name); ?>"
                                                            class="btn btn-xs btn-success">
                                                            <i class="mdi mdi-eye"></i> View
                                                        </a>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        <?php } ?>
                                    </table>
                                </div> <!-- end table-responsive -->
                            </div> <!-- end card-box -->
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

    <!-- Right bar overlay (if used) -->
    <div class="rightbar-overlay"></div>

    <!-- Vendor js -->
    <script src="assets/js/vendor.min.js"></script>

    <!-- Plugins and App js (adjust as needed) -->
    <script src="assets/libs/flatpickr/flatpickr.min.js"></script>
    <script src="assets/libs/jquery-knob/jquery.knob.min.js"></script>
    <script src="assets/libs/jquery-sparkline/jquery.sparkline.min.js"></script>
    <script src="assets/js/pages/dashboard-1.init.js"></script>
    <script src="assets/js/app.min.js"></script>

</body>

</html>