<?php
	require "../../shared/database.php";
	require "admin-access.php";
	
	if(!empty($_POST['id'])){
	$transaction_id = $_POST['id'];
	$conn->beginTransaction();
	$stmt = $conn->prepare("update user u inner join cash_transaction ct on ct.steamid = u.steamid set u.credit - ct.amount where ct.id = ? and ct.type = 1");
	$stmt->execute(array($_POST['id']));		
	}