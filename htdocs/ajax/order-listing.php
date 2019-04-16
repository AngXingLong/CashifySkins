<?php
	require "../shared/database.php";
		
	$output = "";

	if(empty($_POST['id'])){
		die;
	}
	$item_id = $_POST['id'];
	
	$sell_order = select("select count(*) as quantity, it.price from item_transaction it inner join inventory i on i.botsid = it.botsid and i.assetid = it.assetid and i.item_id = it.item_id where it.item_id = ? and it.status = 1 group by it.price order by 
	it.price limit 5;",array($item_id));
	
	$buy_order = select("select sum(quantity) as quantity, price from buy_order where item_id = ? group by price order by price 
	limit 5",array($item_id));
	
	$sell_total = 0;
	if(count($sell_order) == 5){
		 $price =  end($sell_order);
		 $price = $sell_order['price'];
			
		 $leftover = select("select count(*) as quantity from item_transaction it inner join inventory i on i.botsid = it.botsid and i.assetid = it.assetid and i.item_id = it.item_id where it.item_id = ? and it.price >= ? and it.status = 1 ;",array($item_id,$price));
		 
		 if(!empty($leftover)){
			  $sell_order[4] = array("quantity"=>$leftover[0]['quantity'],"price"=>$price." or less");
		 }
		 
		 $leftover = "";
	}
	
	if(count($buy_order) == 5){
		$price =  end($buy_order);
		$price = $price['price'];
		
		$leftover = select("select sum(quantity) as quantity from buy_order where item_id = ? and price >= ? ",array($name,$appid,$price));

		if(!empty($leftover)){
			  $buy_order[4] = array("quantity"=>$leftover[0]['quantity'],"price"=>$price." or more");
		 }
	}

	$output = array("buy"=>$sell_order,"sell"=>$buy_order);
	echo json_encode($output);
	