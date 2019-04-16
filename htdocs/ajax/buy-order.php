<?php
	
	session_start();
	require "member-access.php";
	require "../shared/database.php";
	require "../shared/tools.php";
	require "order-matcher.php";
	require "trade-validation.php";
	/*
	session_start();
	$_POST['price'] = 5;
	$_POST['id'] = 141;
	$_POST['quantity'] = 1;
	$_POST['type'] = 2;*/
	//$_POST['id'] = 119;
	//$_POST['price'] = 1;

	$output['success'] = 0;

	if(check_fields(array("id","quantity","price","type"),"post") && validate_int($_POST["quantity"]) && validate_currency($_POST["price"]))
	{
		global $output,$user,$item_id,$quantity,$price,$usersid,$grand_total,$conn;
		$usersid = $_SESSION['steamid'];
		$type = $_POST['type'];
		$item_id = $_POST['id'];
		$quantity = $_POST['quantity'];
		$price = $_POST['price'];

		$conn->exec("LOCK TABLES user write, pricelist read, buy_order write, item_transaction write, trade_transaction read;");

		if(validate_int($type) && generic_validation() && validate_order()){
			if($type == 1){
				buy_order();
			}
			else if($type == 2){
				quick_buy_order();
			}
		}
	}
	
	echo json_encode($output);
	
	function validate_order(){
		
		global $output,$user,$item_id,$quantity,$price,$usersid,$grand_total,$conn;

		if(!validate_int($item_id) || !validate_int($quantity) || !validate_currency($price) || 0 >= $quantity || 0.01 >= $price){
			
			return false;
		}

		$grand_total = $price*$quantity;
	
		$pricelist = select("select count(*) as count from pricelist where id = ?;",array($item_id));

		if(empty($pricelist[0]['count']) || $pricelist[0]['count'] == 0){
			return false;
		}	

		if($grand_total > $user['credit']){
			$output["msg"] = "You do not have sufficient credits to place a buy order.";
			return false;
		}

		return true;
		
	}

	function buy_order(){
		
		global $output,$user,$item_id,$quantity,$price,$usersid,$grand_total,$conn;
		
		$grand_total = 0;
		
		$exist = count_row("select count(*) from buy_order where item_id = ? and steamid = ? limit 1;", array($item_id,$usersid));
		
		if($exist){
			$output['msg'] = "Please closed your existing buy order for this item before opening another.";
			return false;
		}
		if(!generic_validation()){
			return false;
		}
	
		$conn->beginTransaction();
			
		$grand_total += $quantity*$price;
		
		$stmt = $conn->prepare("update user set credit = credit - ? where steamid = ?;");
		$stmt->execute(array($grand_total,$usersid));
		
		$stmt = $conn->prepare("insert into buy_order (item_id, price, quantity, steamid) values (?,?,?,?) ;");
		$stmt->execute(array($item_id, $price, $quantity, $usersid));
		$id = $conn->lastInsertId();
		match_buy_order($id);
		$conn->commit();
		
		$output['success'] = 1;
		$output['msg'] = "Your order has been placed. You may check your status or cancel your order under the <a href='manage-orders.php'>manage order</a> page";	
		return;
		
	}
	
	function quick_buy_order(){
		
		global $output,$user,$item_id,$quantity,$price,$usersid,$conn;
		
		$grand_total = 0;
		
		$id_list = select("select id from item_transaction where item_id = ? and ? >= price and status = 1 order by price, time_in limit 1;",array($item_id,$price));
		
		if(!count($id_list)){
			$output['msg'] = "Sorry this item pricing has changed or has been taken.";
			return;
		}
		
		buy_order();
		return;
	}
	

	
