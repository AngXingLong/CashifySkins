<?php
	session_start();
	require "admin-access.php";
	require $_SERVER['DOCUMENT_ROOT']."/shared/database.php";
	
	$output['success'] = 0;
 
	if(!empty($_POST['invoice_id'])){
		$invoice_id = $_POST['invoice_id'];
		
		$conn->beginTransaction();
		$validation = select("select u.credit as fund_balance, ct.amount, ct.steamid from cash_transaction ct inner join user u on ct.steamid = u.steamid where ct.id = ? and ct.type = 1",array($usersid));
		
		if(empty($validation) || $validation[0]["amount"] > $validation[0]["fund_balance"] ){
			die;
		}
		
		$stmt = $conn->prepare("update user set credit = credit - ? where steamid = ?",array($validation[0]["amount"],$validation[0]["steamid"]));
		$stmt->execute(array($refund,$usersid));
		$stmt = $conn->prepare("insert into cash_transaction user set amount = ?, type = 2, status = 2 where steamid = ?");
		$stmt->execute(array($refund,$usersid));	
		$conn->commit();
		$output['success'] = 1;
	}
	
	echo json_encode($output);
	