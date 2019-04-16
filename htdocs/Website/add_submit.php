<?php
	require_once('secret/database.php');

	if(isset($_POST['submit_client'])){
		$id = $_POST['id'];
		$name = $_POST['name'];
		$contact = $_POST['contact'];
		$address = $_POST['address'];
		$result_add = mysql_query("INSERT INTO Organisation VALUES ('$id','$name','$contact','$address')");
		if (!$result_add){
			die('Could not add data: ' . mysql_error());
		} else{
			echo "<script>alert('Added client data successfully!')</script>";
			ob_start(); // ensures anything dumped out will be caught
			header("Location: add_client.php");
		}	
	} elseif (isset($_POST['submit_event'])){
		$id = $_POST['id'];
		$event_name = $_POST['event_name'];
		$location_id = $_POST['location_id'];
		$host_id = $_POST['host_id'];
		$time_start = $_POST['time_start'];
		$time_end = $_POST['time_end'];
		//$location_id = mysql_query("SELECT ID FROM Location WHERE name = '$location_name' ");
		$result_add = mysql_query("INSERT INTO Event (ID, Event_Name, Location_ID, Host_ID, Time_Start, Time_End) VALUES ('$id','$event_name','$location_id','$host_id','$time_start','$time_end')");
		if (!$result_add){
			die('Could not add data: ' . mysql_error());
		} else{
			echo "<script>alert('Added event data successfully!')</script>";
			ob_start(); // ensures anything dumped out will be caught
			header("Location: add_event.php");
		}
	} elseif (isset($_POST['submit_location'])){
		$id = $_POST['id'];
		$location_name = $_POST['location_name'];
		$street_name = $_POST['street_name'];
		$postal_code = $_POST['postal_code'];
		$longitude = $_POST['longitude'];
		$latitude = $_POST['latitude'];
		$image = $_POST['image'];
		$result_add = mysql_query("INSERT INTO Location VALUES ('$id',' $location_name','$street_name',' $postal_code ','$longtitude',' $latitude', '$image')");
		
		if (!$result_add){
			die('Could not add data: ' . mysql_error());
		} else{
			echo "<script>alert('Added location data successfully!')</script>";
			ob_start(); // ensures anything dumped out will be caught
			header("Location: add_location.php");
		}
	} elseif (isset($_POST['submit_user'])){
		$name = $_POST['name'];
		$nric = $_POST['nric'];
		$username = $_POST['username'];
		$staffid = $_POST['staffid'];
		$password = $_POST['password'];
		//$secret = $_POST['secret'];
		$role = $_POST['role'];
		$inactive = $_POST['inactive'];
		$photo = $_POST['photo'];
		$organisation_id = $_POST['organisation_id'];
		$result_update = mysql_query("INSERT INTO UserProfile (ID, Name, NRIC, Username, StaffID, Password, Role, Inactive, Photo, organisation_id) VALUES ('$id','$name','$nric','$username','$staffid', '$password', '$role', '$inactive', '$photo', '$organisation_id' )");
		// Writes the photo to the server
		//$target = "image/profile-photo/2/";			
		
		if (!$result_update){
			die('Could not add data: ' . mysql_error());
		} else{
			echo "<script>alert('Added user data successfully!')</script>";
			ob_start(); // ensures anything dumped out will be caught
			header("Location: add_user.php");
		}
	}
	
	$result=mysql_query("INSERT INTO Location (name, Street_Name, Postal_Code, Longitude, Latitude, image) VALUES ");
?>