<?php
header("Access-Control-Allow-Origin: *");
require_once('secret/database.php');
//showAttendance();

//function showAttendance()
 //{
	//global $conn;
		
		$query = select("SELECT u.ID, u.Username, e.Event_Name, a.photo, a.Time_In
		FROM Attendance AS a
		INNER JOIN Event AS e 
		ON a.Event_ID = e.ID
		INNER JOIN UserProfile AS u
		ON a.User_ID = u.ID") ;
		
		//$result = $conn->query($query);

		//while ($row = mysql_fetch_assoc($result)) {
		
//}
	 	echo json_encode($query);

 //}
?>