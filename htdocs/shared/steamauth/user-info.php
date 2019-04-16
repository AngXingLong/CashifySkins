<?php

	
    require $_SERVER['DOCUMENT_ROOT']."/../settings/steam-config.php";
	
	$GLOBALS['apikey'] = $steamauth['apikey'];

	function getuserdetails($usersid){

		global $apikey,$conn;
	
		if (!empty($usersid)) {
			
			$url = sprintf("https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=%s&steamids=%s", $apikey, $usersid);
			$data = getdata($url);
			
			if(!empty($data)){
				$_SESSION['steam_personaname'] = filter_var($data['response']['players'][0]['personaname'], FILTER_SANITIZE_STRING);
				$_SESSION['steam_profileurl'] = $data['response']['players'][0]['profileurl'];
				//$_SESSION['steam_avatarmedium'] = $data['response']['players'][0]['avatarmedium'];
				$_SESSION['steam_avatarfull'] = $data['response']['players'][0]['avatarfull'];
			}


			$name = $_SESSION['steam_personaname'];
			
			$user_details = select("select trust_score from user where steamid = ?",array($usersid));

			if(!empty($user_details) && $user_details[0]['trust_score'] != 2 ){
				
				if(!empty($data) && $data['response']['players'][0]['communityvisibilitystate'] != 3){
					$trust_score = 0;
				}
				else{
					$time_now = time();
					$date_created = $data['response']['players'][0]['timecreated'];
					$diff = $time_now -$date_created;
					$years = floor($diff / (365*60*60*24));
					
					$trust_score = trustscore($usersid,$years);
				}
				$conn->beginTransaction();
				$stmt = $conn->prepare("update user set trust_score = ?, name = ? where steamid = ?");
				$stmt->execute(array($trust_score,$name,$usersid));
				$conn->commit();
			}
			else{
				$conn->beginTransaction();
				$stmt = $conn->prepare("update user set name = ? where steamid = ?");
				$stmt->execute(array($name,$usersid));
				$conn->commit();
			}
		}
	}
	
	function getdata($url){

		for( $i=0; $i<=3; $i++ ) {
			
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 3);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
			$json = json_decode(curl_exec($curl),true);
			curl_close($curl);
			
			if($json){
				return $json;
			}
			//sleep(0.5);
		}
		return false;
	}

	function trustscore($usersid,$year_ago){
		
		global $apikey;
		
		$oldfriends = 0;
		$friends = 0;
		
		$url = sprintf("https://api.steampowered.com/ISteamUser/GetFriendList/v0001/?key=%s&steamid=%s&relationship=friend", $apikey, $usersid);
		$data = getdata($url);
			
		if(!empty($data['friendslist']['friends'])){
			
			foreach($data['friendslist']['friends'] as $value){
					
				$friends++;
				$relationship_age = (time() - $value['friend_since'])/86400; //number of days
					
				if($relationship_age > 30){
					$oldfriends ++;
				}
			}
		}
			
		$numberofgames = 0;
		$playtime = 0;
			
		$url = sprintf("https://api.steampowered.com/IPlayerService/GetOwnedGames/v0001/?key=%s&steamid=%s&format=json&include_played_free_games=1",$apikey, $usersid);
		$data = getdata($url);
		
		if(!empty($data['response'])){
		
			//$numberofgames = $data['response']['game_count'];
					
			foreach($data['response']['games'] as $value){
				$playtime += $value['playtime_forever'];
				if($value['playtime_forever']/60 > 3){
					$numberofgames++;
				}
			}
			
			$playtime = ceil($playtime/60); //in hours
		}
		
		$steamlevel = 0;
		$url = sprintf("https://api.steampowered.com/IPlayerService/GetSteamLevel/v1?key=%s&steamid=%s", $apikey, $usersid);
		$data = getdata($url);
		
		if(!empty($data['response'])){
			$steamlevel = $data['response']['player_level'];
		}
		
		$score = 0; // Out of 100
		
		function scoring($max_score,$max_item,$item){
			
			if($item >= $max_item){
				return $max_score;
			}
			else{
				return (($max_score/$max_item)*$item);
			}
			
		}
		
		$score += scoring(5,4, $oldfriends);
		$score += scoring(60,500, $playtime);
		$score += scoring(15,10, $steamlevel);
		$score += scoring(5,4, $numberofgames);
		$score += scoring(15,1, $year_ago);
		
		if($score >= 60){
			return 2; //Good
		}
		else{
			return 1; // Poor
		}
	}
?>
    
