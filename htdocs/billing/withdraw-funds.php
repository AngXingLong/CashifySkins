<?php
require '../shared/nav-menu.php';
require "../../settings/currency-config.php";

if(empty($_SESSION['steamid'])){
	header("Location: /login-required.php");
	die;
}

$user = select ("select 
IFNULL((select sum(amount) from cash_transaction ct where ct.steamid = u.steamid and ct.type = 1 and status = 2),0) - IFNULL((select sum(price) from item_transaction it where it.buyer_sid = u.steamid),0) as purchased_funds, 
(select amount from cash_transaction ct where ct.steamid = u.steamid and ct.status = 0 and ct.type = 0 limit 1) as active_withdrawal,
credit, paypal_email from user u where u.steamid = ?", array($_SESSION['steamid']));

$title = "Fund Withdrawal - CashifySkins";
$css[] = "/css/cashout.css";  
output_header(); 

$purchased_funds = 0;
if(!empty($user[0]['purchased_funds']) && $user[0]['purchased_funds'] > 0 ){
	$purchased_funds = number_format($user[0]['purchased_funds'],2);
}

$withdrawable_funds = number_format($user[0]['credit']-$purchased_funds,2);

if(0 > $withdrawable_funds){
	$withdrawable_funds = 0;
	$purchased_funds =  number_format($user[0]['credit'],2);
}



$withdrawal = !empty($user[0]['active_withdrawal']) ? $user[0]['active_withdrawal'] : '';
$payout = (!empty($withdrawal)) ? round(($withdrawal/100) * (100-$cashout_fee), 2) : '';
//, PHP_ROUND_HALF_UP
?>

<div id="basic_content_wrapper">
<div class="containerinput">
<img id="paypal_logo"  src="/images/paypal.png">

<table border="4">
  <tbody>
    <tr>
      <td onClick="calutate_amount('5')">$ 5</td>
      <td onClick="calutate_amount('10')">$ 10</td>
      <td onClick="calutate_amount('20')">$ 20</td>
      <td onClick="calutate_amount('40')">$ 40</td>
    </tr>
    <tr>
      <td onClick="calutate_amount('50')">$ 50</td>
      <td onClick="calutate_amount('100')">$ 100</td>
      <td onClick="calutate_amount('250')">$ 250</td>
      <td onClick="calutate_amount('500')">$ 500</td>
    </tr>
    <tr>
     <td class='blue' colspan='2' onClick="calutate_amount('',wallet_balance)">Withdraw All</td>
     <td class='blue' colspan='2' onClick="cancel_cashout()">Cancel Withdrawal</td>
    </tr>
  </tbody>
</table>
<input id='cancel' name='cancel' type="checkbox" value='true' hidden></input>
<div class="containerprice">
<span>Withdrawal $</span><input id="withdrawal_counter" name='credit' class='counter'  oninput="custom_amount('cash')" placeholder="Enter Custom Amount"
value='<?php echo $withdrawal; ?>'>
</div>

<div class="containerprice">
<span>Payout $</span><input id="payout_counter" name='cash' class='counter' placeholder="Enter Custom Amount" oninput="custom_amount()" 
value='<?php echo $payout; ?>'>
</div>


<button id="cashout" onClick="submit_cashout()">Cash Out</button>

</div>

<div id="desc_container">
<h1>Withdrawals</h1>

<span class='text'>Current Fee</span><span class='text2'><?php echo $cashout_fee; ?>%</span><br><br>
<span class='text'>Active</span><span class='text2'><?php if($withdrawal == ''){echo "No";}else{echo "$".$withdrawal." | $".$payout.""; } ?></span><br><br>
<span class='text'>Payout Period</span><span class='text2'>1 - 2 Days</span><br><br>
<span class='text'>Paypal Email</span><input id='email' type="text" 
value='<?php if(!empty($user[0]['paypal_email'])){echo $user[0]['paypal_email'] ;} ?>' >
<br>
<button onClick="submit_email()" class='update_email_button'>Submit</button>
<br>
<?php 
if($purchased_funds > 0 ){

	echo "<div class='notice'>";
	echo "<h2>Notice</h2>";
	echo "You currently have $$withdrawable_funds withdrawable funds. <br>";
	echo "$$purchased_funds cannot be cashed out as they are purchased funds.<br><br>";
	//echo "Please submit a support ticket if you like a refund on the unspended funds. (Min $5)";
	echo "</div>";

}
?>

</div>
</div>

<script>

var fee = <?php echo $cashout_fee; ?>;
var wallet_balance = <?php echo $withdrawable_funds; ?>;
var auth = '<?php echo (!empty($_SESSION['auth']) ? true : false ); ?>';
var purchased_funds = <?php echo $purchased_funds; ?>;

function calutate_amount(cash,credit){
	var cash_bool = cash; 
	cash =  parseFloat(cash).toFixed(2);
	credit =  parseFloat(credit).toFixed(2);	

	if(cash_bool){
		document.getElementById("withdrawal_counter").value = cash;	
		document.getElementById("payout_counter").value = (cash/100 * (100-fee)).toFixed(2);
	}
	else{
		document.getElementById("withdrawal_counter").value = (credit/100 * (100+fee)).toFixed(2);	
		document.getElementById("payout_counter").value = credit;
	}
	
}

function custom_amount(type){ 
	var cash = 0;
	var credit = 0;
	
	if(type == 'cash'){
		cash = document.getElementById("withdrawal_counter").value;
		cash = Number(cash.replace(/[^0-9\.]+/g,""));

	}else{
		credit = document.getElementById("payout_counter").value;
		credit = Number(credit.replace(/[^0-9\.]+/g,""));
	}

	cash =  parseFloat(cash).toFixed(2);
	credit =  parseFloat(credit).toFixed(2);	

	if(type == 'cash'){
		document.getElementById("payout_counter").value = (cash/100 * (100-fee)).toFixed(2);
	}
	else{
		document.getElementById("withdrawal_counter").value = (credit/100 * (100+fee)).toFixed(2);	
	}
}

function cancel_cashout(){

	document.getElementById("withdrawal_counter").value = 0;
	document.getElementById("payout_counter").value = 0;
	submit_cashout();
}

function submit_cashout() {
	
	var withdrawal = document.getElementById("withdrawal_counter").value;

	if(5 > withdrawal && withdrawal != 0){
		create_notification("Error","Withdrawal amount must be $5 or greater");
		return;
	}
	
	if(withdrawal > 2000){
		create_notification("Max Withdrawal Reached",'You are allowed to withdraw up to $2000 per transaction.');
		return;
	}
	
	$.ajax({
	type: "POST",
	url: "/ajax/update-cashout.php",
	data: { "withdrawal": withdrawal},
	success: function(r){
		if(r){
			 r = JSON.parse(r);
			if(r['success']){
				create_notification("Success",r['msg']);
				document.getElementById("close_notification_button").addEventListener("click",function(){location.reload();});
			}else{
				create_notification("Error",r['msg']);
			}
		}
		else{
			create_notification("Error","We encountered an error while proccessing your request. Please try again.");
		}
	},
	error: function(r){
		create_notification("Error","There was an error processing your request. Please try again.");
	},
	timeout: 50000
	});
}

function submit_email(){
	/*if(!auth){
		create_notification("Relogin Required","Please relogin to update your payee's paypal");
		return;
	}*/
	
	var payee = document.getElementById("email").value;
	
	$.ajax({
		type: "POST",
		url: "/ajax/update-cashout.php",
		data: { "payee": payee},
		success: function(r){
		 if(r){
			 r = JSON.parse(r);
			 if(r['success']){
				create_notification("Recipient Updated",r['msg']);
			 }else{
				 create_notification("Error",r['msg']);
			 }
		 }else{
			  create_notification("Error","We encountered an error while proccessing your request. Please try again.");
		 }
	 	},
		error: function(r){
			create_notification("Error","There was an error processing your request. Please try again.");
		},
		timeout: 50000
	});
	
}

</script>

<?php footer();?>

</body>
</html>
