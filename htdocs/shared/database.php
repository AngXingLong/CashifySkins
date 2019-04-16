<?php

require(__DIR__."/../../settings/database-config.php");
global $conn;
$conn = new PDO("mysql:host=$servername;dbname=".$dbname.";charset=".$charset."", $username, $password);
$conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

//$item_name = array_column($order,'item_id');
//$item_range  = str_repeat('?,', count($item_name) - 1) . '?';
function select($query,$array = null){
	
		global $conn;
		
		$stmt = $conn->prepare($query);
		$stmt->execute($array);		
		$stmt->setFetchMode(PDO::FETCH_ASSOC); 
		return $stmt->fetchAll();
		
}

function count_row($query,$array = null){ // used for validation
	
		global $conn;
		
		$stmt = $conn->prepare($query);
		$stmt->execute($array);		
		$stmt->setFetchMode(PDO::FETCH_ASSOC); 
		$count = $stmt->fetchAll();
		return (!empty($count[0]['count(*)'])) ? $count[0]['count(*)'] : 0;
		
}

function pagination_offset($current_page,$last_page,$per_page){
		
	if(ctype_digit(strval($current_page))){	
		if($current_page < 1){
			$current_page = 1;
		}else if($current_page < 1){
			$current_page = 1;
			$last_page = 1;
		}
		else if($current_page > $last_page){
			$current_page = $last_page;
		}
	}
	
	return ($current_page-1)*$per_page;
}

function pagination_last_page($number_of_records,$per_page){
	return ceil($number_of_records/$per_page);
}


?>