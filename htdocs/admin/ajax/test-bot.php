<?php
	session_start();
	require "admin-access.php";
	require $_SERVER['DOCUMENT_ROOT']."/shared/redis.php";
	require $_SERVER['DOCUMENT_ROOT']."/shared/database.php";
		
	if(!empty($_POST['steamid']) && ctype_digit(strval($_POST['steamid']))){
		$exist = select("select count(*) as count from bot where status = 1 and steamid = ? limit 1",array($_POST['steamid']));
		if(!empty($exist) && $exist[0]['count'] == 1){
			$redis->lpush($_POST['steamid'], -1); 
		}
		echo json_encode(array('success'=>1));
	}

	