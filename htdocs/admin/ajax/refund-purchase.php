<?php

session_start();
require "admin-access.php";
require "../../shared/database.php";
require "../../shared/transaction-code.php";
//$_POST['id'] = 6;

$output['success'] = 0;

if(empty($_POST['id'])){
	die;
}

$conn->beginTransaction();
$conn->exec("LOCK TABLES item_transaction write, user write;");

$data = select("select buyer_sid, item_id, price from item_transaction where status = 10 and id = ?",array($_POST['id']));

if(!empty($data)){
	$data = $data[0];
	
	
	$stmt = $conn->prepare("UPDATE item_transaction SET status = 13, time_out = now() WHERE id = ?;");
	$stmt->execute(array($_POST['id']));
	
	$stmt = $conn->prepare("UPDATE user SET credit = credit + ? WHERE steamid = ?;");
	$stmt->execute(array($data['price'], $data['buyer_sid']));
	
	$output['success'] = 1;
}

$conn->commit();


echo json_encode($output);
