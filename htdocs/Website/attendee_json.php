<?php
header("Access-Control-Allow-Origin: *");
require_once('secret/database.php');

		
		$query = select("SELECT u.ID, u.Name, u.NRIC, u.Username, u.StaffID, r.Role_Name, u.Inactive, u.Photo
		FROM UserProfile AS u
		INNER JOIN Role AS r
		ON u.Role = r.ID") ;
		

		

	 	echo json_encode($query);


?>