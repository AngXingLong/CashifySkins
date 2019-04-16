<?php 
//disabled
die;
	session_start();
	require "admin-access.php";
	require "../../shared/database.php";
	
	$refund_sender_sid = "76561198157372916";
	
	//$_POST['refunds'] = array(6);
	//$_POST['steamid'] = "76561198075337308";

	$output['success'] = 0;
	
	if(!empty($_POST['refunds']) && !empty($_POST['steamid'])){
		
		$conn->beginTransaction();
		$conn->exec("LOCK TABLES trade_transaction write, trade_transaction_details write, item_transaction write;");
		
		$refund_list = json_decode($_POST['refunds'],1);
		$usersid = $_POST['steamid'];
		
		if(!empty($data)){
			echo json_encode($data[0]);
		}
		$refund_count = count($refund_list);
		$id_range  = str_repeat('?,', $refund_count - 1) . '?';
		$exist_parameters = $refund_list;
		$exist_parameters[]= $_POST['steamid'];
		
		$exist = count_row("select count(*) from item_transaction where id in ($id_range) and seller_sid = ? and status = 1", $exist_parameters);
		
		if($exist == $refund_count){
			$stmt = $conn->prepare("insert into trade_transaction (usersid, botsid, type, status, time_start, time_end) VALUES ( ?, ?, 1, 3, now(), now()) ;");
			$stmt->execute(array($usersid, $refund_sender_sid));
			$transaction_id = $conn->lastInsertId();
			
			$stmt = $conn->prepare("insert into trade_transaction_details(trade_id, item_transaction_id) VALUES (?, ?) ;");
				
			foreach($refund_list as $v){
				$stmt->execute(array($transaction_id,$v));
			}
			
			$stmt = $conn->prepare("update item_transaction set status = 4 where id in ($id_range);");
			$stmt->execute($refund_list);
			$output['success'] = 1;
		}
		
		$conn->commit();
	}
	

	
	echo json_encode($output);