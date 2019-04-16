<?php
	session_start();
	require "admin-access.php";
	require $_SERVER['DOCUMENT_ROOT']."/shared/database.php";
	
	$output['success'] = 0;
 
	if(!empty($_POST['refund']) && !empty($_POST['invoice_id'])){
		$refund = $_POST['refund'];
		$invoice_id = $_POST['invoice_id'];
		
		$conn->beginTransaction();
	
		$invoice = select("select ct.steamid, ct.amount, ct.node_id, ct.node, u.credit as fund_balance from cash_transaction ct inner join user u where ct.id = ?",array($invoice_id));
		
		if(empty($invoice) || $refund > $invoice[0]['fund_balance'] || $refund > $invoice[0]['amount'] ){
			die;
		}

		$refund_steamid = $invoice[0]['steamid'];

		$stmt = $conn->prepare("update user set credit = credit - ? where steamid = ?");
		$stmt->execute(array($refund,$refund_steamid));
		
		$stmt = $conn->prepare("insert into cash_transaction user set amount = ?, node_id = ?, node = ?, type = 2, status = 2 where steamid = ?");
		$stmt->execute(array($refund,$invoice[0]['node_id'],$invoice[0]['node'],$refund_steamid));	
		
		$conn->commit();
		$output['success'] = 1;
	}
	
	echo json_encode($output);
	