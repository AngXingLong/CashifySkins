<?php
$botsid = "76561198173298731";
prepare_trade_offer("76561198173298731");



function prepare_trade_offer(){
	
	require "bot-login.php";
	require "trade-offer-web-api.php";
	
	global $botsid,$credentials,$api_key;
	
	$credentials = json_decode(file_get_contents("bot-credentials.json",true),true);
	$credentials = $credentials[$botsid];
	$api_key = $credentials['api_key'];

	//login();
	SendTradeOfferWithToken('','','','');
}




?>