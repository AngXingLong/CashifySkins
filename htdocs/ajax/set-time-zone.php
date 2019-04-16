<?php
	session_start();

	if(!empty($_SESSION['steamid']) && !empty($_SESSION['expired_time_zone'])){
		$time_zone = $_POST['time_zone'];
		if (preg_match( '/^(?:Z|[+-](?:2[0-3]|[01][0-9]):[0-5][0-9])$/', $time_zone))//[+-][0-9]{2}:[0-9]{2}\b
		{
			require $_SERVER['DOCUMENT_ROOT']."/shared/database.php";
			$validate = select("select CONVERT_TZ(now(), @@session.time_zone, ?) as validation",array($time_zone));
			if(!empty($validate[0]['validation'])){
				 $_SESSION['time_zone'] =  $time_zone;
				 $_SESSION['expired_time_zone'] = 0;
				 $conn->beginTransaction();
				 $stmt = $conn->prepare("update user set time_zone = ? where steamid = ?");
				 $stmt->execute(array($time_zone,$_SESSION['steamid']));
				 $conn->commit();		
			}  
		}    
	}
