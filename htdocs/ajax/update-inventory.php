<?php
global $banned_items;
$banned_items = array("753-Gems","Dueling Mini-Game");

function update_pricelist(){
	global $banned_items, $rgInventory, $rgDescription, $in, $db_input, $conn, $appid;
	
	$in_pricelist = select("select p.name, IF(last_update <= DATE_SUB(NOW(), INTERVAL 1 MONTH),1,0) as should_update FROM pricelist p where p.name in (".$in.") and appid = ?",$db_input);
	
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
			$details_array["complete_description"] = item_details($v);
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
		
		if($v["should_update"]){
			$stmt = $conn->prepare("update pricelist set display_name = ?, image = ?, description = ?, type = ?,tags = ?, commodity = ?, color = ?, last_update = now() where name = ? and appid = ?");
			$stmt->execute(array($display_name,$icon_url,$description,$v['type'],$tags,$commodity, $color, $name, $appid ));
		}
		else{
			$stmt = $conn->prepare("insert into pricelist (name,appid,display_name,image,description,type,tags,commodity,color) VALUES (?,?,?,?,?,?,?,?,?)");
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
	
	$name = $v["market_hash_name"];
	$display_name = $v["market_name"];
	$type = $v["type"];
	$icon_url  = (empty($v["icon_url_large"]) ? $v["icon_url"] : $v["icon_url_large"]);
	
	$tags_array = $v["tags"];
	$tags = array();
		
	if(!empty($v["tags"])){
		foreach($v["tags"] as $tag_value){
			$tags[] = $tag_value['name'];
		}
	}
	
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
			
			$description .= $v2["value"]."<br>";
			
		}
		
		if(!$closed){
			$description .= "</span>";
		}
				
	}
	
	return $description;
			
}
