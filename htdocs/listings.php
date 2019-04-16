<?php
require 'shared/nav-menu.php';
require "../settings/currency-config.php";
require 'shared/app-code.php';

$get_name = (!empty($_GET['name'])) ? htmlentities($_GET['name']) : "" ;

$title = "Listing for ".$get_name." - CashifySkins";
$css[] = "/js/jqplot/jquery.jqplot.css"; 
$css[] = "/css/item-details.css"; 

$js[] = "/js/quick-purchase.js";
$js[] = "/js/jqplot/jquery.jqplot.js";
$js[] = "/js/jqplot/plugins/jqplot.dateAxisRenderer.js";
$js[] = "/js/jqplot/plugins/jqplot.canvasTextRenderer.js";
$js[] = "/js/jqplot/plugins/jqplot.canvasAxisTickRenderer.js";
$js[] = "/js/jqplot/plugins/jqplot.highlighter.js";
$js[] = "/js/jqplot/plugins/jqplot.cursor.js";
$js[] = "/js/jqplot/plugins/jqplot.trendline.min.js";	

output_header();
 
$script = "";

$display_name = "";
$item_id = "";
$app_icon = "";
$app_name = "";
$appid = "";
$pictureurl = "";
$type = "";
$chart_hour = "";
$chart_day = "";
$commodity = 1;
$exist = false;
$listing = "";
$color = "";
$chart_data = "";

if(isset($_GET['name']) && isset($_GET['appid'])){
	$itemname = $_GET['name'];
	$appid = $_GET['appid'];

	$row = select("select p.id, p.display_name, p.commodity, p.color, p.image, p.description, p.type, IFNULL((select min(price) from item_transaction it inner join inventory i on i.assetid = it.assetid and i.botsid = it.botsid and i.item_id = it.item_id where it.item_id = p.id and it.status = 1 ),0) as price FROM pricelist p where p.name = ? and p.appid = ?",
	array($itemname, $appid));

}

if(!empty($row)){
	$exist = true;
	$row = $row[0];
	$item_id = $row['id'];
	$name = $itemname;
	$display_name = $row['display_name'];
	$price = $row["price"];	
	$type = $row["type"];
	$description = $row["description"];
	$pictureurl = "https://steamcommunity-a.akamaihd.net/economy/image/".$row["image"];
	$color = $row['color'];
	$color_header = "";
	$commodity = $row['commodity'];
	if(!empty($row['color'])){
		$color_header = "style='color:#".$row['color'].";'";
	}
	
	$extra_spacing = "";
	if($appid != 730){
		$extra_spacing = "<br>";
	}
//<tr><td colspan='3' class='no_listing'>No Listing Available</td></tr>
	$listing = "<div class='separator'></div><h2 class='listings_header'>Specific Listings</h2><table id='listing_table'><tr><td colspan='3' class='no_listing'>No Listing Available</td></tr></table><div id='pagenation'></div>";
	if($commodity){
		$listing = "";
	}
	$disable = "";
	$appid_code = array(
		753=>array("name"=>"Steam","icon"=>"/images/apps/icons/753.jpg"), 
		730=>array("name"=>"Counter-Strike: Global Offensive","icon"=>"/images/apps/icons/730.jpg"), 
		570=>array("name"=>"Dota 2","icon"=>"/images/apps/icons/570.jpg"), 
		440=>array("name"=>"Team Fortress 2","icon"=>"/images/apps/icons/440.jpg") 
	);
	$app_name = $appid_code[$appid]['name'];
	$app_icon = $appid_code[$appid]['icon'];
	
	$stmt = $conn->prepare("SELECT DATE_FORMAT(time,'%d %b %Y'), avg(avg_price), sum(sold) FROM price_summary WHERE item_id = ? and time > date_sub(now(), interval 1 MONTH) group by DATE_FORMAT(time, '%d %b %Y');");
	$stmt->execute(array($item_id));		
	$stmt->setFetchMode(PDO::FETCH_NUM); 
	$chart_data = $stmt->fetchAll();
    echo "<div id='basic_content_wrapper'>
	<div id='bread_crumb'><a href='buy.php'>Shop</a> » <a href='buy.php?appid=".$appid."'>".$app_name."</a> » ".$display_name."</div>
	<div class='img_container'><img src='$pictureurl'></div>
	<div id='description'>
	<h1 $color_header>$display_name</h1>
	<div class='item_description_appid'>
	<img src='$app_icon'>$type <br>$app_name
	</div>
	<br>
	".$description."$extra_spacing
	<br>
	<span class='button' onclick=\"quick_purchase('".$item_id."','".addslashes($display_name)."','".$price."')\" ><img src='/images/thunder.png'/> Buy $$price</span> <a id='marketlink' href=\"https://steamcommunity.com/market/listings/$appid/$name\"><img height='13'src='/images/steam-icon.png'/> Steam Market</a>
	</div>
	<div id='chart'>
	<div id='chart_header'>Last Month - <span>0</span> Sold on Avg <span>$0</span></div>
	<div id='purchased_chart'></div>
	<div id='chart_legend'></div>
	</div>
	<div class='separator'></div>
	<div id='order'>
	<div class='order_table' id='buy_order'>
	<br>
	<span class='order_header'><b id='buy_order_total'>0</b> for sale starting at <b id='buy_order_lowest'>$0</b></span>
	<br>
	<button onClick='buy_order()'>Buy</button>
	<table>
	<tr><td>Price</td><td>Quantity</td></tr>
	<tbody id='buy_order_listing'>
	<tr><td colspan='2' class='order_listing_empty'>No Listing Available</td></tr>
	</tbody>
	</table>
	</div>
	
	<div class='order_table'>
	<br>
	<span class='order_header'><b id='sell_order_total'>0</b> buy request starting at <b id='sell_order_lowest'>$0</b></span>
	<br>
	<button onclick=\"window.location.href='/sell.php'\">Sell</button>
	<table>
	<tr><td>Price</td><td>Quantity</td></tr>
	<tbody id='sell_order_listing'>
	<tr><td colspan='2' class='order_listing_empty'>No Listing Available</td></tr>
	</tbody>
	</table>
	</div>
	
	</div>

	$listing
	<div style='display:block; width:100%; height:150px; float:left;'></div>
	</div>";
	

}
else{
	$itemname = "";
	$appid = "";
	echo "<div id='basic_content_wrapper'><h1><div id='exist'>This item does not exist</div></h1></div>";
}


?>


<script>

var listing = [];
var item_id = '<?php echo $item_id;?>';
var display_name = "<?php echo addslashes($display_name);?>";
var appid_icon= '<?php echo $app_icon; ?>';
var appid_name = '<?php echo $app_name;?>';
var appid = '<?php echo $appid;?>';
var image_url = "<?php echo $pictureurl;?>";
var item_type = "<?php echo $type; ?>";
var color = '<?php echo $color; ?>';
var chart_data = <?php echo json_encode($chart_data) ?>;
var commodity  = <?php echo $commodity; ?>;

<?php 
if($exist){
	if(!$commodity){
		echo "fetch_listing(1);";
	}

echo "select_price_history('month');";
echo "fetch_orders();";
echo "setInterval(function(){fetch_orders();}, 5000);";
}

?>

function display_listing_details(index){
	
	create_notification("","");
	
	document.getElementById("notification").className = "notification_product_listing";
	document.getElementById("notification").style.top = (window.pageYOffset+40)+"px";
	document.getElementById("notification_header").style.display = "none";
	document.getElementById("notification_body").className = "notification_body_product_display";
	
	r = document.getElementById("notification_footer");
	r.parentNode.removeChild(r);
	
	
	document.getElementById("notification_body").innerHTML += listing[index]['description'];

}


function fetch_listing(page){

	 $.ajax({
	 url: "/ajax/specific-item-listing.php",
	 type:'POST',
	 data: {
          "id": item_id,
		  "page": page
     },
	 success: function(r){
		 r = JSON.parse(r);
		 listing = r['data'];
		 generate_listing();
		 pagination(r['currentpage'],r['totalpage']);
     },
	 error: function(){
		fetch_listing();
	 },
	 timeout: 30000
	});
		
}

function generate_listing(){
	
	var display = document.getElementById("listing_table");
	if(0 == listing.length){
		display.innerHTML = "<tr><td colspan='3' class='no_listing'>No Listing Available</td></tr>";
		return;
	}
	
	display.innerHTML = "";
	
	var index = 0;
	display.innerHTML = "<tr><th>Name</th><th>Price</th><th></th></tr>";
	for(key in listing){
		
		var v = listing[key]; 
		var sale_id = v['id'];
		var price = v['price'];
		//var listing_name = "<span class='listing_cell_text'>"+name+"<br>"+appid_name+"</span>";
		var listing_name = "<span class='listing_cell_text'><b style='color:#"+color+"'>"+display_name+"</b><br>"+appid_name+"</span>";
		
		/*
		var str = v['description'];
		var asset_image_url = image_url;
		//var error = "onerror='this.onerror = null; this.src=\""+image_url+"\"";
		try
		{
			var reg = /src='.+?(?='\s)/g;
			var myArray;
			while ((myArray = reg.exec(str)) !== null) {
			   asset_image_url = myArray[0];
			   asset_image_url = asset_image_url.replace(/src='/g, " ");
			   break;
			}

		}
		catch(err)
		{
			
		}
		*/
		var buy_button = "<button onClick='create_specific_purchase_confirmation(\""+price+"\",\""+sale_id+"\")'>Buy</button>";
		display.innerHTML += "<tr><td><div class='listing_cell_name' onClick='display_listing_details("+index+")'><img src='"+image_url+"'>"+listing_name+"</td><td>$"+price+"</td><td class='listing_cell_action'> "+buy_button+"</td></tr>";

		index ++;
	}
	
}

function create_specific_purchase_confirmation(price,sale_id){
	var header = "Purchase Confimation";
	var message = "You are about to purchase "+display_name+" for $"+price+". Please confirm your purchase before continuing.";
	var confirm_click = "specific_purchase(\""+sale_id+"\",\""+price+"\")";
	create_notification_confirmation(header,message,confirm_click);
}

function pagination(current,last){

	var x = document.getElementById("pagenation");
	var pagenation = "";
	
	if(current > 1){
		pagenation += "<span onclick='fetch_listing("+(current-1)+")' class='page_highlight'>«</span>";
	}
	
	var i  = (1 > current-3) ? 1 : current-3; 
	
	
	while(true){
		if(i+6 > last && i != 1){
			i--;
		}else{
			break;
		}
	}
	
	var e = i+7;
	
	for (i; i < e; i++) { 
		
		if(i == current){
			pagenation += "<span onclick='fetch_listing("+i+")' class='page_highlight'>"+i+"</span>";
		}
		else if(i > last){
			break;
		}
		else{
			pagenation += "<span onclick='fetch_listing("+i+")' class='page'>"+i+"</span>";
		}
	}
	
	if(last > current){
		pagenation += "<span onclick='fetch_listing("+(current+1)+")' class='page_highlight'>»</span>";
	}
	
	x.innerHTML = pagenation;
}



function generate_order_details(buy,sell){
	
	var sell_table = document.getElementById("sell_order_listing");
	var buy_table = document.getElementById("buy_order_listing");
	
	sell_table.innerHTML = "";
	buy_table.innerHTML = "";
	
	var sell_total = 0;
	var buy_total = 0; 
	var buy_lowest = 0;
	var sell_lowest = 0;
	
	if(buy.length == 0){
		 buy_table.innerHTML = "<tr><td colspan='2' class='order_listing_empty'>No Sell Order Available</td></tr>";
	}else{
		buy_lowest = buy[0]['price'];
	}
	if(sell.length == 0){
		 sell_table.innerHTML = "<tr><td colspan='2' class='order_listing_empty'>No Buy Order Available</td></tr>";
	}else{
		sell_lowest = sell[0]['price'];
	}


	for(k in sell){
		
		var v = sell[k];
		var row = document.createElement("tr"); 
		var price = document.createElement("td"); 
		price.textContent = "$"+v['price'];
		var quantity = document.createElement("td"); 
		quantity.textContent = v['quantity'];
			
		row.appendChild(price);
		row.appendChild(quantity);
		sell_table.appendChild(row);

		sell_total += parseInt(v['quantity']);
		
	}
	
	for(k in buy){
		
		var v = buy[k];
		var row = document.createElement("tr"); 
		var price = document.createElement("td"); 
		price.textContent = "$"+v['price'];
		var quantity = document.createElement("td");
		quantity.textContent = v['quantity'];
		
		row.appendChild(price);
		row.appendChild(quantity);
		buy_table.appendChild(row);
		
		buy_total += parseInt(v['quantity']);
	}
	
	document.getElementById("buy_order_total").textContent = buy_total;
	document.getElementById("buy_order_lowest").textContent = "$"+buy_lowest;
	document.getElementById("sell_order_total").textContent = sell_total;
	document.getElementById("sell_order_lowest").textContent = "$"+sell_lowest;
	
}


function fetch_orders(){
	
	 $.ajax({
	 url: "/ajax/order-listing.php",
	 type:'POST',
	 data: {
          "id": item_id
     },
	 success: function(r){
		 r = JSON.parse(r);
		 generate_order_details(r['buy'],r['sell']);
     },
	 error: function(){
		fetch_orders();
	 },
	 timeout: 5000
	});
		
}


function specific_purchase(sale_id,price){
	if(csrf == 0){create_notification("Login Required","You must login to place your order"); return;}

	$.ajax({
	 url: "/ajax/specific-purchase.php",
	 type:'POST',
	 data: {
          "sale_id":sale_id,
		  "price":price
     },
	 success: function(r){
		 if(r){
			 r = JSON.parse(r);
			 if(r['success']){
				create_notification("Purchase Success",r['msg']);
			 }else{
				create_notification("Purchase Failed",r['msg']);
			 }
		 }
		 else{
			 create_notification("Purchase Failed","This item could have its price changed or has already been taken");
		 }

     },
	 error: function(){
		create_notification("Purchase Failed","Unable to process your request please try again");
	 },
	 timeout: 50000
	});
	
}

function buy_order(){
	
	if(csrf == 0){create_notification("Login Required","You must login to place your order"); return;}
	
	create_notification("","");

	document.getElementById("notification").className = "notification_product_display";
	document.getElementById("notification").style.top = (window.pageYOffset+40)+"px";
	document.getElementById("notification_header").style.display = "none";
	document.getElementById("notification_body").className = "notification_body_product_display";
	
	r = document.getElementById("notification_footer");
	r.parentNode.removeChild(r);
	
	var header = "<img class='notification_product_display_img' src='"+image_url+"'><h2 style='color:#"+color+";'>"+display_name+"</h2><br>";
	
	var label = "<div class='notification_edit_item_label'>Price Per Item: <br><br>Quantity: <br><br>Total:</div>";
	
	var input = "<div class='notification_edit_item_input_wrapper'><input step='0.01' id='buy_popup_price' type='number' min='0' onChange='buy_calucate_pricing()'><br><br><input min='0' id='buy_popup_quantity' type='number' onChange='buy_calucate_pricing()'><br><br>$<span id='buy_popup_total'>0</span></div>";
	
	var footer = "<div id='buy_popup_warning' class='notification_edit_item_warning'></div><div id='notification_button_wrapper'><button id='notification_button_wrapper' onClick='confirm_buy_order()'>Submit Order</button></div><img id='notification_loader' class='notification_loader_center' src='/images/loader-3.GIF'>";
	
	var display = document.getElementById("notification_body");
	document.getElementById("notification_body").innerHTML += header;
	document.getElementById("notification_body").innerHTML += label;
	document.getElementById("notification_body").innerHTML += input;
	document.getElementById("notification_body").innerHTML += footer;
	buy_calucate_pricing();
	
	
}
function buy_calucate_pricing(){

	var quantity = document.getElementById("buy_popup_quantity").value;
	var price = document.getElementById("buy_popup_price").value;
	document.getElementById("buy_popup_total").innerHTML = Math.ceil(quantity*price*100)/100;
}
function confirm_buy_order(){
	var quantity = document.getElementById("buy_popup_quantity").value;
	var price = document.getElementById("buy_popup_price").value;
	
	if(0 >= quantity){
		document.getElementById("buy_popup_warning").textContent = "Please enter quantity";
		return;
	}
	else if(0.01 >= price){
		document.getElementById("buy_popup_warning").textContent = "Please enter a price greater than $0.01";
		return;
	}
	toggle_notification_loader(true);
	document.getElementById("buy_popup_warning").textContent = "";

	 $.ajax({
	 url: "/ajax/buy-order.php",
	 type:'POST',
	 data: {
          "id":item_id,
		  "quantity":quantity,
		  "price":price,
		  "type":1,
		  
     },
	 success: function(r){
		 toggle_notification_loader(false);
		 if(r){
			 r = JSON.parse(r);
			 if(r['success']){
				close_notification();
				create_notification("Buy Order Placed","Your order has been placed. You may check your order status or cancel your order under the <a href ='/manage-orders.php'>manage order</a> page");
				fetch_user_funds();
			 }
			 else{
				document.getElementById("buy_popup_warning").textContent = r['msg'];
			 }
		 }else{
			document.getElementById("buy_popup_warning").textContent = "Unable to process your request please try again";
		 }

     },
	 error: function(){
		  toggle_notification_loader(false);
		document.getElementById("buy_popup_warning").textContent = "Unable to process your request please try again";
	 },
	 timeout: 50000
	});
	
}



var graph_click_type = "year";

function select_price_history(chart_type){

	if(chart_data.length == 0){
		document.getElementById("purchased_chart").innerHTML = "<div id='no_chart'>Price Graph Not Available</div>";
		
	}else{
		draw_chart(chart_data,"Last Month");
	}
	
}


function chart_header(data,header){
	var total_sold = 0;
	var total_avg_price = 0;
	var count = data.length;

	for(v in data){
		var sold = parseInt(data[v][2]);
		var price = data[v][1];
		total_sold += sold;
		total_avg_price += price;
	}
	if(count != 0){
		total_avg_price = Math.ceil((total_avg_price/count)*100)/100;
	}
	document.getElementById("chart_header").innerHTML = header+" - <span>"+total_sold+"</span> Sold on Avg <span>$"+total_avg_price+"</span>";
}

function draw_chart(data,header_name){
		
	document.getElementById("purchased_chart").innerHTML = "";
	chart_header(data,header_name);
	var tick_interval = null;
	var tick_render = [];
		
	for(k in data){
		var v = data[k];
		tick_render.push = ([v[1],v[0]]);
	}
		
	var tooltip_time_format = "D MMM YYYY";
	var xaxis_label = "%b %#d";

	var graphPlot = $.jqplot('purchased_chart',  [data],
	{ 
			seriesDefaults: {
	
			markerRenderer: $.jqplot.MarkerRenderer,    
	
			markerOptions: {
				show: false,                
			},
			trendline: {
					show: true, 
					color: '#ddd',      
					type: 'linear',     
					shadow: true,      
					lineWidth: 1.5,      
				}
			},
			highlighter: {
				 tooltipContentEditor: function (current, serie, index, plot) {
					var val = plot.data[serie][index];
					return moment(new Date(val[0])).format(tooltip_time_format)+"<br>$"+val[1].toFixed(2)+"<br>"+val[2]+" Sold";
				},
				show: true,
				sizeAdjust: 0,
				formatString: '%s',
				tooltipLocation: 'n',
				useAxesFormatters: false,
				tooltipOffset: 12
			},
			cursor:{ 
				show: true,
				zoom:false, 
				showTooltip:false
			},
			 grid: {
				background: '#333',
				gridLineColor: '#666',
				gridLineWidth: 1
			},
			
			axes:{
				 xaxis:{
					renderer:$.jqplot.DateAxisRenderer,
					tickInterval: tick_interval,
					tickOptions:{formatString:xaxis_label,fontSize: '10pt',textColor:"#ddd",fontFamily: 'Arial',tick:tick_render},
					drawMajorGridlines: true
				 },
				 yaxis: {
					min:0,
					tickOptions: {formatString: "$%#.2f",fontSize: '10pt',textColor:"#ddd",fontFamily: 'Arial'},
				}
			},
			series:[{color:'#FF6D1F'}],
			
	});	
		//create_legend();
		//graphPlot.series[0].show = false;
		//graphPlot.series[0].trendline.show = false;
		
		//graphPlot.redraw(false);
		
	}
	
	function create_legend(){
		var legend = document.getElementById("chart_legend");
		legend.innerHTML = "<div class='legend_series_wrapper'><div id='sold_legend' class='legend_box' style='background:#FA5B0F;'></div>Sold</div> <div class='legend_series_wrapper'><div id='trendline_legend' class='legend_box' style='background:#999;'></div>TrendLine</div>";
		
		$('#trendline_legend').hover(function() {
    		graphPlot.series[0].show = false;
			graphPlot.series[0].trendline.show = false;
			graphPlot.redraw(false);
		})
		
	}
	 
	

</script>

<?php footer();?>	

</body>
</html>
