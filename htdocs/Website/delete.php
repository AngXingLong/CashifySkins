<?php


require_once('secret/database.php');
	
	if(isset($_POST['delete_client'])){
		$id = $_POST['selector1'];
	}
		$result_delete = mysql_query("DELETE FROM Organisation WHERE Name='$id'");
	
		if (!$result_delete){
			die('Could not delete client: ' . mysql_error());
			
		} else{
			echo "<script>alert('Client deleted successfully!')</script>";
			ob_start(); // ensures anything dumped out will be caught
			header("Location: delete_client.php");
			
		}
	

if(isset($_POST['delete_user'])){
		$ID = $_POST['selector2'];
	}
		$result_delete = mysql_query("DELETE FROM UserProfile WHERE ID='$ID'");
	
		if (!$result_delete){
			die('Could not delete user: ' . mysql_error());
			
		} else{
			echo "<script>alert('User deleted successfully!')</script>";
			ob_start(); // ensures anything dumped out will be caught
			header("Location: delete_user.php");
		}

if(isset($_POST['delete_location'])){
		$ID = $_POST['selector3'];
	}
		$result_delete = mysql_query("DELETE FROM Location WHERE ID='$ID'");
	
		if (!$result_delete){
			die('Could not delete location: ' . mysql_error());
		} else{
			echo "<script>alert('Location deleted successfully!')</script>";
			ob_start(); // ensures anything dumped out will be caught
			header("Location: delete_location.php");
			
		}

if(isset($_POST['delete_event'])){
		$ID = $_POST['selector4'];
	}
		$result_delete = mysql_query("DELETE FROM Event WHERE ID='$ID'");
	
		if (!$result_delete){
			die('Could not delete event: ' . mysql_error());
		} else{
			echo "<script>alert('Event deleted successfully!')</script>";
			ob_start(); // ensures anything dumped out will be caught
			header("Location: delete_event.php");
			
		}		
	
?>