<?php

	require "../shared/database.php";
	
	global $botlist;
	$botlist = json_decode(file_get_contents("settings.json"),1);
	$botlist = $botlist['Bots'];

	$conn->exec("LOCK TABLES user write, trade_transaction write, trade_transaction tt write, trade_transaction_details write, 
	trade_transaction_details ttd write, pricelist read, buy_order write, orders write, orders o write;");
	
	$escrow_transactions = select("select id, botsid, offer_id from trade_transaction where status = 5 where time_end >= (CURDATE() - INTERVAL 3 DAY )");


	foreach($escrow_transactions as $v){
		
		foreach($botlist as $v2){
			if($v2['SteamID'] == $v['steamid']){
				$api_key = $v2['ApiKey'];
				break;
			}
		}
		
		$trade_id = $v['id'];
		$offer_id = $v['offer_id'];
	
		$url = "http://api.steampowered.com/IEconService/GetTradeOffer/v1/?key=$api_key&tradeofferid=$offer_id";
		
		$response = file_get_contents($url);
		
		$response = json_decode($response,1);
		
		$offer_state = $response['response']['offer']['trade_offer_state'];
		
		if($offer_state == 11){
			continue;
		}
		else if ($offer_state == 6 || $offer_state == 7){
			
			$stmt = $conn->prepare("update trade_transaction set status = 3, status_comment = 'User Canceled Offer', staff_comment = '' 
			where id = ?");
			$stmt->execute(array($trade_id));
			
			$stmt = $conn->prepare("update orders o left join trade_transaction_details ttd on ttd.order_id = o.id set o.status = 6, 
			where ttd.trade_id = ?");
			$stmt->execute(array($trade_id));
						
		}else{
			
			
			$conn->beginTransaction();
			
			$item_sell_list = select("select item_id, seller_receive, price, count(*) as quantity from orders o left join trade_transaction_detail 
			ttd on ttd.order_id = o.id where ttd.trade_id = ? group by item_id , price",array($order_id));
			
			$seller_receive_total = 0;
			
			foreach($item_sell_list as $v2){
				
				$s_quantity = $v2['quantity'];
				$item_id = $v2['item_id'];
				$s_price = $v2['price'];
				$seller_receive = $v2['seller_receive'];
				
				$buy_order = select("select id, quantity, price, steamid from buy_order where item_id = ? and price >= ? order by time limit ?;",
				array($item_id,$price,$s_quantity));
				
				foreach($buy_order as $v3){
					
					$id = $v3['id'];
					$b_quantity = $v3['quantity'];
					$buyer_sid = $v3['steamid'];
					$b_price = $v3['price'];
					
					if($b_quantity > $s_quantity){
						
						$stmt = $conn->prepare("update buy_order set quantity = quantity - ? where id = ?;");
						$stmt->execute(array($s_quantity,$v3['id']));	
						
						$stmt = $conn->prepare("update orders o left join trade_transaction_details ttd on ttd.orderid = o.id set buyer_sid = ?, 
						status = 10 where o.item_id = ? and ttd.trade_id = ? ;");
						$stmt->execute(array($buyer_sid, $item_id, $trade_id));			
						
						$seller_receive_total += $seller_receive * $s_quantity;

                        if ($b_price > $s_price)
                        {
							 $refund = ($b_price - $s_price) * $s_quantity;
							 $stmt = $conn->prepare("update user set credit = credit + ? where steamid = ?;");
							 $stmt->execute(array($refund, $buyer_sid));	
                        }
						
						$s_quantity = 0;
						
					}
					else{
						
						$stmt = $conn->prepare("delete from buy_order where id = ?;");
						$stmt->execute(array($id));	
						
						$stmt = $conn->prepare("update orders o left join trade_transaction_details ttd on ttd.orderid = o.id set buyer_sid = ?,
						status = 10 where o.item_id and ttd.trade_id = ? limit ?;");
						$stmt->execute(array($buyer_sid, $item_id, $trade_id, $b_quantity));
						
						if ($b_price > $s_price)
                        {
							 $refund = ($b_price - $s_price) * $s_quantity;
							 $stmt = $conn->prepare("update user set credit = credit + ? where steamid = ?;");
							 $stmt->execute(array($refund, $buyer_sid));	
                        }
						
						$seller_receive_total += $seller_receive * $b_quantity;
                      	$s_quantity -= $b_quantity;
						
					}
					
					if ($s_quantity == 0) { break; }
				}
			}
	
			 $stmt = $conn->prepare("update orders o left join trade_transaction_details ttd on ttd.order_id = o.id set status = 7 where 
			 ttd.trade_id = ? and status = 1;");
			 $stmt->execute(array($trade_id));
			 
			 $stmt = $conn->prepare("update user set credit = credit + ? where steamid = ?");
			 $stmt->execute(array($seller_receive_total, $seller_sid));	
			 $conn->commit();
			 
			 $conn->exec("UNLOCK Tables;");

		}
	}
				
				
			
			
			
		

                


