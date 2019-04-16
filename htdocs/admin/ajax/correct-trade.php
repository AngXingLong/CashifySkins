<?php
$output['success'] = 0;
session_start();
require "admin-access.php";
require "../../shared/database.php";
require "../../shared/transaction-code.php";

//Corrects trade transaction mistake
//$_POST['id'] = 61;

if(empty($_POST['id'])){
	goto end;
	die;
}

$id = $_POST['id'];

$conn->beginTransaction();
$conn->exec("LOCK TABLES trade_transaction write, trade_transaction tt write, trade_transaction_details write, trade_transaction_details ttd write, item_transaction write, item_transaction it write;");

$data = select("select type, status from trade_transaction where id = ? and botsid <> 1 and status NOT IN (0,1);",array($id));

if(!empty($data)){
	$data = $data[0];
	
	if($data['type'] == 0){
		if($data['status'] != 2){// Deposit does not assume success  || Transaction Completed but not recorded
			$stmt = $conn->prepare("update trade_transaction set status = 3, status_comment = '' where id = ?");
			$stmt->execute(array($id));	
			$stmt = $conn->prepare("update item_transaction it left join trade_transaction_details ttd on ttd.item_transaction_id = it.id set 
			it.status = 1, it.user_specific = 0 where ttd.trade_id = ?;");
			$stmt->execute(array($id));	
		}
	}
	else if($data['type'] == 1){//Transaction not completed but recorded as complete
		$stmt = $conn->prepare("update trade_transaction set status = 5, status_comment = '' where id = ?");
		$stmt->execute(array($id));	
		$stmt = $conn->prepare("update item_transaction it left join trade_transaction_details ttd on ttd.item_transaction_id = it.id set it.status = 1,
		it.time_out = NULL where ttd.trade_id = ?;");
		$stmt->execute(array($id));	
	}
	else if($data['type'] == 2){//Transaction not completed but recorded as complete
		$stmt = $conn->prepare("update trade_transaction set status = 5, status_comment = '' where id = ?");
		$stmt->execute(array($id));	
		$stmt = $conn->prepare("update item_transaction it left join trade_transaction_details ttd on ttd.item_transaction_id = it.id set it.status = 10, 
		it.time_out = NULL where ttd.trade_id = ?;");
		$stmt->execute(array($id));		
	}//Specific Purchase
	else if($data['type'] == 3){//Transaction not completed but recorded as complete
		$stmt = $conn->prepare("update trade_transaction set status = 5, status_comment = '' where id = ?");
		$stmt->execute(array($id));	
		$stmt = $conn->prepare("update item_transaction it left join trade_transaction_details ttd on ttd.item_transaction_id = it.id set it.status = 10, 
		it.time_out = NULL where ttd.trade_id = ?;");
		$stmt->execute(array($id));
	}
	
	$output['success'] = 1;
	
	$conn->commit();
}	

end:
echo json_encode($output);
die;