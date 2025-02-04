<?php
session_start();
include('assets/inc/config.php'); // Ensure $mysqli is created in this file

$errorMessage = "";
// Removed the GET-based success message block

if (isset($_POST['add_patient'])) {
    // Collect patient data from the form
    $pat_fname              = $_POST['pat_fname'];
    $pat_lname              = $_POST['pat_lname'];
    $pat_dob                = date('Y-m-d', strtotime($_POST['pat_dob']));
    $pat_addr               = $_POST['pat_addr'];
    $pat_phone              = $_POST['pat_phone'];
    $pat_emer_con           = $_POST['pat_emer_con'];
    $blood_type             = $_POST['blood_type'];
    $past_illnesses         = isset($_POST['past_illnesses']) ? json_encode($_POST['past_illnesses']) : json_encode([]);
    $surgeries              = isset($_POST['surgeries']) ? json_encode($_POST['surgeries']) : json_encode([]);
    $chronic_conditions     = isset($_POST['chronic_conditions']) ? json_encode($_POST['chronic_conditions']) : json_encode([]);
    $family_medical_history = isset($_POST['family_medical_history']) ? json_encode($_POST['family_medical_history']) : json_encode([]);

    // Process medications array
    $medications = [];
    if (isset($_POST['medications'])) {
        foreach ($_POST['medications'] as $medication) {
            if (!empty($medication['name']) && !empty($medication['dose'])) {
                $medications[] = [
                    'name' => $medication['name'],
                    'dose' => $medication['dose']
                ];
            }
        }
    }
    $medications_json = json_encode($medications);

    $allergies = $_POST['allergies'];
    $password  = password_hash($_POST['patient_password'], PASSWORD_DEFAULT);

    // ------------------------------
    // 1) Check if phone number exists
    // ------------------------------
    $check_phone_sql = "SELECT contact_information FROM patient WHERE contact_information = ?";
    if ($check_stmt = $mysqli->prepare($check_phone_sql)) {
        $check_stmt->bind_param('s', $pat_phone);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            $errorMessage = "<div class='alert alert-danger'>Error: This phone number is already registered in the database. Please use a different contact number.</div>";
        }
        $check_stmt->close();
    } else {
        $errorMessage = "<div class='alert alert-danger'>Error: Could not prepare phone-check statement: " . htmlspecialchars($mysqli->error) . "</div>";
    }

    // Only proceed if there is no error message from the phone check
    if (empty($errorMessage)) {

        // ------------------------------
        // Multiple file upload handling
        // ------------------------------
        $investigationFileNames = [];
        if (isset($_FILES['investigations'])) {
            $allowedExts = array('jpg', 'jpeg', 'png', 'gif', 'pdf');

            for ($i = 0; $i < count($_FILES['investigations']['name']); $i++) {
                $filename = $_FILES['investigations']['name'][$i];
                $fileTmp  = $_FILES['investigations']['tmp_name'][$i];
                $error    = $_FILES['investigations']['error'][$i];

                if ($error === UPLOAD_ERR_OK && !empty($filename)) {
                    $fileExt = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                    if (in_array($fileExt, $allowedExts)) {
                        $newFileName = uniqid('investigation_', true) . '.' . $fileExt;
                        $uploadDir   = 'uploads/';
                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0777, true);
                        }
                        $destination = $uploadDir . $newFileName;
                        if (move_uploaded_file($fileTmp, $destination)) {
                            $investigationFileNames[] = $newFileName;
                        } else {
                            die("Error: Failed to move uploaded file $filename.");
                        }
                    } else {
                        die("Error: Invalid file type for $filename. Allowed: " . implode(', ', $allowedExts));
                    }
                }
            }
        }
        $investigationFilesJson = json_encode($investigationFileNames);

        //  Generate the QR code 
        require_once '../../phpqrcode-2010100721_1.1.4/phpqrcode/qrlib.php';
        $qr_code_generated_url = "http://192.168.1.7/HMS/backend/patient/his_pat_dashboard.php?pat_phone=" . $pat_phone;
        $path = './assets/qr_code_images/';
        $qrcode = $path . time() . ".png";
        QRcode::png($qr_code_generated_url, $qrcode, 'H', 4, 4);

        // -----------------------------
        // Prepare the INSERT statement
        // -----------------------------
        $query = "INSERT INTO patient 
            (first_name, last_name, date_of_birth, address, contact_information, emergency_contact_detail, 
             blood_type, past_illnesses, surgeries, chronic_conditions, family_medical_history, 
             medication, allergies, investigations, patient_password, qr_code_image_path) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        if ($stmt = $mysqli->prepare($query)) {
            $stmt->bind_param(
                'ssssssssssssssss',
                $pat_fname,
                $pat_lname,
                $pat_dob,
                $pat_addr,
                $pat_phone,
                $pat_emer_con,
                $blood_type,
                $past_illnesses,
                $surgeries,
                $chronic_conditions,
                $family_medical_history,
                $medications_json,
                $allergies,
                $investigationFilesJson,
                $password,
                $qrcode
            );

            if ($stmt->execute()) {
                // On success, redirect back to the same page without any success flag.
                header("Location: " . $_SERVER['PHP_SELF']);
                exit;
            } else {
                $errorMessage = "<div class='alert alert-danger'>Error executing insert query: " . htmlspecialchars($stmt->error) . "</div>";
            }
            $stmt->close();
        } else {
            $errorMessage = "<div class='alert alert-danger'>Error preparing insert statement: " . htmlspecialchars($mysqli->error) . "</div>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<?php include('assets/inc/head.php'); ?>

<body>
    <div id="wrapper">
        <?php include("assets/inc/nav.php"); ?>
        <?php include("assets/inc/sidebar.php"); ?>
        <div class="content-page">
            <div class="content">
                <div class="container-fluid">
                    <!-- Page Title -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box">
                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="his_admin_dashboard.php">Dashboard</a></li>
                                        <li class="breadcrumb-item"><a href="javascript: void(0);">Patients</a></li>
                                        <li class="breadcrumb-item active">Record Patient Details</li>
                                    </ol>
                                </div>
                                <h4 class="page-title">Record Patient Information</h4>
                            </div>
                        </div>
                    </div>

                    <!-- Display error messages only -->
                    <div class="row">
                        <div class="col-12">
                            <?php
                            if (!empty($errorMessage)) {
                                echo $errorMessage;
                            }
                            // Removed the success message display.
                            ?>
                        </div>
                    </div>

                    <!-- Form Row -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="header-title">Fill all fields</h4>
                                    <form method="post" enctype="multipart/form-data">
                                        <!-- Patient Name -->
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label class="col-form-label">First Name</label>
                                                <input type="text" name="pat_fname" required class="form-control" placeholder="Patient's First Name">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label class="col-form-label">Last Name</label>
                                                <input type="text" name="pat_lname" required class="form-control" placeholder="Patient's Last Name">
                                            </div>
                                        </div>

                                        <!-- DOB & Password -->
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label class="col-form-label">Date Of Birth</label>
                                                <input type="text" name="pat_dob" required class="form-control" placeholder="DD/MM/YYYY">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label class="col-form-label">Patient Password</label>
                                                <input type="text" name="patient_password" required class="form-control" placeholder="password">
                                            </div>
                                        </div>

                                        <!-- Address -->
                                        <div class="form-group">
                                            <label class="col-form-label">Address</label>
                                            <input type="text" name="pat_addr" required class="form-control" placeholder="Patient's Address">
                                        </div>

                                        <!-- Contact Info, Emergency Contact, Blood Type -->
                                        <div class="form-row">
                                            <div class="form-group col-md-4">
                                                <label class="col-form-label">Contact Information</label>
                                                <input type="text" name="pat_phone" required class="form-control" placeholder="Phone Number">
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label class="col-form-label">Emergency Contact</label>
                                                <input type="text" name="pat_emer_con" required class="form-control" placeholder="Emergency Contact Detail">
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label class="col-form-label">Blood Type</label>
                                                <input type="text" name="blood_type" required class="form-control" placeholder="e.g. O+, A-, B+">
                                            </div>
                                        </div>

                                        <!-- Past Illnesses -->
                                        <div class="form-group">
                                            <label class="col-form-label">Past Illnesses</label>
                                            <div id="past_illnesses_container">
                                                <input type="text" name="past_illnesses[]" class="form-control mb-2" placeholder="Enter past illness">
                                            </div>
                                            <button type="button" onclick="addField('past_illnesses_container', 'past_illnesses[]')" class="btn btn-secondary btn-sm">Add More</button>
                                        </div>

                                        <!-- Surgeries -->
                                        <div class="form-group">
                                            <label class="col-form-label">Surgeries</label>
                                            <div id="surgeries_container">
                                                <input type="text" name="surgeries[]" class="form-control mb-2" placeholder="Enter surgery details">
                                            </div>
                                            <button type="button" onclick="addField('surgeries_container', 'surgeries[]')" class="btn btn-secondary btn-sm">Add More</button>
                                        </div>

                                        <!-- Chronic Conditions -->
                                        <div class="form-group">
                                            <label class="col-form-label">Chronic Conditions</label>
                                            <div id="chronic_conditions_container">
                                                <input type="text" name="chronic_conditions[]" class="form-control mb-2" placeholder="Enter chronic condition">
                                            </div>
                                            <button type="button" onclick="addField('chronic_conditions_container', 'chronic_conditions[]')" class="btn btn-secondary btn-sm">Add More</button>
                                        </div>

                                        <!-- Family Medical History -->
                                        <div class="form-group">
                                            <label class="col-form-label">Family Medical History</label>
                                            <div id="family_medical_history_container">
                                                <input type="text" name="family_medical_history[]" class="form-control mb-2" placeholder="Enter family medical history">
                                            </div>
                                            <button type="button" onclick="addField('family_medical_history_container', 'family_medical_history[]')" class="btn btn-secondary btn-sm">Add More</button>
                                        </div>

                                        <!-- Medications -->
                                        <div class="form-group">
                                            <label class="col-form-label">Medications</label>
                                            <div id="medications_container">
                                                <div class="input-group mb-2">
                                                    <input type="text" name="medications[0][name]" class="form-control" placeholder="Medication Name">
                                                    <input type="text" name="medications[0][dose]" class="form-control" placeholder="Dose">
                                                </div>
                                            </div>
                                            <button type="button" onclick="addMedicationField()" class="btn btn-secondary btn-sm">Add More</button>
                                        </div>

                                        <!-- Allergies -->
                                        <div class="form-group">
                                            <label class="col-form-label">Allergies</label>
                                            <input type="text" name="allergies" class="form-control" placeholder="Enter allergies">
                                        </div>

                                        <!-- Investigations (Multiple File Upload) -->
                                        <div class="form-group">
                                            <label class="col-form-label">Investigations</label>
                                            <div id="investigations_container">
                                                <div class="input-group mb-2">
                                                    <input type="file" name="investigations[]" class="form-control">
                                                </div>
                                            </div>
                                            <button type="button" onclick="addInvestigationField()" class="btn btn-secondary btn-sm">Add More</button>
                                        </div>

                                        <!-- Submit Button -->
                                        <button type="submit" name="add_patient" class="ladda-button btn btn-primary" data-style="expand-right">
                                            Add The Record
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php include('assets/inc/footer.php'); ?>
        </div>
    </div>

    <!-- JavaScript for dynamic fields and delete functionality -->
    <script>
        function addField(containerId, fieldName) {
            var container = document.getElementById(containerId);
            var wrapper = document.createElement("div");
            wrapper.className = "input-group mb-2";
            var input = document.createElement("input");
            input.type = "text";
            input.name = fieldName;
            input.className = "form-control";
            input.placeholder = "Enter more details";
            var deleteButton = document.createElement("button");
            deleteButton.type = "button";
            deleteButton.className = "btn btn-danger";
            deleteButton.innerHTML = "Delete";
            deleteButton.onclick = function() {
                container.removeChild(wrapper);
            };
            wrapper.appendChild(input);
            wrapper.appendChild(deleteButton);
            container.appendChild(wrapper);
        }

        let medicationIndex = 1;

        function addMedicationField() {
            const container = document.getElementById('medications_container');
            const wrapper = document.createElement('div');
            wrapper.classList.add('input-group', 'mb-2');
            const nameInput = document.createElement('input');
            nameInput.type = "text";
            nameInput.name = `medications[${medicationIndex}][name]`;
            nameInput.className = "form-control";
            nameInput.placeholder = "Medication Name";
            const doseInput = document.createElement('input');
            doseInput.type = "text";
            doseInput.name = `medications[${medicationIndex}][dose]`;
            doseInput.className = "form-control";
            doseInput.placeholder = "Dose";
            const deleteButton = document.createElement('button');
            deleteButton.type = "button";
            deleteButton.className = "btn btn-danger";
            deleteButton.innerHTML = "Delete";
            deleteButton.onclick = function() {
                container.removeChild(wrapper);
            };
            wrapper.appendChild(nameInput);
            wrapper.appendChild(doseInput);
            wrapper.appendChild(deleteButton);
            container.appendChild(wrapper);
            medicationIndex++;
        }

        function addInvestigationField() {
            const container = document.getElementById('investigations_container');
            const wrapper = document.createElement('div');
            wrapper.className = "input-group mb-2";
            const fileInput = document.createElement('input');
            fileInput.type = 'file';
            fileInput.name = 'investigations[]';
            fileInput.className = 'form-control';
            const deleteButton = document.createElement('button');
            deleteButton.type = 'button';
            deleteButton.className = 'btn btn-danger';
            deleteButton.innerText = 'Delete';
            deleteButton.onclick = function() {
                container.removeChild(wrapper);
            };
            wrapper.appendChild(fileInput);
            wrapper.appendChild(deleteButton);
            container.appendChild(wrapper);
        }
    </script>
</body>

</html>