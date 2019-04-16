<?php 

session_start();
require "member-access.php";

require $_SERVER['DOCUMENT_ROOT'].'/shared/database.php';
$error_msg = "";
$output = array("success"=>0);

if(!empty($_POST['message_id']) && !empty($_POST['message'])){
	
	require $_SERVER['DOCUMENT_ROOT']."/../composer/htmlpurifier/library/HTMLPurifier.auto.php";
	$conn->beginTransaction();
	$message_id = $_POST['message_id'];
	$message = $purifier->purify(nl2br($_POST['message']));
	$ticket_id = select("select ticket_id from support_ticket_message where id = ? and poster_sid = ?",array($message_id, $_SESSION['steamid']));
	if(empty($ticket_id)){die;}
	$ticket_id = $ticket_id[0]['ticket_id'];
	$ticket_status = select("select status from support_ticket where id = ? and original_poster_sid = ?",array($ticket_id,$_SESSION['steamid']));
	
	if(empty($ticket_status)){die;}
	if($ticket_status[0]['status'] == 1 || $ticket_status[0]['status'] == 2){goto end;}

	$stmt = $conn->prepare("update support_ticket_message set message = ?, edited = 1 where id = ?;");
	$stmt->execute(array($message,$message_id));	
	$conn->commit();	
	$output = array("success"=>1);
}
end:
echo json_encode($output);