<?php 

	session_start();
	require "admin-access.php";
	require "../../shared/database.php";
	
	$output = array("success"=>0);
	
	$conn->beginTransaction();
	$conn->exec("LOCK TABLES support_ticket write");	
	
	$validation = select("select staff_assigned_sid from support_ticket where id = ?",array($_POST['ticket_id']));
	if($validation[0]['staff_assigned_sid'] == 0){
		$stmt = $conn->prepare("update support_ticket set staff_assigned_sid = ? where id = ?");
		$stmt->execute(array($_SESSION['steamid'],$_POST['ticket_id']));	
		$output['success'] = 1;
	}
	$conn->commit();
	echo json_encode($output);