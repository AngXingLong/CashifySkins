<?php
require "shared/nav-menu.php";
require $_SERVER['DOCUMENT_ROOT']."/shared/trust-score-code.php";
$staff_type = array("Undefined","Moderator","Admin","Support Staff");
$title = "Manage Account - CashifySkin";
$css[] = "css/accounts.css";  
output_header();
$steamid = "";
$js_action = "";
if(!empty($_GET['steamid'])){
	$steamid = $_GET['steamid'];
	$js_action = "fetch_user_info()";
}

?>

<div id='basic_content_wrapper'>
<h1>User Info</h1>

<?php echo "<input ID='steamid' value='$steamid' placeholder='Enter SteamID'><button id='submit_button' class='button_theme' onclick='fetch_user_info()'>Submit</button><br><br>"; ?>

<div id='content'>
</div>
</div>

<script>

var trust_score_code  = <?php echo json_encode($trust_score_code); ?>;
var row_count = 0;
var table_display_data;
var staff_type = <?php echo json_encode($staff_type);?>;
<?php echo $js_action;?> 
function fetch_user_info(){
	var steamid = document.getElementById("steamid").value;
	
	 $.ajax({
	 url: "ajax/user-info.php",
	 type:'POST',
	 data: {
		 "steamid":steamid
     },
	 success: function(r){
		 if(r){
			r = JSON.parse(r);
			display_user(r);
		 }else{
			document.getElementById("content").innerHTML = "<div class='user_info_wrapper'>No matching results</div>";
		 }
     },
	 error: function(r){

	 },
	 timeout: 50000
	});
}

function display_user(r){
	var steamid = document.getElementById("steamid").value;
	
	var display_content = document.getElementById("content");

	var banned = "";
	var banned;
	if(r['reason'] != null){
		banned = "Yes";
	}else{
		banned = "No";
	}
	var ban_catorgory = r['reason'];
	var ban_full_reason = r['full_reason'];
	
	var credit = r['credit'];
	var purchased_funds = r['purchased_funds'];
	if(0 > purchased_funds){
		purchased_funds = 0;
	}
	var withdrawable_funds = credit - purchased_funds;
	if(0 > withdrawable_funds){
		withdrawable_funds = 0;
	}
	
	  
	
	if(ban_catorgory == null){ban_catorgory = "";}else{ban_catorgory = "("+ban_catorgory+")";}
	if(ban_full_reason == null){ban_full_reason = "n/a";}
	
	var time_created = r['time_created'];
	
	var labels = "<div class='user_info_label'>Name: <br><br> Trust Score: <br><br>Withdrawable Funds: <br><br> Date Joined: <br><br>Banned:<br><br>Reason:<br><br>Research Tools: </div>";
	
	var fields = "<div class='user_info_field'>"+r['name']+"<br><br>"+trust_score_code[r['trust_score']]+"<br><br>$"+withdrawable_funds+" ($"+credit+" - $"+purchased_funds+")<br><br>"+time_created+"<br><br>"+banned+" "+ban_catorgory+"<br><br>"+ban_full_reason+" <br><br><a href='https://steamcommunity.com/profiles/"+steamid+"' target=_blank>Steam Communtity</a> | <a href='http://steamrep.com/search?q="+steamid+"' target=_blank>Steam Rep</a></div>";
	
	if(banned == "Yes"){
		var action = "<div class='user_info_actions'><button class='button_theme' onClick='unban_user();'>Unban</button></div>";
	}else{
		var action = "<div class='user_info_actions'><button class='button_theme' onClick='ban_user_input();'>Ban</button> <button class='button_theme' onClick='add_funds_input();'>Give Funds</button></div>";
	}
	
	display_content.innerHTML = "<div class='user_info_wrapper'>"+labels+fields+action+"</div>";
}

function unban_user(){
	
	var steamid = document.getElementById("steamid").value;
	
 	$.ajax({
	 url: "ajax/unban-user.php",
	 type:'POST',
	 data: {
		 "steamid":steamid
     },
	 success: function(r){
		 if(r){
			fetch_user_info();
		 }else{
			create_notification("Error","Unable to unban user. Please try again");
		 }
     },
	 error: function(r){
		create_notification("Error","Unable to unban user. Please try again");
	 },
	 timeout: 50000
	});

}

function ban_user_input(){
	
	var state_drop_down = "<select id='reason' onChange='ban_user_input_change()'>";
	state_drop_down += "<option value=''>Reason</option>";
	state_drop_down += "<option value='PayPal ChargeBack'>PayPal ChargeBack</option>";
	state_drop_down += "<option value='Website Exploit'>Website Exploit</option>";
	state_drop_down += "<option value='Bot'>Bot</option>";
	state_drop_down += "</select>";
	
	var b = "Reason:<br>"+state_drop_down+"<br><div id='invoice_id_wrapper'>Invoice ID <br><input placeholder='Invoice ID' id='invoice_id_input' type='number'></div>";
	
	create_notification_confirmation("Ban User",b,"ban_user();");
}

function ban_user_input_change(){

	var reason = document.getElementById("reason").value;
	
	if(reason == "PayPal ChargeBack"){
		document.getElementById("invoice_id_wrapper").style.display = "block";
	}else{
		document.getElementById("invoice_id_wrapper").style.display = "none";
	}
}

function ban_user(){
	
	var steamid = document.getElementById("steamid").value;
	var reason = document.getElementById("reason").value;
	var invoice_id = document.getElementById("invoice_id_input").value;
	
	if(reason == ''){close_notification(); return;}
	
	 $.ajax({
	 url: "ajax/ban-user.php",
	 type:'POST',
	 data: {
		 "steamid":steamid,
		 "reason":reason,
		 "invoice_id":invoice_id
     },
	 success: function(r){
		 if(r){
			fetch_user_info();
			close_notification();
		 }else{
			document.getElementById("error_msg").innerHTML = "Unable to ban user. Please try again";
		 }
     },
	 error: function(r){
		document.getElementById("error_msg").innerHTML = "Unable to ban user. Please try again";
	 },
	 timeout: 50000
	});
	
}

function add_funds_input(){
	
	create_notification("Add Funds","Amount to give <br><br><input id='fund_amount' type='number'>");
	document.getElementById("notification_button_wrapper").innerHTML = "<button class='notifcation_button' onClick='add_funds();'>Confirm</button><div id='error_msg' class='notification_error_msg_left'></div>";
}

function add_funds(){
	
	var fund = document.getElementById("fund_amount").value;
	var steamid = document.getElementById("steamid").value;

	 $.ajax({
	 url: "ajax/add-funds.php",
	 type:'POST',
	 data: {
		 "steamid":steamid,
		 "fund":fund
     },
	 success: function(r){
		 if(r){
			fetch_user_info();
			close_notification();
		 }else{
			document.getElementById("content").innerHTML = "<div class='user_info_wrapper'>No matching results</div>";
		 }
     },
	 error: function(r){
	
	 },
	 timeout: 50000
	});
}


function give_credits(){
	 $.ajax({
	 url: "ajax/staff-trade.php",
	 type:'POST',
	 data: {
         "id":id,
		 "status":status
     },
	 success: function(r){
		 if(r){
			 r = JSON.parse(r);
			 if(r['success']){
				get_transaction_data(current_page);
				close_notification();
			 }else{
		
				 document.getElementById("notification_error_msg").innerHTML = "Update Failed. Please try again";
			 }
		 }else{
			 document.getElementById("notification_error_msg").innerHTML = "Update Failed. Please try again";
		 }
     },
	 error: function(r){
		 document.getElementById("notification_error_msg").innerHTML = "Error "+r;
	 },
	 timeout: 50000
	});
}


</script>
<?php footer();?>
</body>
</html>