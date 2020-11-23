<?php
	include('dbconn2.php');

	if(isset($_POST['loadpatients'])) {
		$data = '<table class="table table-bordered table-striped text-center">
					<tr class="bg-danger text-white">
						<th>Sl.</th>
						<th>Patient ID</th>
						<th>Name</th>
						<th>Age</th>
						<th>Address</th>
						<th>Mobile</th>
						<th>Operation(s)</th>
					</tr>';
		$query = "SELECT * FROM `patients`"; 
		$result = mysqli_query($conn, $query);

		if(mysqli_num_rows($result) > 0) {
			$serial = 1;
			
			while($row = mysqli_fetch_array($result)) {
				$data .= '<tr class="bg-light text-dark">
							<td>'.$serial.'</td>
							<td>'.$row['patientid'].'</td>
							<td>'.$row['name'].'</td>
							<td>'.$row['age'].'</td>
							<td>'.$row['address'].'</td>
							<td>'.$row['mobile'].'</td>
							<td>
								<button style="font-weight: bold" onclick="getPatientDetails('.$row['patientid'].')" class="btn btn-success btn-sm">Edit</button>
								<button style="font-weight: bold" onclick="deletePatient('.$row['patientid'].')" class="btn btn-danger btn-sm">Delete</button>
							</td>
    					</tr>';
    			$serial++;
			}
		}

		$data .= '</table>';
    	
		echo $data;
	}

	if(isset($_POST['deleteid'])) {
		$patientid = $_POST['deleteid'];
		$query = "DELETE FROM patients WHERE patientid = '$patientid'";
		mysqli_query($conn, $query);
	}
	
	if(isset($_POST['id']) && isset($_POST['id']) != "") {
    	$patientid = $_POST['id'];
    	$query = "SELECT * FROM patients WHERE patientid = '$patientid'";
    	
    	if(!$result = mysqli_query($conn, $query)) {
        	exit(mysqli_error());
    	}
    	
    	$response = array();
    	
    	if(mysqli_num_rows($result) > 0) {
    		while($row = mysqli_fetch_array($result)) {
            	$response = $row;
        	}
    	}
    	else {
        	$response['status'] = 200;
        	$response['message'] = "Data not found!";
    	}

    	echo json_encode($response);
	}
	else {
    	$response['status'] = 200;
    	$response['message'] = "Invalid request!";
	}

	if(isset($_POST['hidden_patient_id'])) {
		$hidden_patient_id = $_POST['hidden_patient_id'];
		$newname = $_POST['newname'];
		$newage = $_POST['newage'];
		$newaddress = $_POST['newaddress'];
		$newmobile = $_POST['newmobile'];
    	$query = "UPDATE patients SET name = '$newname', age = '$newage', address = '$newaddress', mobile = '$newmobile' WHERE patientid = '$hidden_patient_id'";
    	mysqli_query($conn, $query);
    }
?>