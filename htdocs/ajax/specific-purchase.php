<?php
	// This page proccess purchased item collection request.

	session_start();
	require "member-access.php";
	require "../shared/database.php";
	require "trade-validation.php";
	require "../shared/tools.php";
	
	$output['success'] = 0;
	$output['msg'] = "This item has been taken or it's price has been changed";
	
	/*	
	$_POST['id'] = 141;
	$_POST['assetid'] = 32;
	$_POST['botsid'] = "76561198157332263";
	$_POST['price'] = 1;
	*/

	if(empty($_POST['sale_id']) || empty($_POST['price']))
	{
		die;
	}

	$sale_id = $_POST['sale_id'];
	$price = $_POST['price'];
	$usersid = $_SESSION['steamid'];
	
	if(!validate_int($sale_id) || !validate_currency($price)){
		die;
	}
	$conn->beginTransaction();
	$conn->exec("LOCK TABLES user write, inventory i read, inventory read, buy_order write, item_transaction write,  item_transaction it write,
	trade_transaction write, trade_transaction tt write, trade_transaction_details write, trade_transaction_details ttd write;");
	
	global $user;
		
	if(!generic_validation()){
		goto end;
	}
	
	if($price > $user['credit']){
		$output["msg"] = "You have insufficient funds to purchase this item.";
		goto end;
		die;
	}
	
	$sale_details = select("select it.item_id, it.assetid, it.botsid from inventory i inner join item_transaction it on i.assetid = it.assetid and i.botsid = it.botsid and i.item_id = it.item_id where it.id = ? and it.price = ? and it.status = 1 ;", array($sale_id,$price));

	if(!empty($sale_details)){
		
		$v = $sale_details[0];
		$item_id = $v['item_id'];
		$assetid = $v['assetid'];
		$botsid = $v['botsid'];
	
		$security_token = generate_security_token();
		
		//$stmt = $conn->prepare("update user set credit = credit - ? where steamid= ?;");
		//$stmt->execute(array($price, $usersid));
		
		$stmt = $conn->prepare("update item_transaction set status = 8, buyer_sid = ? where id = ?;");
		$stmt->execute(array($usersid, $sale_id));
		
		$stmt = $conn->prepare("insert into trade_transaction (usersid, botsid, security_token, type, time_start) VALUES ( ?, ?, ?, 3, now()) ;");
		$stmt->execute(array($usersid, $botsid, $security_token));
		$transaction_id = $conn->lastInsertId();
				
		$stmt = $conn->prepare("insert into trade_transaction_details(trade_id, item_transaction_id) VALUES (?, ?) ;");
		$stmt->execute(array($transaction_id, $sale_id));
		
		require $_SERVER['DOCUMENT_ROOT']."/shared/redis.php";
		$redis->rpush($botsid, $transaction_id);
		
		$output['success'] = 1;
		$output['msg'] = "Please review your item before accepting the trade offer. A trade offer will be sent to you shorty using the security token '$security_token'. You may check its offer status under the <a href='manage-orders.php'>manage order</a> page.";
		
	}
	$conn->commit();
	
	end:
	echo json_encode($output);
	

		
