<?php

session_start();
require "member-access.php";
require "../shared/database.php";

$output['success'] = 0;

$id = $_POST['id'];

$conn->beginTransaction();
$conn->exec("LOCK TABLES buy_order write, user write;");

$exist = select("select price, quantity from buy_order where id = ? and steamid = ?",array($id,$_SESSION['steamid']));
if(empty($exist)){die;}

$stmt = $conn->prepare("update user set credit = credit + ? where steamid = ?");
$stmt->execute(array($exist[0]['quantity']*$exist[0]['price'],$_SESSION['steamid']));

$stmt = $conn->prepare("delete from buy_order where id = ? and steamid = ?");
$stmt->execute(array($id,$_SESSION['steamid']));
$success = $stmt->rowCount();
$conn->commit();

if($success){
	$output['success'] = 1;
	$output['msg'] =  "Your buy order has been deleted";
}else{
	$output['msg'] =  "Unable to delete buy order";
}

echo json_encode($output);