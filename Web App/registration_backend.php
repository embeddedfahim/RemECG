<?php
	include('dbconn2.php');
	$id = $_POST["id"];
	$fullname = '';
	$age = '';
	$address = '';
	$mobile = '';
	$error_fullname = '';
	$error_age = '';
	$error_address = '';
	$error_mobile = '';
	$error = 0;

	if(empty($_POST["fullname"])) {
		$error_fullname = 'Full name is required!';
		$error++;
	}
	else {
		$fullname = $_POST["fullname"];
	}
	if(empty($_POST["age"])) {
		$error_age = 'Age is required!';
		$error++;
	}
	else {
		$age = $_POST["age"];
	}
	if(empty($_POST["address"])) {
		$error_address = 'Address is required!';
		$error++;
	}
	else {
		$address = $_POST["address"];
	}
	if(empty($_POST["mobile"])) {
		$error_mobile = 'Mobile no. is required!';
		$error++;
	}
	else {
		$mobile = $_POST["mobile"];
		$query = "SELECT * FROM patients WHERE mobile = '$mobile'";
		$result = mysqli_query($conn, $query);
		
		if(mysqli_num_rows($result) > 0) {
			$error_mobile = "Mobile no. already exists!!";
			$error++;
		}
	}
	
	if($error == 0) {
		$query = "INSERT INTO patients (patientid, name, age, address, mobile) VALUES ('$id', '$fullname', '$age', '$address', '$mobile')";
		mysqli_query($conn, $query);
	}
	if($error > 0) {
		$output = array(
			'error'				=>	true,
			'error_fullname'	=>	$error_fullname,
			'error_age'			=>	$error_age,
			'error_address'		=>	$error_address,
			'error_mobile'		=>	$error_mobile,
		);
	}
	else {
		$output = array(
			'success'			=>	true
		);	
	}

	echo json_encode($output);
?>