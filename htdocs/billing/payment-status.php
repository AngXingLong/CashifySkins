<?php

require $_SERVER['DOCUMENT_ROOT']."/shared/nav-menu.php";

if(empty($_SESSION['steamid'])){
	header("Location: ".$_SERVER['DOCUMENT_ROOT']."/login-required.php");
	die;
}

$title = "Payment Status - CashifySkins"; 
output_header(); 

$header = "There is nothing here";
$comment = "";
if(isset($_GET["status"])){
	
	switch ($_GET["status"]) {
    case 0:
        $header = "Transaction Failed";
		$comment = "We encountered an error with PayPal. <br><a class='redirect_link' href='/billing/add-funds.php'>Click here to try again.</a>";
        break;
    case 1:
	 	$header = "Transaction Success";
	    $comment = "You will be redirected in <span id='time'>7</span> seconds <br><br><a id='redirect_link' class='redirect_link' href='/buy.php'>Click here if you are not redirected </a>";
	    break;
    case 2:
        $header = "Transaction Failed";
		$comment = "Your paypal account needs to be verfied before we can proccess your payment.<br>You have not been charged.";
        break;
	case 3:
        $header = "Transaction Failed";
		$comment = "Your fund purchase exceeds more than quota $700 within this month. <br>You must wait until next month before you can purchase again.";
        break;
	}
	
	
 
}

echo "<div id='basic_content_wrapper'>";
echo "<div id='content'>";
echo "<h1>".$header."</h1>";
echo $comment."<br>";
echo "</div>";
echo "</div>";
?>
<script>
var time = 7;
<?php if(!empty($_GET["status"]) && $_GET["status"] == 1){echo "start_redirect();";} ?>
function start_redirect(){
	setInterval(function(){ 
		time--; 
		document.getElementById("time").innerHTML = time; 
		if(time == 0){
			window.location = "/buy.php";
		}
		
	}, 1000);
}
//

</script>
<style>
#redirect_link{
	font-size:18px;
}
#content{
	font-size:20px;
	margin-left:25px;
}
.redirect_link{
	color:#EE5004;
}
.redirect_link:hover{
	color:#FF6A1B;
}

</style>
<?php footer();?>
</body>
</html>