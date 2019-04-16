<?php
//disabled 
die;
	require "../../shared/database.php";
	require "../../shared/transaction-code.php";
	require "admin-access.php";
	
	$output['success'] = 0;
 
	if(!empty($_POST['steamid']) && !empty($_POST['fund'])){
		$stmt = $conn->prepare("update user set credit = ? + credit where steamid = ?");
		$stmt->execute(array($_POST['fund'],$_POST['steamid']));	
		$output['success'] = 1;
	}
	
	echo json_encode($output);
	