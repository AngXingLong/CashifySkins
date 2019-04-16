<?php 
	session_start();
	require "admin-access.php";
	require "../../shared/database.php";
	require "../../shared/transaction-code.php";
	
	if(!empty($_POST['steamid'])){
		
		$stmt = $conn->prepare("set time_zone = ?;");
		$stmt->execute(array($_SESSION['time_zone']));	

		$data = select("select u.name, u.credit, IFNULL((select sum(amount) from cash_transaction ct where ct.steamid = u.steamid and ct.type = 1 and status = 2),0) - IFNULL((select sum(price) from item_transaction it where it.buyer_sid = u.steamid),0) as purchased_funds, u.trust_score, u.time_created, b.reason, b.full_reason from user u left join ban b on b.steamid = u.steamid where u.steamid = ?;", array($_POST['steamid']));

		if(!empty($data)){
			echo json_encode($data[0]);
		}
	}