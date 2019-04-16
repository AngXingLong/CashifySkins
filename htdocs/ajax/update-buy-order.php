<?php
	session_start();
	require "member-access.php";
	require "../shared/database.php";
	require "../shared/tools.php";
	require "order-matcher.php";

	$output['success'] = 0;
	
	if(!empty($_POST['id']) && isset($_POST['quantity']) && isset($_POST['price']) && validate_int($_POST['id']) && validate_int($_POST['quantity']) && validate_currency($_POST['price'])){
		
		$id = $_POST['id'];
		$new_quantity = $_POST['quantity'];
		$new_price = $_POST['price'];
	
		$conn->beginTransaction();
		if($new_quantity > 0 && $new_price > 0.01){
			
			$conn->exec("LOCK TABLES buy_order write, user write, item_transaction write, trade_transaction read;");
			
			$old_buy_order = select("select item_id, quantity, price from buy_order where id = ? and steamid = ?",array($id,$_SESSION['steamid']));
			
			if(empty($old_buy_order)){goto end;}
			
			$item_id = $old_buy_order[0]['item_id'];
			$old_quantity = $old_buy_order[0]['quantity'];
			$old_price = $old_buy_order[0]['price'];
			
			$buyer_refund = ($old_quantity*$old_price) - ($new_quantity*$new_price); //if postive remove funds from account
			
			
			$user = select("select (select COUNT(*) from trade_transaction where usersid = user.steamid and status in (0,1,2) limit 1) as inqueue,
			credit from user where steamid = ?",array($_SESSION['steamid']));
		 
			$user = $user[0];
			
			if($user['inqueue'] > 0){ // To prevent user from having negative account balances
				$output['msg'] = "Please settle your active trade offer before you procceed";
				goto end;
			}
			
			if(0 > $user['credit'] + $buyer_refund){
				$output['msg'] = "You do not have enough funds to purchase this item";
				goto end;
			}
		
			$stmt = $conn->prepare("update user set credit = credit + ? where steamid = ?");
			$stmt->execute(array($buyer_refund,$_SESSION['steamid']));
			
			$stmt = $conn->prepare("update buy_order set quantity = ?, price = ? where id = ?");
			$stmt->execute(array($new_quantity,$new_price,$id));
			
			match_buy_order($id);

			$output['success'] = 1;
		}
		else{

			$conn->exec("LOCK TABLES buy_order write, user write;");
			
			$exist = select("select price, quantity from buy_order where id = ? and steamid = ?",array($id,$_SESSION['steamid']));
			if(empty($exist)){die;}
			
			$stmt = $conn->prepare("update user set credit = credit + ? where steamid = ?");
			$stmt->execute(array($exist[0]['quantity']*$exist[0]['price'],$_SESSION['steamid']));
			
			$stmt = $conn->prepare("delete from buy_order where id = ? and steamid = ?");
			$stmt->execute(array($id,$_SESSION['steamid']));
			
			$output['success'] = 1;
		}
		$conn->commit();
	}
	
	end:
	echo json_encode($output);