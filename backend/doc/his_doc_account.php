<?php
session_start();
include('assets/inc/config.php');
include('assets/inc/checklogin.php');
check_login();

// We'll use doc_id from the session, referencing the 'id' column in the 'doctor' table
$doc_id = $_SESSION['doc_id'];

// --------------------------------------------
// Handle the form submission for profile update
// --------------------------------------------
if (isset($_POST['update_profile'])) {
    // Collect and sanitize form inputs
    $first_name = htmlspecialchars($_POST['first_name']);
    $last_name  = htmlspecialchars($_POST['last_name']);
    $email      = htmlspecialchars($_POST['email']);

    // Update the database (no profile picture involved)
    $update = "UPDATE doctor 
               SET first_name = ?, 
                   last_name  = ?, 
                   email      = ?
               WHERE id = ?";
    $stmt = $mysqli->prepare($update);
    $stmt->bind_param('sssi', $first_name, $last_name, $email, $doc_id);

    if ($stmt->execute()) {
        $success = "Profile updated successfully!";
    } else {
        $err = "Error updating profile: " . $stmt->error;
    }
}

// --------------------------------------------
// Fetch the latest doctor info for display
// --------------------------------------------
$query = "SELECT first_name, last_name, email 
          FROM doctor 
          WHERE id = ?";
$stmt  = $mysqli->prepare($query);
$stmt->bind_param('i', $doc_id);
$stmt->execute();
$res   = $stmt->get_result();
$doc   = $res->fetch_object(); // We expect exactly one row
?>
<!DOCTYPE html>
<html lang="en">
<?php include('assets/inc/head.php'); ?>

<body>
    <!-- Begin page -->
    <div id="wrapper">
        <!-- Topbar Start -->
        <?php include("assets/inc/nav.php"); ?>
        <!-- end Topbar -->

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
                                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                                        <li class="breadcrumb-item"><a href="#">Profile</a></li>
                                        <li class="breadcrumb-item active">View / Edit My Profile</li>
                                    </ol>
                                </div>
                                <h4 class="page-title">
                                    <?php
                                    if ($doc) {
                                        echo htmlspecialchars($doc->first_name) . ' ' . htmlspecialchars($doc->last_name);
                                    } else {
                                        echo "Doctor Not Found";
                                    }
                                    ?>'s Profile
                                </h4>
                            </div>
                        </div>
                    </div>
                    <!-- end page title -->

                    <!-- Display success / error messages -->
                    <?php if (isset($success)): ?>
                        <div class="alert alert-success" role="alert">
                            <?php echo $success; ?>
                        </div>
                    <?php elseif (isset($err)): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo $err; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($doc): ?>
                        <div class="row">
                            <!-- Left column: Display current profile -->
                            <div class="col-lg-4 col-xl-4">
                                <div class="card-box text-center">
                                    <!-- Show current name and email -->
                                    <h5 class="mb-0">
                                        <?php echo htmlspecialchars($doc->first_name) . ' ' . htmlspecialchars($doc->last_name); ?>
                                    </h5>
                                    <p class="text-muted">
                                        <?php echo htmlspecialchars($doc->email); ?>
                                    </p>
                                </div> <!-- end card-box -->
                            </div> <!-- end col -->

                            <!-- Right column: Edit form -->
                            <div class="col-lg-8 col-xl-8">
                                <div class="card-box">
                                    <h4 class="header-title mb-3">Edit Profile</h4>
                                    <form method="post">
                                        <div class="form-group mb-3">
                                            <label for="first_name">First Name</label>
                                            <input type="text" name="first_name"
                                                class="form-control"
                                                value="<?php echo htmlspecialchars($doc->first_name); ?>"
                                                required>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="last_name">Last Name</label>
                                            <input type="text" name="last_name"
                                                class="form-control"
                                                value="<?php echo htmlspecialchars($doc->last_name); ?>"
                                                required>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="email">Email</label>
                                            <input type="email" name="email"
                                                class="form-control"
                                                value="<?php echo htmlspecialchars($doc->email); ?>"
                                                required>
                                        </div>
                                        <!-- No file upload here -->
                                        <button type="submit" name="update_profile"
                                            class="btn btn-primary">
                                            Update Profile
                                        </button>
                                    </form>
                                </div> <!-- end card-box -->
                            </div> <!-- end col -->
                        </div>
                        <!-- end row -->
                    <?php else: ?>
                        <div class="alert alert-danger">
                            Unable to find doctor record. Please check your database.
                        </div>
                    <?php endif; ?>

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
    <!-- App js -->
    <script src="assets/js/app.min.js"></script>
</body>

</html>