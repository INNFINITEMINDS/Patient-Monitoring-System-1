<?php
	session_start();
	if (!isset($_SESSION['sessionName'])) header("location:index.php"); 
	$timeout2 = 300; // 5 minutes
	$duration = time() - $_SESSION['timeout'];
	if($duration > $timeout2) 
	{
		session_destroy();
		header("location:timeOut.php");
	}
	$_SESSION['timeout'] = time();		
	include("connectDB.php");
	$myPatientID=$_POST['patientList'];
	$myTagID=0;
	$result = mysql_query( "SELECT * FROM tagtracker WHERE patientID='$myPatientID'"); 
	while ( $row1 = mysql_fetch_array( $result ))
	{
		$myTagID = $row1[ tagID ];
	}
	mysql_free_result( $result );
	mysql_close();	
	$_SESSION['tagID']=$myTagID;
?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="assets/ico/favicon.png">
	<link href='http://fonts.googleapis.com/css?family=Raleway' rel='stylesheet' type='text/css'>

    <title>PMS (Patient Monitoring System) - Patient Current Data</title>

    <!-- Bootstrap core CSS -->
    <link href="dist/css/bootstrap.css" rel="stylesheet">
	<link href="dist/css/animate.css" rel="stylesheet">
	<script type="text/javascript" src="dist/js/dygraph-combined.js"></script>
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="assets/js/html5shiv.js"></script>
      <script src="assets/js/respond.min.js"></script>
    <![endif]-->
	<script src="assets/js/jquery.js"></script>
	<script>
		$(document).ready(function() 
		{
			$("#timeContainer").load("timeContainer.php");
			var refreshId = setInterval(function() 
			{ 
				$("#timeContainer").load('timeContainer.php?randval='+ Math.random());
			}, 60000);
		$.ajaxSetup({ cache: false });
		});
	</script>
  </head>

  <body>
	<div id="spin"></div>
    <div class="navbar navbar-inverse navbar-static-top">
      <div class="container">
		<div class="navbar-header">
		  
		  <a class="navbar-brand"><img src="images/PMSLogoSmall.png"></a>
		  <a class="btn btn-custom" href="dashboard.php"><i class="glyphicon glyphicon-signal"></i><b> DASHBOARD</b></a>
		  <a class="btn btn-custom" href="patientArchives.php"><i class="glyphicon glyphicon-user"></i><b> PATIENT ARCHIVES</b></a>
		  <a class="btn btn-custom" href="addUser.php"><i class="glyphicon glyphicon-user"></i><b> ADD USER</b></a>
		  <a class="btn btn-custom" href="addPatient.php"><i class="glyphicon glyphicon-user"></i><b> ADD PATIENT</b></a>
		  <a class="btn btn-custom" href="logOut.php"><i class="glyphicon glyphicon-pencil"></i><b> LOGOUT</b></a>
		 </div>
		 
      </div>
    </div>

    <div class="container">
	<div class="animated bounceInLeft">
	<div class="page-header">
	<div class="row">
		<div class="col-sm-6">
			<?php
				echo "<p align=left>" . $_SESSION['sessionName'] . "</p>";
			?>
		</div>  
		<div class="col-sm-6" id="timeContainer">
			
		</div> 
	</div>
	</div> 

	<div class="row">
		<div class="col-sm-4">
			<div class="panel panel-default">
				<div class="panel-heading">
				<h3 class="panel-title" align=center><b>PATIENT CURRENT DATA</b></h3>
				</div>
				<div class="panel-body">
				<?php
					include("connectDB.php");
					$myPatientID=$_POST['patientList'];
					echo "<table class='table table-bordered'>";
					$result1 = mysql_query( "SELECT * FROM patient WHERE patientID='$myPatientID'");
					while ( $row1 = mysql_fetch_array( $result1 ))
					{
						echo "<tr>";
						echo "<td align=left><p><b>Patient ID</b></p></td>";
						echo "<td align=left><p>" . $row1[ patientID ] . "</p></td>";
						echo "</tr>";
						echo "<tr>";
						echo "<td align=left><p><b>Name</b></p></td>";
						echo "<td align=left><p>" . $row1[ name ] . "</p></td>";
						echo "</tr>";
						echo "<tr>";
						echo "<td align=left><p><b>Gender</b></p></td>";
						if ( $row1[ gender ] == 'M' ) echo "<td align=left><p>Male</p></td>";
						else if ( $row1[ gender ] == 'F' ) echo "<td align=left><p>Female</p></td>";
						echo "</tr>";
						echo "<tr>";
						echo "<td align=left><p><b>Age</b></p></td>";
						$bdate = $row1[ dateBirth ];
						$now = date('Y-m-d');
						$diff = abs(strtotime($now) - strtotime($bdate));
						$years   = floor($diff / (365*60*60*24));
						echo "<td align=left><p>$years</p></td>";
						echo "</tr>";
						echo "<tr>";
						echo "<td align=left><p><b>Room</b></p></td>";
						echo "<td align=left><p>" . $row1[ room ] . "</p></td>";
						echo "</tr>";
						echo "<tr>";
						echo "<td align=left><p><b>Bed</b></p></td>";
						echo "<td align=left><p>" . $row1[ bed ] . "</p></td>";
						echo "</tr>";
						echo "<tr>";
						echo "<td align=left><p><b>Diagnosis</b></p></td>";
						echo "<td align=left><p>" . $row1[ diagnosis ] . "</p></td>";
						echo "</tr>";
						echo "<tr>";
						echo "<td align=left><p><b>Status</b></p></td>";
						if ( $row1[ healthStatus ] == 1 ) echo "<td align=left><p>Stable</p></td>";
						else if ( $row1[ healthStatus ] == 2 ) echo "<td align=left><p>Requires Attention</p></td>";
						else if ( $row1[ healthStatus ] == 3 ) echo "<td align=left><p>Critical</p></td>";
						echo "</tr>";
					}
					mysql_free_result( $result1 );
					mysql_close();
					echo "</table>";
				?>
				</div>
			</div>
		</div>
		<div class="col-sm-8">
			<div class="panel panel-default">
				<div class="panel-heading">
				<h3 class="panel-title" align=center><b>TEMPERATURE</b></h3>
				</div>
				<div class="panel-body">
					<div id="graphdiv1" style="width:600px; height:200px;"></div>
					<script type="text/javascript">
					g1 = new Dygraph( document.getElementById("graphdiv1"), "getTemperature.php", // path to CSV file
					{
						 
					}          
					);
					</script>
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading">
				<h3 class="panel-title" align=center><b>HEART RATE</b></h3>
				</div>
				<div class="panel-body">
					<div id="graphdiv2" style="width:600px; height:200px;"></div>
					<script type="text/javascript">
					g2 = new Dygraph( document.getElementById("graphdiv2"), "getHeartRate.php", // path to CSV file
					{}          // options
					);
					</script>
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading">
				<h3 class="panel-title" align=center><b>BLOOD PRESSURE</b></h3>
				</div>
				<div class="panel-body">
					<div id="graphdiv3" style="width:600px; height:200px;"></div>
					<script type="text/javascript">
					g3 = new Dygraph( document.getElementById("graphdiv3"), "getBloodPressure.php", // path to CSV file
					{}          // options
					);
					</script>
				</div>
			</div>
		</div>	
	</div>	
	
	<hr>
      <footer>
	  <div class="row text-center">
      <p><small>Patient Monitoring System Version 0.50 &copy; UTP 2013</small></p>
	  </div>
      </footer>
	</div>  
    </div> <!-- /container -->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="assets/js/jquery.js"></script>
    <script src="dist/js/bootstrap.min.js"></script>
	<script src="dist/js/spin.js"></script>
	<script type="text/javascript">
var opts = {
  lines: 13, // The number of lines to draw
  length: 20, // The length of each line
  width: 10, // The line thickness
  radius: 30, // The radius of the inner circle
  corners: 1, // Corner roundness (0..1)
  rotate: 0, // The rotation offset
  direction: 1, // 1: clockwise, -1: counterclockwise
  color: '#000', // #rgb or #rrggbb or array of colors
  speed: 1, // Rounds per second
  trail: 60, // Afterglow percentage
  shadow: false, // Whether to render a shadow
  hwaccel: false, // Whether to use hardware acceleration
  className: 'spinner', // The CSS class to assign to the spinner
  zIndex: 2e9, // The z-index (defaults to 2000000000)
  top: 'auto', // Top position relative to parent in px
  left: 'auto' // Left position relative to parent in px
};
var target = document.getElementById('spin');
var spinner = new Spinner(opts).spin(target);

$(window).load(function(){
// Once the page is fully loaded, stop spinning
spinner.stop();
});
	</script>
  </body>
</html>
