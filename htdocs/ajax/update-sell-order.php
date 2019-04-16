<?php

session_start();
require "member-access.php";
require "../shared/database.php";
require "../shared/tools.php";
require "../../settings/currency-config.php";
require "order-matcher.php";

$output['success'] = 0;
//$_POST['data'] = json_encode(array(array('item_id'=>1,'new_price'=>1,'past_price'=>1,'quantity'=>'1')));

if(!empty($_POST['price_data']) && !empty($_POST['item_id']) && !empty($_POST['past_price'])){
	
	$data = json_decode($_POST['price_data'],true);
	$item_id = $_POST['item_id'];
	$past_seller_receive = $_POST['past_price'];
	
	if(!validate_currency($past_seller_receive) || !validate_int($item_id)){
		die;
	}
	
	$trust_score = select("select trust_score from user where steamid = ?",array($_SESSION['steamid']));
	if(!empty($trust_score)){
		$trust_score = $trust_score[0]['trust_score'];
	}
	else{
		$trust_score = 0;
	}
	
	$conn->beginTransaction();
	$conn->exec("LOCK TABLES buy_order write, user write, item_transaction write;");
	
	foreach($data as $v){
		
		if(empty($v['new_price']) || empty($v['quantity'])){
			die;
		}
		
		$seller_receive = $v['new_price'];
		$quantity = $v['quantity'];

		if(validate_currency($seller_receive) && validate_int($quantity) && $quantity > 0 && $seller_receive >= $min_listing_price && $max_listing_price >= $seller_receive){
			
			if($trust_score != 2 && $seller_receive > $bad_trust_max_listing_price){
				$output['success'] = 0;
				$output['msg'] = "You may only list items for below $1 as your trust score is unverified/poor. For more info please read our faq page.";
				goto end;
			}
			
			$price = calculate_listing_price($seller_receive,true);
			
			$stmt = $conn->prepare("update item_transaction set seller_receive = ?, price = ? where seller_sid = ? and item_id = ? and seller_receive = ? and status = 1 limit ?");
			$stmt->execute(array($seller_receive, $price, $_SESSION['steamid'], $item_id, $past_seller_receive, $quantity));
			match_sell_order($_SESSION['steamid'],$item_id);
		}
		else
		{
			die;
		}
		
	}
	$conn->commit();
	$output['success'] = 1;
	
}

end:
echo json_encode($output);