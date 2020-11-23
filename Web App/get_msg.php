<?php
	$msg = file_get_contents('msg.txt');
	
	if($msg == "ECG report file uploaded successfully..") {
		echo $msg;
		file_put_contents('mode.txt', 0);
		file_put_contents('msg.txt', '');
	}
	else {
		echo $msg;
	}
?>