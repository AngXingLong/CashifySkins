<?php 
require 'shared/nav-menu.php';
require $_SERVER['DOCUMENT_ROOT']."/shared/support-category-code.php";

$title = "CashifySkins - Support";
$description = "CashifySkins is digtal marketplace where you can trade CS:GO, TF2, Dota 2 and Steam virtual items using real money!";
$css[] = "/css/pagenation.css";
$css[] = "/css/ticket-inbox.css";
output_header(); 

	$page;
	$lastpage;
	$data = array();
	
	$number_of_records = select("select count(*) as count from support_ticket where status = 0");
	$number_of_records = $number_of_records[0]['count'];
	
	if(!empty($_GET['page']) && filter_var($_GET['page'], FILTER_VALIDATE_INT)){
		$page = $_GET['page'];
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

	$inbox = select("select st.id, st.staff_assigned_sid, u.name, st.category, st.title, st.status, DATE_FORMAT(st.time_opened,'%d %b %Y %h:%i %p') as time_opened,  DATE_FORMAT(st.time_closed,'%d %b %Y %h:%i %p') as time_closed from support_ticket st left join user u on st.staff_assigned_sid = u.steamid where st.status = 0 or st.time_closed > DATE_SUB(now(), INTERVAL 1 DAY) order by st.status,id desc limit ? , ?",array($start,$per_page));

	//left join user u on st.staff_assigned_sid = u.steamid 

?>
<div id="basic_content_wrapper">
<h1 style='float:left; margin-left:20px;'>Support Tickets</h1>

<table>
<thead><tr><th>Ticket ID</th><th>Title</th><th>Category</th><th>Assigned</th><th>Created</th><th>Status</th></tr></thead>
<?php 
foreach($inbox as $v){
	
	$ticket_id = $v['id'];
	$category = $support_catergory_code[$v['category']];
	$title = $v['title'];
	$time_opened = $v['time_opened'];
	$time_closed = $v['time_closed'];	
	$staff_assigned_sid = $v['staff_assigned_sid'];
	$assigned = $v['name'];
	$status = $support_ticket_status[$v['status']];
	if($status == "Closed" || $status == "Locked"){
		$status = $status." ($time_closed)";
	}
	
	if($staff_assigned_sid == 0){
		$assigned = "Unassigned";
	}
	else if($staff_assigned_sid == -1){
		$assigned = "Unassigned";
	}

	echo "<tr><td>$ticket_id</td><td><a href='/admin/ticket-read.php?ticket_id=$ticket_id'>$title</a></td><td>$category</td><td>$assigned</td><td>$time_opened</td><td>$status</td></tr>";
		

}
echo "</table>";
	$pagenation = "";

	if($page > 1){
		$pagenation .= "<a href='ticket-inbox.php?page=".($page-1)."' class='page_highlight'>«</a>";
	}
	
	$i = (1 > $page-3) ? $i = 1 : $page-3; 
	
	
	while(true){
		if($i+6 > $last_page && $i != 1){
			$i--;
		}else{
			break;
		}
	}
	
	
	$e = $i+7;

	for ($i; $i < $e; $i++) { 
		
		if($i == $page){
			$pagenation .= "<a href='ticket-inbox.php?page=".$i."' class='page_highlight'>".$i."</a>";
		}
		else if($i > $last_page){
			break;
		}
		else{
			$pagenation .= "<a href='ticket-inbox.php?page=".$i."'  class='page'>".$i."</a>";
		}
	}
	
	if($last_page > $page){
		$pagenation .= "<a href='ticket-inbox.php?page=".($last_page+1)."' class='page_highlight'>»</a>";
	}
	echo  "<div id='pagenation'>$pagenation</div>";
	
?>



</div>
<?php footer();?>

</body>
</html>



