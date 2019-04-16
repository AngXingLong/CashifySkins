<?php

	session_start();
	require "member-access.php";
	require "trade-validation.php";
	require "fetch-steam-inventory.php";
	require "../../settings/currency-config.php";
	require "../shared/database.php";
	require "../shared/tools.php";

	global $listing_fee, $max_listings;
	$output['success'] = 0;
	$max_listings = 100;

	if(!empty($_POST['order']) && !empty($_POST['appid']) && validate_appid($_POST['appid']))
	{	
		global $output,$user,$order,$usersid,$appid,$grand_total,$conn;
		
		$usersid = $_SESSION['steamid'];
		$order = json_decode($_POST['order'],true);
		$appid = $_POST['appid'];
		
		if(!generic_validation()){goto end;}
		
		if(empty($_SESSION['cached_time']) || time() - $_SESSION['cached_time'] > 600){
			fetch_inventory();
			if(empty($_SESSION['cached_assets'])){
				$output['msg'] = "Unable to fetch your inventory please try again.";
				echo json_encode($output);
				return;
			}
		}
		
		//$conn->exec("LOCK TABLES user read, pricelist read, pricelist p read, item_transaction write, item_transaction it write,  trade_transaction write, trade_transaction_details write, bot b read, inventory i read;");
		
		if(!validate_sell_order()){goto end;}
		
		insert_listing();
		
		end:
		echo json_encode($output);
	}
	
	function validate_amount($item_id,$amount){
		foreach($_SESSION['cached_assets'] as $cached_item_id => $asset_list){
			if($cached_item_id == $item_id){
				if(count($asset_list) >= $amount){
					return true;
				}
				break;
			}
		}
	}
	
	function validate_sell_order(){
		
		global $output,$user,$order,$usersid,$grand_total,$conn,$max_listings,$appid,$max_listing_price,$min_listing_price,$bad_trust_max_listing_price;
	
		$listing_count = 0;
		
		$item_name = array();
		
		foreach($order as $v){
			
			if(!validate_int($v['id']) || !validate_int($v['quantity']) || !validate_currency($v['price']) || 0 >= $v['quantity'] || $min_listing_price > $v['price'] || $v['price'] > $max_listing_price){
				return false;
			}
			
			$item_name[] = $v['id'];
			$listing_count += $v['quantity'];
			
			if($user['trust_score'] != 2 && $v['price'] > $bad_trust_max_listing_price){
				$output['msg'] = "You may only list items for below $1 as your trust score is unverified/poor. For more info please read our faq page.";
				return false;
			}
			
			if(!validate_amount($v['id'],$v['quantity'])){
				$output['msg'] = "There are item missing from inventory, please refresh your inventory and update your values";
				return false;
			}
			
		}
		
		$item_count = count($item_name);
		
		if(0 >= count($item_name)){die;}
		$in  = str_repeat('?,', $item_count - 1) . '?';
		$item_name[] = $appid;
		
		$pricelist = select("select count(*) as count from pricelist where id in (".$in.") and appid = ?;",$item_name);
		
		$slots_used = select("select count(*) as count from item_transaction it where seller_sid = ? and status in (0,1,3,8);",array($usersid));
		$slots_used = $slots_used[0]['count'];
		
		if(($slots_used + $listing_count) > $max_listings){
			$output['msg'] = "You allowed to list up to ".$max_listings." items to sell. You currently have ".($max_listings - $slots_used).
			" slots left";
			return false;
		}
		
	
		if($pricelist[0]['count'] !=  $item_count){
			return false;
		}	
	
		return true;
		
	}

	function insert_listing(){
		
		global $conn,$order,$output,$usersid,$type,$user,$listing_fee,$appid,$redis;

		$inventory_limit = array(730=>1000,753=>2400,570=>2400,440=>1000);
		
		$data = select("select b.steamid, ((select count(*) from inventory i join pricelist p on p.id = i.item_id where b.steamid = i.botsid 
		and p.appid = ?) + (select count(*) from trade_transaction left join trade_transaction_details on 
		trade_transaction.id = trade_transaction_details.trade_id where type = 0 and status in (0,1,2))) as slotsused from bot b where b.status = 1 ORDER BY slotsused limit 1;",array($appid));
			
		if(empty($data[0]['steamid']) || !$inventory_limit[$appid] > $data[0]['slotsused']){
			$output["msg"] = "All bots inventory are full for this game type";
			return;
		}
		
		$botsid = $data[0]['steamid'];
		
		try{
			
			$security_token = generate_security_token();
			
			$conn->beginTransaction();
			
			$stmt = $conn->prepare("insert into trade_transaction (usersid, botsid, security_token, time_start) VALUES (?, ?, ?, now()) ;");
			$stmt->execute(array($usersid, $botsid, $security_token));
			$transaction_id = $conn->lastInsertId();
		
			foreach($order as $v){
				
				$item_id = $v['id'];
				$assetid_list = $_SESSION['cached_assets'][$item_id];
				for ($x = $v['quantity']; $x > 0; $x--){
					
					$seller_receive = $v['price'];
					$price = calculate_listing_price($seller_receive,true);
					$stmt = $conn->prepare("insert into item_transaction (item_id, seller_receive, price, seller_sid, assetid, time_in) values (?,?,?,?,?,now()) ;");
					$stmt->execute(array($item_id, $seller_receive, $price, $usersid, $assetid_list[$x-1]));
		
					$sale_id = $conn->lastInsertId();
				
					$stmt = $conn->prepare("insert into trade_transaction_details (trade_id, item_transaction_id) VALUES (?, ?) ;");
					$stmt->execute(array($transaction_id,$sale_id));
					
				}
			}
	
			$redis->rpush($botsid, $transaction_id);
			$_SESSION['last-traded'] = time();
			$conn->commit();
			
			$output["msg"] = "A trade offer will be sent to you shortly, you may check it's status under the <a href='manage-orders.php'>manage order</a> page. Your security token is '$security_token'";
			$output["success"] = 1;

		}
		catch (Exception $e) {
			//echo $e;
			$output["msg"] = "Unable to put you into queue please try again later";
			return;
		}
		unset_inventory_session();
	}
	
