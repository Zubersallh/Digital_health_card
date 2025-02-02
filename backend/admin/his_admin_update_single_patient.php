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
$fname = $row->first_name;
$lname = $row->last_name;
$dob = $row->date_of_birth;
$address = $row->address;
$mobile = $row->contact_information;
$emer_con = $row->emergency_contact_detail;
$blood_type = $row->blood_type;


// $allergies = $row->allergies;
// $investigationFileName = $row->investigations ?? '';

/// Safely decode JSON fields and ensure they are arrays
// $past_illnesses = json_decode($row->past_illnesses ?? '[]', true) ?? [];

// $surgeries = json_decode($row->surgeries ?? '[]', true) ?? [];
// $chronic_conditions = json_decode($row->chronic_conditions ?? '[]', true) ?? [];
// $family_medical_history = json_decode($row->family_medical_history ?? '[]', true) ?? [];
// $medications = json_decode($row->medication ?? '[]', true) ?? [];

// Ensure these variables are always arrays
// $past_illnesses = is_array($past_illnesses) ? $past_illnesses : [];
// $surgeries = is_array($surgeries) ? $surgeries : [];
// $chronic_conditions = is_array($chronic_conditions) ? $chronic_conditions : [];
// $family_medical_history = is_array($family_medical_history) ? $family_medical_history : [];
// $medications = is_array($medications) ? $medications : [];

// Handle form submission
if (isset($_POST['update_patient'])) {
    // Collect form data
    $pat_fname = $_POST['pat_fname'];
    $pat_lname = $_POST['pat_lname'];
    $pat_dob = $_POST['pat_dob'];
    $pat_addr = $_POST['pat_addr'];
    $pat_phone = $_POST['pat_phone'];
    $pat_emer_con = $_POST['pat_emer_con'];
    $blood_type2 = $_POST['blood_type'];
    $patient_password2 = $_POST['pat_pass'];

    // Hash the new password only if it's not empty
    if (!empty($patient_password2)) {
        $hashed_password = password_hash($patient_password2, PASSWORD_DEFAULT);
    } else {
        // Keep existing password if no new password provided
        $hashed_password = $row->patient_password;
    }

    // Update quer


    // $allergies = $_POST['allergies'];
  

    // // Process JSON fields (convert to array before encoding)
    // $past_illnesses2 = json_encode( ($_POST['past_illnesses'] ?? []));
    // $surgeries2 = json_encode(($_POST['surgeries'] ?? []));
    // $chronic_conditions2 = json_encode( ($_POST['chronic_conditions'] ?? []));
    // $family_medical_history2 = json_encode( ($_POST['family_medical_history'] ?? []));
    

    // Process medications
   // Process medications
        // $medications2 = []; // Initialize a fresh array for the new data
        // foreach ($_POST['medications'] ?? [] as $med) {
        //     if (!empty($med['name']) || !empty($med['dose'])) {
        //         $medications2[] = [
        //             'name' => $med['name'],
        //             'dose' => $med['dose']
        //         ];
        //     }
        // }
        // $medications_json2 = json_encode($medications2);

    // Handle investigation file update
    // if (isset($_POST['delete_investigation'])) {
    //     if (!empty($investigationFileName) && file_exists("uploads/$investigationFileName")) {
    //         unlink("uploads/$investigationFileName");
    //     }
    //     $investigationFileName = '';
    // }

    // if ($_FILES['investigation']['error'] === UPLOAD_ERR_OK) {
    //     $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
    //     $fileExt = strtolower(pathinfo($_FILES['investigation']['name'], PATHINFO_EXTENSION));

    //     if (in_array($fileExt, $allowedExts)) {
    //         // Delete old file if exists
    //         if (!empty($investigationFileName) && file_exists("uploads/$investigationFileName")) {
    //             unlink("uploads/$investigationFileName");
    //         }

    //         // Save new file
    //         $newFileName = uniqid('investigation_', true) . '.' . $fileExt;
    //         $destination = "uploads/$newFileName";

    //         if (!is_dir('uploads')) mkdir('uploads', 0777, true);
    //         if (move_uploaded_file($_FILES['investigation']['tmp_name'], $destination)) {
    //             $investigationFileName = $newFileName;
    //         }
    //     }
    // }

    // Update query
    $query = "UPDATE patient SET 
              first_name = ?,
              last_name = ?,
              date_of_birth = ?,
              address = ?,
              contact_information = ?,
              emergency_contact_detail = ?,
              blood_type = ?,
              patient_password = ?
            --   past_illnesses = ?,
            --   surgeries = ?,
            --   chronic_conditions = ?,
            --   family_medical_history = ?,
            --   medication = ?,
            --   allergies = ?,
            --   investigations = ?
              WHERE patient_id = ?";

    $stmt = $mysqli->prepare($query);
    $stmt->bind_param(
        'ssssssssi',
        $pat_fname,
        $pat_lname,
        $pat_dob,
        $pat_addr,
        $pat_phone,
        $pat_emer_con,
        $blood_type2,
        $hashed_password,
        $pat_id
    );

    if ($stmt->execute()) {
        $success = "Patient Details Updated Successfully";
    } else {
        $err = "Error: " . $stmt->error;
    }
}
?>
<!--End Server Side-->
<!--End Patient Registration-->
<!DOCTYPE html>
<html lang="en">
    <?php include('assets/inc/head.php');?>
    <body>
        <div id="wrapper">
            <?php include("assets/inc/nav.php");?>
            <?php include("assets/inc/sidebar.php");?>
            <div class="content-page">
                <div class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box">
                                    <div class="page-title-right">
                                        <ol class="breadcrumb m-0">
                                            <li class="breadcrumb-item"><a href="his_admin_dashboard.php">Dashboard</a></li>
                                            <li class="breadcrumb-item"><a href="javascript: void(0);">Patients</a></li>
                                            <li class="breadcrumb-item active">Update Patient Details</li>
                                        </ol>
                                    </div>
                                    <h4 class="page-title">Update Patient Information</h4>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <h4 class="header-title">Fill all fields</h4>
                                        <form method="post" enctype="multipart/form-data">
                                            <!-- Basic Information -->
                                            <div class="form-row">
                                                <div class="form-group col-md-6">
                                                    <label class="col-form-label">First Name</label>
                                                    <input type="text" required name="pat_fname" class="form-control" value="<?= htmlspecialchars($fname) ?>">
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label class="col-form-label">Last Name</label>
                                                    <input type="text" required name="pat_lname" class="form-control" value="<?= htmlspecialchars($lname) ?>">
                                                </div>
                                            </div>

                                            <div class="form-row">
                                                <div class="form-group col-md-6">
                                                    <label class="col-form-label">Date of Birth</label>
                                                    <input type="text" required name="pat_dob" class="form-control" value="<?= htmlspecialchars($dob) ?>">
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label class="col-form-label">Address</label>
                                                    <input type="text" required name="pat_addr" class="form-control" value="<?= htmlspecialchars($address) ?>">
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label class="col-form-label">Password(Leave empty if you don't want change the password !)</label>
                                                    <input type="password" name="pat_pass" class="form-control"  ?>
                                                </div>
                                            </div>

                                            <div class="form-row">
                                                <div class="form-group col-md-4">
                                                    <label class="col-form-label">Mobile Number</label>
                                                    <input type="text" required name="pat_phone" class="form-control" value="<?= htmlspecialchars($mobile) ?>">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label class="col-form-label">Emergency Contact</label>
                                                    <input type="text" required name="pat_emer_con" class="form-control" value="<?= htmlspecialchars($emer_con) ?>">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label class="col-form-label">Blood Type</label>
                                                    <input type="text" required name="blood_type" class="form-control" value="<?= htmlspecialchars($blood_type) ?>">
                                                </div>
                                            </div>

<!--                                            
                                            <div class="form-group">
                                                <label class="col-form-label">Past Illnesses</label>
                                                <div id="past_illnesses_container">
                                                    <//?php  foreach ($past_illnesses as $illness): ?>
                                                    <div class="input-group mb-2">
                                                        <input type="text" name="past_illnesses[]" class="form-control" value="<//?= htmlspecialchars($illness) ?>">
                                                        <button type="button" class="btn btn-danger" onclick="removeField(this)">Delete</button>
                                                    </div>
                                                    <//?php endforeach; ?>
                                                </div>
                                                <button type="button" onclick="addField('past_illnesses_container', 'past_illnesses[]')" class="btn btn-secondary btn-sm">Add More</button>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-form-label">Surgeries</label>
                                                <div id="surgeries_container">
                                                    <//?php foreach ($surgeries as $surgery): ?>
                                                    <div class="input-group mb-2">
                                                        <input type="text" name="surgeries[]" class="form-control" value="<//?= htmlspecialchars($surgery) ?>">
                                                        <button type="button" class="btn btn-danger" onclick="removeField(this)">Delete</button>
                                                    </div>
                                                    <//?php endforeach; ?>
                                                </div>
                                                <button type="button" onclick="addField('surgeries_container', 'surgeries[]')" class="btn btn-secondary btn-sm">Add More</button>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-form-label">Chronic Conditions</label>
                                                <div id="chronic_conditions_container">
                                                    <//?php foreach ($chronic_conditions as $condition): ?>
                                                    <div class="input-group mb-2">
                                                        <input type="text" name="chronic_conditions[]" class="form-control" value="<//?= htmlspecialchars($condition) ?>">
                                                        <button type="button" class="btn btn-danger" onclick="removeField(this)">Delete</button>
                                                    </div>
                                                    <//?php endforeach; ?>
                                                </div>
                                                <button type="button" onclick="addField('chronic_conditions_container', 'chronic_conditions[]')" class="btn btn-secondary btn-sm">Add More</button>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-form-label">Family Medical History</label>
                                                <div id="family_medical_history_container">
                                                    <//?php foreach ($family_medical_history as $history): ?>
                                                    <div class="input-group mb-2">
                                                        <input type="text" name="family_medical_history[]" class="form-control" value="<//?= htmlspecialchars($history) ?>">
                                                        <button type="button" class="btn btn-danger" onclick="removeField(this)">Delete</button>
                                                    </div>
                                                    <//?php endforeach; ?>
                                                </div>
                                                <button type="button" onclick="addField('family_medical_history_container', 'family_medical_history[]')" class="btn btn-secondary btn-sm">Add More</button>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-form-label">Medications</label>
                                                <div id="medications_container">
                                                    <//?php foreach ($medications as $index => $med): ?>
                                                    <div class="input-group mb-2">
                                                        <input type="text" name="medications[<//?= $index ?>][name]" class="form-control" placeholder="Name" value="<//?= htmlspecialchars($med['name']) ?>">
                                                        <input type="text" name="medications[<//?= $index ?>][dose]" class="form-control" placeholder="Dose" value="<//?= htmlspecialchars($med['dose']) ?>">
                                                        <button type="button" class="btn btn-danger" onclick="removeField(this)">Delete</button>
                                                    </div>
                                                    <//?php endforeach; ?>
                                                </div>
                                                <button type="button" onclick="addMedicationField()" class="btn btn-secondary btn-sm">Add More</button>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-form-label">Allergies</label>
                                                <input type="text" name="allergies" class="form-control" value="<//?= htmlspecialchars($allergies) ?>">
                                            </div>

                                            <div class="form-group">
                                                <label class="col-form-label">Investigation</label>
                                                <//?php if (!empty($investigationFileName)): ?>
                                                <div class="mb-2">
                                                    </?php
                                                    $fileExt = pathinfo($investigationFileName, PATHINFO_EXTENSION);
                                                    if (in_array($fileExt, ['jpg', 'jpeg', 'png', 'gif'])): ?>
                                                        <img src="uploads/</?= htmlspecialchars($investigationFileName) ?>" 
                                                             alt="Current Report" 
                                                             style="max-width: 200px;">
                                                    </?php else: ?>
                                                        <a href="uploads/</?= htmlspecialchars($investigationFileName) ?>" 
                                                           target="_blank" 
                                                           class="btn btn-primary btn-sm">
                                                            Download Current Report
                                                        </a>
                                                    </?php endif; ?>
                                                    <p class="mt-2">Current File: </?= htmlspecialchars($investigationFileName) ?></p>
                                                </div>
                                                </?php else: ?>
                                                <div class="mb-2">
                                                    <p class="text-muted">No investigation file uploaded.</p>
                                                </div>
                                                </?php endif; ?>
                                                <input type="file" name="investigation" class="form-control">
                                                </?php if (!empty($investigationFileName)): ?>
                                                <div class="form-check mt-2">
                                                    <input type="checkbox" name="delete_investigation" 
                                                           class="form-check-input">
                                                    <label class="form-check-label">
                                                        Delete current investigation file
                                                    </label>
                                                </div>
                                                </?php endif; ?>
                                            </div> -->

                                            <button type="submit" name="update_patient" class="btn btn-success">Update Patient</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php include('assets/inc/footer.php');?>
            </div>
        </div>

        <!-- JavaScript for Dynamic Fields -->
        <!-- <script>
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

                wrapper.append(input, delBtn);
                container.appendChild(wrapper);
            }

            let medicationIndex = </?= count($medications) ?>;
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

                wrapper.append(nameInput, doseInput, delBtn);
                container.appendChild(wrapper);
                medicationIndex++;
            }

            function removeField(btn) {
                btn.closest('.input-group').remove();
            }
        </script> -->
    </body>
</html>