<?php 
session_start();

require 'shared/nav-menu.php';
require "../settings/currency-config.php";

if(empty($_SESSION['steamid'])){
	header("Location: /login-required.php");
	die;
}




$title = "Sell Items - CashifySkins";
$css[] = "/css/sell.css";

function create_option($id,$class,$first,$array){
	$element = "<select id='$id' class='$class' onchange='populate_table()'>";
	$element .= "<option value=''>$first</option>";
	foreach($array as $value){	
		$element .= "<option value='$value'>$value</option>";
	}
	$element .= "</select>";
	return $element;
}
$game_preference = select("select game_preference from user where steamid = ?", array($_SESSION['steamid']));
$game_preference = $game_preference[0]['game_preference'];
$game_preference = ($game_preference == 1) ? 730 : $game_preference ;

$game_select = array("753"=>"Steam","730"=>"Counter-Strike: Global Offensive","570"=>"Dota 2","440"=>"Team Fortress 2");
$game_select_output = "";
$game_select_output .= "<select class='filter_medium' id='appid' onchange='change_inventory()'>";
foreach($game_select as $appid => $name){
	if($game_preference == $appid){
		$game_select_output .= "<option value='$appid' selected>$name</option>";
	}else{
		$game_select_output .= "<option value='$appid'>$name</option>";
	}
}
$game_select_output .= "</select>";
 
output_header(); 
 
?>

<div id ="main_wrapper">
<div id="filterwraper">
<div id="fixed_filter">

<?php 
echo $game_select_output;
?>
<select class="filter_small" id="orderby"  onchange="execute_search()">
<option value="">Sort By: </option>
<option value="type">Type</option>
<option value="high">Highest  Price</option>
<option value="low">Lowest Price</option>
<option value="AZ">A - Z</option>
<option value="ZA">Z - A</option>
</select>

<input class="filter_large" placeholder="Search" id="search" onKeyUp="populate_table()" onKeyPress="populate_table()">

<button onclick="clear_all()">Clear All</button> <button onclick='fetch_inventory(appid,1)'>Force Refresh</button> 


<div class='filter_advance' >
</div>
</div>	

<div id="custom_filter">

<div id='submit_sell_order_loader_wrapper'>Processing <img src="images/loader-5.gif" id='submit_sell_order_loader'></div> <button id="trade" onclick="submit_sell_order()">Submit Trade »</button> 
<div style='float:right;font-size:14px; margin-top:10px; border:2px solid #fff;  background:#222; color:#fff; text-align:center; padding:5px;'>A <?php echo $listing_fee; ?>% fee will be charged when you make a sale. This has been added to the price calculation.</div>
</div>

</div>

<div class="main_wrapper">

<div id="tablecontent"></div>
<div id ="contentdisplay"></div>

</div>
</div>

<script>
$('#tablecontent').on('wheel', function(event) { // prevent page scrolling
    var block = event.target,
        toTop = event.originalEvent.deltaY < 0,
        stop = false;
		
  	var obj =  document.getElementById("tablecontent");
	
	if(toTop){
		if(obj.scrollTop === 0){// if top of div
			stop = true;
		}
	}else{
		if( obj.scrollTop === (obj.scrollHeight - obj.offsetHeight))// if bottom of div
		{
			stop = true;
		}
	}

    if (stop) {
      event.preventDefault();
    }

    event.stopPropagation();
	
});

var cart = []; 
var inventory = [];
var placed_item_toggle = false;
var appid = <?php echo $game_preference; ?>;
var filter_toggle = false;	

fetch_inventory(appid);

function calucate_pricing(bool){
	
	var seller_amount = document.getElementById("seller_amount");
	var buyer_amount = document.getElementById("buyer_amount");
	
	if(bool){
		var price = seller_amount.value;
		buyer_amount.value =  Math.ceil((price*100)/(100-listing_fee)*100)/100;
	}
	else{
		var price = buyer_amount.value;
		seller_amount.value = Math.floor((price/100)*(100-listing_fee)*100)/100;
	}
	
}

var returns = [];
function add_to_returns(index){
	
	var id = listings[index]['id'];
	var array_key = returns.indexOf(id);
	if(array_key != -1){
		returns.splice(array_key, 1);
	}else{
		returns.push(listings[index]['id']);
	}
	display_orders(3);
	
}


function advanced_filter_toggle(){
	(filter_toggle ?  filter_toggle = true : filter_toggle = false);
	if(filter_toggle){
		clear_custom_filter();
		filter_toggle = false;
	}
	else{
		display_custom_filter();
		filter_toggle = true;
	}

}
function toggle_submit_sell_order_loader(toggle){
	if(toggle){
		document.getElementById("trade").style.display = "none";
		document.getElementById("submit_sell_order_loader_wrapper").style.display = "inline-block";
	}else{
		document.getElementById("trade").style.display = "inline-block";
		document.getElementById("submit_sell_order_loader_wrapper").style.display = "none";
	}
	
}

function submit_sell_order(){

	if(cart.length == 0){
		create_notification("Empty Cart","You must add something to sell before you can proceed.");
		return;
	}
	
	toggle_submit_sell_order_loader(true);
	
	$.ajax({
			type: "POST",
			url: "/ajax/sell-order.php",
			data: { 
			"order": JSON.stringify(cart), 
			"appid":appid,
			},
			timeout: 50000,
			success: function(r) {
				alert(r);
				toggle_submit_sell_order_loader(false);
				r = JSON.parse(r);
				if(r['success']){
					create_notification("Success",r['msg']);
				}else{
					create_notification("Error",r['msg']);
				}
				
			},
			error: function(request, status, err) {
				toggle_submit_sell_order_loader(false);
				create_notification("Error","There was an error proccessing your request. Please try again");
			}
		});
}

function clear_all(){
	cart = [];
	var orderby = orderby = document.getElementById("orderby").value = "";
	var searchterm = document.getElementById("search").value = "";
	populate_table();
}


function execute_search(){
	populate_table();
}

function change_inventory(ignore){
	
	var appid_new = document.getElementById("appid").value;

	if(appid_new != appid && cart.length > 0){
		if(!ignore){
		create_notification_confirmation("Progress Lost","You are allowed to sell items from one game type at a time. Your placed items will be lost if you choose to proceed.","change_inventory(true);close_notification();");
		return;
		}
		cart = [];
	}
	
	appid = appid_new;
	
	if(filter_toggle){
		clear_custom_filter();
		display_custom_filter();
	}
	
	if(inventory[appid]){
		 populate_table();
	}
	else{
		fetch_inventory(appid);
	}
}

function fetch_inventory(fetch_appid, force_refresh) {
	var tablecontent = document.getElementById("tablecontent");
	tablecontent.innerHTML = "<div id='loadingscreen'><p><img id='loading' src='images/loader-1.GIF'></p>Retrieving Your Inventory</div>";
	cart = [];

	$.ajax({
	 url: "/ajax/sell-inventory-data.php",
	 type:'POST',
	 data: {
         "appid":appid,
		 
     },
	 success: function(r){
		if(r){
        	r = JSON.parse(r);
			if(r['success']){
				inventory[fetch_appid] = r["data"];
       			populate_table(fetch_appid);
			}
			else{
				fetch_inventory_error_msg(r['error']);
			}
		 }else{
			 fetch_inventory_error_msg("Unable to retrive inventory, please try again");
		 }
     },
	 error: function(r){
		fetch_inventory_error_msg("Unable to retrive inventory, please try again");
	 },
	 timeout: 30000
	});
}

function fetch_inventory_error_msg(msg){
	var table_content = document.getElementById("tablecontent");
	table_content.innerHTML = "<div id='loadingscreen'>"+msg+"<br><br><button onClick='fetch_inventory(appid,1)'>Refresh Inventory</button></div>";
}

function populate_table(){
	
	var array_name = [];
	var array_tag = [];
	var array_type = [];
	
	var orderby = orderby = document.getElementById("orderby").value;
	var searchterm = document.getElementById("search").value;

	array_name.push(searchterm);
	
	switch(orderby) {
	case "type":
	 inventory[appid].sort(function(obj1, obj2) {
			 if (obj1["type"] > obj2["type"]) {
				return 1;
			  }
			  if (obj1["type"] < obj2["type"]) {
				return -1;
			  }
			  return 0;
		})
	break;
    case "high":
       inventory[appid].sort(function(obj1, obj2) {
			return obj2.suggested_price - obj1.suggested_price ;
		})
        break;
    case "low":
         inventory[appid].sort(function(obj1, obj2) {
			return  obj1.suggested_price - obj2.suggested_price  ;
		})
        break;
 	case "AZ":
         inventory[appid].sort(function(obj1, obj2) {
			 if (obj1.display_name > obj2.display_name) {
				return 1;
			  }
			  if (obj1.display_name < obj2.display_name) {
				return -1;
			  }
			  return 0;
		})
        break;	
	case "ZA":
	inventory[appid].sort(function(obj1, obj2) {
		if (obj1.display_name > obj2.display_name) {
			return -1;
		}
		if (obj1.display_name < obj2.display_name) {
			return 1;
		}
			return 0;
	})
    break;
    default:
     break; 
	}
	
	function in_array(array, value){
	
		var bool = false; 
		if(array.length > 0){
			for(key in array){
				if(value.toLowerCase().indexOf(array[key].toLowerCase()) >= 0){
					bool = true;
				}else{
					return false;
					
				}
			}
		}else{
			bool = true;
		}
		return bool;
	}
	
	var cell_index = 0;
	var object = document.createElement("div");
	
	for(key in inventory[appid]) {
		var v = inventory[appid][key];
		var item_id = v['id'];
		var name = v['display_name'];
		var type = v['type'];
		var pictureurl = "https://steamcommunity-a.akamaihd.net/economy/image/"+v['url']+"/120fx100f";
		var subtitle = custom_subtitle(appid,name);
		
		var placed = false;
		
		for(key in cart){
				
			if(cart[key]['id'] == item_id){
				placed = true;
				break;
			}
				
		}
		
		
		var click_handler = function(arg) {
		  return function() { select_item(arg); };
		}
		
		if(in_array(array_name, name)){	
			var cell = document.createElement("div"); 
			cell.id = "cell_"+cell_index;
			if(placed){
				cell.className = "itemcell_placed";
			}else{
				cell.className = "itemcell";
			}
			
			cell.onclick = click_handler(cell_index);

			var img = document.createElement("img"); 
			img.src =  pictureurl;
			cell.appendChild(img);
			
			if(subtitle){
				var text = document.createElement("div"); 
				text.className = "itemtext";
				text.appendChild(document.createTextNode(subtitle));
				cell.appendChild(text);
			}
			
			object.appendChild(cell);
		
		}
		
		cell_index++;
	}
	
	var table = document.getElementById("tablecontent");
	var spacing = document.createElement("div"); 
	spacing.className = 'spacing';
	object.appendChild(spacing);
	table.innerHTML = "";
	table.appendChild(object);
	if(inventory.length > 0 && inventory[appid]){
		select_item(0);
	}
	
}	

	function custom_subtitle(appid, name){
		if(appid == 730 || appid == 440){
			var exterior = ["Minimal Wear","Battle-Scarred","Well-Worn","Field-Tested","Factory New"];
			for(i = 0; i < exterior.length; i++){
				var exterior_name = exterior[i] ;
					
				if(name.indexOf(exterior_name)>= 0){
					return exterior_name;
				}
			}
		}
		return;
	}
		
	function select_item(index){

		var v = inventory[appid][index];
		var quantity = v['quantity'];
		var pictureurl = "https://steamcommunity-a.akamaihd.net/economy/image/"+v['url'];
		var display_name = v['display_name'];
		var suggested_price = v['suggested_price'];
		var item_id = v['id'];
		var color = v['color'];

		var contentdisplay =  document.getElementById("contentdisplay");
		
		contentdisplay.innerHTML = "";
		contentdisplay.innerHTML += "<div class='selected_img'><img src='"+pictureurl+"'/></div><div class='description'><p id='selected_name' style='color:#"+color+";'>"+display_name+"</p>Inventory: "+quantity+" | Suggested: $"+suggested_price+"</div>";
		contentdisplay.innerHTML += "<div class='description_name'>Quantity:<br><br>Buyer Pays:<br><br>You Receive:</div>";
		contentdisplay.innerHTML += "<div class='description_input'><input id='quantity' type='number' min='0'><br><br><input id='buyer_price' onChange='calculate_pricing()' type='number' min='0' placeholder='Per Item' step='0.01'><br><br><input id='seller_price' onChange='calculate_pricing(true)'  type='number' placeholder='Per Item' step='0.01'></div>";
		contentdisplay.innerHTML += "<button id='placeitem' onclick=\"place_item('"+index+"')\">Place Item</button>";
		

		for(key in cart){
			var v2 = cart[key];
				
			if(v2['id'] == item_id){
				var quantity = v2['quantity'];
				var seller_price = v2['price'];
				document.getElementById("quantity").value = quantity;
				document.getElementById("seller_price").value = seller_price;
				calculate_pricing(true);
				break;
			}
		}
		
	}
		
	

	function calculate_pricing(bool){
	
		var seller_price = document.getElementById("seller_price");
		var buyer_price = document.getElementById("buyer_price");
		
		if(bool){
			var price = seller_price.value;
			buyer_price.value =  Math.ceil((price*100)/(100-listing_fee)*100)/100;
		}else{
			var price = buyer_price.value;
			seller_price.value = Math.floor((price/100)*(100-listing_fee)*100)/100;
		}
		
	}

	function place_item(index){
		
		var quantity = document.getElementById("quantity").value;
		var seller_price = document.getElementById("seller_price").value;
		
		if (min_seller_listing_price > seller_price ||  seller_price > max_seller_listing_price){
			return;
		}
		
		if (0 > quantity){
			quantity = 0;
		}
		
		var in_cart = false;
		
		var v = inventory[appid][index];
		var name = v['name'];
		var price = v['sell'];
		var in_inventory = v['quantity'];
		var id = v['id'];
		
		var cell = document.getElementById("cell_"+index);

		if(quantity > in_inventory){
			quantity = in_inventory;
			document.getElementById("quantity").value = in_inventory;
		}
		
		for (var key in cart){
				
			var v = cart[key];
				
			if(id == v['id']){
			
				if(quantity > 0){
					cart[key] = {"id":id,"quantity":quantity,"price":seller_price};
					cell.className = "itemcell_placed";
				}else{
					cart.splice(key,1);
					cell.className = "itemcell";
				}
				in_cart = true;
				break;
			}
				
		}
			
		if(!in_cart && quantity > 0){
			cart.push({"id":id,"quantity":quantity,"price":seller_price});
			cell.className = "itemcell_placed";
		}	
	}
	
	
	
	
</script>

<?php footer();?>
</body>
</html>
