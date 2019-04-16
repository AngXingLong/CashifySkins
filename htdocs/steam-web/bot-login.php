<?php

	
		
	function login(){
		
		global $credentials,$botsid;
		
		$output = curl("https://steamcommunity.com/login/getrsakey/",'a',array("username" =>$credentials['username']));
		$output = json_decode($output,true);


		$rsa_mod = $output['publickey_mod'];
		$rsa_exp = $output['publickey_exp'];
		$rsa_timestamp = $output['timestamp'];
		
		include("Crypt/RSA.php");
			
		$RSA = new Crypt_RSA();
		$RSA->setEncryptionMode(CRYPT_RSA_ENCRYPTION_PKCS1);
		$n = new Math_BigInteger($rsa_mod, 16);
		$e = new Math_BigInteger($rsa_exp, 16);
		$key = array("modulus"=>$n,"publicExponent"=>$e);
		$RSA->loadKey($key);
		$encryptedPassword = base64_encode($RSA->encrypt($credentials['password']));
		
		$login_data = array(
				"password" => $encryptedPassword,
				"username" => $credentials['username'],
				"emailauth" => "PTFWH",
				"twofactorcode" => "",
				"loginfriendlyname" => "LocalHost",
				"captchagid" => "-1",
				"captcha_text" => "",
				"emailsteamid" => "",
				"rsatimestamp" => $rsa_timestamp,
				"remember_login" => "true");
		
		$login = curl("https://steamcommunity.com/login/dologin/",'a',$login_data);
		//print_r($login);		
		$login = json_decode($login,true);
		
		if(empty($login['success'])){
			echo $login['message'];
			die;
		}
		else if(!empty($login['emailauth_needed'])){
			echo "emailauth needed";
			die;
			//$captcha_gid = $output['captcha_gid'];
			//echo "<img src=https://steamcommunity.com/public/captcha.php?gid=$captcha_gid> $captcha_gid";
		}
	}

	
			
			
?>

