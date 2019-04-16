<?php

require "../database.php";
$_POST['id'] = 1;
if(!empty($_POST['id']) && ctype_digit(strval($_POST['id']))){
$location = $_POST['id'];

$location = select("select id, name FROM Location WHERE id = ?",array($location));

if(!empty($location)){
	echo json_encode(array("location"=>$location[0]));
}

}
?>