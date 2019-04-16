<?php
class login 
{
	private $usersid;
	private $token;
	private $salt;
	private $hashedtoken;
	//http://security.stackexchange.com/questions/6957/length-of-csrf-token
	function generate_details(){
		$this->salt = bin2hex(openssl_random_pseudo_bytes(32)); // set to 16 if slow performace
		$this->generate_token();
	}
	
	function generate_token(){
		$this->token = bin2hex(openssl_random_pseudo_bytes(32));
		$this->hashedtoken =  hash("sha256", $this->salt.$this->token);
	}
	
	function returncookie(){
		$cookievalue = json_encode(array($this->usersid,$this->salt,$this->token));
		setcookie("userinfo", $cookievalue, time() + (86400 * 2), "/","",true,true);  // will expire in 3 days
		//isset($_SERVER["HTTPS"])
	}
	// a salted token is added to the database as incase it stolen like randow bobby tables it cannot be used
	// 
	function store_token(){
		global $conn;
		$conn->beginTransaction();
		
		$stmt = $conn->prepare("insert into auth_tokens (usersid,salt,token,expire) values (?,?,?, DATE_ADD(now(),INTERVAL 2 DAY)) on duplicate key update salt = ?, token = ?, expire = DATE_ADD(now(),INTERVAL 2 DAY)");
		$stmt->execute(array($this->usersid,$this->salt,$this->hashedtoken,$this->salt,$this->hashedtoken));
		$conn->commit();
		$this->returncookie();
	}
	
	function set_session(){
		$_SESSION["csrf_token"] = bin2hex(openssl_random_pseudo_bytes(32));
		$_SESSION["steamid"] = $this->usersid;
		
		$user_data = select("select type, time_zone from user where steamid = ?;",array($this->usersid));
		if(!empty($user_data) && ($user_data[0]['type'] == 1 || $user_data[0]['type'] == 2)){
			$_SESSION["account_type"] = $user_data[0]['type'];
		}
		
		if(!empty($user_data[0]['time_zone'])){
			$_SESSION['time_zone'] = $user_data[0]['time_zone'];
			$_SESSION['expired_time_zone'] = 0;
		}
		else{
			$_SESSION['time_zone'] = "+00:00";
			$_SESSION['expired_time_zone'] = 1; 
		}
		session_regenerate_id(true);
	}

	function cookie_details(){
		
		if(isset($_COOKIE["userinfo"])){
		$userinfo =  json_decode($_COOKIE["userinfo"]);

			if(isset($userinfo[0]) && isset($userinfo[1]) &&  isset($userinfo[2]) && is_numeric($userinfo[0])){

			$this->usersid = $userinfo[0];
			$this->salt = $userinfo[1];
			$this->token = $userinfo[2];
			
			return true;
			}
		}
		return false;
	}
	
	//http://stackoverflow.com/questions/244882/what-is-the-best-way-to-implement-remember-me-for-a-website
	function verify_cookie(){
		if($this->cookie_details()){
			$this->check_banned();
			$usersid = $this->usersid;
			$salt = $this->salt;
			$token = $this->token;
			
			$val =  select("select salt, token from auth_tokens where usersid = ? and expire > now() limit 1",array($this->usersid));
		
			if(!empty($val)){
			
				$dbtoken = $val[0]['token'];
				$dbsalt = $val[0]['salt'];
				if($this->salt == $dbsalt){
					if(hash("sha256", $this->salt.$this->token) == $dbtoken){
				
						$this->generate_token();
						$this->store_token();
						$this->set_session();				
						return true;
					}
					else{
						$this->delete_user_tokens();
					}
				}
			}
		}
		$this->logout();
		return false; 

	}
	function delete_user_tokens(){
		global $conn;
		$conn->beginTransaction();
		$stmt = $conn->prepare("delete from auth_tokens where usersid = ?");
		$stmt->execute(array($this->usersid));
		$conn->commit();
	}
	
	function check_banned(){
		
	   	$dbban = select("select reason from ban where steamid = ? limit 1",array($this->usersid));
		
		if(!empty($dbban)){
			header("Location: /ban.php?reason=".$dbban[0]['reason']);
			die;
		}
	}
	
	function logout(){
		header("Location: /shared/steamauth/logout.php");
	}
	
	function steamlogin()
	{
		global $conn;
		try {
			
			require $_SERVER['DOCUMENT_ROOT']."/../settings/steam-config.php";
			require 'openid.php';
			
			$openid = new LightOpenID($steamauth['domainname']);
			
			//Settings
			$button = "large_noborder";
			$login_page = "/";
			
			if(!$openid->mode) {
				if(isset($_GET['login'])) {
					$openid->identity = 'http://steamcommunity.com/openid';
					header('Location: ' . $openid->authUrl());
				}
			return "<form action=\"?login\" method=\"post\"> <input class='login_button' type=\"image\" src=\"/images/steam-login.png\"></form>";
		}
		
			 else if($openid->mode == 'cancel') {
				header('Location: '.$login_page);
			} else {
				if($openid->validate()) { 
						$id = $openid->identity;
						$ptn = "/^http:\/\/steamcommunity\.com\/openid\/id\/(7[0-9]{15,25}+)$/";
						preg_match($ptn, $id, $matches);
						session_regenerate_id(true);
				
						$this->usersid = $matches[1];
						$this->check_banned();
						$this->generate_details();
						$this->store_token();
						$this->set_session();
						header('Location: '.$login_page);
						 
				} 
				else {
						header('Location: '.$login_page);
				}
		
			}
		} catch(ErrorException $e) {
			echo $e->getMessage();
		}
	}


}

