<?php
	session_start();
	require $_SERVER['DOCUMENT_ROOT']."/shared/database.php";
	require $_SERVER['DOCUMENT_ROOT']."/shared/tools.php";
	require "admin-access.php";
	
	if(!empty($_POST['withdrawal_amount']) && !empty($_POST['withdrawal_amount'])){
		
	}