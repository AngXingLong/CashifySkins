<?php 
session_start();
require "member-access.php";
require $_SERVER['DOCUMENT_ROOT']."/shared/database.php";
require $_SERVER['DOCUMENT_ROOT']."/../composer/SteamID.php-master/SteamID.php";

$allowed_appid = array(1,753,730,570,440);

$output = array("success"=>1);

//if(!empty($_SESSION['auth'])){
	
	$usersid = $_SESSION['steamid'];
	$usersid32 = "";
	$tradetoken = "";
	$game_preference = 730;
	
	if(!empty($_POST['game_preference']) && in_array($_POST['game_preference'],$allowed_appid)){
		$game_preference = $_POST['game_preference'];
	}
	
	if(!empty($_POST['trade_token'])){
		$pass = false;
		$url = @parse_url($_POST['trade_token']);
		if(!empty($url['query'])){
			parse_str($url['query'], $url);
			
			if(!empty($url['partner']) && !empty($url['token'])){
				
				$usersid32 = $url['partner'];
				$pass = true;
				$tradetoken = filter_var($url['token'],FILTER_SANITIZE_STRING,FILTER_FLAG_STRIP_HIGH);
			}
		}
		if(!$pass){
			$output["success"] = 0;
			$output["msg"] = "Unable to proccess your trade url. Please try again";
			goto end;
		}
		
		
		try
		{
			$s = new SteamID($_SESSION['steamid']);
			
			if($s->RenderSteam3() != $usersid32) {
				$output["msg"] = "Your trade url does not belong to this Steam account. Please correct your trade url";
				goto end;
			} 
		}
		catch( InvalidArgumentException $e )
		{
			$output["msg"] = "Unable to proccess your trade url. Please try again";
			goto end;
		}
	}
	$conn->beginTransaction();
	$stmt = $conn->prepare("update user set steamid32 = ?, offer_token = ?, game_preference = ? where steamid = ? ;");
	$stmt->execute(array($usersid32,$tradetoken,$game_preference,$_SESSION['steamid']));
	$conn->commit();
	$output["msg"] = "Your account settings has been updated";
	
/*}else{
	$output["success"] = 0;
	$output["msg"] = "Please Relogin to update your account settings";
}*/

if(!empty($output)){
	end:
	echo json_encode($output);
}

?>