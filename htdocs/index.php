<?php 
require 'shared/nav-menu.php';
require 'shared/app-code.php';

$statistics = select("select 
(select count(*) from trade_transaction where status in (0,1)) as trades,
(select count(*) from user) as users,
(select count(Distinct o.item_id) from item_transaction o where status = 1) as items,
(select count(Distinct o.item_id) from item_transaction o left join pricelist p on o.item_id = p.id where p.appid = 730 and o.status = 1) as csgo_item,
(select count(Distinct o.item_id) from item_transaction o left join pricelist p on o.item_id = p.id where p.appid = 440 and o.status = 1) as tf_item,
(select count(Distinct o.item_id) from item_transaction o left join pricelist p on o.item_id = p.id where p.appid = 570 and o.status = 1) as dota_item,
(select count(Distinct o.item_id) from item_transaction o left join pricelist p on o.item_id = p.id where p.appid = 753 and o.status = 1) as steam_item"
);

$popular = select("select p.id, p.appid, p.color, (select min(o.price) from item_transaction o where o.item_id = p.id and o.status = 1) as price, (select count(*) from item_transaction o where o.item_id = p.id and o.status = 1) as quantity, p.name, p.display_name, p.image FROM price_summary psd left JOIN pricelist p ON psd.item_id = p.id group by psd.item_id having quantity > 0 order by sum(psd.sold) desc limit 20");
	
$title = "CashifySkins - Convert virtual goodies to cash";
$css[] = "/css/index.css";  
$css[] = "/css/buy.css";  
$js[] ="/js/quick-purchase.js";

$appid_code = array(
		753=>array("name"=>"Steam","icon"=>"/images/apps/icons/753.jpg"), 
		730=>array("name"=>"Counter-Strike: Global Offensive","icon"=>"/images/apps/icons/730.jpg"), 
		570=>array("name"=>"Dota 2","icon"=>"/images/apps/icons/570.jpg"), 
		440=>array("name"=>"Team Fortress 2","icon"=>"/images/apps/icons/440.jpg") 
	);
	
output_header(); 

?>

<div id="bg_1">
	<div id="bg_container_1">
		<div id="bg_1_text">
        <h1>Welcome To CashifySkins </h1> 
        <!--A fast and secure way to trade CS:GO, TF2, Dota 2 <br> goodies using real money-->
        <!--A fast and secure marketplace to trade steam <br>economy goodies using real money<br><br>-->
        A fast and secure marketplace to trade steam <br>economy goodies for real money<br><br>
       	<a class='buttons' href="buy.php">Buy</a>  <a id ='sell_button' class='buttons' href="sell.php" rel="nofollow">Sell</a>
		</div>
		<img src="images/bg.png">
	</div>
</div>

<div id = "bg_2">
<div class='bg_2_container'>
<div class = 'bg_2_cell'>
<span><?php echo $statistics[0]['trades'] ?></span><br>
Active Trades
</div>
<div class = 'bg_2_cell'>
<span><?php echo $statistics[0]['users'] ?></span><br>
Users To Date
</div>

<div class = 'bg_2_cell'>
<span><?php echo $statistics[0]['items'] ?></span><br>
Items For Sale
</div>
</div>

<div id="bg_3">
<h2>Get Started</h2>
<div id='bg_3_container'>
<a href="buy.php?appid=570">
<div class='bg_3_cell'><img src="images/dota_logo.png"><br><br><b><?php echo $statistics[0]['dota_item']; ?> Items For Sale</b></div>
</a>
<a href="buy.php?appid=730">
<div class='bg_3_cell'><img src="images/csgo_logo.png"><br><br><b><?php echo $statistics[0]['csgo_item']; ?> Items For Sale</b></div>
</a>
<a href="buy.php?appid=440">
<div class='bg_3_cell'><img src="images/tf_logo.png"><br><br><b><?php echo $statistics[0]['tf_item']; ?> Items For Sale</b></div>
</a>
</div>
</div>

<div id="bg_4">
<div id='bg_4_container'>
<h2><img src="/images/fire.png" height="30"> Popular Items</h2>
<?php
foreach($popular as $v){

		$item_id = $v['id'];
		$name = $v['name'];
		$appid =$v['appid'];
		$display_name = $v['display_name'];
		$picture_url = $v['image'];
		$picture_url = "<img src='https://steamcommunity-a.akamaihd.net/economy/image/$picture_url/220fx220f'>";
		$price = $v['price'];
		$quantity = $v['quantity'];
		$color = $v['color'];
		$border_color = "";
		if(!empty($color)){
			$border_color = "style='border-bottom-color: #$color;'";
		}
		
		$app_icon = $appid_code[$appid]['icon'];
	
		echo "<div class='card'><a href=\"/listings.php?name=$name&appid=$appid\" class='card_img' $border_color>$picture_url<img class='card_app_icon' src='$app_icon'></a><div class='description'>
		$display_name</div><div class='card_bottom'>$$price <span class='divider'>|</span> <img height='12' src='/images/stock.png'> $quantity</div><div 
		onclick=\"quick_purchase('$item_id','".addslashes($display_name)."','$price')\" class='quick'><img src='images/thunder.png'/> Quick Buy</div></div>";

	
	
}
?>

</div>
</div>
</div>
<?php footer();?>

</body>
</html>



