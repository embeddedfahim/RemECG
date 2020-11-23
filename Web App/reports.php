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
		<title>Reports - RemECG</title>
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
		<div class="container">
			<div style="margin-top: 30px" id="report_records"></div>
			<div class="modal" id="view_ecg">
				<div class="modal-dialog modal-xl">
    				<div class="modal-content">
    					<div class="modal-header">
        					<h4 class="modal-title">View ECG</h4>
        					<button type="button" class="close" data-dismiss="modal">&times;</button>
      					</div>
      					<div class="modal-body">
      						<div id="ecg_graph"></div>
      					</div>
      					<div class="modal-footer">
        					<button style="font-weight: bold" type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Close</button>
      					</div>
    				</div>
  				</div>
			</div>
		</div>
		<script type="text/javascript">
			var id = '';
			
			$(document).ready(function() {
    			loadReports();
			});
			
			function loadReports() {
				var loadreports = "loadreports";

				$.ajax({
					url: "reports_backend.php",
					type: "POST",
					data: {loadreports: loadreports},
				
					success: function(data) {
						$('#report_records').html(data);
					}
				});
			}

			function deleteReport(deleteid) {
				$.ajax({
					url: "reports_backend.php",
					type: "POST",
					data: {deleteid: deleteid},

					success: function(data) {
						loadReports();
					}
				});
			}

			function makePlot(reportid) {
				id = reportid;
				var reportidString = reportid.toString();
				var filename = reportidString.concat('.csv');
				var file = 'reports/'.concat(filename);
				
				$('#view_ecg').modal('show');

				Plotly.d3.csv(file, function(data) {
					processData(data)
				});
			}

			function processData(allRows) {
				var x = [], y = [], standard_deviation = [];

				for(var i = 0; i < allRows.length; i++) {
					row = allRows[i];
					x.push(row['time']);
					y.push(row['ecg_reading']);
				}

				makePlotly(x, y, standard_deviation);
			}

			function makePlotly(x, y, standard_deviation) {
				var traces = [{
					x: x, 
					y: y,
					type: 'line',
					line: {color: '#d9534f'}
				}];

				$.ajax({
					url: "reports_backend.php",
					type: "POST",
					data: {id: id},
					async: false,

					success: function(data) {
						Plotly.newPlot('ecg_graph', traces, {title: data});
					}
				});
			}
		</script>
	</body>
</html>