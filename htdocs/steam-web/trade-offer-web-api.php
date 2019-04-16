<?php

	function get_trade_offer_state($offer_id){
		return get_trade_offer($offer_id);
	}
	function get_trade_offer($offer_id){
		$url = "https://api.steampowered.com/IEconService/GetTradeOffer/v1/?key=".$GLOBALS['api_key']."&tradeofferid=".$offer_id;
		return curl_get($url);
	}
	
	function get_sent_offers(){	
		$url = "https://api.steampowered.com/IEconService/GetTradeOffers/v1/?key=".$GLOBALS['api_key']."&get_sent_offers=true";
		return curl_get($url);
	}
	function cancel_trade_offer($offer_id){
		$url = "https://api.steampowered.com/IEconService/CancelTradeOffer/v1/?key=".$GLOBALS['api_key']."&tradeofferid=".$offer_id;
		curl_post($url,"");
	}
	function SendTradeOfferWithToken($message, $usersid32, $status, $token){
		$usersid = "76561198075337308";
		$usersid32 = "115071580";
	 	$token = "YfrPpOpm";
		//json_encode($status)
		$sendurl = "https://steamcommunity.com/tradeoffer/new/send";
		
		$status = array("newversion"=>true,"version"=>2,
		"me"=>array("ready"=>false,"currency"=>array(),"assets"=>array()),
		"them"=>array("assets"=>array(array("appid"=>"730","contextid"=>"2","amount"=>1,"assetid"=>"2854320310")),"currency"=>array(),"ready"=>false));
		
		$data = array("sessionid"=>get_sessionid(''),
		"serverid"=>1,
		"partner"=>$usersid,
		"tradeoffermessage"=>"Hi",
		"json_tradeoffer"=>json_encode($status),"captcha"=>"",
		"trade_offer_create_params"=>json_encode(array("trade_offer_access_token"=>$token)));
		
	
		
		$referer = "https://steamcommunity.com/tradeoffer/new/?partner=".$usersid32."&token=".$token;
		
	//echo $json =  curl("https://steamcommunity.com/market/eligibilitycheck/?goto=%2Ftradeoffer%2Fnew%2F%3Fpartner%3D115071580%26token%3DYfrPpOpm",'a','',1);
		$cookie = file_get_contents(__DIR__."/steam_cookie.txt");
		
		$search_expression = '/sessionid\s+(.*)/';
		if (preg_match($search_expression, $cookie, $search_result)) {
		  	return $search_result[1];
		}
		die;

        echo curl($sendurl,'a',$data,0,$referer);
	
    }
	
	function curl($url,$cookie,$data = '',$header = 0,$referer = ''){
		
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		
		if(!empty($cookie)){
			$cookie = __DIR__."/steam_cookie.txt";
			curl_setopt($curl, CURLOPT_COOKIESESSION, true);
			curl_setopt($curl, CURLOPT_COOKIEJAR, $cookie);
			curl_setopt($curl, CURLOPT_COOKIEFILE, $cookie);
		}
		
		if(!empty($data)){
			print_r($data);
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		}
		
		if(!empty($referer)){
			curl_setopt($curl, CURLOPT_REFERER, $referer);
			
		}
		
		curl_setopt($curl, CURLOPT_HEADER, $header);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.116 Safari/537.36");
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('charset: UTF-8'));
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		return curl_exec($curl);
		
	}
	function get_sessionid($cookie)
	{
		$cookie = file_get_contents(__DIR__."/steam_cookie.txt");
		
		$search_expression = '/sessionid\s+(.*)/';
		if (preg_match($search_expression, $cookie, $search_result)) {
		  	return $search_result[1];
		}
	}
	
	
	

			
			
?>

