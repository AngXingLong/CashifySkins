<?php
session_start();
require "shared/app-code.php";
$appid_code = array(1=>"None") + $appid_code;

if(empty($_SESSION["steamid"])){
	header('Location: /index.php');
	die;
}else{
	$game_preference = (!empty($_POST["game_preference"])) ? $_POST["game_preference"] : '';
	
	if(!empty($_POST["terms"]) && $_POST["terms"] == 1 && validate_appid($game_preference)){
		require "shared/database.php";
		$conn->beginTransaction();
		
		$user_exist = count_row("select count(*) from user where steamid = ? limit 1",array($_SESSION['steamid']));
		$referent_reward = 0;
		
		if(!$user_exist){
			if(!empty($_SESSION['ref'])){
				require $_SERVER['DOCUMENT_ROOT']."/../settings/currency-config.php";
				$referrer_sid = select("select steamid from user where referal_id = ?;", array($_SESSION['ref']));

				if(!empty($referrer_sid)){
					$referrer_sid = $referrer_sid[0]['steamid'];
					
					$stmt = $conn->prepare("INSERT INTO referals (referrer_sid,referent_sid,complete,paid,amount) VALUES (?,?,0,0,?);");
					$stmt->execute(array($referrer_sid,$_SESSION['steamid'],$referal_reward));
					$referent_reward = $referal_reward;
		
					$stmt = $conn->prepare("INSERT INTO cash_transaction (steamid,amount,type,status,time) VALUES (?,2,3,2,now());");
					$stmt->execute(array($_SESSION['steamid']));
				}
			}
		}
		
		$stmt = $conn->prepare("INSERT INTO user(steamid,game_preference,accepted_tac,time_created,credit) VALUES (?,?,1,now(),?) 
		ON DUPLICATE KEY UPDATE game_preference = ?,accepted_tac = 1;");
		$stmt->execute(array($_SESSION["steamid"],$_POST["game_preference"],$referent_reward,$_POST["game_preference"]));
		
		$conn->commit();
		
		header('Location: /index.php');
		die;

	}
	
	/*if(!empty($_POST["terms"]) && $_POST["terms"] == 1 && validate_appid($game_preference)){
		require "shared/database.php";
		$conn->beginTransaction();
		$stmt = $conn->prepare("INSERT INTO user(steamid,game_preference,accepted_tac,time_created) VALUES (?,?,1,now()) ON DUPLICATE KEY UPDATE game_preference = ?,accepted_tac = 1 ;");
		$stmt->execute(array($_SESSION["steamid"],$_POST["game_preference"],$_POST["game_preference"]));
		$conn->commit();
		header('Location: /index.php');
		die;
	}*/
}
?>
<!DOCTYPE html>
<html>
<head>
<title>First Use - CashifySkins</title>
<link rel='icon' type='image/png' href='/images/cashifyskins-logo.png'>
</head>
<body>

<div id="center">
<img id="icon">
<br>
<form action="first-use.php" method="post">
<p>Select your game preference</p>
<select name="game_preference" id="" onchange="iconchange(this.value)">
  <option value="730">Counter-Strike: Global Offensive</option>
  <option value="440">Team Fortress 2</option>
  <option value="570">Dota 2</option>
  <option value="753">Steam</option>
  <option value="1">None</option>
</select>  <input id="sumbit"type="submit" value="Submit">


<br>
<br>
<input type="checkbox" name="terms" value="1" required>I agree to CashifySkins<a href="terms-and-condition.php"> Terms & Condition</a> and <a href="terms-and-condition.php">Privacy Policy</a>

</form>
</div>	
</body>
</html>

<script>
//<div >In order to use our services, you must agree to Inventory2Trade's Terms & Condition</div>
iconchange(730);
function iconchange(value){
	document.getElementById("icon").style.display = "block";
	document.getElementById("icon").src = "http://cdn.akamai.steamstatic.com/steam/apps/"+value+"/header.jpg";
	if(value == 753){
		document.getElementById("icon").src = "/images/steam-banner.jpg";
	}
	else if(value == 1){
		document.getElementById("icon").style.display = "none";
	}
}


</script>
<style>
button{


}
body{
background-image:url(images/bg_footer.jpg);
background-repeat:no-repeat;
background-size:100%;
background-color:#000000;
color:#ffffff;
font-family:arial;
}
img{
width:500px;
margin:0 auto;
display:block;
margin-bottom:20px;
}
#center{
width:500px;
height:400px;
position: absolute;
top: 0; bottom:0; left: 0; right:0;
margin: auto;
text-align:center;
 
}
p{
font-size:22px;
}
select{
font-size:18px;
border:0px;
padding:2px
}
a{
	color:#FF6D1F;
	text-decoration:none;
}
a:hover{
	color:#FA5B0F;
}
#sumbit{
padding:3px 10px;
font-size:18px;
border:0px;
background-color:#FA5B0F;
cursor:pointer;
color:#fff;
}

</style>