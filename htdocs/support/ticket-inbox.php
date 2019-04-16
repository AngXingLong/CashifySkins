<?php 
session_start();
if(empty($_SESSION['steamid'])){
	header("Location: /login-required.php");
	die;
}


require $_SERVER['DOCUMENT_ROOT'].'/shared/nav-menu.php';
require $_SERVER['DOCUMENT_ROOT']."/shared/support-category-code.php";

$title = "CashifySkins - Support";
$description = "CashifySkins is digtal marketplace where you can trade CS:GO, TF2, Dota 2 and Steam virtual items using real money!";
$css[] = "/css/pagenation.css";
$css[] = "/css/ticket-inbox.css";
output_header(); 

	$page;
	$lastpage;
	$data = array();
	
	$number_of_records = select("select count(*) as count from support_ticket where original_poster_sid = ?",array($_SESSION['steamid']));
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

	$inbox = select("select id, category, title, status, DATE_FORMAT(CONVERT_TZ(time_opened, '+00:00', ?),'%d %b %Y %h:%i %p') as time_opened,  DATE_FORMAT(time_closed,'%d %b %Y %h:%i %p')as time_closed from support_ticket where original_poster_sid = ? order by id desc limit ? , ?",array($_SESSION['time_zone'],$_SESSION['steamid'],$start,$per_page));

?>
<div id="basic_content_wrapper">
<h1 style='float:left; margin-left:20px;'>My Support Tickets</h1> <a href='/support/ticket-new.php' class='new_ticket_button'>Create Ticket</a>

<table>
<thead><tr><th>Title</th><th>Category</th><th>Status</th><th>Created</th></tr></thead>
<?php 
foreach($inbox as $v){
	
	$ticket_id = $v['id'];
	$category = $support_catergory_code[$v['category']];
	$title = $v['title'];
	$time_opened = $v['time_opened'];
	$time_closed = $v['time_closed'];
	$status = $v['status'];
	$status = $support_ticket_status[$status];
	if($status == "Closed" || $status == "Locked"){
		$status = $status." ($time_closed)";
	}
	echo "<tr><td><a href='/support/ticket-read.php?ticket_id=$ticket_id'>$title</a></td><td>$category</td><td>$status</td><td>$time_opened</td></tr>";
		

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



