<?php 
session_start(); 
//ToDo set login first before outputing the nav menu
require $_SERVER['DOCUMENT_ROOT']."/shared/database.php";
require $_SERVER['DOCUMENT_ROOT']."/shared/user-type-code.php";

if(empty($_SESSION['account_type']) || !array_key_exists($_SESSION['account_type'],$is_staff)){
	header("Location: /error.php");
	die;
}

$csrf = (!empty($_SESSION['csrf_token'])) ? $_SESSION['csrf_token'] : 0;

if (!isset($_SESSION['created'])) {
    $_SESSION['created'] = time();
} 
else if (time() - $_SESSION['created'] > 300) { // 10 minutes ago // 60 = 1 min
    session_regenerate_id(true);  
    $_SESSION['created'] = time(); 
	if(isset($_SESSION['auth'])){
		unset($_SESSION['auth']);
	}
}

if(!empty($_SESSION['expired_time_zone'])){
	$js[] = "/js/set-time-zone.js";
}
if(empty($_SESSION['time_zone'])){
	$_SESSION['time_zone'] = '+00:00';
}

global $user_details,$title,$description,$css,$js;

$description = "Buy & Sell CSGO, Dota 2 and TF2 cosmetics items using real money.";
$css = array("/css/menu.css");
$js = array("https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js","/js/global.js");

if(!isset($_SESSION['steamid']) && isset($_COOKIE['userinfo'])) {
   	$obj = new login();
	$obj->verify_cookie();
}  

function footer(){
	echo "<footer><div id='footer_container'><div id='footer_logo'><a href='/index.php'><img class='footer_logo' src='/images/cashifyskins-banner.png' height='54px'></a></div>2016 · <a href='http://store.steampowered.com/'>Powered by Steam</a> ·<a href='terms-and-condition.php'>Terms & Conditions</a></div></footer>";
}

function output_header(){
	global $title,$description,$css,$js;
	
	$csrf = (!empty($_SESSION['csrf_token'])) ? $_SESSION['csrf_token'] : 0;
	
	echo "<!doctype html>\r\n";
	echo "<head>\r\n";
	echo "<title>".$title."</title>";
	echo "<meta charset='utf-8'>\r\n";
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
	$user_details = select("select credit, trust_score, game_preference, accepted_tac from user where steamid = ?", array($_SESSION['steamid']));

	if(empty($user_details) || !$user_details[0]['accepted_tac']){
		 header("Location: first-use.php");
	}
	
	$user_details = $user_details[0];

	if(empty($_SESSION['steam_personaname'])) {
		require 'steamauth/user-info.php';
		getuserdetails($_SESSION['steamid']);
	}
		
	return 	"<div class='nav_side_menu'>
	<img class='nav_side_menu_avatar' src='".$_SESSION['steam_avatarfull']."'/>
	<ul>
 	 <li>".$_SESSION['steam_personaname']." <span class='arrow'>▼</span><br>
		<ul>
			<li><a href='https://steamcommunity.com/groups/CashifySkins'>Forum</a></li>	
			<li><a href='/index.php'>Main Site</a></li>	
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
	} 
	else{
		$obj = new login();
		$side_bar = $obj->steamlogin();
	}

	return "
<nav>
<div id='nav_container'>
<div class='nav_left'>
  <a href='/admin/index.php' id='logo'><img src='/images/cashifyskins-banner.png'></a>
  	<ul>	
		<li><a href='/admin/transactions.php'>Transactions</a></li>
		<li><a href='/admin/accounts.php'>Accounts</a></li>
		<li><a href='/admin/ticket-inbox.php'>Support</a></li>
		<li><a href='/admin/bot-status.php'>Bot Status</a></li>
	</ul>
 </div>

 <div id = 'steam_login'>
 $side_bar
 </div>
 
 </div>
 </nav>";

}






