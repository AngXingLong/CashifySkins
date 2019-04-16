<?php

require "../secret/database.php";
$search = "";
$page = 1;
$last_page = 1;
$per_page = 6;
$output = array();
$attendees = array();


if(!empty($_POST['id']) && ctype_digit(strval($_POST['id']))){
	
	$event_id = $_POST['id'];
	
	if(!empty($_POST['search'])){
		$search_parameters = array($event_id,"%".$_POST['search']."%");
		
		$number_of_records = select("select count(*) as count from Attendance ed LEFT JOIN UserProfile u on u.ID = ed.User_ID where ed.event_ID = ? and u.name like ? ;",$search_parameters);

		$number_of_records = $number_of_records[0]['count'];
		
		if(!empty($_POST['page']) && filter_var($_POST['page'], FILTER_VALIDATE_INT)){
			$page = $_POST['page'];
		}else{
			$page = 1;
		}
	
		$last_page = ceil($number_of_records/$per_page);	
			
		if($page < 1){
			$page = 1;
		}else if($last_page < 1){
			$page = 1;
			$last_page = 1;
		}
		else if($page > $last_page){
			$page = $last_page;
		}
		
		$start = ($page-1)*$per_page;
		
		$search_parameters[] = $start;
		$search_parameters[] = $per_page;
		
		$output['data'] = select("select u.name, u.photo, if(ed.time_in = '0000-00-00 00:00:00',0,1) as attended from Attendance ed LEFT JOIN UserProfile u on u.ID = ed.User_ID where ed.event_ID = ? and u.name like ? order by u.name limit ?,?",$search_parameters);
	}
	else{
		
		$number_of_records = select("select count(*) as count from Attendance ed LEFT JOIN UserProfile u on u.ID = ed.User_ID where ed.event_ID = ?",
		array($event_id));

		$number_of_records = $number_of_records[0]['count'];
	
		if(!empty($_POST['page']) && filter_var($_POST['page'], FILTER_VALIDATE_INT)){
			$page = $_POST['page'];
		}else{
			$page = 1;
		}
	
		$last_page = ceil($number_of_records/$per_page);	
			
		if($page < 1){
			$page = 1;
		}else if($last_page < 1){
			$page = 1;
			$last_page = 1;
		}
		else if($page > $last_page){
			$page = $last_page;
		}
		
		$start = ($page-1)*$per_page;
		
		$output['data'] = select("select u.name, u.photo, if(ed.time_in = '0000-00-00 00:00:00',0,1) as attended from Attendance ed LEFT JOIN UserProfile u on u.ID = ed.User_ID where ed.event_ID = ? order by u.name limit ?,?",array($event_id,$start,$per_page));
	}
	$output['last_page'] = $last_page;
	echo json_encode($output);
}