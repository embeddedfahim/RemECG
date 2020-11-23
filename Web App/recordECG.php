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
		<title>Record New ECG - RemECG</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="icon" href="images/logo.png">
		<link rel="stylesheet" href="css/bootstrap.min.css">
		<script src="js/jquery.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<script src="js/plotly.min.js"></script>
		<link href="https://fonts.googleapis.com/css?family=Ubuntu&display=swap" rel="stylesheet">
		<style type="text/css">
			body {
				font-family: 'Ubuntu', sans-serif;
			}
		</style>
	</head>
	<body>
		<div class="container" style="margin-top: 30px">
			<div class="modal" id="recordecg">
				<div class="modal-dialog">
    				<div class="modal-content">
    					<div class="modal-header">
        					<h4 class="modal-title">Record ECG</h4>
        					<button type="button" class="close" data-dismiss="modal">&times;</button>
      					</div>
      					<div class="modal-body">
        					<div class="form-group">
        						<label>Status:</label>
								<div id="status_area"></div>
        					</div>
      					</div>
      					<div class="modal-footer">
        					<button type="button" style="font-weight: bold" class="btn btn-danger btn-sm" onClick="stopRecording()">Stop</button>
      					</div>
    				</div>
  				</div>
			</div>
			<div class="row">
				<div class="col-md-4"></div>
				<div class="col-md-4">
					<div class="card">
						<div class="card-header bg-danger text-white font-weight-bold"style="text-align: center; font-size: 20px">New ECG</div>
						<div class="card-body bg-light">
							<form method="POST" id="newecg">
								<div class="form-group">
									<label style="font-weight: bold">ECG ID</label>
									<input type="text" name="ecgid" id="ecgid" placeholder="Enter ECG ID.." class="form-control" />
									<span id="error_ecgid" class="text-danger"></span>
								</div>
								<div class="form-group">
									<label style="font-weight: bold">Patient</label>
									<select type="text" name="patients" id="patients" placeholder="Select Patient.." class="form-control"></select>
									<span id="error_patient" class="text-danger"></span>
								</div>
								<div class="form-group col text-center" style="margin-top: 20px; margin-bottom: 0px">
									<button style="font-weight: bold; font-size: 15px" type="button" name="record" id="record" class="btn btn-sm btn-danger" onClick="recordECG()">Start Recording</button>
								</div>
							</form>
						</div>
					</div>
				</div>
				<div class="col-md-4"></div>
			</div>
		</div>
		<script type="text/javascript">
			var reportid  = '';

			$(document).ready(function() {
    			loadPatients();
    			getReportID();
    			$('#ecgid').attr('disabled', true);
			});

			function getReportID() {
				$.ajax({
           			url: 'get_reportid.php',
           			type: 'POST',

            		success: function(data) {
            			$('#ecgid').val(data);
           				reportid = data;
           			}
           		});
			}
			
			function loadPatients() {
				var loadpatients = "loadpatients";

				$.ajax({
					url: "recordECG_backend.php",
					type: "POST",
					data: {loadpatients: loadpatients},
				
					success: function(data) {
						$('#patients').html(data);
					}
				});
			}

			function recordECG() {
				var mode = '1';

				if($('#patients').val() == "" || $('#patients').val() == "Select Patient..") {
					alert("Select patient first!!");
				}
				else {
					$('#recordecg').modal("show");

					$.ajax({
           				url: 'recordECG_backend.php',
            			type: 'POST',
            			data: {mode: mode}
        			});

					setInterval(function() {
						$('#status_area').load('get_msg.php', function(response) {
      						if(response == "ECG report file uploaded successfully..") {
      							addReport();
      							$('#recordecg').modal("hide");
      						}
    					});
					}, 100);
				}
			}

			function addReport() {
				var patient = $('#patients').val();
				var patientid = patient.split(" ", 1);
				
				$.ajax({
					url: "recordECG_backend.php",
					type: "POST",
					data: {
						reportid: reportid,
						patientid: patientid
					},

					success: function(data) {
						alert("ECG report stored successfully..");
						getReportID();
      					$('#patients').val("");
					}
				});
			}
			
			function stopRecording() {
			    var mode = '0';
			    
			    $('#recordecg').modal("hide");
			    
			    $.ajax({
			        url: 'recordECG_backend.php',
            		type: 'POST',
            		data: {mode: mode}
        		});
			}
		</script>
	</body>
</html>