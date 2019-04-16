<?php 
	session_start();
	require "admin-access.php";
	require "../../shared/database.php";
	require "../../shared/transaction-code.php";
	
	$output['success'] = 0;
	
	//$_POST['invoice_id'] = 1;
	//$_POST['steamid'] = "76561198075337308";
	//$_POST['reason'] = "PayPal ChargeBack";
	
	if(!empty($_POST['steamid']) && !empty($_POST['reason'])){
		$conn->beginTransaction();
		$offender_sid = $_POST['steamid'];
		$reason = filter_var($_POST['reason'], FILTER_SANITIZE_STRING);
	
		if($reason == "PayPal ChargeBack"){
			
			$user_paypal = select("select DISTINCT node from cash_transaction where steamid = ?", array($offender_sid));
			
			if(!empty($user_paypal) && !empty($_POST['invoice_id']) && ctype_digit(strval($_POST['invoice_id'])) ){
				
				$exist = count_row("select count(*) from cash_transaction where id = ?",array($_POST['invoice_id']));
				
				if(!$exist){goto end;}
				
				$full_reason = "User was banned to due paypal chargeback on invoice id '".$_POST['invoice_id']."'.";
				
				$parameters = array_column($user_paypal,'node');
				$paypal_range  = str_repeat('?,', count($parameters) - 1) . '?';
			
				$banned_steamid = select("select distinct steamid from cash_transaction where node in ($paypal_range)",$parameters);
				
				$stmt = $conn->prepare("INSERT IGNORE INTO ban (steamid,reason,full_reason,issued_by) VALUES (?,?,?,?);");
				foreach($banned_steamid as $v){
					$stmt->execute(array($v['steamid'], $reason, $full_reason, $_SESSION['steamid']));	
				}
				
				$stmt->execute(array($offender_sid, $reason, $full_reason, $_SESSION['steamid']));	
				
			}
			else{
				goto end;
			}
		}
		else{
			$stmt = $conn->prepare("INSERT IGNORE INTO ban (steamid,reason,full_reason,issued_by) VALUES (?,?,?,?);");
			$stmt->execute(array($offender_sid, $reason,"",$_SESSION['steamid']));	
		}
		$conn->commit();
		$output['success'] = 1;
		
	}
	
	end:
	echo json_encode($output);