<?php
	session_start();
	
	//require "member-access.php";
	require $_SERVER['DOCUMENT_ROOT']."/shared/database.php";
	
	$data = select("select credit from user where steamid = ? limit 1",array($_SESSION['steamid']));
	$funds = !empty($data[0]['credit']) ? $data[0]['credit'] : 0;
	echo $funds;

	