<?php

require $_SERVER['DOCUMENT_ROOT']."/shared/user-type-code.php";

if(empty($_SESSION['account_type']) || !array_key_exists($_SESSION['account_type'],$is_staff) || empty($_SESSION['steamid']) || empty($_SESSION['csrf_token']) || empty(getallheaders()['X-CSRF-TOKEN']) || $_SESSION['csrf_token'] != getallheaders()['X-CSRF-TOKEN']){
	die;
}
