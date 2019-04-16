<?php
header("Access-Control-Allow-Origin: *");
require_once('secret/database.php');

		
		$query = select("SELECT g.user_id, u.Username 
		FROM Login_Log AS g
		INNER JOIN UserProfile AS u
		ON g.user_id = u.ID") ;
		

		

	 	echo json_encode($query);


?>