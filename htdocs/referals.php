<?php

require $_SERVER['DOCUMENT_ROOT']."/shared/nav-menu.php";

if(empty($_SESSION["steamid"])){
	header('Location: /index.php');
	die;
}

$title = "Referals - CashifySkins";
$css[] = "/css/referals.css";
output_header(); 

$user_referal_id = select("select referal_id from user where steamid = ?",array($_SESSION['steamid']));
if(!empty($user_referal_id[0]['referal_id'])){
	$referal_id = $user_referal_id[0]['referal_id'];
}
else{
	while(true){
		$referal_id =  uniqid(1);
		$exist = count_row("select count(*) from user where referal_id = ?",array($referal_id));
		if(!$exist){
			$stmt = $conn->prepare("update user set referal_id = ? where steamid = ?");
			$stmt->execute(array($referal_id, $_SESSION['steamid']));
			break;
		}
	}
}

$referal_url = "https://cashifyskins.com/?ref=$referal_id";
$referal_stats = select("select count(*) as refered, sum(amount*paid) as paid from referals where referrer_sid = ?",array($_SESSION['steamid']));

$refered = 0;
$earned = 0;
$paid = 0;
if(!empty($referal_stats)){
	$refered = $referal_stats[0]['refered'];
	//$earned = $referal_stats[0]['earned'];
	$paid = $referal_stats[0]['paid'];
	if(empty($paid)){$paid = 0;}
}

?>




<div id='basic_content_wrapper'>
<div id='referal_header'>
<div id='referal_header_description'>
<h1>Earn Real Money</h1>
Know a friend who is looking to buy virtual goodies?<br> 
Gift your friends $2 funds using your referral link and <br>
get rewarded with $2 funds when they make their first purchase.
</div>
<img src="/images/the_mines.jpg">
</div>


<div class='content_wrapper'>
<h1>Share Your Link</h1>
Copy your personal referral link and share it with your friends and followers.<br><br><br>
<div style='margin:5px 0; font-size:14px;'>Referal Link</div>
<input id='refer_link' <?php echo "value='$referal_url'";?>></input><br><br>
You may also append your referral parameters on any of our page.
</div>

<div class='content_wrapper'>
<div class='cell_divder'>
<h2>Share on Steam</h2>
Post on your Steam activity wall <br><br>

<span style='margin:5px 0; font-size:14px;'>Sample Templete</span>
<div id='share_templete' >
Buy and Sell steam economy goodies @CashifySkins.<br>
Sign up using my link and receive $2 in funds:  <?php echo $referal_url; ?>
</div><br>
<a class='share_button' target="_blank" <?php echo 'href="'.$_SESSION['steam_profileurl'].'home/"'?> >Post Activity</a>
</div>

<div class='cell_divder'>
<h2>Share on FaceBook </h2>
Post on your FaceBook wall<br>
<a class='share_button' target="_blank" <?php echo "href='https://www.facebook.com/sharer/sharer.php?u=$referal_url'"; ?>>Post on your wall</a>
</div>

</div>

<div id='referal_stats' class='content_wrapper'>
<h1>Referral Stats</h1>
<div style="float:left;">
People Refered: <br><br>
Funds Earned: <br><br>
</div>
<div style="float:left; margin-left:12px;">
<?php
echo "	$refered<br><br>
		$$paid<br><br>";

?>

</div>
</div>
</div>
<?php footer();?>
</body>
</html>