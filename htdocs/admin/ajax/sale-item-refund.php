<?php 
	session_start();
	require "admin-access.php";
	require "../../shared/database.php";
	
	$refund_sender_sid = "76561198157372916";
	
	$output['success'] = 0;
	
	if(!empty($_POST['id'])){
		
		$conn->beginTransaction();
		$conn->exec("LOCK TABLES item_transaction write, item_transaction it write, user write, user u write;");
	
		$stmt = $conn->prepare("update item_transaction it inner join user u on it.seller_sid = u.steamid set u.credit = u.credit + it.seller_receive, it.status = 12, it.buyer_sid = ? where it.id = ? and it.status = 1;");
		$stmt->execute(array($refund_sender_sid,$_POST['id']));
		$output['success'] = 1;
	
		$conn->commit();
	}
	
	echo json_encode($output);