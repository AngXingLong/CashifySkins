<?php
	//Output Error type 
	//type 1 nric
	//type 2 location id
	//type 3 facial recongition
	//type 4 geolocation
	//type 5 already stamp attendence or user does not need to stamp their attendence for this location

	header("Access-Control-Allow-Origin: *");
	
	$output = array("success"=>0,"type"=>0);

	$file_name = "";
	$image_url = "http://mp08.bit-mp.biz/image/attendance-photo/";
	$storage_directory = "/home/www/mp08.bit-mp.biz/image/attendance-photo/";
	
	if(empty($_POST['nric']) || empty( $_POST['location_id']) || !$_FILES['photo']['name']){
		die;
	}
	
	if(!$_FILES['photo']['error'])
	{
		$valid_file = true;
		
		$file_name = uniqid().".png";
		$image_url = $image_url.$file_name;
		
		/*	
		if($_FILES['photo']['size'] > (3145728)) //can't be larger than 3 MB
		{
			$valid_file = false;
			$output["msg"] = "Your file\'s size is to large.";
			echo json_encode($output);
			die;
		}*/
		
		if($valid_file)
		{
			move_uploaded_file($_FILES['photo']['tmp_name'], $storage_directory.$file_name);
		}

	}
	else
	{
		$output["msg"] = 'Your upload triggered the following error:  '.$_FILES['photo']['error'];
		$output["type"] = 3;
		echo json_encode($output);
		die;
	}
	
	require "../secret/database.php";
	require "../secret/skybiometry-creditationals.php";
	require "FCClientPHP.php";
	
	$bio = new FCClientPHP($bio_api_key,$bio_api_secret);
	
	$nric = $_POST['nric'];
	$location_id = $_POST['location_id'];
	
	$longitude = $_POST['longitude'];
	$latitude = $_POST['latitude'];
	
	$coordinates = select("select latitude, longitude from Location where id = ?",array($location_id));
	if(!empty($coordinates)){
		$threshold = 0.025;
		$coordinates = $coordinates[0];
		
		if($latitude > $coordinates['latitude'] + $threshold || $latitude < $coordinates['latitude'] - $threshold){
			$output["msg"] = 'Your GPS coordiates shows you are not within building premises to take attendence';
			$output["type"] = 4;
			echo json_encode($output);
			die;
		}
		
		if($longitude > $coordinates['longitude'] + $threshold && $longitude < $coordinates['longitude'] - $threshold){
			$output["msg"] = 'Your GPS coordiates shows you are not within building premises to take attendence';
			$output["type"] = 4;
			echo json_encode($output);
			die;
		}
	}

	
	
	$uid = select("select id from UserProfile where NRIC = ?",array($nric));
	
	if(empty($uid[0]['id'])){
		$output["msg"] = "User does not exist in record";
		$output["type"] = 1;
		echo json_encode($output);
		die;
	}
	
	$uid = $uid[0]['id'];

	$verfication_result = $bio->faces_recognize($image_url,$uid,"tracking",null,null,null);
	$confidence = $verfication_result['photos'][0]["tags"][0]["uids"];
	
	if(!empty($confidence)){
		$confidence = $confidence[0]["confidence"];
	}else{
		$confidence  = 0;
	}
	
	if($confidence > 50){
		$stmt = $conn->prepare("update Attendance a inner join Event e on e.ID = a.Event_ID set a.Time_In = now(), a.photo = ? where a.User_ID = ? and e.Location_ID = ? and a.Time_In is null and now() > DATE_SUB(e.Time_Start, INTERVAL 2 HOUR);");
		
		$stmt->execute(array($file_name,$uid,$location_id));	
		$count = $stmt->rowCount();
		
		if($count >= 1){
			$output["success"] = 1;
		}else{
			$output["type"] = 5;
			$output["msg"] = "Your attendence has already been taken";
		}
	}
	else{
		unlink ($storage_directory.$file_name);
		$output["msg"] = "Facical reconigition does not match.";
		$output["type"] = 3;
	}
	
	echo json_encode($output);
