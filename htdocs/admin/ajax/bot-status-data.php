<?php 
	session_start();
	require "admin-access.php";
	require "../../shared/database.php";
	
	$bot_status = select("select *, CAST(steamid as CHAR(50)) as steamid, CONVERT_TZ(last_reported,'+00:00',?) as last_reported from bot where steamid <> '76561198157372916' ",array($_SESSION['time_zone']));

	echo json_encode($bot_status);