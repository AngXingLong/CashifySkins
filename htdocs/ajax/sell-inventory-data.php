<?php
	session_start();
	require "member-access.php";
	require "../shared/database.php";
	require "fetch-steam-inventory.php";
	//$_POST['appid'] = 730;
	
	$output['success'] = 0;

	if(!empty($_POST['appid']) && validate_appid($_POST['appid'])){
		$appid = $_POST['appid'];
		$output = fetch_inventory();
	}
	
	echo json_encode($output);