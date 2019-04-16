<?php
set_time_limit(300);
require "cron-job-settings.php";
require $server_root."/shared/database.php";

$conn->beginTransaction();  
$conn->exec("LOCK tables buy_order write, item_transaction write, user write");  
$buy_order = select("select * from buy_order where matched = 0 order by id");

foreach($buy_order as $v){
	
	$buy_order_id = $v['id'];
	$item_id = $v['item_id'];
	$quantity = $v['quantity'];
	$buyer_price = $v['price'];
	$buyer_sid = $v['steamid'];
	$buyer_refund = 0;
	
	$sell_order =  select("select id, price, seller_receive, seller_sid from item_transaction where item_id = ? and price <= ? and status = 1 order 
	by price, id limit ? ;", array($item_id,$buyer_price,$quantity));

	if(!empty($sell_order)){
		foreach($sell_order as $v2){
			
			$seller_sid = $v2['seller_sid'];
			$sale_id = $v2['id'];
			$seller_price = $v2['price'];
			$seller_receive = $v2['seller_receive'];
			
			if($buyer_price > $seller_price){
				$buyer_refund += $buyer_price - $seller_price;
			}
			
			$stmt = $conn->prepare("update item_transaction set status = 10, time_transacted = now(), buyer_sid = ? where id = ?;");
			$stmt->execute(array($buyer_sid,$sale_id));		
			
			$stmt = $conn->prepare("update user set credit = credit + ? where steamid = ?;");
			$stmt->execute(array($seller_receive,$seller_sid));
			
			$quantity--;
		}
		
		if($buyer_refund != 0){
			$stmt = $conn->prepare("update user set credit = credit + ? where steamid = ?;");
			$stmt->execute(array($buyer_refund,$buyer_sid));
		}

		if($quantity == 0){
			$stmt = $conn->prepare("delete from buy_order where id = ?;");
			$stmt->execute(array($buy_order_id));
		}
		else{
			$stmt = $conn->prepare("update buy_order set quantity = ? where id = ?;");
			$stmt->execute(array($quantity,$buy_order_id));
		}
	}
}

$sell_order = array();
$buy_order = array();

$sell_order = select("select id, item_id, price, seller_receive, seller_sid from item_transaction where matched = 0 and status = 1 order by id, price desc ;");

foreach($sell_order as $v){
	
	$sale_id = $v['id'];
	$item_id = $v['item_id'];
	$seller_price = $v['price'];
	$seller_receive = $v['seller_receive'];
	$seller_sid = $v['seller_sid'];
	
	$buy_order = select("select * from buy_order where item_id = ? and price >= ? order by id limit 1", array($item_id,$seller_price));
	
	if(!empty($buy_order)){
		$v2 = $buy_order[0];

		$buy_order_id = $v2['id'];
		$item_id = $v2['item_id'];
		$quantity = $v2['quantity'];
		$buyer_price = $v2['price'];
		$buyer_sid = $v2['steamid'];
	
		$stmt = $conn->prepare("update item_transaction set status = 10, time_transacted = now(), buyer_sid = ? where id = ?;");
		$stmt->execute(array($buyer_sid,$sale_id));		
			
		if($buyer_price > $seller_price){
			$stmt = $conn->prepare("update user set credit = credit + ? where steamid = ?;");
			$stmt->execute(array(($buyer_price - $seller_price),$buyer_sid));
		}
			
		$stmt = $conn->prepare("update user set credit = credit + ? where steamid = ?;");
		$stmt->execute(array($seller_receive,$seller_sid));
			
		$quantity--;
			
		if($quantity == 0){
			$stmt = $conn->prepare("delete from buy_order where id = ?;");
			$stmt->execute(array($buy_order_id));
		}
		else
		{
			$stmt = $conn->prepare("update buy_order set quantity = ? where id = ?;");
			$stmt->execute(array($quantity,$buy_order_id));
		}
	}
}

$stmt = $conn->prepare("update item_transaction set matched = 1 where matched = 0 and status = 1;");
$stmt->execute();
$stmt = $conn->prepare("update buy_order set matched = 1 where matched = 0;");
$stmt->execute();
$conn->commit();  