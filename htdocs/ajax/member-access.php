<?php

if(empty($_SESSION['steamid']) || empty($_SESSION['csrf_token']) || empty(getallheaders()['X-CSRF-TOKEN']) || $_SESSION['csrf_token'] != getallheaders()['X-CSRF-TOKEN']){
	die;
}

?>