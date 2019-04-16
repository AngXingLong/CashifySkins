<?php
	
	
	session_start();
	require "member-access.php";
	require "../shared/database.php";
	
	$output['success'] = 0;
	$max_offer_sentable = 2;
	//$_POST["id"] = json_encode(array(10,11));
	//$_POST["returns"][] = array("item_id"=>8,"seller_receive"=>0.01);
	//$_POST["returns"] = json_encode($_POST["returns"]);
	
	if(!empty($_POST["returns"]))
	{
		$usersid = $_SESSION['steamid'];
		
		if(!empty($_POST['steamid'])){ // Admin use
			require $_SERVER['DOCUMENT_ROOT']."/shared/user-type-code.php";
			if(!empty($_SESSION['account_type']) || array_key_exists($_SESSION['account_type'],$is_staff)){
				$usersid = $_POST['steamid'];
			}
		}

		$conn->exec("LOCK TABLES user read, inventory i read, inventory read, pricelist read, item_transaction write, item_transaction it write, trade_transaction write, trade_transaction tt write, trade_transaction_details write, trade_transaction_details ttd write, bot b read, bot read;");
		
		$inqueue = select("select COUNT(*) as count from trade_transaction where usersid = ? and status in (0,1) limit 1 ",array($usersid));
		
		if($inqueue[0]['count'] > 0){
			$output['msg'] = "Please settle your active trade offers before you procceed";
			goto end;
		}

		$returns = json_decode($_POST["returns"],true);
		$order = array();
		
		foreach($returns as $v){
			
			if(empty($v['seller_receive']) || empty($v['item_id'])){
				die;
			}
			
			$seller_receive = $v['seller_receive'];
			$item_id = $v['item_id'];
			
			$unsorted_order = select("select it.id, it.botsid from item_transaction it inner join inventory i on it.botsid = i.botsid and it.assetid = i.assetid and i.item_id = it.item_id where it.seller_receive = ? and it.item_id = ? and it.seller_sid = ? and it.status = 1;", array($seller_receive,$item_id,$usersid));

			foreach($unsorted_order as $v){
				$order[$v['botsid']][] = $v['id']; 
			}

		}

		if(empty($order)){
			
			$validate_assets = count_row("select count(*) from item_transaction where status = 1 and seller_sid = ?;", array($usersid));
			
			if($validate_assets){
				$output['msg'] = "We are unable to fulfill this request at the moment as your items are in a bad state. Please wait a few hours before trying again. However if this continues to persist please submit a support ticket.";
			}
			else{
				$output['msg'] = "Some of your item selected could have been sold or is in the process of being returned to you";
			}
			
			goto end;
		}
		
		$security_token = generate_security_token();
		
		try{
			require "../shared/redis.php";
			
			$number_of_offers = 0;
	
			$conn->beginTransaction();
			
			foreach($order as $botsid => $v){
				
				$number_of_offers ++;
				$stmt = $conn->prepare("insert into trade_transaction (usersid, botsid, security_token, type, time_start) VALUES ( ?, ?, ?, 1, now()) ;");
				$stmt->execute(array($usersid, $botsid, $security_token));
				$transaction_id = $conn->lastInsertId();
				
				$stmt = $conn->prepare("insert into trade_transaction_details (trade_id, item_transaction_id) VALUES (?, ?) ;");
				
				foreach($v as $v2){
					$stmt->execute(array($transaction_id,$v2));
				}
				
				$stmt = $conn->prepare("update item_transaction it inner join trade_transaction_details ttd on ttd.item_transaction_id = it.id set it.status = 3 where ttd.trade_id = ? ;");
				$stmt->execute(array($transaction_id));
				
				$redis->rpush($botsid, $transaction_id);

				if(count($order) > $max_offer_sentable && $number_of_offers >= $max_offer_sentable){
					break;
				}

			}
			
			$conn->commit();
			
			if(count($order) > $max_offer_sentable ){
				$output["msg"] = "$max_offer_sentable trade offers will be sent to you under the security token '$security_token'. You can collect the rest of your item by resending the same request after you completed your item collection.";
			}
			else{
				$output["msg"] = "You will receive your items in a course of $number_of_offers trade offers under the security token '$security_token'.";
			}
		
			$_SESSION['last-traded'] = time();
			$output["success"] = 1;
		}
		catch (Exception $e) {
			echo $e;
			$output["msg"] = "Unable to put you into queue please try again later";
		}
	}
	
	end:
	echo json_encode($output);
	
	function generate_security_token(){
		$security_token = array("Neo", "Morpheus", "Trinity", "Cypher", "Tank", "Sniper", "Infinity", "Elite", "Goat", "Phoenix", "Vanguard", "Deal");
		$security_token = $security_token[array_rand($security_token , 1 )];
		return $security_token;
	}


		
