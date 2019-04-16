<?php

require "../database.php";

if(!empty($_POST['id']) && ctype_digit(strval($_POST['id']))){
	
	$event_id = $_POST['id'];
	
	$event = select("select e.id, e.event_name, if(time_start > now(),0,1) as active, DATE_FORMAT(e.time_start,'%d %b %Y (%h:%i %p)') as time_start ,  DATE_FORMAT(e.time_end,'%d %b %Y (%h:%i %p)') as time_end, l.name as location_name, l.street_name as address, l.image FROM Event e LEFT JOIN Location l on e.location_id = l.id where e.id = ?",array($event_id));
	
	echo json_encode($event);
}