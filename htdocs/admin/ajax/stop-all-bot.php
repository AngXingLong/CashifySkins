<?php 
	session_start();
	require "admin-access.php";
	require "../../shared/database.php";
	require "../../shared/transaction-code.php";
	require $_SERVER['DOCUMENT_ROOT']."/shared/redis.php";
	
	$redis->publish("bot manager", json_encode(array("process"=>0,"steamid"=>"1")));
	echo json_encode(array("success"=>1));
	