<?php

require "../secret/database.php";
$search = "";
$page = 1;
$last_page = 1;
$per_page = 6;
$output = array();

if(!empty($_POST['search'])){
	$search_parameters = array("%".$_POST['search']."%","%".$_POST['search']."%");
	$number_of_records = select("select count(*) as count FROM Event e LEFT JOIN Location l on e.location_id = l.id where e.time_end > now() and (e.event_name like ? or l.name like ?);",$search_parameters);
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
	
	$output['data'] = select("select e.id, e.event_name, e.strict, if( DATE_SUB(time_start, INTERVAL 2 HOUR) >now(),0,1) as active, DATE_FORMAT(e.time_start,'%d %b %Y (%h:%i %p)') as time_start , l.name as location_name, l.image FROM Event e LEFT JOIN Location l on e.location_id = l.id where e.time_end > now() and (e.event_name like ? or l.name like ?) order by e.time_start limit ?,?;",$search_parameters);
	
}
else{
	
	$number_of_records = select("select count(*) as count FROM Event e LEFT JOIN Location l on e.location_id = l.id where e.time_end > now();");
	
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
	
	$output['data'] = select("select e.id, e.event_name, e.strict, if(DATE_SUB(time_start, INTERVAL 2 HOUR) > now(),0,1) as active, DATE_FORMAT(e.time_start,'%d %b %Y (%h:%i %p)') as time_start, l.name as location_name, l.image FROM Event e LEFT JOIN Location l on e.location_id = l.id where e.time_end > now() order by e.time_start limit ?,?;",array($start,$per_page));
	
}

$output['last_page'] = $last_page;
echo json_encode($output);
