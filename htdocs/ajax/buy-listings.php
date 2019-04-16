<?php
	require "../shared/database.php";
	
	global $query_parameter,$query_filter,$added;
	$pagenation_parameters = array();
	$query_parameter = array();
	$query_filter = "";
	
	function add_parameter($value,$parameter){
		global $query_parameter,$query_filter;
		$query_filter .= " and ".$parameter;
		$query_parameter[] = $value;
	}
	
	$game_preference = 1;
	
	if(empty($_GET['appid'])){
		if(!empty($_SESSION['steamid'])){
			$game_preference = select("select game_preference from user where steamid = ?", array($_SESSION['steamid']));
			$appid = $game_preference[0]['game_preference'];
		}else{
			$appid = 1;
		}
	}
	else{
		$appid = $_GET['appid'];
	}
	
	
	
	if($appid != 1){
		add_parameter($appid,"p.appid = ?");	
	}
		
	if(!empty($_GET['search_term'])){
		$search_term = $_GET['search_term'];
		add_parameter("%".$search_term."%","p.name like ?"); 
	}

	if(!empty($_GET['exterior'])){
		add_parameter("%".$_GET['exterior']."%","p.display_name like ?");
	}
	if(!empty($_GET['quality'])){
		add_parameter("%".$_GET['quality']."%","p.type like ?");
	}
	if(!empty($_GET['class'])){
		add_parameter("%".$_GET['class']."%","p.tags like ?");
	}
	if(!empty($_GET['grade'])){
		add_parameter("%".$_GET['grade']."%","p.type like ?");
	}
	if(!empty($_GET['rarity'])){
		add_parameter("%".$_GET['rarity']."%","p.tags like ?");
	}
	if(!empty($_GET['type'])){
		add_parameter("%".$_GET['type']."%","p.type like ?");
	}
	if(!empty($_GET['hero'])){
		add_parameter("%".$_GET['hero']."%","p.tags like ?");
	}
	if(!empty($_GET['category'])){
		add_parameter("%".$_GET['category']."%","p.tags like ?");
	}
	if(!empty($_GET['card'])){
		if($_GET['card'] == "Normal"){
			add_parameter("%(Foil)%","p.display_name not like ?");
		}else{
			add_parameter("%(Foil)%","p.display_name like ?");
		}
	}

	$orderby = "";
	$sort = "";
	
	if(!empty($_GET["sort"])){
		$sort = $_GET["sort"];
		switch ($sort) {	
			case 1:
			$orderby = "order by price DESC ";
			break;
					
			case 2:
			$orderby = "order by price ";
			break;
					
			case 3:
			$orderby = "order by p.display_name ";
			break;
						
			case 4:
			$orderby = "order by p.display_name DESC ";
			break;
		}
	}
	
	$count = select("select count(*) as count from pricelist p where (select count(*) from item_transaction o where o.status = 1 and p.id = o.item_id) > 0 $query_filter ",$query_parameter);
	
	$count = $count[0]['count'];

	if(!empty($_GET['page']) && filter_var($_GET['page'], FILTER_VALIDATE_INT)){
		$page = $_GET['page'];
	}else{
		$page = 1;
	}
		
	$perpage = 20;
	$lastpage = ceil($count/$perpage);	
		
	if($page < 1){
		$page = 1;
	}else if($lastpage < 1){
		$page = 1;
		$lastpage = 1;
	}
	else if($page > $lastpage){
		$page = $lastpage;
	}
		
	$start = ($page-1)*$perpage;	
	
	$query_parameter[] = $start;
	$query_parameter[] = $perpage;
	
	$query_filter = preg_replace("/and/","where",$query_filter, 1);
	
	$result = select("select p.id, p.name, p.display_name, p.color, p.appid, p.image, (select min(o.price) from item_transaction o where o.item_id = p.id
	and o.status = 1) as price, (select count(*) from item_transaction o where o.item_id = p.id and o.status = 1 ) as quantity
	from pricelist p $query_filter having quantity > 0 $orderby limit ? , ? ;",$query_parameter); 
	$output['data'] = $result;
	$output['totalpage'] = $lastpage;
	
	echo json_encode($output);