<?php

	function generic_validation(){
		
		global $output,$user;
		
		$user = select("select (select COUNT(*) from trade_transaction where usersid = user.steamid and status in (0,1,2) limit 1) as inqueue,
	offer_token, trust_score, credit from user where steamid = ?",array($_SESSION['steamid']));
		 
		$user = $user[0];
		
		if(empty($user['offer_token'])){
			$output["msg"] = "Your trade offer url has not set. Please go to your <a href='https://cashifyskins.com/account.php' target='_blank'>Account page</a> to set your url.";
			return false;
		}
		
		if($user['inqueue'] > 0){
			$output['msg'] = "Please settle your active trade offer before you procceed";
			return false;
		}
		
		/*if(!empty($_SESSION['last-traded']) && (time() - $_SESSION['last-traded']) < 60 ){ // 1 Min
			$output["msg"] = "You must wait ".(60 - (time() - $_SESSION['last-traded']))." seconds to trade again";
			return false;
		}*/
		
		return true;
	
	}
	
	
	
	function generate_security_token(){
		$security_token = array("Neo", "Morpheus", "Trinity", "Cypher", "Tank", "Sniper", "Infinity", "Elite", "Goat", "Phoenix", "Vanguard", "Deal");
		$security_token = $security_token[array_rand($security_token , 1 )];
		return $security_token;
	}