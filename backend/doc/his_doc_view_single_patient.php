<?php
session_start();
include('assets/inc/config.php');
include('assets/inc/checklogin.php');
check_login();
$aid = $_SESSION['doc_id'];

$patient_id = filter_input(INPUT_GET, 'patient_id', FILTER_VALIDATE_INT);
$pat_phone  = filter_input(INPUT_GET, 'pat_phone', FILTER_SANITIZE_SPECIAL_CHARS);

if (!$patient_id || !$pat_phone) {
    die("Invalid patient ID or phone number.");
}


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

        // If you have a 'pat_date_joinded' column
        $mysqlDateTime = $row->pat_date_joinded;
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
                                        <li class="breadcrumb-item"><a href="his_admin_dashboard.php">Dashboard</a></li>
                                        <li class="breadcrumb-item"><a href="javascript: void(0);">Patients</a></li>
                                        <li class="breadcrumb-item active">View Patients</li>
                                    </ol>
                                </div>
                                <h4 class="page-title">
                                    <?php echo htmlspecialchars($row->first_name . ' ' . $row->last_name); ?>'s Profile
                                </h4>
                            </div>
                        </div>
                    </div>
                    <!-- End page title -->

                    <div class="row">
                        <!-- Patient Summary (Left Column) -->
                        <div class="col-lg-4 col-xl-4">
                            <div class="card-box text-center">
                                <!-- Patient Image -->
                                <div class="text-center mb-3">
                                    <?php
                                    // Assuming you have a column in the database for the patient's profile image
                                    $patient_image = !empty($row->profile_image) ? $row->profile_image : 'assets/images/users/patient.png';
                                    ?>
                                    <img src="<?php echo htmlspecialchars($patient_image); ?>"
                                        class="rounded-circle avatar-xxl img-thumbnail"
                                        alt="Patient Profile Image"
                                        style="width: 150px; height: 150px; object-fit: cover;">
                                </div>

                                <!-- Patient Details -->
                                <div class="text-left mt-3">
                                    <p class="text-muted mb-2 font-13">
                                        <strong>Full Name :</strong>
                                        <span class="ml-2">
                                            <?php echo htmlspecialchars($row->first_name . ' ' . $row->last_name); ?>
                                        </span>
                                    </p>
                                    <p class="text-muted mb-2 font-13">
                                        <strong>Mobile :</strong>
                                        <span class="ml-2">
                                            <?php echo htmlspecialchars($row->contact_information); ?>
                                        </span>
                                    </p>
                                    <p class="text-muted mb-2 font-13">
                                        <strong>Address :</strong>
                                        <span class="ml-2">
                                            <?php echo htmlspecialchars($row->address); ?>
                                        </span>
                                    </p>
                                    <p class="text-muted mb-2 font-13">
                                        <strong>Date Of Birth :</strong>
                                        <span class="ml-2">
                                            <?php echo htmlspecialchars($row->date_of_birth); ?>
                                        </span>
                                    </p>
                                    <hr>
                                    <!-- If you have a date joined column -->
                                    <?php if (!empty($mysqlDateTime)): ?>
                                        <p class="text-muted mb-2 font-13">
                                            <strong>Date Recorded :</strong>
                                            <span class="ml-2">
                                                <?php echo date("d/m/Y - h:i", strtotime($mysqlDateTime)); ?>
                                            </span>
                                        </p>
                                    <?php endif; ?>
                                    <hr>
                                </div>

                                <!-- Square Image -->
                                <!-- QR Code as a Downloadable Link -->
                                <div class="text-center mt-3">
                                    <?php
                                    // Assuming you have a column in the database for the patient's QR code image
                                    $qr_code_image = !empty($row->qr_code_image_path) ? $row->qr_code_image_path : 'assets/images/default_qr.png';
                                    ?>
                                    <a href="download_card.php?patient_id=<?php echo urlencode($patient_id); ?>" download>
                                        <img src="<?php echo htmlspecialchars($qr_code_image); ?>"
                                            class="img-fluid rounded"
                                            alt="QR Code"
                                            style="max-width: 30%; height: auto; border: 1px solid #ddd;">
                                    </a>
                                    <p class="mt-2">Click the QR code to download the patient card</p>
                                </div>

                            </div> <!-- end card-box -->
                        </div> <!-- end col -->
                        <!-- Patient Details (Right Column) -->
                        <div class="col-lg-8 col-xl-8">
                            <div class="card-box">
                                <ul class="nav nav-pills navtab-bg nav-justified">
                                    <li class="nav-item">
                                        <a href="#medical-history" data-toggle="tab"
                                            aria-expanded="false" class="nav-link active">
                                            Medical History
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#medications" data-toggle="tab"
                                            aria-expanded="true" class="nav-link">
                                            Medications
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#allergies" data-toggle="tab"
                                            aria-expanded="false" class="nav-link">
                                            Allergies
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#investigation" data-toggle="tab"
                                            aria-expanded="false" class="nav-link">
                                            Investigation
                                        </a>
                                    </li>
                                </ul>

                                <!-- Tab Content -->
                                <div class="tab-content">
                                    <!-- Medical History Tab -->
                                    <div class="tab-pane show active" id="medical-history">
                                        <ul class="list-unstyled timeline-sm">
                                            <?php
                                            $past_illnesses     = json_decode($row->past_illnesses, true);
                                            $surgeries          = json_decode($row->surgeries, true);
                                            $chronic_conditions = json_decode($row->chronic_conditions, true);
                                            $family_med_hist    = json_decode($row->family_medical_history, true);
                                            ?>
                                            <li class="timeline-sm-item">
                                                <h5 class="mt-0 mb-1">Past Illnesses</h5>
                                                <p class="text-muted mt-2">
                                                    <?php
                                                    echo !empty($past_illnesses)
                                                        ? implode(', ', $past_illnesses)
                                                        : 'No past illnesses recorded';
                                                    ?>
                                                </p>
                                            </li>
                                            <li class="timeline-sm-item">
                                                <h5 class="mt-0 mb-1">Surgeries</h5>
                                                <p class="text-muted mt-2">
                                                    <?php
                                                    echo !empty($surgeries)
                                                        ? implode(', ', $surgeries)
                                                        : 'No surgeries recorded';
                                                    ?>
                                                </p>
                                            </li>
                                            <li class="timeline-sm-item">
                                                <h5 class="mt-0 mb-1">Chronic Conditions</h5>
                                                <p class="text-muted mt-2">
                                                    <?php
                                                    echo !empty($chronic_conditions)
                                                        ? implode(', ', $chronic_conditions)
                                                        : 'No chronic conditions recorded';
                                                    ?>
                                                </p>
                                            </li>
                                            <li class="timeline-sm-item">
                                                <h5 class="mt-0 mb-1">Family Medical History</h5>
                                                <p class="text-muted mt-2">
                                                    <?php
                                                    echo !empty($family_med_hist)
                                                        ? implode(', ', $family_med_hist)
                                                        : 'No family medical history recorded';
                                                    ?>
                                                </p>
                                            </li>
                                        </ul>
                                    </div>
                                    <!-- end medical-history tab -->

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
                                                            $med_name = isset($medication['name'])
                                                                ? htmlspecialchars($medication['name'])
                                                                : "N/A";
                                                            $med_dose = isset($medication['dose'])
                                                                ? htmlspecialchars($medication['dose'])
                                                                : "N/A";

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
                                    </div>
                                    <!-- end medications tab -->

                                    <!-- Allergies Tab -->
                                    <div class="tab-pane" id="allergies">
                                        <ul class="list-unstyled timeline-sm">
                                            <?php
                                            $allergies = $row->allergies;
                                            if (!empty($allergies)) {
                                                echo "<li class='timeline-sm-item'>
                                                        <h5 class='mt-0 mb-1'>Allergy</h5>
                                                        <p class='text-muted mt-2'>"
                                                    . htmlspecialchars($allergies) .
                                                    "</p>
                                                      </li>";
                                            } else {
                                                echo "<li class='timeline-sm-item'>
                                                      <p class='text-muted'>No allergies recorded</p></li>";
                                            }
                                            ?>
                                        </ul>
                                    </div>
                                    <!-- end allergies tab -->

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
                                                    $filePath = 'uploads/' . $fileName;

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
                                    <!-- end investigation tab -->

                                </div> <!-- end tab-content -->
                            </div> <!-- end card-box -->
                        </div> <!-- end col -->
                    </div> <!-- end row -->
                </div> <!-- container -->
            </div> <!-- content -->

            <!-- Footer Start -->
            <?php include('assets/inc/footer.php'); ?>
            <!-- end Footer -->
        </div>
    </div>

    <!-- Vendor js -->
    <script src="assets/js/vendor.min.js"></script>

    <!-- App js -->
    <script src="assets/js/app.min.js"></script>

</body>

</html>