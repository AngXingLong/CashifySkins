<?php
header("Access-Control-Allow-Origin: *");
require_once('secret/database.php');

		
		$query = select("SELECT a.User_ID, e.Event_Name, l.Street_Name, e.Host_ID, e.Time_Start, e.Time_End
		FROM Event AS e
		INNER JOIN Location AS l
		ON e.Location_ID = l.ID
		INNER JOIN Attendance AS a
		ON e.ID = a.Event_ID ") ;
		

		

	 	echo json_encode($query);


?>