<?php 

	
	function match_buy_order($buy_order_id){
		global $conn;
		$buy_order = select("select item_id, quantity, price, steamid from buy_order where id = ?",array($buy_order_id));
		
		if(!empty($buy_order)){
			$v = $buy_order[0];
			$item_id = $v['item_id'];
			$quantity = $v['quantity'];
			$buyer_price = $v['price'];
			$buyer_sid = $v['steamid'];
			$buyer_refund = 0;
				
			$sell_order =  select("select id, price, seller_receive, seller_sid from item_transaction where item_id = ? and price <= ? and status = 1 order by price, id limit ?;", array($item_id,$buyer_price,$quantity));
		
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
					
				if($buyer_refund > 0){
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

	}
	
	function match_sell_order($seller_sid,$item_id){ // for change in item_price
		global $conn;
		$sell_order = select("select id, price, seller_receive from item_transaction where status = 1 and seller_sid = ? and item_id = ? order by id, price desc;",array($seller_sid,$item_id));
		
		$total_earnings = 0;
		
		foreach($sell_order as $v){
			$sale_id = $v['id'];
			$seller_price = $v['price'];
			$seller_receive = $v['seller_receive'];
			
			$buy_order = select("select * from buy_order where item_id = ? and price >= ? order by id limit 1", array($item_id,$seller_price));
			
			if(empty($buy_order)){continue;}
			$v2 = $buy_order[0];
			$buy_order_id = $v2['id'];
			$quantity = $v2['quantity'];
			$buyer_price = $v2['price'];
			$buyer_sid = $v2['steamid'];
			
			$stmt = $conn->prepare("update item_transaction set status = 10, time_transacted = now(), buyer_sid = ? where id = ?;");
			$stmt->execute(array($buyer_sid,$sale_id));		
				
			if($buyer_price > $seller_price){
				$stmt = $conn->prepare("update user set credit = credit + ? where steamid = ?;");
				$stmt->execute(array(($buyer_price - $seller_price),$buyer_sid));
			}
				
			$total_earnings += $seller_receive;
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
		
		if($total_earnings > 0){
			$stmt = $conn->prepare("update user set credit = credit + ? where steamid = ?;");
			$stmt->execute(array($total_earnings,$seller_sid));
		}
		
	}