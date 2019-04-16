<?php
	//Output Error type 
	//type 1 nric
	//type 2 location id
	//type 3 facial recongition
	//type 4 geolocation
	//type 5 already stamp attendence or user does not need to stamp their attendence for this location

	header("Access-Control-Allow-Origin: *");
	
	$output = array("success"=>0,"type"=>0);
	
	if(empty($_POST['nric']) || empty($_POST['location_id'])){
		die;
	}
	
	require "../secret/database.php";
	
	$nric = $_POST['nric'];
	$location_id = $_POST['location_id'];
	
	// Check if user exist in database
	$uid = select("select id from UserProfile where NRIC = ?",array($nric));
	
	if(empty($uid[0]['id'])){
		$output["msg"] = "User does not exist in record";
		$output["type"] = 1;
		echo json_encode($output);
		die;
	}
	
	$uid = $uid[0]['id'];
	
	$user_registered_validation = select("select count(*) as count from Attendance a inner join Event e on e.ID = a.Event_ID where a.User_ID = ? and e.Location_ID = ? and now() > DATE_SUB(e.Time_Start, INTERVAL 2 HOUR) and strict = 0;",array($uid,$location_id));
	
	if($user_registered_validation[0]['count'] == 0){
		$output["msg"] = "You are not registed for this event";
		$output["type"] = 1;
		echo json_encode($output);
		die;
	}
	
	$stmt = $conn->prepare("update Attendance a inner join Event e on e.ID = a.Event_ID set a.Time_In = now() where a.User_ID = ? and e.Location_ID = ? and a.Time_In is null and now() > DATE_SUB(e.Time_Start, INTERVAL 2 HOUR) and strict = 0;");
		
	$stmt->execute(array($uid,$location_id));	
	$count = $stmt->rowCount();
		
	if($count >= 1){
		$output["success"] = 1;
	}
	else{
		$output["type"] = 5;
		$output["msg"] = "Your attendence has already been taken";
	}	
	

	echo json_encode($output);
