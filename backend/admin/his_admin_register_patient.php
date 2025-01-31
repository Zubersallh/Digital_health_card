<?php
	session_start();
	include('assets/inc/config.php');
		if(isset($_POST['record_patient_det']))
		{
            $pat_fname=$_POST['pat_fname'];
			$pat_lname=$_POST['pat_lname'];
			$pat_number=$_POST['pat_number'];
            $pat_phone=$_POST['pat_phone'];
            $blood_type=$_POST['blood_type'];
            $pat_addr=$_POST['pat_addr'];
            $pat_dob = $_POST['pat_dob'];
            $pat_emer_con = $_POST['pat_emer-con'];
			$past_illnesses = json_encode($_POST['past_illnesses']);
			$surgeries = json_encode($_POST['surgeries']);
			$chronic_conditions = json_encode($_POST['chronic_conditions']);
			$family_medical_history = json_encode($_POST['family_medical_history']);
            $medications = json_encode($_POST['medications']);
            $allergies = $_POST['allergies'];
            $investigation = $_POST['investigation'];
            
			$query="insert into his_patients (past_illnesses, surgeries, chronic_conditions, family_medical_history, medications, allergies, investigation) values(?,?,?,?,?,?,?)";
			$stmt = $mysqli->prepare($query);
			$rc=$stmt->bind_param('sssssss', $past_illnesses, $surgeries, $chronic_conditions, $family_medical_history, $medications, $allergies, $investigation);
			$stmt->execute();
			
			if($stmt)
			{
				$success = "Patient Details Added";
			}
			else {
				$err = "Please Try Again Or Try Later";
			}
			
		}
?>
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
                                            <li class="breadcrumb-item active">Record Patient Details</li>
                                        </ol>
                                    </div>
                                    <h4 class="page-title">Record Patient information</h4>
                                </div>
                            </div>
                        </div>     
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <h4 class="header-title">Fill all fields</h4>
                                        <form method="post">
                                        <div class="form-row">
                                                <div class="form-group col-md-6">
                                                    <label for="inputEmail4" class="col-form-label">First Name</label>
                                                    <input type="text" required="required" name="pat_fname" class="form-control" id="inputEmail4" placeholder="Patient's First Name">
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="inputPassword4" class="col-form-label">Last Name</label>
                                                    <input required="required" type="text" name="pat_lname" class="form-control"  id="inputPassword4" placeholder="Patient`s Last Name">
                                                </div>
                                            </div>

                                            <div class="form-row">
                                                <div class="form-group col-md-6">
                                                    <label for="inputEmail4" class="col-form-label">Date Of Birth</label>
                                                    <input type="text" required="required" name="pat_dob" class="form-control" id="inputEmail4" placeholder="DD/MM/YYYY">
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="inputEmail4" class="col-form-label">Patient Password </label>
                                                    <input type="text" required="required" name="pat_pass" class="form-control" id="inputEmail4" placeholder="password">
                                                </div>
                                               
                                            </div>
                                           

                                            <div class="form-group">
                                                <label for="inputAddress" class="col-form-label">Address</label>
                                                <input required="required" type="text" class="form-control" name="pat_addr" id="inputAddress" placeholder="Patient's Addresss">
                                            </div>

                                            <div class="form-row">
                                                <div class="form-group col-md-4">
                                                    <label for="inputCity" class="col-form-label">Contact inforamtion</label>
                                                    <input required="required" type="text" name="pat_phone" class="form-control" id="inputCity">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label for="inputCity" class="col-form-label">Emergency contact detail</label>
                                                    <input required="required" type="text" name="pat_emer-con" class="form-control" id="inputCity">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label for="inputState" class="col-form-label">Blood Type(trusted)</label>
                                                    <input required="required" type="text" name="blood_type" class="form-control" id="inputCity">
                                                </div>
                                                <div class="form-group col-md-2" style="display:none">
                                                    <?php 
                                                        $length = 5;    
                                                        $patient_number =  substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'),1,$length);
                                                    ?>
                                                    <label for="inputZip" class="col-form-label">Patient Number</label>
                                                    <input type="text" name="pat_number" value="<?php echo $patient_number;?>" class="form-control" id="inputZip">
                                                </div>
                                            </div>
                                       

                                            <div class="form-group">
                                                <label for="past_illnesses" class="col-form-label">Past Illnesses</label>
                                                <div id="past_illnesses_container">
                                                    <input type="text" name="past_illnesses[]" class="form-control mb-2" placeholder="Enter past illness">
                                                </div>
                                                <button type="button" onclick="addField('past_illnesses_container', 'past_illnesses[]')" class="btn btn-secondary btn-sm">Add More</button>
                                            </div>
                                            <div class="form-group col-mid-4">
                                                <label for="surgeries" class="col-form-label">Surgeries</label>
                                                <div id="surgeries_container">
                                                    <input type="text" name="surgeries[]" class="form-control mb-2" placeholder="Enter surgery details">
                                                </div>
                                                <button type="button" onclick="addField('surgeries_container', 'surgeries[]')" class="btn btn-secondary btn-sm">Add More</button>
                                            </div>
                                       
                                            <div class="form-group">
                                                <label for="chronic_conditions" class="col-form-label">Chronic Conditions</label>
                                                <div id="chronic_conditions_container">
                                                    <input type="text" name="chronic_conditions[]" class="form-control mb-2" placeholder="Enter chronic condition">
                                                </div>
                                                <button type="button" onclick="addField('chronic_conditions_container', 'chronic_conditions[]')" class="btn btn-secondary btn-sm">Add More</button>
                                            </div>
                                            <div class="form-group">
                                                <label for="family_medical_history" class="col-form-label">Family Medical History</label>
                                                <div id="family_medical_history_container">
                                                    <input type="text" name="family_medical_history[]" class="form-control mb-2" placeholder="Enter family medical history">
                                                </div>
                                                <button type="button" onclick="addField('family_medical_history_container', 'family_medical_history[]')" class="btn btn-secondary btn-sm">Add More</button>
                                            </div>
                                            <div class="form-group">
                                                <label for="medications" class="col-form-label">Medications</label>
                                                <div id="medications_container">
                                                    <div class="input-group mb-2">
                                                        <input type="text" name="medications[][name]" class="form-control" placeholder="Medication Name">
                                                        <input type="text" name="medications[][dose]" class="form-control" placeholder="Dose">
                                                    </div>
                                                </div>
                                                <button type="button" onclick="addMedicationField()" class="btn btn-secondary btn-sm">Add More</button>
                                            </div>
                                            <div class="form-group">
                                                <label for="allergies" class="col-form-label">Allergies</label>
                                                <input type="text" name="allergies" class="form-control" placeholder="Enter allergies">
                                            </div>
                                            <div class="form-group">
                                                <label for="investigation" class="col-form-label">Investigation</label>
                                                <input type="text" name="investigation" class="form-control" placeholder="Enter investigation details">
                                            </div>
                                            <button type="submit" name="add_patient" class="ladda-button btn btn-primary" data-style="expand-right">Add The Record </button>
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
        <script>
            function addField(containerId, fieldName) {
                var container = document.getElementById(containerId);
                var input = document.createElement("input");
                input.type = "text";
                input.name = fieldName;
                input.className = "form-control mb-2";
                input.placeholder = "Enter more details";
                container.appendChild(input);
            }
            function addMedicationField() {
                var container = document.getElementById("medications_container");
                var div = document.createElement("div");
                div.className = "input-group mb-2";
                div.innerHTML = '<input type="text" name="medications[][name]" class="form-control" placeholder="Medication Name">' +
                                '<input type="text" name="medications[][dose]" class="form-control" placeholder="Dose">';
                container.appendChild(div);
            }
        </script>
    </body>
</html>
