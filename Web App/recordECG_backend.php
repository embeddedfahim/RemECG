<?php
	include('dbconn2.php');

	if(isset($_POST['reportid']) && isset($_POST['patientid'])) {
		$reportid = $_POST['reportid'];
		$patientid = implode($_POST['patientid']);
		date_default_timezone_set('Asia/Dhaka');
		$timestamp = date('F j, Y, g:i A', time());
		$filename = $reportid.'.csv';
		$query = "INSERT INTO reports (reportid, timestamp, filename) VALUES ('$reportid', '$timestamp', '$filename')";
		mysqli_query($conn, $query);
		$query = "INSERT INTO correspondence (patient_id, report_id) VALUES ('$patientid', '$reportid')";
		mysqli_query($conn, $query);
	}

	if(isset($_POST['loadpatients'])) {
		$data = '<option value="" selected="selected">Select Patient..</option>';
		$query = "SELECT * FROM patients";
		$result = mysqli_query($conn, $query);

		if(mysqli_num_rows($result) > 0) {
			while($row = mysqli_fetch_assoc($result)) {
				$data .= '<option value="'.$row['patientid'].'">'.$row['patientid'].' - '.$row['name'].'</option>';
			}
		}

		echo $data;
	}

	if(isset($_POST['mode'])){
		$mode = $_POST['mode'];
		file_put_contents('mode.txt', $mode);
	}
?>