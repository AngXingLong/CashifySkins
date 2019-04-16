<?php
	//Start session
	session_start();
	header("Access-Control-Allow-Origin: *");
 
	//Include database connection details
	require_once('secret/database.php');
 
	//Array to store validation errors
	$errmsg_arr = array();
 
	//Validation error flag
	$errflag = false;
 
	//Function to sanitize values received from the form. Prevents SQL injection
	function clean($str) {
		$str = @trim($str);
		if(get_magic_quotes_gpc()) {
			$str = stripslashes($str);
		}
		return mysql_real_escape_string($str);
	}
 
	//Sanitize the POST values
	$username = clean($_POST['username']);
	$password = clean($_POST['password']);
 
	//Input Validations
	if($username == '') {
		$errmsg_arr[] = 'Username missing';
		$errflag = true;
	}
	if($password == '') {
		$errmsg_arr[] = 'Password missing';
		$errflag = true;
	}
 
	//If there are input validations, redirect back to the login form
	if($errflag) {
		$_SESSION['ERRMSG_ARR'] = $errmsg_arr;
		session_write_close();
		header("location: index.php");
		exit();
	}
 
	//Create query
	$qry="SELECT * FROM UserProfile WHERE username='$username' AND password=PASSWORD('$password')";
	$result=mysql_query($qry);
 
	//Check whether the query was successful or not
	if($result) {
		if(mysql_num_rows($result) > 0) {
			//Login Successful
			//session_regenerate_id();
			$row = mysql_fetch_assoc($result);
			$log_id = $row['ID'];
			mysql_query("INSERT INTO Login_Log (user_id) VALUES ('$log_id')");
			$_SESSION['SESS_ID'] = $row['ID'];
			$_SESSION['SESS_NAME'] = $row['Username'];
			//$_SESSION['SESS_NAME'] = $row['Password'];
			
			
			session_write_close();
			header("location: home.php");
			exit();
			  

		}else {
			//Login failed
			$errmsg_arr[] = 'Invalid User name or Password!';
			$errflag = true;
			if($errflag) {
				$_SESSION['ERRMSG_ARR'] = $errmsg_arr;
				session_write_close();
				header("location: index.php");
				exit();
			}
		}
	}else {
		die("Query failed");
	}
?>