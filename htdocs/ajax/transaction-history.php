<?php 

session_start();
require "member-access.php";
require "../shared/database.php";


if(!isset($_POST['type'])){
	die;
}
	
$page;
$lastpage;
$data = array();
$steamid = $_SESSION['steamid'];
$type = $_POST['type'];
	
$admin = 0;
$active = !empty($_POST['active']) ? $_POST['active'] : 0;
	
if(!empty($_POST['steamid'])){ 
	require $_SERVER['DOCUMENT_ROOT']."/shared/user-type-code.php";
	if(!empty($_SESSION['account_type']) || array_key_exists($_SESSION['account_type'],$is_staff)){
		$usersid = $_POST['steamid'];
		$admin = 1;
	}
}
	
	
$stmt = $conn->prepare("set @time_zone := ?;");
$stmt->execute(array($_SESSION['time_zone']));	

if($type == 0){
	$query_filter = "from trade_transaction tt inner join bot b on tt.botsid = b.steamid where tt.usersid = ?";
	$additional_col = "";
	
	if($admin){
		$additional_col = "tt.staff_comment,";
	}
	
	if($active){
		$query_filter .= " and (tt.time_end > date_sub(now(), interval 10 minute) or tt.status in (0,1,2))";
	}

	$number_of_records = count_row("select count(*) $query_filter;",array($steamid));

	if(!empty($_POST['page']) && filter_var($_POST['page'], FILTER_VALIDATE_INT)){
		$page = $_POST['page'];
	}else{
		$page = 1;
	}
		
	$per_page = 10;
	
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

	$summary = select("select tt.id, b.name, tt.type, tt.status, tt.security_token, IFNULL(tt.status_comment,'') as status_comment, $additional_col DATE_FORMAT(CONVERT_TZ(tt.time_start,'+00:00',@time_zone),'%d %b %Y %h:%i %p') as time_start, IFNULL(DATE_FORMAT(CONVERT_TZ(tt.time_end,'+00:00',@time_zone),'%d %b %Y  %h:%i %p'),'') as time_end $query_filter ORDER BY id DESC limit ? , ? ;" , 
	array($steamid,$start,$per_page));
		
	$id = array_column($summary,'id');
	$details = [];

	if(!empty($id)){
		$in  = str_repeat('?,', count($id) - 1) . '?';
		$details = select("select ttd.trade_id as id, p.display_name, p.appid, count(*) as quantity from trade_transaction_details ttd inner join item_transaction it on ttd.item_transaction_id = it.id inner join pricelist p on p.id = it.item_id where ttd.trade_id in (".$in .")  group by p.id, ttd.trade_id ;" , $id);

	}

	$output['totalpage'] = $last_page;	
	$output['summary'] = $summary;
	$output['details'] = $details;
}
else if($type == 1){

	$number_of_records = count_row("select count(*) from buy_order where steamid = ?", array($steamid));
	
	if(!empty($_POST['page']) && filter_var($_POST['page'], FILTER_VALIDATE_INT)){
		$page = $_POST['page'];
	}else{
		$page = 1;
	}
		
	$per_page = 10;
	
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
	
	$query_parameter[] = $start;
	$query_parameter[] = $per_page;

	$summary = select("select o.id, p.id as item_id, p.name, p.display_name, p.color, p.appid, o.quantity, o.price, p.image from buy_order o inner join pricelist p on p.id = o.item_id where o.steamid = ? limit ? , ? ;", array($steamid,$start,$per_page));
		
	$output['totalpage'] = $last_page;	
	$output['summary'] = $summary;
	
}
else if($type == 2){
	
	$additional_col = "";
	$query_filter = "from item_transaction it inner join pricelist p on p.id = it.item_id where it.buyer_sid = ?";
	$query_parameter = array($steamid);
	$filter = "";
	
	if(!empty($_POST['filter'])){
		
		$filter = json_decode($_POST['filter'],true);
	
		if(!empty($filter['search_term'])){
			$query_filter .= " and p.display_name like ?";
			$query_parameter[] = "%".$filter['search_term']."%";
		}
		
		if(!empty($filter['appid'])){
			$query_filter .= " and p.appid = ?";
			$query_parameter[] = $filter['appid'];
		}
		if(isset($filter['status']) && !$active){
			$query_filter .= " and it.status = ?";
			$query_parameter[] = $filter['status'];
		}
		
	}

	if($active){
		$additional_col = "p.name ,count(*) as quantity,";
		$query_filter .= " and it.status in (8,10,11,16) group by p.id, it.status, it.price";
		$number_of_records = select("select SQL_CALC_FOUND_ROWS count(*) $query_filter;", $query_parameter);
		$number_of_records = select("SELECT FOUND_ROWS() as records");
		$number_of_records = $number_of_records[0]['records'];
	}
	else{
		$query_filter .= " order by it.id desc";
		$additional_col = "it.id, IFNULL(DATE_FORMAT(CONVERT_TZ(it.time_transacted,'+00:00',@time_zone),'%d %b %Y %h:%i %p'),'') as time_in , IFNULL(DATE_FORMAT(CONVERT_TZ(it.time_out,'+00:00',@time_zone),'%d %b %Y %h:%i %p'),'') as time_out,";
		$number_of_records = count_row("select count(*) $query_filter;",$query_parameter);
	}
	
	if(!empty($_POST['page']) && filter_var($_POST['page'], FILTER_VALIDATE_INT)){
		$page = $_POST['page'];
	}else{
		$page = 1;
	}
		
	$per_page = 10;
	
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
	
	$query_parameter[] = $start;
	$query_parameter[] = $per_page;

	$summary = select("select p.image, p.display_name, $additional_col p.appid, it.price, it.status, p.color $query_filter limit ? , ? ;",$query_parameter);
		
	$output['totalpage'] = $last_page;	
	$output['summary'] = $summary;

}
else if($type == 3){
	
	$query_filter = "from item_transaction it inner join pricelist p on p.id = it.item_id where it.seller_sid = ?";
	$query_parameter = array($steamid);
	$additional_col = "";
	if(!empty($_POST['filter'])){
		$filter = json_decode($_POST['filter'],true);
		if(!empty($filter['search_term'])){
			$query_filter .= " and p.display_name like ?";
			$query_parameter[] = "%".$filter['search_term']."%";
		}
		
		if(!empty($filter['appid'])){
			$query_filter .= " and p.appid = ?";
			$query_parameter[] = $filter['appid'];
		}

		if(isset($filter['status']) && !$active){
		
			if($filter['status'] == 2){
				$query_filter .= " and it.status > 9";
			}else{
				$query_filter .= " and it.status = ?";
				$query_parameter[] = $filter['status'];
			}
			
		}
	}
	
	if($active){
		$additional_col = "p.name, count(*) as quantity,";
		$query_filter .= " and it.status in (0,1,3,8) group by it.item_id, it.status, it.seller_receive";
		$number_of_records = select("select SQL_CALC_FOUND_ROWS count(*) $query_filter;", $query_parameter);
		$number_of_records = select("SELECT FOUND_ROWS() as records");
		$number_of_records = $number_of_records[0]['records'];
	}
	else{
		$query_filter .= " order by it.id desc";
		$additional_col = "it.id, it.price, IFNULL(DATE_FORMAT(CONVERT_TZ(it.time_in,'+00:00',@time_zone),'%d %b %Y %h:%i %p'),'') as time_in, IFNULL(DATE_FORMAT(CONVERT_TZ(it.time_transacted,'+00:00',@time_zone),'%d %b %Y %h:%i %p'),'') as time_out,";
		$number_of_records = count_row("select count(*) $query_filter;", $query_parameter);
	}
	
	if(!empty($_POST['page']) && filter_var($_POST['page'], FILTER_VALIDATE_INT)){
		$page = $_POST['page'];
	}else{
		$page = 1;
	}
		
	$per_page = 10;
	
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
	
	$query_parameter[] = $start;
	$query_parameter[] = $per_page;

	$summary = select("select p.image, p.display_name, it.item_id, $additional_col p.color, p.appid, it.seller_receive, if(it.status < 9, it.status, 2) as status $query_filter limit ? , ? ;" , $query_parameter);
		
	$output['totalpage'] = $last_page;	
	$output['summary'] = $summary;
	
}
else if($type == 4){
	$additional_col = "";
	if($admin){
		$additional_col = "node, staff_comment,";
	}else{
		$additional_col = "(CASE WHEN type = 0 THEN node ELSE '' END) AS node,";
	}
	$count = count_row("select count(*) from cash_transaction where steamid = ?;",array($steamid));
		
	if(!empty($_POST['page']) && filter_var($_POST['page'], FILTER_VALIDATE_INT)){
		$page = $_POST['page'];
	}else{
		$page = 1;
	}
		
	$per_page = 10;
	
	$lastpage = ceil($count/$per_page);	
		
	if($page < 1){
		$page = 1;
	}else if($lastpage < 1){
		$page = 1;
		$lastpage = 1;
	}
	else if($page > $lastpage){
		$page = $lastpage;
	}
		
	$start = ($page-1)*$per_page;

	$details = select("select id, $additional_col status, FORMAT(amount,2) as amount, type, status_comment,  DATE_FORMAT(CONVERT_TZ(time,'+00:00',@time_zone),'%d %b %Y %h:%i %p') as time from cash_transaction where steamid = ? ORDER BY id DESC limit ? , ? ;" , array($steamid,$start,$per_page));

	$output['totalpage'] = $lastpage;	
	$output['summary'] = $details;
	
	
}

if(!empty($output)){
	echo json_encode($output);
}


?>