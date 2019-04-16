<?php 
session_start();
if(empty($_SESSION['steamid'])){
	header("Location: /login-required.php");
	die;
}

require $_SERVER['DOCUMENT_ROOT'].'/shared/nav-menu.php';
require $_SERVER['DOCUMENT_ROOT'].'/shared/support-category-code.php';
$error_msg = "";
if(!empty($_POST['title']) && isset($_POST['catergory']) && !empty($_POST['message']) && !empty($_POST['csrf']) && $_SESSION['csrf_token'] == $_POST['csrf']){
	
	//require "/../shared/database.php";
	$ticket_limit = select("select count(*) as count from support_ticket where status = 0 and original_poster_sid = ? limit 1;",array($_SESSION['steamid']));
	
	if($ticket_limit[0]['count'] == 0){
		//session_start();
		require $_SERVER['DOCUMENT_ROOT']."/../composer/htmlpurifier/library/HTMLPurifier.auto.php";
		
		$title = $purifier->purify($_POST['title']);
		$message = $purifier->purify(nl2br($_POST['message']));
		$catergory = $_POST['catergory'];
		if(!array_key_exists($catergory,$support_catergory_code)){die;}
		
		$conn->beginTransaction();
		$stmt = $conn->prepare("insert into support_ticket (title,category,status,original_poster_sid,time_opened) values (?,?,0,?,now())");
		$stmt->execute(array($title,$catergory,$_SESSION['steamid']));	
		$ticket_id = $conn->lastInsertId();
		$stmt = $conn->prepare("insert into support_ticket_message (ticket_id,poster_sid,message,time) values (?,?,?,now())");
		$stmt->execute(array($ticket_id,$_SESSION['steamid'],$message));	
		$conn->commit();
		
		header('Location: /support/ticket-read.php?ticket_id='.$ticket_id);
		die;
	}
	
	$error_msg = "Please close your existing ticket before opening another";
}



$title = "CashifySkins - Support";
$description = "CashifySkins is digtal marketplace where you can trade CS:GO, TF2, Dota 2 and Steam virtual items using real money!";
$css[] = "/css/ticket-new.css";
output_header(); 

?>
<div id='main_wrapper'>
<h1>New Support Ticket</h1>
<form action="ticket-new.php" method="post">
<input name='csrf' value='<?php echo $_SESSION['csrf_token']; ?>' hidden style='display:none;'>
<p><label>Title:</label> <input id='input_title' type="text" maxlength="50" name="title"></p>
<p><label>Catorgery:</label> <select name="catergory"><?php foreach($support_catergory_code as $k=>$v){echo "<option value='$k'>$v</option>";}?></select></p>
<p class="formfield">
    <label for="textarea">Message:</label>
    <textarea id="textarea" placeholder="Message" name="message"></textarea>
</p>
 <?php 	echo "<div class='error_msg'>$error_msg</div>"; ?>
<div class='button_container'><button type="submit">Submit</button></div>
</form>
</div>

<?php footer();?>

</body>
</html>



