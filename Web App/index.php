<?php
	session_start();
	include('dbconn2.php');

	if(!isset($_SESSION['username'])) {
		header('location: login.php');
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<title>Home - RemECG</title>
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
		<div class="jumbotron-small text-center" style="margin-top: 20px; margin-bottom: 20px">
			<h2 class="text-danger">RemECG: IoT Based Remote Electrocardiography System</h2>
			<h5 class="text-dark">Developed by EMBEDDEDFAHIM</h5>
		</div>
		<div class="container" style="margin-top: 50px">
			<div class="row">
				<div class="col-md-4"></div>
				<div class="col-md-4"></div>
				<div class="col-md-4">
					<button type="button" class="btn btn-danger float-right btn-sm" style="margin-top: 10px; margin-bottom: 50px; font-weight: bold" onClick="window.location.href='logout.php'">Log Out</button>
				</div>
			</div>
			<div class="row">
				<div class="col-md-4"></div>
				<div class="col-md-4">
					<div class="card">
						<div class="card-header bg-danger text-white font-weight-bold"style="text-align: center; font-size: 20px">Main Menu</div>
						<div class="card-body bg-light">
							<div class="text-center" style="margin-top: 20px; margin-bottom: 0px">
								<input style="font-weight: bold; font-size: 15px" class="btn btn-sm btn-danger" onClick="window.location.href='registration.php'" value="Add New Patient" />
							</div>
							<div class="text-center" style="margin-top: 20px; margin-bottom: 0px">
								<input style="font-weight: bold; font-size: 15px" class="btn btn-sm btn-danger" onClick="window.location.href='patients.php'" value="Manage Patients" />
							</div>
							<div class="text-center" style="margin-top: 20px; margin-bottom: 0px">
								<input style="font-weight: bold; font-size: 15px" class="btn btn-sm btn-danger" onClick="window.location.href='recordECG.php'" value="Record New ECG" />
							</div>
							<div class="text-center" style="margin-top: 20px; margin-bottom: 20px">
								<input style="font-weight: bold; font-size: 15px" class="btn btn-sm btn-danger" onClick="window.location.href='reports.php'" value="Manage ECG Reports" />
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-4"></div>
			</div>
		</div>
	</body>
</html>
<script>
	
</script>