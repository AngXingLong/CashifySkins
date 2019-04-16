<?php
session_start();
require "member-access.php";
require "../shared/database.php";
require "../shared/tools.php";
require "../../settings/currency-config.php";

$output = array("success"=>0,"msg"=>"There was an error proccessing your request");

$conn->beginTransaction();

if(!empty($_POST['payee'])){
	
	$payee = $_POST['payee'];
	
	if (filter_var($payee, FILTER_VALIDATE_EMAIL)) {
		
		$payee = filter_var($payee, FILTER_SANITIZE_EMAIL);
		
		$ban = count_row("select count(*) from cash_transaction ct inner join ban b on ct.steamid = b.steamid where ct.node = ? limit 1",array($payee));
		
		if($ban){
			$output["msg"] = "Receipient Banned"; 
			goto end;
		}
	
		$stmt = $conn->prepare("update user set paypal_email = ? where steamid = ? ;");
		$stmt->execute(array($payee,$_SESSION['steamid']));
		
		$stmt = $conn->prepare("update cash_transaction set node = ? where steamid = ? and type = 0 and status = 0 limit 1;");
		$stmt->execute(array($payee,$_SESSION['steamid']));
	
		$output["success"] = 1;
		$output["msg"] = "Receipient has been successfully updated";
		
	}
	else{
		$output["msg"] = "Receipient update failed";
	}
	
}
else if(isset($_POST['withdrawal']) && $_POST['withdrawal'] == 0){
	
	$id = select("select id from cash_transaction where steamid = ? and type = 0 and status = 0",array($_SESSION['steamid']));
	
	if(!empty($id[0]["id"])){
		$stmt = $conn->prepare("update cash_transaction set status = 4 where id = ?");	
		$stmt->execute(array($id[0]["id"]));
		$output["success"] = 1;
		$output["msg"] = "Cashout request has been cancelled.";
	}
	
}
else if(isset($_POST['withdrawal']) && validate_currency($_POST['withdrawal']) && $_POST['withdrawal'] >= 5){

	$withdrawal = $_POST['withdrawal'];

	$validation = select ("select credit, IFNULL((select sum(amount) from cash_transaction ct where ct.steamid = u.steamid and ct.type = 1 and ct.status = 2),0) -  IFNULL((select sum(price) from item_transaction it where it.buyer_sid = u.steamid),0) as purchased_funds from user u where u.steamid = ?", array($_SESSION['steamid']));

	if(!empty($validation[0]['purchased_funds'])){
	
		$purchased_funds = 0 > $validation[0]['purchased_funds'] ? 0 : $validation[0]['purchased_funds'];
		$withdrawable_funds = $validation[0]['credit'] - $purchased_funds;
		
		if($withdrawal > $withdrawable_funds){
			$output["msg"] = "Insufficent funds to withdraw.";
			goto end;
		}
		
	}
		
	if($withdrawal > 2000){
		$output["msg"] = "The maximum amount of funds you can withdraw at a time is $2000";
		goto end;
	}
	
	if(5 > $withdrawal){die;}
	
	$id = select("select id from cash_transaction where steamid = ? and type = 0 and status = 0 limit 1",array($_SESSION['steamid']));
	
	if(!empty($id[0]['id'])){
		$stmt = $conn->prepare("update cash_transaction set amount = ?, time = now() where id = ?;");	
		$stmt->execute(array($withdrawal, $id[0]["id"]));
		$output["success"] = 1;
		$output["msg"] = "Withdrawal request successfully updated";
	}
	else{
		$payee = select("select paypal_email, credit from user where steamid = ?",array($_SESSION['steamid']));
		
		if(!empty($payee[0]["paypal_email"])){
			$stmt = $conn->prepare("insert into cash_transaction (steamid, node, amount, type, status, time) values 
			(?, ?, ?, 0, 0, now()) ;");	
			$stmt->execute(array($_SESSION['steamid'],$payee[0]["paypal_email"],$withdrawal));
			$output["success"] = 1;
			$output["msg"] = "Withdrawal request successfully recorded";
		}
		else{
			$output["msg"] = "withdrawal request failed. Please input your receipient paypal email";
		}
	}
	
}
$conn->commit();

if(!empty($output)){
	end:
	echo json_encode($output);
	
}


?>