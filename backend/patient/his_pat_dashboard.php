<?php
session_start();
include('../doc/assets/inc/config.php');
include('../doc/assets/inc/checklogin.php');
check_login();
//if the user is log in with login form his credianality
        if (isset($_SESSION['pat_id']) && isset($_SESSION['pat_number'])){
            $patient_id = $_SESSION['pat_id'];
            $pat_phone = $_SESSION['pat_number'];
        }
        //iif the user log in with qrcode
        else {


                $pat_phone = filter_input(INPUT_GET, 'pat_phone', FILTER_SANITIZE_SPECIAL_CHARS);

            // Check if the input is valid
            if ($pat_phone) {
            
                $sql = "SELECT patient_id FROM patient WHERE contact_information = ?";
                $stmt = $mysqli->prepare($sql);

                if ($stmt) {    

                    $stmt->bind_param("s", $pat_phone);

                    $stmt->execute();
                    $stmt->bind_result($patient_id);

                    // Fetch the result
                    if ($stmt->fetch()) {
                    $patient_id =$patient_id;
                    } else {
                        echo "No patient found with the provided phone number.";
                    }

                
                    $stmt->close();
                } else {
                    echo "Failed to prepare the SQL statement.";
                }
            } else {
                echo "Invalid phone number provided.";
            }
        }
?>



<!DOCTYPE html>
<html lang="en">

<head>
        <meta charset="utf-8" />
        <title>Digital Health Card</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description" />
        <meta content="MartDevelopers" name="author" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <!-- App favicon -->
        <link rel="shortcut icon" href="assets/images/favicon.ico">

        <!-- Plugins css -->
        <link href="../doc/assets/libs/flatpickr/flatpickr.min.css" rel="stylesheet" type="text/css" />

        <!-- App css -->
        <link href="../doc/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="../doc/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
        <link href="../doc/assets/css/app.min.css" rel="stylesheet" type="text/css" />
         <!-- Loading button css -->
         <link href="../doc/assets/libs/ladda/ladda-themeless.min.css" rel="stylesheet" type="text/css" />

        <!-- Footable css -->
        <link href="../doc/assets/libs/footable/footable.core.min.css" rel="stylesheet" type="text/css" />

       <!--Load Sweet Alert Javascript-->
       <script src="../doc/assets/js/swal.js"></script>
       
        <!--Inject SWAL-->
        <?php if(isset($success)) {?>
        <!--This code for injecting an alert-->
                <script>
                            setTimeout(function () 
                            { 
                                swal("Success","<?php echo $success;?>","success");
                            },
                                100);
                </script>

        <?php } ?>

        <?php if(isset($err)) {?>
        <!--This code for injecting an alert-->
                <script>
                            setTimeout(function () 
                            { 
                                swal("Failed","<?php echo $err;?>","Failed");
                            },
                                100);
                </script>

        <?php } ?>

</head>

<body>

    <!-- Begin page -->
    <div id="wrapper">

        <!-- Topbar Start -->
        <!-- </?php include("../doc/assets/inc/nav.php"); ?> -->
        <!-- end Topbar -->

        <!-- ========== Left Sidebar Start ========== -->
        <div class="left-side-menu">

<div class="slimscroll-menu">

    <!--- Sidemenu -->
    <div id="sidebar-menu">

        <ul class="metismenu" id="side-menu">
 
             </li>
                    <?php
                // Fetch patient details
                $ret1 = "SELECT patient_id FROM patient WHERE patient_id = ?";
                $stmt1 = $mysqli->prepare($ret1);
                $stmt1->bind_param('i', $patient_id);
                $stmt1->execute();
                $res1 = $stmt1->get_result();

                if ($res1->num_rows === 0) {
                    die("Patient not found.");
                }

                $row1 = $res1->fetch_object();
                ?>
                        <div class="alert alert-info text-center" role="alert">
                            <i class="fas fa-user-md fa-2x text-primary"></i>
                            <h5 class="mt-2">Are you a doctor?</h5>
                            <p class="mb-3">Login to modify patient records.</p>
                            <a href="../doc/index.php?patient_id=<?php echo $row1->patient_id;?>">  <i class="fas fa-sign-in-alt"></i> Login as Doctor</a>
                    </div>

                <a href="his_pat_logout.php">
                    <i class="fe-airplay"></i>
                    <span> Logout</span>
                </a>

            </li>

        </ul>

    </div>
    <!-- End Sidebar -->

    <div class="clearfix"></div>

</div>
<!-- Sidebar -left -->

</div>
        <!-- Left Sidebar End -->

        <!-- ============================================================== -->
        <!-- Start Page Content here -->
        <!-- ============================================================== -->

        <!-- Get Details Of A Single User And Display Them Here -->
        <?php
        // Fetch patient details
        $ret = "SELECT * FROM patient WHERE patient_id = ?";
        $stmt = $mysqli->prepare($ret);
        $stmt->bind_param('i', $patient_id);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows === 0) {
            die("Patient not found.");
        }

        $row = $res->fetch_object();
        // Assign the date recorded from the new column
        $mysqlDateTime = $row->pat_date_joined;
        ?>

        <div class="content-page">
            <div class="content">

                <!-- Start Content-->
                <div class="container-fluid">

                    <!-- Start page title -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box">
                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        
                                        <li class="breadcrumb-item"><a href="javascript: void(0);">Patients</a></li>
                                        <li class="breadcrumb-item active">Profiles</li>
                                    </ol>
                                </div>
                                <h4 class="page-title"><?php echo htmlspecialchars($row->first_name . ' ' . $row->last_name); ?>'s Profile</h4>
                            </div>
                        </div>
                    </div>
                    <!-- End page title -->

                    <div class="row">
                        <div class="col-lg-4 col-xl-4">
                            <div class="card-box text-center">
                                <img src="../doc/assets/images/users/patient.png" class="rounded-circle avatar-lg img-thumbnail" alt="profile-image">

                                <div class="text-left mt-3">
                                    <p class="text-muted mb-2 font-13">
                                        <strong>Full Name :</strong> <span class="ml-2"><?php echo htmlspecialchars($row->first_name . ' ' . $row->last_name); ?></span>
                                    </p>
                                    <p class="text-muted mb-2 font-13">
                                        <strong>Mobile :</strong><span class="ml-2"><?php echo htmlspecialchars($row->contact_information); ?></span>
                                    </p>
                                    <p class="text-muted mb-2 font-13">
                                        <strong>Address :</strong> <span class="ml-2"><?php echo htmlspecialchars($row->address); ?></span>
                                    </p>
                                    <p class="text-muted mb-2 font-13">
                                        <strong>Date Of Birth :</strong> <span class="ml-2"><?php echo htmlspecialchars($row->date_of_birth); ?></span>
                                    </p>
                                    <hr>
                                    <p class="text-muted mb-2 font-13">
                                        <strong>Date Recorded :</strong> <span class="ml-2"><?php echo date("d/m/Y - h:i", strtotime($mysqlDateTime)); ?></span>
                                    </p>
                                    <hr>
                                </div>
                            </div> <!-- end card-box -->
                        </div> <!-- end col -->

                        <div class="col-lg-8 col-xl-8">
                            <div class="card-box">
                                <ul class="nav nav-pills navtab-bg nav-justified">
                                    <li class="nav-item">
                                        <a href="#medical-history" data-toggle="tab" aria-expanded="false" class="nav-link active">Medical History</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#medications" data-toggle="tab" aria-expanded="true" class="nav-link">Medications</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#allergies" data-toggle="tab" aria-expanded="false" class="nav-link">Allergies</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#investigation" data-toggle="tab" aria-expanded="false" class="nav-link">Investigation</a>
                                    </li>
                                </ul>

                                <!-- Tab Content -->
                                <div class="tab-content">
                                    <!-- Medical History Tab -->
                                    <div class="tab-pane show active" id="medical-history">
                                        <ul class="list-unstyled timeline-sm">
                                            <?php
                                            $past_illnesses = json_decode($row->past_illnesses, true);
                                            $surgeries = json_decode($row->surgeries, true);
                                            $chronic_conditions = json_decode($row->chronic_conditions, true);
                                            $family_medical_history = json_decode($row->family_medical_history, true);
                                            ?>
                                            <li class="timeline-sm-item">
                                                <h5 class="mt-0 mb-1">Past Illnesses</h5>
                                                <p class="text-muted mt-2"><?php echo !empty($past_illnesses) ? implode(', ', $past_illnesses) : 'No past illnesses recorded'; ?></p>
                                            </li>
                                            <li class="timeline-sm-item">
                                                <h5 class="mt-0 mb-1">Surgeries</h5>
                                                <p class="text-muted mt-2"><?php echo !empty($surgeries) ? implode(', ', $surgeries) : 'No surgeries recorded'; ?></p>
                                            </li>
                                            <li class="timeline-sm-item">
                                                <h5 class="mt-0 mb-1">Chronic Conditions</h5>
                                                <p class="text-muted mt-2"><?php echo !empty($chronic_conditions) ? implode(', ', $chronic_conditions) : 'No chronic conditions recorded'; ?></p>
                                            </li>
                                            <li class="timeline-sm-item">
                                                <h5 class="mt-0 mb-1">Family Medical History</h5>
                                                <p class="text-muted mt-2"><?php echo !empty($family_medical_history) ? implode(', ', $family_medical_history) : 'No family medical history recorded'; ?></p>
                                            </li>
                                        </ul>
                                    </div> <!-- end medical-history tab -->

                                    <!-- Medications Tab -->
                                    <div class="tab-pane" id="medications">
                                        <div class="table-responsive">
                                            <table class="table table-borderless mb-0">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th>Medication Name</th>
                                                        <th>Dosage</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    // Decode the medication JSON string
                                                    $medications = json_decode($row->medication, true);

                                                    // Check if JSON decoding was successful
                                                    if (json_last_error() !== JSON_ERROR_NONE) {
                                                        error_log("JSON decoding error: " . json_last_error_msg());
                                                        echo "<tr><td colspan='2' class='text-danger'>Error decoding medication data</td></tr>";
                                                    } elseif (is_array($medications) && !empty($medications)) {
                                                        foreach ($medications as $medication) {
                                                            $med_name = isset($medication['name']) ? htmlspecialchars($medication['name']) : "N/A";
                                                            $med_dose = isset($medication['dose']) ? htmlspecialchars($medication['dose']) : "N/A";

                                                            echo "<tr>
                                                                    <td>{$med_name}</td>
                                                                    <td>{$med_dose}</td>
                                                                  </tr>";
                                                        }
                                                    } else {
                                                        echo "<tr><td colspan='2' class='text-muted'>No medications recorded</td></tr>";
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div> <!-- end medications tab -->

                                    <!-- Allergies Tab -->
                                    <div class="tab-pane" id="allergies">
                                        <ul class="list-unstyled timeline-sm">
                                            <?php
                                            $allergies = $row->allergies;
                                            if (!empty($allergies)) {
                                                echo "<li class='timeline-sm-item'>
                                                        <h5 class='mt-0 mb-1'>Allergy</h5>
                                                        <p class='text-muted mt-2'>" . htmlspecialchars($allergies) . "</p>
                                                      </li>";
                                            } else {
                                                echo "<li class='timeline-sm-item'><p class='text-muted'>No allergies recorded</p></li>";
                                            }
                                            ?>
                                        </ul>
                                    </div> <!-- end allergies tab -->

                                    <!-- Investigation Tab (Multiple Files) -->
                                    <div class="tab-pane" id="investigation">
                                        <ul class="list-unstyled timeline-sm">
                                            <?php
                                            // Decode the JSON array of investigation filenames
                                            $investigationFiles = !is_null($row->investigations)
                                                ? json_decode($row->investigations, true)
                                                : [];
                                            $investigationFiles = is_array($investigationFiles)
                                                ? $investigationFiles
                                                : [];

                                            if (!empty($investigationFiles)) {
                                                echo "<h5 class='mt-0 mb-1'>Investigation Files</h5><hr>";
                                                foreach ($investigationFiles as $fileName) {
                                                    $fileName = trim($fileName);
                                                    $filePath = '../doc/uploads/' . $fileName;

                                                    if (file_exists($filePath)) {
                                                        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                                                        // Display differently based on file type
                                                        if (in_array($fileExt, ['jpg', 'jpeg', 'png', 'gif'])) {
                                                            echo "<p><strong>File:</strong> {$fileName}</p>";
                                                            // Wrap the <img> in an <a> so clicking the image opens in a new tab
                                                            echo "<a href='{$filePath}' target='_blank'>
                                                                    <img src='{$filePath}' alt='Investigation Image' 
                                                                         class='img-fluid mb-3'
                                                                         style='max-width: 100%; height: auto;'>
                                                                  </a>";
                                                            echo "<hr>";
                                                        } elseif ($fileExt === 'pdf') {
                                                            // PDF link
                                                            echo "<p><strong>File:</strong> {$fileName}</p>";
                                                            echo "<p><a href='{$filePath}' target='_blank'>Open PDF</a></p>";
                                                            echo "<hr>";
                                                        } else {
                                                            // Other file type
                                                            echo "<p><strong>File:</strong> {$fileName} 
                                                                  <a href='{$filePath}' download>Download</a></p>";
                                                            echo "<hr>";
                                                        }
                                                    } else {
                                                        echo "<p class='text-danger'>File not found: {$fileName}</p><hr>";
                                                    }
                                                }
                                            } else {
                                                echo "<li class='timeline-sm-item'>
                                                      <p class='text-muted'>No investigation record found.</p>
                                                      </li>";
                                            }
                                            ?>
                                        </ul>
                                    </div>

                                </div> <!-- end tab-content -->
                            </div> <!-- end card-box -->
                        </div> <!-- end col -->
                    </div> <!-- end row -->
                </div> <!-- container -->
            </div> <!-- content -->

            <!-- Footer Start -->
            <?php include('../doc/assets/inc/footer.php'); ?>
            <!-- end Footer -->
        </div>
    </div>

    <!-- Vendor js -->
    <script src="../doc/assets/js/vendor.min.js"></script>

    <!-- App js -->
    <script src="../doc/assets/js/app.min.js"></script>
</body>
</html>
