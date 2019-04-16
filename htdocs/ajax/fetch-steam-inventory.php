<?php
	global $appid;
	require $_SERVER['DOCUMENT_ROOT']."/shared/app-code.php";
	require $_SERVER['DOCUMENT_ROOT']."/shared/redis.php";
	
	$banned_items = array("753-Gems","Dueling Mini-Game");

/*	session_start();
	require "../shared/database.php";
	global $appid;
	$appid = 570;
	fetch_inventory();*/
	
	function unset_inventory_session(){
		unset($_SESSION["cached_time"]);
		unset($_SESSION["cached_appid"]);	
		unset($_SESSION['cached_assets']);
	}
	
	function fetch_inventory(){

		global $rgInventory, $rgDescription, $in, $db_input, $banned_items, $appid;

		unset_inventory_session();
		
		$usersid = $_SESSION['steamid'];

		if($appid == 753){
			$url = sprintf('https://steamcommunity.com/profiles/%s/inventory/json/%s/6?trading=1', $usersid, $appid);
		}else{
			$url = sprintf('https://steamcommunity.com/profiles/%s/inventory/json/%s/2?trading=1', $usersid, $appid);
		}
		
		$fetch_inventory_success = false;
		$detailed_inventory;
		$summarized_inventory = "";
		$cached_assets = "";
		$inventory_sort;
		
		for ($x = 0; $x <= 3; $x++) {
			$json = getdata($url);
		
			if(isset($json['success'])){
				break;
			}
		} 

		if(!$json['success']){
			$output["success"] = 0;
			$output["error"] = "Your inventory is private";
			return $output;
		}
				
		if(empty($json['rgInventory']) || empty($json['rgDescriptions'])){
			$output["success"] = 0;
			$output["error"] = "Nothing tradeable";
			return $output;
		}
			
		$rgInventory = $json['rgInventory'];
		$rgDescription = $json['rgDescriptions'];
			
		foreach($rgInventory as $IKey=>$Ivalue){
				
			$classid = $Ivalue["classid"];
			$instanceid = $Ivalue["instanceid"];
			$identifier = $classid."_".$instanceid;
				
			if(!empty($Ivalue["amount"]) && $Ivalue["amount"] > 1){
				unset($rgDescription[$identifier]);
				unset($rgInventory[$IKey]);
				continue;
			}
				
			$name = $rgDescription[$identifier]["market_hash_name"];
			$deny = false;
	
			if(empty($filter) || in_array($name,$filter,true)){
				$inventory_sort[] = $name;
				$cached_assets[$name][] = $IKey;
			}
	
		}
			
		$inventory_sort = array_count_values($inventory_sort);
		$db_input = array_keys($inventory_sort);
		$in  = str_repeat('?,', count($db_input) - 1) . '?';
		$db_input[] = $appid;
			
		
		//Update Inventory	
		update_pricelist();
		$cached_assets_by_id = "";
		$db_data = select("select id, name, IFNULL((select avg_price from price_summary psd where psd.item_id = p.id order by psd.time limit 1 ),0) as steam_market_price FROM pricelist p where p.name in (".$in.") and p.appid = ?",$db_input);
			
		foreach($rgDescription as $key=>$value){
					
			$name = $value["market_hash_name"];
				
			if(in_array($name,$banned_items)){
				continue;
			}
				
			$display_name = $value["market_name"];
			$tags_array = $value["tags"];
			$color = $value["name_color"];
			/*$tags = array();
					
			if(!empty($value["tags"])){
				foreach($value["tags"] as $tag_value){
					$tags[] = $tag_value['name'];
				}
			}*/
				
			if(empty($inventory_sort[$name])){
				continue;
			}
				
			$quantity =	$inventory_sort[$name];
			$icon_url = isset($value['icon_url_large']) ? $value['icon_url_large'] : $value['icon_url'];
			$type = $value["type"];
							
			$steam_market_price = "0";
							
			foreach($db_data as $v2){
				if($value["market_hash_name"] == $v2["name"]){
					$steam_market_price = $v2["steam_market_price"];
					$id = $v2["id"];					
					break;
				}
			}
			
			$cached_assets_by_id[$id] = $cached_assets[$name];
					
			$summarized_inventory[] = array(
					"id"=>$id,
					"display_name"=>$display_name,
					"type"=>$type,
					"quantity"=>$quantity,
					"suggested_price"=>$steam_market_price,
					"url"=>$icon_url,
					"color"=>$color
					//"tags"=>$tags
			);
								
		}
		
		$output["data"] = $summarized_inventory;
		$output["success"] = 1;
		
		$_SESSION["cached_assets"] = $cached_assets_by_id;	
		$_SESSION["cached_time"] = time();
		$_SESSION["cached_appid"] = $appid;
		
		return $output;

	}

	
	
		
	function update_pricelist(){
		global $banned_items, $rgInventory, $rgDescription, $in, $db_input, $conn,$appid;
		
		$in_pricelist = select("select p.name, IF(expire > NOW(),1,0) as should_update FROM pricelist p where p.name in (".$in.") and appid = ?",$db_input);
		
		$update_pricelist = array();
		$duplicates = array();
		
		foreach($rgDescription as $key => $v){
		
			$name = $v['market_hash_name'];
			$bool = false;
			$should_update = 0;
			$has_duplicate = false;
			
			if(in_array($name,$banned_items)){
				continue;
			}
			
			foreach($duplicates as $v2){
				if($v2 == $name){
					unset($rgDescription[$key]);
					$has_duplicate = true;
					break;
				}
			}
			
			if($has_duplicate){
				continue;
			}
			
			$duplicates[] = $name; 
			
			foreach($in_pricelist as $value2){
				if($value2['name'] == $name){
					
					if($value2['should_update'] == 1){
						$should_update = 1;
					}else{
						$bool = true;
					}
					break;
				}
			}
		
			if(!$bool){
				$details_array = $v;
				$details_array = array_merge($details_array,item_details($v));
				if($details_array["bad_image"] && $should_update){continue;} // if item already in db and contains a bad image we can discard it
				$details_array["should_update"] = $should_update;
				$update_pricelist[] = $details_array;
			}
		
		}
		
		try{
			
		$conn->beginTransaction();
			
		foreach($update_pricelist as $v){
			$color = (!empty($v['name_color'])) ? $v['name_color'] : "";
			$commodity = (!empty($v['commodity'])) ? 1 : 0;
			
			$tags_array = $v["tags"];
			$tags = array();
				
			if(!empty($v["tags"])){
				foreach($v["tags"] as $tag_value){
					$tags[] = $tag_value['name'];
				}
			}
			$tags = implode(",", $tags);
			
			$description = $v['complete_description'];
			$display_name = $v["market_name"];
			$name = $v["market_hash_name"];
			$icon_url  = (empty($v["icon_url_large"]) ? $v["icon_url"] : $v["icon_url_large"]); 
			//DATE_SUB(NOW(), INTERVAL 1 MONTH)
			
			if($v["should_update"]){
				$stmt = $conn->prepare("update pricelist set display_name = ?, image = ?, description = ?, type = ?,tags = ?, commodity = ?, color = ?, expire = DATE_ADD(NOW(),INTERVAL 1 MONTH) where name = ? and appid = ?");
				$stmt->execute(array($display_name,$icon_url,$description,$v['type'],$tags,$commodity, $color, $name, $appid ));
			}
			else{
				$expire_value = $v["bad_image"] ? "NOW()" : "DATE_ADD(NOW(),INTERVAL 1 MONTH)";
				$stmt = $conn->prepare("insert into pricelist (name,appid,display_name,image,description,type,tags,commodity,color,expire) VALUES (?,?,?,?,?,?,?,?,?,$expire_value)");
				$stmt->execute(array($name, $appid, $display_name, $icon_url, $description, $v['type'], $tags, $commodity, $color));
			}
		}
		
		$conn->commit();
		}
		catch(exception $e){
			echo $e;
		}
	
	}
	function item_details($v){
		global $appid;
		
		$bad_image = false;
		$description = "";
		$color = "";
		$closed = true;
		$break_added = 0;
	
		
		if(!empty($v["descriptions"]) && count($v["descriptions"]) > 0){
			
			foreach($v["descriptions"] as $v2){
				
				if(isset($v2["color"]) && $v2["color"] != " "){	
							
					if($v2["color"] != $color && !$closed){
						$description .= "</span>";
						$closed = true;
					}
								
					if($closed){
						$color = $v2["color"];
							
						if($color){
							$description .= "<span style=color:#".$color.";>";
							$closed = false;
						}
								
					}
				}
				else if(!$closed && $color){
					$description .= "</span>";
					$closed = true;
					$color = false;
				}
				
				// Remove additional spacing
				if($appid == 753 && $v2["value"] == "\n"){
					$description .= "<br>";
				}
				else{
					$description .= $v2["value"]."<br>";
					// we want a stock image
					if($appid == 570 && strpos($v2["value"], 'Games Watched:') !== false){
						$bad_image = true;
					}
					else if ($appid == 440 && strpos($v2["value"], 'Paint Color:') !== false){
						$bad_image = true;
					}
				}
			}
			
			if(!$closed){
				$description .= "</span>";
			}
					
		}
		
		return array("complete_description"=>$description,"bad_image"=>$bad_image);
				
	}
	
	
	function getdata($url){
		global $redis;
		
		$cookie = "";
		$cookie = $redis->get('auth_cookie');
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_COOKIE, $cookie);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POST, 0);

		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.94 Safari/537.36");
		
		$headers = array();
		$headers[] = 'Accept: application/json, text/javascript;q=0.9, */*;q=0.5';
		//$headers[] = 'Accept-Encoding: gzip, deflate';
		$headers[] = 'Cache-Control: no-cache';
		$headers[] = 'Content-Type: application/x-www-form-urlencoded; charset=utf-8';
	
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		//curl_setopt($ch, CURLOPT_ENCODING, "gzip");
		$json = curl_exec($ch);
		
		curl_close($ch);
		
		if($json){
			return json_decode($json,true);
		}
		
		return false;	
	}
	
	





?>