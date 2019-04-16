<?php 

	session_start();
	require "admin-access.php";
	require "../../shared/database.php";

	$output = array("success"=>0);
	$conn->beginTransaction();
	if(!empty($_POST['ticket_id'])){
		
		$stmt = $conn->prepare("update support_ticket set status = 1,time_closed = now() where id = ?");
		$stmt->execute(array($_POST['ticket_id']));		
		$output = array("success"=>1);
	}
	$conn->commit();
	end:
	echo json_encode($output);