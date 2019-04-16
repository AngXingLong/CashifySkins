<?php
	session_start();
	require "admin-access.php";
	require $_SERVER['DOCUMENT_ROOT']."/shared/database.php";
	require $_SERVER['DOCUMENT_ROOT']."/shared/tools.php";
	$output['success'] = 0;
 
	if(!empty($_POST['refund_amount']) && !empty($_POST['invoice_id']) && validate_currency($_POST['refund_amount']) && $_POST['refund_amount'] > 0){
		$refund = $_POST['refund_amount'];
		$invoice_id = $_POST['invoice_id'];
		
		$conn->beginTransaction();
	
		$invoice = select("select IFNULL((select sum(ct2.amount) from cash_transaction ct2 where ct2.refund_id = ct.id),0) as refunded, ct.steamid, ct.amount, ct.node_id, ct.node, u.credit as fund_balance from cash_transaction ct inner join user u on ct.steamid = u.steamid where ct.id = ? and ct.type = 1",array($invoice_id));

		if(empty($invoice) || $refund > $invoice[0]['fund_balance']){
			$output['msg'] = "User has insuffient funds for refund.";
			goto end;
		}
		else if($refund > $invoice[0]['amount'] || $refund > ($invoice[0]['amount'] - $invoice[0]['refunded'])){
			$output['msg'] = "Funds larger than invoice amount";
			goto end;
		}
	

		$refund_steamid = $invoice[0]['steamid'];
		$node_id = $invoice[0]['node_id'];
		$node = $invoice[0]['node'];
		
		$stmt = $conn->prepare("update user set credit = credit - ? where steamid = ?");
		$stmt->execute(array($refund,$refund_steamid));
		
		$stmt = $conn->prepare("insert into cash_transaction (amount,node_id,node,steamid,refund_id,type,status) VALUES (?,?,?,?,?,2,2); ");
		$stmt->execute(array($refund,$node_id,$node,$refund_steamid,$invoice_id));	
		
		$conn->commit();
		$output['success'] = 1;
	}
	end:
	echo json_encode($output);
	