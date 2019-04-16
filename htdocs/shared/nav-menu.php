<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require "database.php";
require $_SERVER['DOCUMENT_ROOT']."/shared/steamauth/login.php";

global $user_details,$title,$description,$css,$js;

$description = "A fast and secure marketplace to trade steam economy goodies for real money!";
$css = array("/css/menu.css");
$js = array("https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js","/js/global.js");

if (!isset($_SESSION['session_regen'])) {
    $_SESSION['session_regen'] = time() + 300;// 10 minutes ago // 60 = 1 min
} 
else if (time() >  $_SESSION['session_regen']) { 
    //session_regenerate_id(true);  
    $_SESSION['session_regen'] = time() + 300; 
	if(isset($_SESSION['auth'])){
		unset($_SESSION['auth']);
	}
}

if(!isset($_SESSION['steamid']) && isset($_COOKIE['userinfo'])) {
   	$obj = new login();
	$obj->verify_cookie();
}  
if(isset($_GET['ref']) && !isset($_SESSION['ref'])){
	$_SESSION['ref'] = $_GET['ref'];
}

if(!empty($_SESSION['expired_time_zone'])){
	$js[] = "/js/moment.js";
	$js[] = "/js/set-time-zone.js";
}

if(empty($_SESSION['time_zone'])){
	$_SESSION['time_zone'] = '+00:00';
}

function footer(){
	echo "<footer><div id='footer_container'><div id='footer_logo'><a href='/index.php'><img class='footer_logo' src='/images/cashifyskins-banner.png' height='54px'></a></div>2016 · <a href='http://store.steampowered.com/'>Powered by Steam</a> ·<a href='/terms-and-condition.php'>Terms & Conditions</a></div></footer>";
}

function output_header(){
	global $title,$description,$css,$js;
	
	$csrf = (!empty($_SESSION['csrf_token'])) ? $_SESSION['csrf_token'] : 0;
	
	echo "<!doctype html>\r\n";
	echo "<head>\r\n";
	echo "<title>".$title."</title>";
	echo "<meta charset='utf-8'>\r\n";
	echo "<meta property='og:image' content='https://cashifyskins.com/images/cashifyskins-og-logo.png'>\r\n";  
	echo "<meta property='og:url' content='https://cashifyskins.com'>\r\n";  
	echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>\r\n";
	echo "<meta name='description' content='".$description."'>\r\n";
		
	foreach($css as $value){
		echo "<link href='$value' rel='stylesheet'>\r\n";
	}
	foreach($js as $value){
		echo "<script src='$value'></script>\r\n";
	}
	echo "<script>var csrf = '$csrf'; $.ajaxPrefilter(function(options) {if(options.type == 'POST'){options.beforeSend = function (xhr) { xhr.setRequestHeader('X-CSRF-TOKEN',csrf);}}});</script>";
	echo "<link rel='icon' type='image/png' href='/images/cashifyskins-logo.png'>\r\n";
	echo "</head>\r\n";
	echo "<body>\r\n";
	echo nav_menu();
	
}


function sidebar(){
	
	global $user_details;
	$user_details = select("select credit, accepted_tac from user where steamid = ?", array($_SESSION['steamid']));

	if(empty($user_details) || !$user_details[0]['accepted_tac']){
		 header("Location: /first-use.php");
		 die;
	}
	
	$user_details = $user_details[0];

	if(empty($_SESSION['steam_personaname'])) {
		require 'steamauth/user-info.php';
		getuserdetails($_SESSION['steamid']);
	}
		
	return 	"<div class='nav_side_menu'>
	<img class='nav_side_menu_avatar' src='".$_SESSION['steam_avatarfull']."'/>
	<ul>
 	 <li><span id='nav_side_menu_name'>".$_SESSION['steam_personaname']." <span class='arrow'>▼</span></span><br>
	 <div class='nav_side_menu_credit'>$<span id='user_funds'>".number_format($user_details['credit'],2)."</span></div>
		<ul>
			<li><a href='/account.php'>Account</a></li>
			<li><a href='/manage-orders.php'>Manage Orders</a></li>
			<li><a href='/billing/withdraw-funds.php'>Withdraw Funds</a></li>
			<li><a href='/billing/add-funds.php'>Add Funds</a></li>
			<li><a href='/referals.php'>Referals</a></li>
			<li><a href='/shared/steamauth/logout.php'>Logout</a></li>
  		</ul>
	</li>
	</ul>
	
	</div></div></div>";
	
	
}


function nav_menu(){
	$side_bar = "";
	if(isset($_SESSION['steamid'])) {
		$side_bar = sidebar();
	} else{
		$obj = new login();
		$side_bar = $obj->steamlogin();
	}

	return "
<nav>
<div id='nav_container'>
<div class='nav_left'>
  <a href='/index.php' id='logo'><img src='/images/cashifyskins-banner.png'></a>
 
  <ul>	
  
		<li><a href='#'>Trade <span class='arrow'>▼</span></a>
			<ul>
				<li><a href='/buy.php'>Buy</a></li>
				<li><a href='/sell.php' rel='nofollow'>Sell</a></li>
			</ul>
		</li>
		<li><a href='http://steamcommunity.com/groups/CashifySkins'>Community</a></li>
        <li><a href='#'>Support <span class='arrow'>▼</span></a>
        	<ul>
				<li><a href='/support/ticket-inbox.php'>Support Ticket</a></li>
				<li><a href='/faq.php'>FAQ</a></li>
				<li><a href='https://steamcommunity.com/groups/CashifySkins/discussions/'>Forum</a></li>
			</ul>
        </li>

	</ul>
    
 </div>

 <div id = 'steam_login'>
 $side_bar
 </div>
 
 </div>
 </nav>";

}



