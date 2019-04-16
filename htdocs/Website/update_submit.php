<?php
	require_once('secret/database.php');
	//Array to store validation errors

	$id = '';
	if(isset($_POST['submit_client'])){
		
		$name = $_POST['name'];
		$contact = $_POST['contact'];
		$address = $_POST['address'];
		$result_update = mysql_query("UPDATE Organisation set Name = '$name', Contact = '$contact', Address = '$address' WHERE Name = '$name' ");
		if (!$result_update){
			die('Could not update data: ' . mysql_error());
		} else{
			//echo "<script>alert('Updated client data successfully!')</script>";
			ob_start(); // ensures anything dumped out will be caught
			header("Location: update_client.php");
		}
	} elseif (isset($_POST['submit_event'])){
		
		$event_name = $_POST['Event_Name'];
		$location_id = $_POST['location_id'];
		$host_id = $_POST['host_id'];
		$time_start = $_POST['time_start'];
		$time_end = $_POST['time_end'];
		$result_update = mysql_query("UPDATE Event set Event_Name = '$Event_Name', Location_ID = '$location_id', Host_ID = '$host_id', Time_Start = '$time_start', Time_End = '$time_end' WHERE ID = '$Event_Name' ");
		if (!$result_update){
			die('Could not update data: ' . mysql_error());
		} else{
			echo "<script>alert('Updated event data successfully!')</script>";
			ob_start(); // ensures anything dumped out will be caught
			header("Location: update_event.php");
		}
	} elseif (isset($_POST['submit_location'])){
		
		$location_name = $_POST['name'];
		$street_name = $_POST['street_name'];
		$postal_code = $_POST['postal_code'];
		$longitude = $_POST['longitude'];
		$latitude = $_POST['latitude'];
		$image = $_POST['photo'];
		$result_update = mysql_query("UPDATE Location set name = '$location_name', Street_Name = '$street_name', Postal_Code = '$postal_code', Longitude = 'longitude', Latitude = 'latitude', image = 'image' WHERE ID = '$name' ");
		if (!$result_update){
			die('Could not update data: ' . mysql_error());
		} else{
			echo "<script>alert('Updated location data successfully!')</script>";
			ob_start(); // ensures anything dumped out will be caught
			header("Location: update_location.php");
		}
	} elseif (isset($_POST['submit_user'])){
	
		$name = $_POST['name'];
		$nric = $_POST['nric'];
		$username = $_POST['username'];
		$staffid = $_POST['staffid'];
		$password = $_POST['password'];
		$role = $_POST['role'];
		$inactive = $_POST['inactive'];
		$photo = $_POST['photo'];
		$organisation_id = $_POST['organisation_id'];
		$result_update = mysql_query("UPDATE UserProfile set Name = '$name', NRIC = '$nric', Username = '$username', StaffID = '$staffid', Password = '$password', Role = '$role', Inactive = '$inactive', Photo = '$photo', organisation_id = '$organisation_id' WHERE ID = '$name' ");
		if (!$result_update){
			die('Could not update data: ' . mysql_error());
		} else{
			echo "<script>alert('Updated user data successfully!')</script>";
			ob_start(); // ensures anything dumped out will be caught
			header("Location: update_user.php");
		}
	}

?>