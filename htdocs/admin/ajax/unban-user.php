<?php

	session_start();
	require "admin-access.php";
	require "../../shared/database.php";
	require "../../shared/transaction-code.php";
	$output['success'] = 0;
	//$_POST['paypal_email'] = "angxinglong@gmail.com";
	//$_POST['steamid'] = "76561198075337308";
	$conn->beginTransaction();
	if(!empty($_POST['steamid'])){
		
		$stmt = $conn->prepare("delete from ban where steamid = ?;");
		$stmt->execute(array($_POST['steamid']));	
		$output["success"] = 1;
		
	}
	else if(!empty($_POST['paypal_email'])){
		
		$data = select("select steamid from user where paypal_email = ?",array($_POST['paypal_email']));
		$data = array_column ($data,"steamid");
		
		$in  = str_repeat('?,', count($data) - 1) . '?';
		
		$stmt = $conn->prepare("delete from ban where steamid in ($in);");
		$stmt->execute($data);	
		
		$output["success"] = 1;
		
	}
	$conn->commit();
	
	echo json_encode($output);