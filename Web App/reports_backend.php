<?php
	include('dbconn2.php');

	if(isset($_POST['loadreports'])) {
		$data = '<table class="table table-bordered table-striped text-center">
					<tr class="bg-danger text-white">
						<th>Sl.</th>
						<th>Report ID</th>
						<th>Date and Time</th>
						<th>Patient ID</th>
						<th>Patient Name</th>
						<th>Filename</th>
						<th>Operation(s)</th>
					</tr>';
		$query = "SELECT * FROM reports r JOIN correspondence c ON r.reportid = c.report_id JOIN patients p ON p.patientid = c.patient_id";
		$result = mysqli_query($conn, $query);

		if(mysqli_num_rows($result) > 0) {
			$serial = 1;
			
			while($row = mysqli_fetch_array($result)) {
				$data .= '<tr class="bg-light text-dark">
							<td>'.$serial.'</td>
							<td>'.$row['reportid'].'</td>
							<td>'.$row['timestamp'].'</td>
							<td>'.$row['patientid'].'</td>
							<td>'.$row['name'].'</td>
							<td>'.$row['filename'].'</td>
							<td>
								<button style="font-weight: bold" onclick="makePlot('.$row['reportid'].')" class="btn btn-success btn-sm">View</button>
								<button style="font-weight: bold" onclick="deleteReport('.$row['reportid'].')" class="btn btn-danger btn-sm">Delete</button>
							</td>
    					</tr>';
    			$serial++;
			}
		}

		$data .= '</table>';
    	
		echo $data;
	}

	if(isset($_POST['deleteid'])) {
		$reportid = $_POST['deleteid'];
		$query = "DELETE FROM reports WHERE reportid = '$reportid'";
		mysqli_query($conn, $query);
		$query = "DELETE FROM correspondence WHERE report_id = '$reportid'";
		mysqli_query($conn, $query);
		$filename = $reportid.'.csv';
		$file = 'reports/'.$filename;
		unlink($file);
		file_put_contents('deleteid.txt', $reportid);
		file_put_contents('mode.txt', 2);
		sleep(1);
		file_put_contents('deleteid.txt', '');
		file_put_contents('mode.txt', 0);
	}

	if(isset($_POST['id'])) {
		$reportid = $_POST['id'];
		$query = "SELECT * FROM reports r JOIN correspondence c ON r.reportid = c.report_id JOIN patients p ON p.patientid = c.patient_id WHERE reportid = '$reportid'";
		$result = mysqli_query($conn, $query);

		if(mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_array($result);
			echo 'Patient ID: '.$row['patientid'].', Report ID: '.$row['reportid'].', Recorded On: '.$row['timestamp'].'';
		}
	}
?>