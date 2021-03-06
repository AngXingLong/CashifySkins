<?php 
require 'shared/nav-menu.php';
require $_SERVER['DOCUMENT_ROOT']."/shared/support-category-code.php";
require $_SERVER['DOCUMENT_ROOT']."/shared/user-type-code.php";

$title = "CashifySkins - Support";
$description = "CashifySkins is digtal marketplace where you can trade CS:GO, TF2, Dota 2 and Steam virtual items using real money!";
$js[] = "/js/tinymce/tinymce.min.js";
$css[] = "/css/ticket-read.css";
output_header(); 
$ticket_id = 0;
$empty_ticket = true;
echo "<div id='basic_content_wrapper'>";
if(!empty($_GET['ticket_id']) && ctype_digit(strval($_GET['ticket_id']))){
	$ticket_id = $_GET['ticket_id'];
	$ticket_details = select("select *, (select name from user u where u.steamid = st.staff_assigned_sid ) as staff_name from support_ticket st where id = ? ",array($ticket_id));
	$ticket_messages = select("select tr.id, tr.poster_sid, u.name, u.type, tr.message, DATE_FORMAT(tr.time,'%h:%i %p, %d %b %Y ') as time, tr.edited from support_ticket_message tr left join user u on u.steamid = tr.poster_sid where tr.ticket_id = ? order by id ",array($ticket_id));
	
	if(!empty($ticket_details)){
		$empty_ticket = false;
		$title = $ticket_details[0]['title'];
		$category = $support_catergory_code[$ticket_details[0]['category']];
		$status = $ticket_details[0]['status'];
		$poster = $ticket_details[0]['original_poster_sid'];
		$staff_assigned_sid = $ticket_details[0]['staff_assigned_sid'];
		$staff_name = $ticket_details[0]['staff_name'];
		$ticket_claim_button = "";
		$close_ticket_button = "";
		if($status == 0){
			$close_ticket_button = "<button id='close_ticket' class='button_theme' onClick='close_ticket()'>Close Ticket</button";
			if(empty($staff_assigned_sid)){
			$ticket_claim_button = "<button id='get_poster_details_button' class='manage_ticket_button' onClick='claim_ticket()'>Claim Ticket</button>";
				}else if($staff_assigned_sid == $_SESSION['steamid']){
			$ticket_claim_button = "<button id='get_poster_details_button' class='manage_ticket_button' onClick='unclaim_ticket()'>Unclaim Ticket</button>";
			}
		}
		
		
		
		
		echo "<h1>$title</h1>";
		echo "<span class='ticket_details_label'>Catogery: </span> <span class='ticket_details'>$category</span><br>";
		echo "<span class='ticket_details_label'>Ticket No: </span> <span class='ticket_details'>$ticket_id</span><br>";
		echo "<span class='ticket_details_label'>Staff Assigned: </span> <span class='ticket_details'>$staff_name</span><br>";
		echo "<div class='message_title'><span>Ticket Question Message</span> <span> <input id='get_poster_details_button' type='button' class='manage_ticket_button' onclick=\"location.href='/admin/transactions.php?steamid=$poster';\" value='Poster Transactions'/><input id='get_poster_details_button' type='button' class='manage_ticket_button' onclick=\"location.href='/admin/accounts.php?steamid=$poster';\" value='Poster Details'/>$ticket_claim_button $close_ticket_button</span></div>";
		foreach($ticket_messages as $k=>$v){
			$poster_name = $v['name'];
			$poster_message = $v['message'];
			$time_created = $v['time'];
			$edited = $v['edited'];
			$user_type = $v['type'];
			$message_id = $v['id'];
			$poster_sid = $v['poster_sid'];
			if($user_type != 0){
				$user_type = $user_type_code[$user_type];
				$user_type = "<span class='user_role'>[$user_type]</span>";
			}else{
				$user_type = "";
			}
			
			if($edited){
				$edited = "<span class='ticket_msg_edited'>*</span>";
			}else{
				$edited = "";
			}
			$edit = ""; 
			if($poster_sid == $_SESSION['steamid'] && $status == 0){
				$edit = "<div class='message_actions' onClick='edit_message($k,$message_id)'>Edit</div>";
			}
			
			echo "<div class='message_container'>";
			echo "<div class='message_header'> <div class='message_details'>$user_type $poster_name <span class='time'>$time_created$edited</span></div>$edit</div>";
			echo "<div id='message_$k'class='message'>$poster_message</div>";
			echo "</div>";
		}
		
		if($status == 0 ){
			echo "<textarea placeholder='Enter New Message' id='new_message_input'></textarea><br>";
		echo "<div id='error_msg'></div>";
		echo "<button class='button_theme' onClick='new_message()'>Submit</button> ";
		}
		else if($status == 1){
			echo "This ticket has been closed.";
		}
		else{
			echo "This ticket has been locked";
		}	
		echo "<div class='spacing'></div>";

	}
	
}

if($empty_ticket){
	echo "<h1>Error</h1>";
	echo "<h2>Ticket does not exist or you do not have permission to view it</h2>";
}

echo "</div>";

?>

<script>
var ticket_id = <?php echo $ticket_id; ?>;

function claim_ticket(){
	$.ajax({
	 url: "ajax/claim-ticket.php",
	 type:'POST',
	 data: {
         "ticket_id":ticket_id,
     },
	 success: function(r){
		if(r){
			r = JSON.parse(r);
			if(r['success']){
				location.reload();
			}else{
				create_notification("Error","There was an error processing your request");
			}
		}else{
			create_notification("Error","There was an error processing your request");
		}
     },
	 error: function(){
		create_notification("Error","There was an error processing your request");
	 },
	});
}
function unclaim_ticket(){
	$.ajax({
	 url: "ajax/unclaim-ticket.php",
	 type:'POST',
	 data: {
         "ticket_id":ticket_id,
     },
	 success: function(r){
		if(r){
			r = JSON.parse(r);
			if(r['success']){
				location.reload();
			}else{
				create_notification("Error","There was an error processing your request");
			}
		}else{
			create_notification("Error","There was an error processing your request");
		}
     },
	 error: function(){
		create_notification("Error","There was an error processing your request");
	 },
	});
}
function close_ticket(){
	$.ajax({
	 url: "/admin/ajax/close-ticket.php",
	 type:'POST',
	 data: {
         "ticket_id":ticket_id,
     },
	 success: function(r){
		if(r){
			r = JSON.parse(r);
			if(r['success']){
				location.reload();
			}else{
				create_notification("Error","There was an error processing your request");
			}
		}else{
			create_notification("Error","There was an error processing your request");
		}
     },
	 error: function(){
		create_notification("Error","There was an error processing your request");
	 },
	});
}
function edit_message(message_row,message_id){

	var message = document.getElementById("message_"+message_row).innerHTML;
	message = message.replace(/(<br>)*/g,"");
	create_notification("Edit Message","");
	document.getElementById("notification").className = "edit_message_wrapper";
	document.getElementById("notification_header").className = "edit_message_header_wrapper";
	document.getElementById("notification_body").innerHTML = "<textarea>"+message+"</textarea>";
	document.getElementById("notification_body").className = "edit_message_text_wrapper";
	document.getElementById("notification_footer").className = "edit_message_button_wrapper";
	document.getElementById("notification_footer").innerHTML = "<button class='notifcation_button' onClick='submit_edit_message("+message_id+")'>Submit</button>";
	//var message = document.getElementById("message_"+message_index).value;
	
	
}

function submit_edit_message(message_id){

	var message = document.getElementById("notification_body").getElementsByTagName("textarea")[0].value;

	$.ajax({
	 url: "/ajax/edit-ticket-message.php",
	 type:'POST',
	 data: {
         "message_id":message_id,
		 "message":message,
     },
	 success: function(r){
		if(r){
			r = JSON.parse(r);
			if(r['success']){
				location.reload();
			}else{
				create_notification("Error","There was an error processing your request");
			}
		}else{
			create_notification("Error","There was an error processing your request");
		}
     },
	 error: function(){
		create_notification("Error","There was an error processing your request");
	 },
	});
}



function new_message(){
	var message = document.getElementById("new_message_input").value;

	$.ajax({
	 url: "/ajax/new-ticket-message.php",
	 type:'POST',
	 data: {
         "ticket_id":ticket_id,
		 "message":message,
     },
	 success: function(r){
		if(r){
			r = JSON.parse(r);
			if(r['success']){
				location.reload();
			}else{
				create_notification("Error","There was an error processing your request");
			}
		}else{
			create_notification("Error","There was an error processing your request");
		}
     },
	 error: function(){
		create_notification("Error","There was an error processing your request");
	 },
	});
}


</script>

<?php footer();?>

</body>
</html>



