<?php 
	session_start();
	require "admin-access.php";
	require "../../shared/database.php";
	require "../../shared/transaction-code.php";
	require $_SERVER['DOCUMENT_ROOT']."/shared/redis.php";
	
	if(!empty($_POST['steamid']) && ctype_digit(strval($_POST['steamid']))){
		$validation = select("select count(*) as count from bot where steamid = ? and status in (1,2)", array($_POST['steamid']));
		if(!empty($validation) && $validation[0]['count'] == 1){
			
			//$stmt = $conn->prepare("UPDATE bot SET status = 2 WHERE steamid = ?");
			//$stmt->execute(array($_POST['steamid']));
			$conn->beginTransaction();
			$stmt = $conn->prepare("update bot set expected_status = 0 where steamid = ?");
			$stmt->execute(array($_POST['steamid']));	
			$conn->commit();
			$redis->publish("bot manager",json_encode(array("process"=>0,"steamid"=>$_POST['steamid'])));
			echo json_encode(array('success'=>1));
		}
	}