<?php 
session_start();
require "member-access.php";
require $_SERVER['DOCUMENT_ROOT'].'/shared/database.php';

$output = array("success"=>0);

if(!empty($_POST['ticket_id'])){
	
	$conn->beginTransaction();
	$stmt = $conn->prepare("update support_ticket set status = 1,time_closed = now() where id = ? and original_poster_sid = ?");
	$stmt->execute(array($_POST['ticket_id'], $_SESSION['steamid']));	
	$conn->commit();	
	$output = array("success"=>1);
}

end:
echo json_encode($output);