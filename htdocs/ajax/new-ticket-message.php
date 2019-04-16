<?php 

session_start();
require "member-access.php";
require $_SERVER['DOCUMENT_ROOT'].'/shared/support-category-code.php';
require $_SERVER['DOCUMENT_ROOT'].'/shared/database.php';

$output = array("success"=>0);

if(!empty($_POST['ticket_id']) && !empty($_POST['message'])){
	
	require $_SERVER['DOCUMENT_ROOT']."/../composer/htmlpurifier/library/HTMLPurifier.auto.php";
	
	$ticket_id = $_POST['ticket_id'];
	$message = $purifier->purify(nl2br($_POST['message']));
	
	$ticket_status = select("select status from support_ticket where id = ? and original_poster_sid = ?", array($ticket_id,$_SESSION['steamid']));
	
	if(empty($ticket_status)){die;}
	if($ticket_status[0]['status'] == 1 || $ticket_status[0]['status'] == 2){goto end;}
	$conn->beginTransaction();
	$stmt = $conn->prepare("insert into support_ticket_message (ticket_id,message,poster_sid,time) values (?,?,?,now())");
	$stmt->execute(array($ticket_id,$message,$_SESSION['steamid']));	
	$conn->commit();	
	$output = array("success"=>1);

}
end:
echo json_encode($output);