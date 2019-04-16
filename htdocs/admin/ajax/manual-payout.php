<?php
	session_start();
	require "admin-access.php";
	require $_SERVER['DOCUMENT_ROOT']."/shared/database.php";
	require $_SERVER['DOCUMENT_ROOT']."/shared/tools.php";
	$output['success'] = 0;
	
	if(!empty($_POST['payout_amount']) && !empty($_POST['steamid']) && validate_currency($_POST['payout_amount']) && $_POST['payout_amount'] > 0){
		
		$conn->beginTransaction();
		$conn->exec("lock tables user write,cash_transaction write");
		$payout = $_POST['payout_amount'];
		$steamid = $_POST['steamid'];
		
		$user_details = select("select credit, paypal_email from user  where steamid = ?;",array($steamid));
		
		if(empty($user_details[0]['credit']) || $payout > $user_details[0]['credit']){
			$output['msg'] = "User payout larger than fund balance";
			goto end;
		}
		
		if(empty($user_details[0]['paypal_email'])){
			$output['msg'] = "User recipient not set";
			goto end;
		}
		
		$stmt = $conn->prepare("update user set credit = credit - ? where steamid = ?");
		$stmt->execute(array($payout,$steamid));	
		
		$stmt = $conn->prepare("insert into cash_transaction (steamid,amount,node,type,time,status) values (?,?,?,0,now(),2)");
		$stmt->execute(array($steamid,$payout,$user_details[0]['paypal_email']));	
		$conn->commit();
		$output['success'] = 1;
			
	}
	end:
	echo json_encode($output);