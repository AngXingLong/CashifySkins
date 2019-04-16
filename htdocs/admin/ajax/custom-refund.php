<?php
	session_start();
	require "admin-access.php";
	require $_SERVER['DOCUMENT_ROOT']."/shared/database.php";
	
	$output['success'] = 0;
 
	if(!empty($_POST['steamid']) && !empty($_POST['refund'])){
		$usersid = $_POST['steamid'];
		$refund = $_POST['refund'];
		
		$conn->beginTransaction();
		$validation = select("select credit from user where steamid = ?",array($usersid));
		if(empty($validation) || $refund > $validation[0]['credit']){
			die;
		}
		
		$stmt = $conn->prepare("update user set credit = credit - ? where steamid = ?");
		$stmt->execute(array($refund,$usersid));
		$stmt = $conn->prepare("insert into cash_transaction user set amount = ?, type = 2, status = 2 where steamid = ?");
		$stmt->execute(array($refund,$usersid));	
		$conn->commit();
		$output['success'] = 1;
	}
	
	echo json_encode($output);
	