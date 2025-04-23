<?php
session_start();
include('assets/inc/config.php');

// Fetch patient details
$pat_id = $_GET['pat_id'] ?? null;
if (!$pat_id) die("Invalid patient ID");

$stmt = $mysqli->prepare("SELECT * FROM patient WHERE patient_id = ?");
$stmt->bind_param('i', $pat_id);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_object();

if (!$row) die("Patient not found");

// Initialize variables from database
$fname                 = $row->first_name;
$lname                 = $row->last_name;
$dob                   = $row->date_of_birth;
$address               = $row->address;
// $mobile                = $row->contact_information;
// $emer_con              = $row->emergency_contact_detail;
$blood_type            = $row->blood_type;
$allergies             = $row->allergies;

// Decode `investigations` as a JSON array of filenames (or fallback to an empty array)
$investigationFileNames = json_decode($row->investigations ?? '[]', true);
if (!is_array($investigationFileNames)) {
    $investigationFileNames = [];
}

// Decode remaining JSON fields safely
$past_illnesses         = json_decode($row->past_illnesses         ?? '[]', true) ?? [];
$surgeries              = json_decode($row->surgeries              ?? '[]', true) ?? [];
$chronic_conditions     = json_decode($row->chronic_conditions     ?? '[]', true) ?? [];
$family_medical_history = json_decode($row->family_medical_history ?? '[]', true) ?? [];
$medications            = json_decode($row->medication             ?? '[]', true) ?? [];

// Ensure arrays
$past_illnesses         = is_array($past_illnesses)         ? $past_illnesses         : [];
$surgeries              = is_array($surgeries)              ? $surgeries              : [];
$chronic_conditions     = is_array($chronic_conditions)     ? $chronic_conditions     : [];
$family_medical_history = is_array($family_medical_history) ? $family_medical_history : [];
$medications            = is_array($medications)            ? $medications            : [];

// Handle form submission
if (isset($_POST['update_patient'])) {

    // 1) Collect form data
    $pat_fname    = $_POST['pat_fname'];
    $pat_lname    = $_POST['pat_lname'];
    $pat_dob      = $_POST['pat_dob'];
    $pat_addr     = $_POST['pat_addr'];
    // $pat_phone    = $_POST['pat_phone'];
    // $pat_emer_con = $_POST['pat_emer_con'];
    $blood_type   = $_POST['blood_type'];
    $allergies    = $_POST['allergies'];

    // 2) Encode JSON fields
    $past_illnesses2         = json_encode($_POST['past_illnesses']         ?? []);
    $surgeries2              = json_encode($_POST['surgeries']              ?? []);
    $chronic_conditions2     = json_encode($_POST['chronic_conditions']     ?? []);
    $family_medical_history2 = json_encode($_POST['family_medical_history'] ?? []);

    // 3) Medications
    $medications2 = [];
    if (!empty($_POST['medications'])) {
        foreach ($_POST['medications'] as $med) {
            if (!empty($med['name']) || !empty($med['dose'])) {
                $medications2[] = [
                    'name' => $med['name'],
                    'dose' => $med['dose']
                ];
            }
        }
    }
    $medications_json2 = json_encode($medications2);

    // 4) Deletion of existing files
    if (!empty($_POST['delete_investigation']) && is_array($_POST['delete_investigation'])) {
        foreach ($_POST['delete_investigation'] as $filenameToDelete) {
            if (in_array($filenameToDelete, $investigationFileNames)) {
                $filePath = "uploads/$filenameToDelete";
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
                // Remove from array
                $investigationFileNames = array_values(
                    array_diff($investigationFileNames, [$filenameToDelete])
                );
            }
        }
    }

    // 5) Handle new file uploads from dynamically added file inputs
    //    Each new file input is: name="investigation[]"
    //    So we'll loop over `$_FILES['investigation']['tmp_name']`
    if (!empty($_FILES['investigation']['name'])) {
        // Check each uploaded file
        foreach ($_FILES['investigation']['tmp_name'] as $index => $tmpName) {
            // Skip empty fields
            if ($_FILES['investigation']['error'][$index] !== UPLOAD_ERR_OK) {
                continue;
            }

            $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
            $fileExt = strtolower(pathinfo($_FILES['investigation']['name'][$index], PATHINFO_EXTENSION));

            if (in_array($fileExt, $allowedExts)) {
                $newFileName = uniqid('investigation_', true) . '.' . $fileExt;
                $destination = "uploads/$newFileName";

                if (!is_dir('uploads')) {
                    mkdir('uploads', 0777, true);
                }
                if (move_uploaded_file($tmpName, $destination)) {
                    // Add this file's name to our list
                    $investigationFileNames[] = $newFileName;
                }
            }
        }
    }

    // 6) Re-encode updated file name array into DB
    $investigations_json = json_encode($investigationFileNames);

    // 7) Update query
    $query = "UPDATE patient SET 
                first_name = ?,
                last_name = ?,
                date_of_birth = ?,
                address = ?,
                -- contact_information = ?,
                -- emergency_contact_detail = ?,
                blood_type = ?,
                past_illnesses = ?,
                surgeries = ?,
                chronic_conditions = ?,
                family_medical_history = ?,
                medication = ?,
                allergies = ?,
                investigations = ?
              WHERE patient_id = ?";

    $stmt = $mysqli->prepare($query);
    $stmt->bind_param(
        'ssssssssssssi',
        $pat_fname,
        $pat_lname,
        $pat_dob,
        $pat_addr,
        // $pat_phone,
        // $pat_emer_con,
        $blood_type,
        $past_illnesses2,
        $surgeries2,
        $chronic_conditions2,
        $family_medical_history2,
        $medications_json2,
        $allergies,
        $investigations_json,
        $pat_id
    );

    if ($stmt->execute()) {
        $success = "Patient Details Updated Successfully";
    } else {
        $err = "Error: " . $stmt->error;
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
                                        <li class="breadcrumb-item"><a href="javascript:void(0);">Patients</a></li>
                                        <li class="breadcrumb-item active">Update Patient Details</li>
                                    </ol>
                                </div>
                                <h4 class="page-title">Update Patient Information</h4>
                            </div>
                        </div>
                    </div>

                    <!-- Alerts -->
                    <?php if (isset($success)): ?>
                        <div class="alert alert-success" role="alert">
                            <?= htmlspecialchars($success) ?>
                        </div>
                    <?php elseif (isset($err)): ?>
                        <div class="alert alert-danger" role="alert">
                            <?= htmlspecialchars($err) ?>
                        </div>
                    <?php endif; ?>

                    <!-- Main Form -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="header-title">Fill all fields</h4>
                                    <form method="post" enctype="multipart/form-data">
                                        <!-- Basic Info -->
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label>First Name</label>
                                                <input type="text" name="pat_fname" class="form-control"
                                                    value="<?= htmlspecialchars($fname) ?>" required>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>Last Name</label>
                                                <input type="text" name="pat_lname" class="form-control"
                                                    value="<?= htmlspecialchars($lname) ?>" required>
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label>Date of Birth</label>
                                                <input type="text" name="pat_dob" class="form-control"
                                                    value="<?= htmlspecialchars($dob) ?>" required>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>Address</label>
                                                <input type="text" name="pat_addr" class="form-control"
                                                    value="<?= htmlspecialchars($address) ?>" required>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-4">
                                                <label>Blood Type</label>
                                                <input type="text" name="blood_type" class="form-control"
                                                    value="<?= htmlspecialchars($blood_type) ?>" required>
                                            </div>
                                        <!-- <div class="form-row">
                                            <div class="form-group col-md-4">
                                                <label>Mobile Number</label>
                                                <input type="text" name="pat_phone" class="form-control"
                                                    value="</?= htmlspecialchars($mobile) ?>" required>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label>Emergency Contact</label>
                                                <input type="text" name="pat_emer_con" class="form-control"
                                                    value="</?= htmlspecialchars($emer_con) ?>" required>
                                            </div>
                                           
                                        </div> -->

                                        <!-- Past Illnesses -->
                                        <div class="form-group">
                                            <label>Past Illnesses</label>
                                            <div id="past_illnesses_container">
                                                <?php foreach ($past_illnesses as $illness): ?>
                                                    <div class="input-group mb-2">
                                                        <input type="text"
                                                            name="past_illnesses[]"
                                                            class="form-control"
                                                            value="<?= htmlspecialchars($illness) ?>">
                                                        <button type="button"
                                                            class="btn btn-danger"
                                                            onclick="removeField(this)">
                                                            Delete
                                                        </button>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                            <button type="button"
                                                onclick="addField('past_illnesses_container','past_illnesses[]')"
                                                class="btn btn-secondary btn-sm">
                                                Add More
                                            </button>
                                        </div>

                                        <!-- Surgeries -->
                                        <div class="form-group">
                                            <label>Surgeries</label>
                                            <div id="surgeries_container">
                                                <?php foreach ($surgeries as $surgery): ?>
                                                    <div class="input-group mb-2">
                                                        <input type="text"
                                                            name="surgeries[]"
                                                            class="form-control"
                                                            value="<?= htmlspecialchars($surgery) ?>">
                                                        <button type="button"
                                                            class="btn btn-danger"
                                                            onclick="removeField(this)">
                                                            Delete
                                                        </button>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                            <button type="button"
                                                onclick="addField('surgeries_container','surgeries[]')"
                                                class="btn btn-secondary btn-sm">
                                                Add More
                                            </button>
                                        </div>

                                        <!-- Chronic Conditions -->
                                        <div class="form-group">
                                            <label>Chronic Conditions</label>
                                            <div id="chronic_conditions_container">
                                                <?php foreach ($chronic_conditions as $condition): ?>
                                                    <div class="input-group mb-2">
                                                        <input type="text"
                                                            name="chronic_conditions[]"
                                                            class="form-control"
                                                            value="<?= htmlspecialchars($condition) ?>">
                                                        <button type="button"
                                                            class="btn btn-danger"
                                                            onclick="removeField(this)">
                                                            Delete
                                                        </button>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                            <button type="button"
                                                onclick="addField('chronic_conditions_container','chronic_conditions[]')"
                                                class="btn btn-secondary btn-sm">
                                                Add More
                                            </button>
                                        </div>

                                        <!-- Family Medical History -->
                                        <div class="form-group">
                                            <label>Family Medical History</label>
                                            <div id="family_medical_history_container">
                                                <?php foreach ($family_medical_history as $history): ?>
                                                    <div class="input-group mb-2">
                                                        <input type="text"
                                                            name="family_medical_history[]"
                                                            class="form-control"
                                                            value="<?= htmlspecialchars($history) ?>">
                                                        <button type="button"
                                                            class="btn btn-danger"
                                                            onclick="removeField(this)">
                                                            Delete
                                                        </button>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                            <button type="button"
                                                onclick="addField('family_medical_history_container','family_medical_history[]')"
                                                class="btn btn-secondary btn-sm">
                                                Add More
                                            </button>
                                        </div>

                                        <!-- Medications -->
                                        <div class="form-group">
                                            <label>Medications</label>
                                            <div id="medications_container">
                                                <?php foreach ($medications as $index => $med): ?>
                                                    <div class="input-group mb-2">
                                                        <input type="text"
                                                            name="medications[<?= $index ?>][name]"
                                                            class="form-control"
                                                            placeholder="Name"
                                                            value="<?= htmlspecialchars($med['name']) ?>">
                                                        <input type="text"
                                                            name="medications[<?= $index ?>][dose]"
                                                            class="form-control"
                                                            placeholder="Dose"
                                                            value="<?= htmlspecialchars($med['dose']) ?>">
                                                        <button type="button"
                                                            class="btn btn-danger"
                                                            onclick="removeField(this)">
                                                            Delete
                                                        </button>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                            <button type="button"
                                                onclick="addMedicationField()"
                                                class="btn btn-secondary btn-sm">
                                                Add More
                                            </button>
                                        </div>

                                        <!-- Allergies -->
                                        <div class="form-group">
                                            <label>Allergies</label>
                                            <input type="text"
                                                name="allergies"
                                                class="form-control"
                                                value="<?= htmlspecialchars($allergies) ?>">
                                        </div>

                                        <!-- Existing Investigation Files (with delete checkboxes) -->
                                        <div class="form-group">
                                            <label>Current Investigation Files</label>
                                            <?php if (!empty($investigationFileNames)): ?>
                                                <ul>
                                                    <?php foreach ($investigationFileNames as $fileName): ?>
                                                        <?php
                                                        $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
                                                        $filePath = "uploads/" . htmlspecialchars($fileName);
                                                        ?>
                                                        <li style="margin-bottom: 5px;">
                                                            <?php if (in_array($fileExt, ['jpg', 'jpeg', 'png', 'gif'])): ?>
                                                                <img src="<?= $filePath ?>"
                                                                    alt="Investigation File"
                                                                    style="max-width:150px;max-height:150px;" />
                                                                <br>
                                                            <?php else: ?>
                                                                <a href="<?= $filePath ?>"
                                                                    target="_blank"
                                                                    class="btn btn-primary btn-sm">
                                                                    Download <?= htmlspecialchars($fileName) ?>
                                                                </a>
                                                            <?php endif; ?>
                                                            <!-- Checkbox to delete -->
                                                            <div class="form-check">
                                                                <input class="form-check-input"
                                                                    type="checkbox"
                                                                    name="delete_investigation[]"
                                                                    value="<?= htmlspecialchars($fileName) ?>">
                                                                <label class="form-check-label">
                                                                    Delete
                                                                </label>
                                                            </div>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            <?php else: ?>
                                                <p class="text-muted">No files uploaded yet.</p>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Dynamic Upload for New Files (Add More) -->
                                        <div class="form-group">
                                            <label>Upload New File(s)</label>
                                            <div id="investigations_container">
                                                <!-- First file input by default -->
                                                <div class="mb-2">
                                                    <input type="file" name="investigation[]" class="form-control">
                                                </div>
                                            </div>
                                            <button type="button" class="btn btn-secondary btn-sm" onclick="addInvestigationField()">
                                                Add More
                                            </button>
                                        </div>

                                        <!-- Submit -->
                                        <button type="submit" name="update_patient" class="btn btn-success">
                                            Update Patient
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div> <!-- end row -->

                </div> <!-- end container -->
            </div> <!-- end content -->
            <?php include('assets/inc/footer.php'); ?>
        </div>
    </div>

    <!-- JavaScript for Dynamic Fields -->
    <script>
        /* 
         * Add "text" fields for Past Illnesses, Surgeries, etc.
         */
        function addField(containerId, fieldName) {
            const container = document.getElementById(containerId);
            const wrapper = document.createElement('div');
            wrapper.className = 'input-group mb-2';

            const input = document.createElement('input');
            input.type = 'text';
            input.name = fieldName;
            input.className = 'form-control';
            input.placeholder = 'Enter details';

            const delBtn = document.createElement('button');
            delBtn.type = 'button';
            delBtn.className = 'btn btn-danger';
            delBtn.textContent = 'Delete';
            delBtn.onclick = () => container.removeChild(wrapper);

            wrapper.appendChild(input);
            wrapper.appendChild(delBtn);
            container.appendChild(wrapper);
        }

        let medicationIndex = <?= count($medications) ?>;

        function addMedicationField() {
            const container = document.getElementById('medications_container');
            const wrapper = document.createElement('div');
            wrapper.className = 'input-group mb-2';

            const nameInput = document.createElement('input');
            nameInput.type = 'text';
            nameInput.name = `medications[${medicationIndex}][name]`;
            nameInput.className = 'form-control';
            nameInput.placeholder = 'Name';

            const doseInput = document.createElement('input');
            doseInput.type = 'text';
            doseInput.name = `medications[${medicationIndex}][dose]`;
            doseInput.className = 'form-control';
            doseInput.placeholder = 'Dose';

            const delBtn = document.createElement('button');
            delBtn.type = 'button';
            delBtn.className = 'btn btn-danger';
            delBtn.textContent = 'Delete';
            delBtn.onclick = () => container.removeChild(wrapper);

            wrapper.appendChild(nameInput);
            wrapper.appendChild(doseInput);
            wrapper.appendChild(delBtn);
            container.appendChild(wrapper);

            medicationIndex++;
        }

        function removeField(btn) {
            btn.closest('.input-group').remove();
        }

        /*
         * Add new <input type="file" name="investigation[]"> for uploading multiple files
         */
        function addInvestigationField() {
            const container = document.getElementById('investigations_container');
            const div = document.createElement('div');
            div.className = 'mb-2';

            const fileInput = document.createElement('input');
            fileInput.type = 'file';
            fileInput.name = 'investigation[]';
            fileInput.className = 'form-control';

            // Optional "Delete" button for each row
            const delBtn = document.createElement('button');
            delBtn.type = 'button';
            delBtn.className = 'btn btn-danger ml-2';
            delBtn.textContent = 'Remove';
            delBtn.onclick = () => container.removeChild(div);

            div.appendChild(fileInput);
            div.appendChild(delBtn);
            container.appendChild(div);
        }
    </script>
</body>

</html>