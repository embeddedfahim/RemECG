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
		<title>Registration - RemECG</title>
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
		<div class="container" style="margin-top: 30px">
			<div class="row">
				<div class="col-md-2"></div>
				<div class="col-md-8">
					<div class="card">
						<div class="card-header bg-danger text-white font-weight-bold"style="text-align: center; font-size: 20px">Registration Form</div>
						<div class="card-body bg-light">
							<form method="POST" id="registration_form">
								<div class="form-group">
									<label style="font-weight: bold">Patient ID</label>
									<input type="text" name="patientid" id="patientid" placeholder="Enter Patient ID.." class="form-control" />
									<span id="error_patientid" class="text-danger"></span>
								</div>
								<div class="form-group">
									<label style="font-weight: bold">Full Name</label>
									<input type="text" name="fullname" id="fullname" placeholder="Enter Full Name.." class="form-control" />
									<span id="error_fullname" class="text-danger"></span>
								</div>
								<div class="form-group">
									<label style="font-weight: bold">Age</label>
									<input type="text" name="age" id="age" placeholder="Enter Age.." class="form-control" />
									<span id="error_age" class="text-danger"></span>
								</div>
								<div class="form-group">
									<label style="font-weight: bold">Address</label>
									<input type="text" name="address" id="address" placeholder="Enter Address.." class="form-control" />
									<span id="error_address" class="text-danger"></span>
								</div>
								<div class="form-group">
									<label style="font-weight: bold">Mobile No.</label>
									<input type="text" name="mobile" id="mobile" placeholder="Enter Mobile Number.." class="form-control" />
									<span id="error_mobile" class="text-danger"></span>
								</div>
								<div class="form-group col text-center" style="margin-top: 20px; margin-bottom: 0px">
									<input style="font-weight: bold; font-size: 15px" type="submit" name="reg" id="reg" class="btn btn-sm btn-danger" value="Submit" />
								</div>
							</form>
						</div>
					</div>
				</div>
				<div class="col-md-2"></div>
			</div>
		</div>
	</body>
</html>
<script>
	var id = '';
	
	$(document).ready(function() {
		$('#patientid').attr('disabled', true);
		getPatientID();

		$('#registration_form').on('submit', function(event) {
			var fullname = $('#fullname').val();
			var age = $('#age').val();
			var address = $('#address').val();
			var mobile = $('#mobile').val();

			event.preventDefault();

			$.ajax({
				url: "registration_backend.php",
				method: "POST",
				data: {id: id, fullname: fullname, age: age, address: address, mobile: mobile},
				dataType: "JSON",

				beforeSend: function() {
					$('#reg').val('Submitting..');
					$('#reg').attr('disabled', true);
				},

				success: function(data) {
					if(data.success) {
						alert("Registration successful..");
						$('#reg').val('Submit');
						$('#reg').attr('disabled', false);
						$('#fullname').val('');
						$('#age').val('');
						$('#address').val('');
						$('#mobile').val('');
						getPatientID();
					}
					if(data.error) {
						$('#reg').val('Submit');
						$('#reg').attr('disabled', false);

						if(data.error_fullname != '') {
							$('#error_fullname').text(data.error_fullname);
						}
						else {
							$('#error_fullname').text('');
						}
						if(data.error_age != '') {
							$('#error_age').text(data.error_age);
						}
						else {
							$('#error_age').text('');
						}
						if(data.error_address != '') {
							$('#error_address').text(data.error_address);
						}
						else {
							$('#error_address').text('');
						}
						if(data.error_mobile != '') {
							$('#error_mobile').text(data.error_mobile);
						}
						else {
							$('#error_mobile').text('');
						}
					}
				}
			});
		});
	});

	function getPatientID() {
		$.ajax({
           	url: 'get_patientid.php',
           	type: 'POST',

            success: function(data) {
            	$('#patientid').val(data);
           		id = data;
           	}
    	});
	}
</script>