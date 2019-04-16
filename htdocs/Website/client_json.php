<?php
header("Access-Control-Allow-Origin: *");
require_once('secret/database.php');

		
		$query = select("SELECT Name,Contact,Address
		FROM Organisation") ;
		

		

	 	echo json_encode($query);


?>