<?php
	session_start();
	
	require "../shared/database.php";
		
	$output = "";
	//$_POST['id'] = 444;
	
	if(empty($_POST['id'])){
		die;
	}
	
	$item_id = $_POST['id'];
	
	$number_of_pages = select("select count(*) as count from inventory i inner join item_transaction it on i.botsid = it.botsid and i.assetid = it.assetid and i.item_id = it.item_id where it.item_id = ? and it.status = 1;",array($item_id));
	$number_of_pages = $number_of_pages[0]['count'];
	
	if(!empty($_POST['page']) && filter_var($_POST['page'], FILTER_VALIDATE_INT)){
		$page = $_POST['page'];
	}else{
		$page = 1;
	}
		
	$per_page = 10;
	
	$last_page = ceil($number_of_pages/$per_page);	
		
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

	$data = select("select it.id, it.description, it.price from inventory i inner join item_transaction it on i.botsid = it.botsid and i.assetid = it.assetid and i.item_id = it.item_id where it.status = 1 and it.item_id = ? limit ? , ? ;",array($item_id,$start,$per_page));

	foreach($data as $k=>$v){
		$data[$k]["description"] =  gzdecode(base64_decode($data[$k]["description"]));
	}
	
	$output['currentpage'] = $page;
	$output['totalpage'] = $last_page;	
	$output['data'] = $data;
	
	echo json_encode($output);
	