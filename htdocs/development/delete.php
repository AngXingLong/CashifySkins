<?php 
	require "../shared/database.php";
	$conn->beginTransaction();
	$stmt = $conn->prepare("TRUNCATE TABLE trade_transaction;");
	$stmt->execute();	
	$stmt = $conn->prepare("TRUNCATE TABLE trade_transaction_details;");
	$stmt->execute();		
	$stmt = $conn->prepare("TRUNCATE TABLE pricelist;");
	$stmt->execute();	
	$stmt = $conn->prepare("TRUNCATE TABLE inventory;");
	$stmt->execute();	
	$stmt = $conn->prepare("TRUNCATE TABLE buy_order;");
	$stmt->execute();	
	$stmt = $conn->prepare("TRUNCATE TABLE item_transaction;");
	$stmt->execute();	
	$conn->commit();
