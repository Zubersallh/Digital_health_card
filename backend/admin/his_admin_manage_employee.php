<?php
session_start();
include('assets/inc/config.php');
include('assets/inc/checklogin.php');
check_login();
$aid = $_SESSION['ad_id'];

// Delete an employee if the delete parameter is set in the URL
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $deleteQuery = "DELETE FROM doctor WHERE id = ?";
    if ($stmt = $mysqli->prepare($deleteQuery)) {
        $stmt->bind_param('i', $id);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            $success = "Employee Deleted";
        } else {
            $err = "Try Again Later";
        }
        $stmt->close();
    } else {
        $err = "Database Error: Unable to prepare statement";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<?php include('assets/inc/head.php'); ?>

<body>

    <!-- Begin page -->
    <div id="wrapper">

        <!-- Topbar Start -->
        <?php include('assets/inc/nav.php'); ?>
        <!-- End Topbar -->

        <!-- ========== Left Sidebar Start ========== -->
        <?php include("assets/inc/sidebar.php"); ?>
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
                                        <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                                        <li class="breadcrumb-item"><a href="javascript: void(0);">Doctors</a></li>
                                        <li class="breadcrumb-item active">Manage Doctors</li>
                                    </ol>
                                </div>
                                <h4 class="page-title">Manage Employee Details</h4>
                            </div>
                        </div>
                    </div>
                    <!-- end page title -->

                    <!-- Display success or error messages -->
                    <?php if (isset($success)) { ?>
                        <div class="alert alert-success" role="alert">
                            <?php echo $success; ?>
                        </div>
                    <?php } ?>
                    <?php if (isset($err)) { ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo $err; ?>
                        </div>
                    <?php } ?>

                    <div class="row">
                        <div class="col-12">
                            <div class="card-box">
                                <div class="mb-2">
                                    <div class="row">
                                        <div class="col-12 text-sm-center form-inline">
                                            <div class="form-group">
                                                <input id="demo-foo-search" type="text" placeholder="Search" class="form-control form-control-sm" autocomplete="on">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table id="demo-foo-filtering" class="table table-bordered toggle-circle mb-0" data-page-size="7">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th data-toggle="true">Name</th>
                                                <th data-hide="phone">Email</th>
                                                <th data-hide="phone">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            // Retrieve all employees from the doctor table in a random order
                                            $ret = "SELECT * FROM doctor ORDER BY RAND()";
                                            $stmt = $mysqli->prepare($ret);
                                            $stmt->execute();
                                            $res = $stmt->get_result();
                                            $cnt = 1;
                                            while ($row = $res->fetch_object()) {
                                            ?>
                                                <tr>
                                                    <td><?php echo $cnt; ?></td>
                                                    <td><?php echo $row->first_name . " " . $row->last_name; ?></td>
                                                    <td><?php echo $row->email; ?></td>
                                                    <td>
                                                        <a href="his_admin_manage_employee.php?delete=<?php echo $row->id; ?>" class="badge badge-danger" onclick="return confirm('Are you sure you want to delete this employee?');">
                                                            <i class="mdi mdi-trash-can-outline"></i> Delete
                                                        </a>
                                                        <a href="his_admin_view_single_employee.php?ad_id=<?php echo $row->id; ?>" class="badge badge-success">
                                                            <i class="mdi mdi-eye"></i> View
                                                        </a>
                                                        <a href="his_admin_update_single_employee.php?ad_id=<?php echo $row->id; ?>" class="badge badge-primary">
                                                            <i class="mdi mdi-check-box-outline"></i> Update
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php
                                                $cnt++;
                                            }
                                            ?>
                                        </tbody>
                                        <tfoot>
                                            <tr class="active">
                                                <td colspan="4">
                                                    <div class="text-right">
                                                        <ul class="pagination pagination-rounded justify-content-end footable-pagination m-t-10 mb-0"></ul>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div> <!-- end .table-responsive-->
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

    <!-- Right bar overlay-->
    <div class="rightbar-overlay"></div>

    <!-- Vendor js -->
    <script src="assets/js/vendor.min.js"></script>

    <!-- Footable js -->
    <script src="assets/libs/footable/footable.all.min.js"></script>

    <!-- Init js -->
    <script src="assets/js/pages/foo-tables.init.js"></script>

    <!-- App js -->
    <script src="assets/js/app.min.js"></script>

</body>

</html>