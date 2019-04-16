<?php

require $_SERVER['DOCUMENT_ROOT']."/shared/nav-menu.php";
require $_SERVER['DOCUMENT_ROOT']."/../settings/currency-config.php";

if(empty($_SESSION['steamid'])){
	header("Location: /login-required.php");
	die;
}

$title = "Add Funds - CashifySkins";
$css[] = "../css/add-funds.css";  
output_header(); 

?>

<div id="basic_content_wrapper">
<div class="containerinput">
<img id="add_funds_banner" src="../images/cashifyskins-wallet-banner.png"></img>


<form id="payment" action="payment.php" method="post">
<input id="type" type="text" name="type" hidden>
<input name='csrf' value='<?php echo $_SESSION['csrf_token'];?>' hidden>
</form> 

<table border="4">
  <tbody>
    <tr>
      <td id='1' onClick="set_amount(1)">$ 5</td>
      <td id='2' onClick="set_amount(2)">$ 10</td>
      <td id='3' onClick="set_amount(3)">$ 20</td>
      <td id='4' onClick="set_amount(4)">$ 40</td>
    </tr>
    <tr>
      <td id='5' onClick="set_amount(5)">$ 50</td>
      <td id='6' onClick="set_amount(6)">$ 100</td>
      <td id='7' onClick="set_amount(7)">$ 250</td>
      <td id='8' onClick="set_amount(8)">$ 500</td>
    </tr>
  </tbody>
</table>
<div class="containerprice">
<span>Price (USD)</span><span id="price_counter" style="float:right;"></span>
</div>
<button id="purchase" onClick="sumbit()">Purchase</button>
</div>

<div id="desc_container">
<h1>Add Funds</h1>

Wallet Balance: $<?php echo $user_details['credit']; ?>

<br>
<br>
Payment Method:<br>
✔ PayPal
<br><br>
<b>Notice</b><br>
For purchases $50 and greater must have a 'Good' trust score otherwise payment will fail.
</div>

</div>

<script>
var selectedid;
//document.getElementById("payment").submit();
function set_amount(type){
	
	var cash = 0;
	
	switch(type) {
    case 1: 
	cash = 5;
	break;  
    case 2:
	cash = 10;
	break; 
	case 3:
	cash = 20;
	break; 
    case 4:
	cash = 40;
	break; 
	case 5:
	cash = 50;
	break; 
    case 6:
	cash = 100;
	break; 
	case 7:
	cash = 250;
	break; 
	case 8:
	cash = 500;
	break; 
    default:
        cash = "";
	}
	if(selectedid){
		document.getElementById(selectedid).style.background = "#ddd";
		document.getElementById(selectedid).style.color = "#000";		
	}
	
	selectedid = type;
	document.getElementById("price_counter").innerHTML = "$"+cash;
	document.getElementById("type").value = type;
	document.getElementById(type).style.background = "#2B99FF";
	document.getElementById(type).style.color = "#fff";
	

}
function sumbit() {
	var value = document.getElementById("type").value;
	if(value){
     	  document.getElementById("payment").submit();
	}
	else{
		create_notification("Enter Amount","Please select a amount");
	}
}


</script>
<?php footer();?>
</body>
</html>
