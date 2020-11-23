<?php
	include('header.php');
	include('dbconn2.php');
	session_start();
	
	if(!isset($_SESSION['username'])) {
		header('location: login.php');
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<title>Patients - RemECG</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="icon" href="images/logo.png">
		<link rel="stylesheet" href="css/bootstrap.min.css">
		<script src="js/jquery.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<link href="https://fonts.googleapis.com/css?family=Ubuntu&display=swap" rel="stylesheet">
		<style type="text/css">
			body {
				font-family: 'Ubuntu', sans-serif;
			}
		</style>
	</head>
	<body>
		<div class="container">
			<div style="margin-top: 30px" id="patient_records"></div>
			<div class="modal" id="updateModal">
				<div class="modal-dialog">
    				<div class="modal-content">
    					<div class="modal-header">
        					<h4 class="modal-title">Edit Patient Details</h4>
        					<button type="button" class="close" data-dismiss="modal">&times;</button>
      					</div>
      					<div class="modal-body">
        					<div class="form-group">
        						<label>Patient Name:</label>
        						<input type="text" name="newname" id="newname" class="form-control" placeholder="Enter Patient Name..">
        					</div>
        					<div class="form-group">
        						<label>Age:</label>
        						<input type="text" name="newage" id="newage" class="form-control" placeholder="Enter Age..">
        					</div>
        					<div class="form-group">
        						<label>Address:</label>
        						<input type="text" name="newaddress" id="newaddress" class="form-control" placeholder="Enter Address..">
        					</div>
        					<div class="form-group">
        						<label>Mobile No.:</label>
        						<input type="text" name="newmobile" id="newmobile" class="form-control" placeholder="Enter Mobile Number..">
        					</div>
      					</div>
      					<div class="modal-footer">
      						<button style="font-weight: bold" type="button" class="btn btn-success btn-sm" data-dismiss="modal" onclick="updatePatientDetails()">Update</button>
        					<button style="font-weight: bold" type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Close</button>
        					<input type="hidden" id="hidden_patient_id">
      					</div>
    				</div>
  				</div>
			</div>
		</div>
		<script type="text/javascript">
			$(document).ready(function() {
    			loadPatients();
			});
			
			function loadPatients() {
				var loadpatients = "loadpatients";

				$.ajax({
					url: "patients_backend.php",
					type: "POST",
					data: {loadpatients: loadpatients},
				
					success: function(data) {
						$('#patient_records').html(data);
					}
				});
			}
			
			function getPatientDetails(id) {
				$("#hidden_patient_id").val(id);
	  			
				$.post("patients_backend.php", {id: id},
					function(data) {
						var user = JSON.parse(data);
						
						$("#newname").val(user.name);
						$("#newage").val(user.age);
						$("#newaddress").val(user.address);
						$("#newmobile").val(user.mobile);
					}
				);
    			
    			$("#updateModal").modal("show");
    		}

			function updatePatientDetails() {
    			var newname = $("#newname").val();
    			var newage = $("#newage").val();
    			var newaddress = $("#newaddress").val();
    			var newmobile = $("#newmobile").val();
    			var hidden_patient_id = $("#hidden_patient_id").val();
    			
				$.post("patients_backend.php", {
						hidden_patient_id: hidden_patient_id,
						newname: newname,
						newage: newage,
						newaddress: newaddress,
						newmobile: newmobile
					},

					function(data) {
						$("#updateModal").modal("hide");
						loadPatients();
					}
				);
			}

			function deletePatient(deleteid) {
				$.ajax({
					url: "patients_backend.php",
					type: "POST",
					data: {deleteid: deleteid},

					success: function(data) {
						loadPatients();
					}
				});
			}
		</script>
	</body>
</html>