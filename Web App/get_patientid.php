<?php
	include('dbconn2.php');
	
	$query = "SELECT patientid FROM patients ORDER BY patientid DESC LIMIT 1";
	$result = mysqli_query($conn, $query);
    
    if(mysqli_num_rows($result) > 0) {
        $latest_row = mysqli_fetch_row($result);
        echo $latest_row[0] + 1;
    }
    else {
        echo 1;
    }
?>