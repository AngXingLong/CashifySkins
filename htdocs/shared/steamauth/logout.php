<?php
session_start();

require $_SERVER['DOCUMENT_ROOT']."/shared/database.php";
require $_SERVER['DOCUMENT_ROOT']."/shared/steamauth/login.php";

if(!empty($_SESSION['steamid'])){
	$conn->beginTransaction();
	$stmt = $conn->prepare("delete from auth_tokens where usersid = ?");
	$stmt->execute(array($_SESSION['steamid']));
	$conn->commit();
}

setcookie ("userinfo", "", 1,"/");
setcookie ("userinfo", "", false,"/");
unset($_COOKIE["userinfo"]);
session_unset();
		
header("Location: /index.php");
die;

?>