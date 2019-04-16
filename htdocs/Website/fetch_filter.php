<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="Easy Admin Panel Responsive web template, Bootstrap Web Templates, Flat Web Templates, Android Compatible web template, 
Smartphone Compatible web template, free webdesigns for Nokia, Samsung, LG, SonyEricsson, Motorola web design" />
<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
 <!-- Bootstrap Core CSS -->
<link href="css/bootstrap.min.css" rel='stylesheet' type='text/css' />
<!-- Custom CSS -->
<link href="css/style.css" rel='stylesheet' type='text/css' />
<!-- Graph CSS -->
<link href="css/font-awesome.css" rel="stylesheet"> 
<!-- jQuery -->
<!-- lined-icons -->
<link rel="stylesheet" href="css/icon-font.min.css" type='text/css' />
<!-- //lined-icons -->
<!-- chart -->
<script src="js/Chart.js"></script>
<!-- //chart -->
<!--animate-->
<link href="css/animate.css" rel="stylesheet" type="text/css" media="all">
<script src="js/wow.min.js"></script>
</head>
</html>
<?php
	 require_once('secret/database.php');
	 
	 $Event_Name = ' ';
 	 $location_name = ' ';
	 
     $Event_Name = $_GET['Event_Name'];
	 $location_name = $_GET['location_name'];
	 //********************Insert Time Condition !!!!!!!!
     $result=mysql_query("SELECT * FROM Event AS e INNER JOIN Location AS l ON e.Location_ID = l.ID  WHERE Event_Name='$Event_Name' OR Street_Name='$location_name'");
	 $result2=mysql_query("SELECT * FROM Event INNER JOIN Location");
	 //$result2=mysql_query("SELECT * FROM Event INNER JOIN Location WHERE Street_Name='$location_name'");
	 //$resultall = mysql_query("SELECT * FROM Event INNER JOIN Location");
     //$row = mysql_fetch_assoc($result);
	 //while ($row = mysql_fetch_array($result)) {
	//if (isset($_GET["Event_Name"])) {
		  echo '
					 <div class="xs tabls">
						<div class="bs-example4" data-example-id="contextual-table">
						<table class="table" id="datatables">
						  <thead>
							<tr>
							  <th>Event Name</th>
							  <th>Location</th>
							  <th>Host ID</th>
							  <th>Time Start</th>
							  <th>Time End</th>
							</tr>
						  </thead>
						  <tbody class="stripe hover">';
	if((isset($_GET['Event_Name'])) || (isset($_GET['location_name'])))	{				  
	while($row = mysql_fetch_array($result)) {
		echo "<tr>";
		echo "<td>" . $row['Event_Name'] . "</td>";
		echo "<td>" . $row['Street_Name'] . "</td>";
		echo "<td>" . $row['Host_ID'] . "</td>";
		echo "<td>" . $row['Time_Start'] . "</td>";
		echo "<td>" . $row['Time_End'] . "</td>";
		echo "</tr>";
	}
	}
	else {
	while($row = mysql_fetch_array($result2)) {
		echo "<tr>";
		echo "<td>" . $row['Event_Name'] . "</td>";
		echo "<td>" . $row['Street_Name'] . "</td>";
		echo "<td>" . $row['Host_ID'] . "</td>";
		echo "<td>" . $row['Time_Start'] . "</td>";
		echo "<td>" . $row['Time_End'] . "</td>";
		echo "</tr>";
	}
	}
	echo "</table>
						   </div>
					</div>
				</div>
			</div>
		</div>";
	//}
	//else {
		//while($row = mysql_fetch_array($resultall)) {
		//echo "<tr>";
		//echo "<td>" . $row['Event_Name'] . "</td>";
		//echo "<td>" . $row['Street_Name'] . "</td>";
		//echo "<td>" . $row['Host_ID'] . "</td>";
		//echo "<td>" . $row['Time_Start'] . "</td>";
		//echo "<td>" . $row['Time_End'] . "</td>";
		//echo "</tr>";	
	//}
			//<tr>
				//<td><?=$row['User_ID']></td>
				//<td><?=$row['Event_Name']></td>
				//<td><?=$row['Street_Name']></td>
				//<td><?=$row['Host_ID']></td>
				//<td><?=$row['Time_Start']></td>
				//<td><?=$row['Time_End']></td>
			//</tr>
//echo "<img class='img-responsive' src='http://mp08.bit-mp.biz/image/attendance-photo/".$row['photo']."' width='20%' height='20%'>
	exit;
	
	//if(isset($_GET['location_name'])){	
	 //$location_name = '';
     //$location_name = $_GET['location_name'];
     //$result2=mysql_query("SELECT * FROM Event WHERE location_name='$location_name'");
     //$row2 = mysql_fetch_assoc($result2);
    
	 
	 			
//exit;
	//};
	
?>