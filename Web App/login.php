<?php
	include('dbconn2.php');
	session_start();
?>

<!DOCTYPE html>
<html>
	<head>
		<title>Log In - RemECG</title>
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
		<div class="container" style="margin-top: 100px">
			<div class="row">
				<div class="col-md-4"></div>
				<div class="col-md-4">
					<div class="card">
						<div class="card-header bg-danger text-white font-weight-bold"style="text-align: center; font-size: 20px">Login Panel</div>
						<div class="card-body bg-light">
							<form method="POST" id="login_form">
								<div class="form-group">
									<label style="font-weight: bold">Username</label>
									<input type="text" name="username" id="username" class="form-control" />
									<span id="error_username" class="text-danger"></span>
								</div>
								<div class="form-group">
									<label style="font-weight: bold">Password</label>
									<input type="password" name="password" id="password" class="form-control" />
									<span id="error_password" class="text-danger"></span>
								</div>
								<div class="form-group col text-center" style="margin-top: 20px; margin-bottom: 0px">
									<input style="font-weight: bold; font-size: 15px" type="submit" name="login" id="login" class="btn btn-sm btn-danger" value="Log In" />
								</div>
							</form>
						</div>
					</div>
				</div>
				<div class="col-md-4"></div>
			</div>
		</div>
	</body>
</html>
<script>
	$(document).ready(function() {
		$('#login_form').on('submit', function(event) {
			event.preventDefault();

			$.ajax({
				url: "login_backend.php",
				method: "POST",
				data: $(this).serialize(),
				dataType: "json",
				
				beforeSend: function() {
					$('#login').val('Validating...');
					$('#login').attr('disabled', true);
				},

				success: function(data) {
					if(data.success) {
						location.href = "index.php";
					}
					if(data.error) {
						$('#login').val('Log In');
						$('#login').attr('disabled', false);
						
						if(data.error_username != '') {
							$('#error_username').text(data.error_username);
						}
						else {
							$('#error_username').text('');
						}
						if(data.error_password != '') {
							$('#error_password').text(data.error_password);
						}
						else {
							$('#error_password').text('');
						}
					}
				}
			});
		});
	});
</script>