<?php 
session_start();
require "shared/nav-menu.php";
require "../settings/currency-config.php";

if(empty($_SESSION['steamid'])){
	header("Location: /login-required.php");
	die;
}

$title = "Manage Orders - CashifySkins";
$css[] = "/css/manage-transaction.css";  

output_header(); 
?>
<div id='basic_content_wrapper'>
<h1 style='margin-left:20px;'>Manage Orders</h1>
<div id='filter'>
<select id="type" class='filter_medium' onChange="change_type()">
<option value=0>Trade Offer</option>
<option value=1>Buy Order</option>
<option value=2>Purchases</option>
<option value=3>Sales</option>
</select><span id='filter_others'></span>
<div id='return_action'></div>
</div>
<div id = "transaction_table"></div>

<div id = 'pagenation'></div>

</div>
<script>

var current_page = 1;
var type = 1;
var type_list = {0:"Trade Offer",1:"Buy Order",2:"Purchases",3:"Sales"};
var listings;
get_transaction_data(current_page);
setInterval(function(){ get_transaction_data(current_page); }, 5000);

var image_url_source = "https://steamcommunity-a.akamaihd.net/economy/image/";

function create_element(element,id){
	var object = document.createElement(element);
	if(id){object.id = id;}
	return object;
}

function display_trade_transaction(summary,detail){

	function count_items(id){
		var item_count = 0;
	
		for(var key in detail){
			if(detail[key]["id"] == id){
				item_count += detail[key]['quantity'];
			}
		}
		
		return item_count;
	}


	var tablecontent = "<table><thead><tr><th>ID</th><th>Items</th><th>Type</th><th>Bot</th><th>Status</th><th>Status Comment</th><th>Token</th></thead></tr>";
	
	var index = 0;
	for(var key in summary){
		
		var v = summary[key];
		
		var id = v["id"];
		var status = v["status"];
		status = trade_status_code[status];
		var time_start = v["time_start"];
		var time_end = v["time_end"];
		var type = v["type"];
		type = trade_type_code[type];
		var status_comment = v["status_comment"];
		var security_token = v["security_token"];
		var item_count = count_items(id);
		var bot = v["name"];
		var type = trade_type_code[v["type"]];
		//<td>"+time_start+"<br>"+time_end+"</td>
		tablecontent += "<tr><td>"+id+"</td><td style='width:140px;'>"+item_count+" Items <button id='show_"+index+"' style ='float:right;' onclick ='show_detail("+index+")' class='action_button_small'>Show</button></td><td>"+type+"</td><td>"+bot+"</td><td>"+status+"</td><td>"+status_comment+"</td><td>"+security_token+"</td></tr>";
		
		for(var key2 in detail){
			
			var v2 = detail[key2];
			if(v2["id"] == id){
				var name = v2['display_name'];
				var quantity = v2['quantity'];
				
				tablecontent += "<tr class='detail_"+index+"'style='display:none;border:0;'><td>"+name+"</td><td>"+quantity+" Qty</td><td colspan='6'></td></tr>";
			}
		}
		index++;
	
	}

 	tablecontent += "</table>";
	

	document.getElementById("transaction_table").innerHTML = tablecontent;
 
}

function show_detail(index){
	var elem = document.getElementById("show_"+index);
	if(elem.innerHTML == "Show"){
		elem.innerHTML = "Hide";
		
		var x = document.getElementsByClassName("detail_"+index);
		
		for (i = 0; i < x.length; i++) {
			x[i].style.display = "table-row";
			
		}
		
	}else{
		elem.innerHTML = "Show";
		
		var x = document.getElementsByClassName("detail_"+index);
		
		for (i = 0; i < x.length; i++) {
			x[i].style.display = "none";
		}
		
	}
}

function in_returns(item_id,seller_receive){
	for(k in returns){
		var v = returns[k];
		if(seller_receive == returns[k]['seller_receive'] && item_id == returns[k]['item_id']){
			return true;
		}
	}
	return false;
}

function display_orders(type){

	var tablecontent = "<table><thead><tr><th>Name</th><th>Quantity</th><th>Price</th><th>Status</th><th></th></thead></tr>";
	var index = 0;
	for(var key in listings){
		
		var v = listings[key];
		var name = v['name'];
		var display_name = v['display_name'];
		var appid_name = app_code[v['appid']]['name'];
		var quantity = v['quantity'];
		var color = v['color'];

		if(type == 3){
			var price = calculate_price(v["seller_receive"],true)+"<br>($"+v["seller_receive"]+")";
		}
		else{
			var price = v["price"];
		}
		
		
		var status = v["status"];
		var button = "";
		
		if(status == 1 && type == 3){
			button += "<button class='action_button' onClick='edit_item_pricing("+index+")'>Edit</button>";
			
			if(!in_returns(v['item_id'],v['seller_receive'])){
				button += "<br><button class='action_button_2' onClick='add_to_returns("+index+")'>Add Returns</button>";
			}
			else{
				button += "<br><button class='action_button_2' onClick='remove_returns("+index+")'>Remove</button>";
			}
			
		}
		if(type == 2){
			status = purchase_status_code[status];
		}else{
			status = sale_status_code[status];
		}
		
		
		var img  = image_url_source+v["image"]+"/80fx80f";
		var listing_url =  '/listings.php?name='+name+'&appid='+v['appid'];
		
		tablecontent += "<tr><td class='listing_cell'><a href=\""+listing_url+"\" target='_blank'><div class='item_name_col'><div class='item_name_col_image_wrapper'><img src='"+img+"'></div><div class='item_name_text'><b style='color:#"+color+";'>"+display_name+"</b><br>"+appid_name+"</div></div></a></td><td>"+quantity+"</td><td>$"+price+"</td><td>"+status+"</td><td class='action_column'>"+button+"</td></tr>";
		index ++;
		
	}
	
 	tablecontent += "</table>";
	
	document.getElementById("transaction_table").innerHTML = tablecontent;
	
}

function create_element(element,text_id,text_class,text_content){
	var e = document.createElement(element);
	if(text_id){
		e.id = text_id;
	}
	if(text_class){
		e.className = text_class;
	}
	if(text_content){
		e.className = text_content;
	}
}

function display_buy_order(){

	var tablecontent = "<table><thead><tr><th>Name</th><th>Quantity</th><th>Price</th><th></th></thead></tr>";

	for(var key in listings){
		
		var v = listings[key];
		var color = v['color'];
		var name = v['name'];
		var display_name = v['display_name'];
		var appid_name = app_code[v['appid']]['name'];
		var price = v["price"];
		var quantity = v["quantity"];
		var id = v['id'];
		var button = "<button class='action_button' onClick='edit_buy_order("+key+")'>Edit</button><br><button class='action_button_2' onClick='cancel_buy_order("+id+")'>Cancel</button>";		
		var img  = image_url_source+v["image"]+"/80fx80f";
		var listing_url = '/listings.php?name='+name+'&appid='+v['appid'];
		
		tablecontent += "<tr><td class='listing_cell'><a href=\""+listing_url+"\" target='_blank'><div class='item_name_col'><div class='item_name_col_image_wrapper'><img src='"+img+"'></div><div class='item_name_text'><b style='color:#"+color+";'>"+display_name+"</b><br>"+appid_name+"</div></div></a></td><td>"+quantity+"</td><td>$"+price+"<br>($"+(price*quantity).toFixed(2)+")</td><td class='action_column'>"+button+"</td></tr>";
	
	}
	
 	tablecontent += "</table>";
	
	document.getElementById("transaction_table").innerHTML = tablecontent;
	
 
}

function edit_buy_order(index){
	
	var v = listings[index];
	var id = v['id'];
	var image_url = image_url_source+v['image'];
	var display_name = v['display_name'];
	var price = v['price'];
	var quantity = v['quantity'];
	var appid = v['appid'];
	var appid_name = app_code[appid]['name'];
	var color = v['color'];
	
	create_notification("","");

	document.getElementById("notification").className = "notification_product_display";
	document.getElementById("notification").style.top = (window.pageYOffset+40)+"px";
	document.getElementById("notification_header").style.display = "none";
	document.getElementById("notification_body").className = "notification_body_product_display";
	
	r = document.getElementById("notification_footer");
	r.parentNode.removeChild(r);
	
	var header = "<img class='notification_product_display_img' src='"+image_url+"'><h2 style='color:#"+color+";'>"+display_name+"</h2><br>";
	
	var label = "<div class='notification_edit_item_label'>Price Per Item: <br><br>Quantity: <br><br>Total:</div>";
	
	var input = "<div class='notification_edit_item_input_wrapper'><input value='"+price+"' step='0.01' id='buy_popup_price' type='number' min='0' onChange='buy_calucate_pricing()'><br><br><input  value='"+quantity+"' min='0' id='buy_popup_quantity' type='number' onChange='buy_calucate_pricing()'><br><br>$<span id='buy_popup_total'>0</span></div>";
	
	var footer = "<div id='buy_popup_warning' class='notification_edit_item_warning'></div><div id='notification_button_wrapper'><button id='notification_button_wrapper' onClick='confirm_buy_order_update("+id+")'>Submit Order</button></div><img id='notification_loader' class='notification_loader_center' src='/images/loader-3.GIF'>";
	
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

function confirm_buy_order_update(id){
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
	 url: "/ajax/update-buy-order.php",
	 type:'POST',
	 data: {
         "id":id,
		 "price":price,
		 "quantity":quantity,
		 
     },
	 success: function(r){
		 alert(r);
		 if(r){
			 r = JSON.parse(r);
			 if(r['success']){
				get_transaction_data(current_page);
				close_notification();
			 }else{
				 toggle_notification_loader(false);
				 document.getElementById("buy_popup_warning").textContent = r['msg'];
			 }
		 }else{
			  toggle_notification_loader(false);
			  document.getElementById("buy_popup_warning").textContent = r['msg'];
		 }
		get_transaction_data(current_page);
     },
	 error: function(r){
		toggle_notification_loader(false);
		document.getElementById("buy_popup_warning").textContent = r['msg'];
	 },
	 timeout: 50000
	});
	
}

function cancel_buy_order(id){

	 $.ajax({
	 url: "/ajax/cancel-buy-order.php",
	 type:'POST',
	 data: {
         "id":id,
		 "quantity":0,
		 "price":0,
		 
     },
	 success: function(r){
		 if(r){
			 r = JSON.parse(r);
			 if(r['success']){
				get_transaction_data(current_page);
				fetch_user_funds();
			 }else{
				 create_notification("Error",r['msg']);
			 }
		 }else{
			  create_notification("Error","We encountered an error while proccessing your request. Please try again.");
		 }
		get_transaction_data(current_page);
     },
	 error: function(r){
		create_notification("Error","There was an error processing your request. Please try again.");
	 },
	 timeout: 50000
	});
	
}


change_type();

function change_type(){ // Change Transaction history and regenerates filters for the table

	type = document.getElementById("type").value;
	
	var output = "";	
	
	if(type == 2){
		output += "<button class='transaction_sumbit' onClick='submit_collection_request()'>Collect purchased Items »</button>";
	}
	else if(type == 3){
		output += "<button class='transaction_sumbit' onClick='submit_return_request()'>Confirm Returns »</button>";
	}

	if(type ==  2 || type == 3){

		var status_array;
		
		if(type == 2){
			status_array = purchase_status_code;
		}
		else{
			status_array = sale_status_code;
		}

		output += "<br><select id='filter_appid' class='filter_large' onChange='get_transaction_data()'>";
		output += "<option value=''>All Games</option>";
		for(k in app_code){
			var name = app_code[k]['name'];
			output += "<option value='"+k+"'>"+name+"</option>";
		}
		output += "</select><br>";
		
		output += "<input id='search' type='text' class='filter_large' placeholder='Item Search' onkeyup='term_search()' >";
	}
	
	var filter = document.getElementById("filter_others");
	filter.innerHTML = output;

	get_transaction_data(1);
}

function get_filter_data(){

	if(type == 2 || type == 3){
		//var status = document.getElementById("filter_status").value;
		var appid = document.getElementById("filter_appid").value;
		var search_term = document.getElementById("search").value;

		return JSON.stringify({"appid":appid,"search_term":search_term});
	}else{
		return "";
	}
}


var delayTimer;
function term_search() {
    clearTimeout(delayTimer);
    delayTimer = setTimeout(function(){get_transaction_data(1);}, 1000); 
}

var current_data = "";

function get_transaction_data(page){
	
	if(!page){page = 1;}
	current_page = page;
	
	var type = document.getElementById("type").value;
	
	var filter = get_filter_data();
	
	 $.ajax({
	 url: "/ajax/transaction-history.php",
	 type:'POST',
	 data: {
         "type":type,
		 "page":page,
		 "filter":filter,
		 "active":1,
		 
     },
	 success: function(r){
		if(current_data == r){
			 return;
		}
		
		if(r){
			current_data = r;
        	r = JSON.parse(r);
			if(type == 0){
				display_trade_transaction(r['summary'],r['details']);
			}
			else if(type == 1){
				listings = "";
				listings = r["summary"];
				display_buy_order();
			}
			else if(type == 2 || type == 3){
				listings = "";
				listings = r["summary"];
				display_orders(type);
			}
			
			pagination(page,r['totalpage']);
			
		 }
     },
	 error: function(r){
	
	 },
	 timeout: 10000
	});

}

function close_popup(){
	document.getElementById("background_fade").style.display = "none";
	document.getElementById("popup_item_display_container").style.display = "none";
	document.getElementById("popup_item_display_container").innerHTML = "";
}

function edit_item_pricing(index){

	create_notification("","");
	
	var v = listings[index];
	var id = v['id'];
	var image_url = image_url_source+v['image'];
	var name = v['name'];
	var seller_receive = v['seller_receive'];
	var quantity = v['quantity'];
	var appid = v['appid'];
	var appid_name = app_code[appid]['name'];
	var color = v['color'];
	
	document.getElementById("notification").className = "notification_product_display";
	document.getElementById("notification").style.top = (window.pageYOffset+40)+"px";
	document.getElementById("notification_header").style.display = "none";
	document.getElementById("notification_body").className = "notification_body_product_display";
	
	r = document.getElementById("notification_footer");
	r.parentNode.removeChild(r);

	document.getElementById("notification_body").innerHTML = "<img class='notification_product_display_img' src='"+image_url+"'><h2 style='color:#"+color+";'>"+name+"</h2><br><div id = 'notification_product_price_input'><div id='notification_product_display_label'>Buyer pays: <br><br>I receive: </div><div id='notification_product_display_input'><input class='input_amount' step='0.01' id='buyer_amount' type='number' onChange='edit_item_pricing_calculate(false)'><br><br><input value="+seller_receive+" step='0.01' class='input_amount' id='seller_amount' type='number' onChange='edit_item_pricing_calculate(true)'><br><span class='text_small'>(Includes "+listing_fee+"% Fee)</span><br><span style='font-size:12px;color:#fff;cursor:pointer;' onClick='create_advance_item_pricing("+index+")'>+ Advanced </span></div></div><div id='product_display_error_callback' style='color:#FF0004;'></div><div id ='notification_button'><button onClick='submit_updated_price("+index+")'>Submit</button></div><img id='notification_loader' src='images/loader-3.GIF' class='notification_loader_center'>";
	
	edit_item_pricing_calculate(true);
	
}

var row_increment = 0;

	
function create_advance_item_pricing(item_index){
	
	var v = listings[item_index];
	var seller_receive = v['seller_receive'];
	var quantity = v['quantity'];
	
	row_increment = 1;
	row_collection = [1];
	
	document.getElementById("notification_product_price_input").innerHTML = "<table id='table_pricing'><thead><tr><th>I receive</th><th>Buyer pays</th><th>Quantity</th><th></th></thead></tr><tbody id='table_pricing_body'></tbody><tfoot><tr><td colspan='2'><span id='quantity_text_total'>Total</span></td><td colspan='2' id='quantity_total' ><span id='quantity_total_counter'>"+quantity+"</span> | "+quantity+"</td></tr></tfoot></table><span id='advanced_pricing_remove' onClick='remove_advance_item_pricing("+item_index+")'>Back</span><br>";
	
	var table = document.getElementById("table_pricing_body");
	
	var row = table.insertRow(0);
	row.id = "edit_price_row_"+row_increment;
	var cell_1 = row.insertCell(-1);
	cell_1.innerHTML = "<input type='number' step='0.01' value='"+seller_receive+"' onChange='edit_row_price("+row_increment+",true)'></input>";
	var cell_2 = row.insertCell(-1);
	cell_2.innerHTML = "<input type='number' step='0.01' onChange='edit_row_price("+row_increment+",false)'></input>";
	var cell_3 = row.insertCell(-1);
	cell_3.innerHTML = "<input type='number' value='"+quantity+"' onChange='edit_quantity("+row_increment+")'></input>";
	var cell_4 = row.insertCell(-1);
	cell_4.innerHTML = "<img title='Split Row' onClick='item_pricing_split_row("+row_increment+")' src='/images/split.png'><img onClick='item_pricing_remove_row("+row_increment+")' title='Delete Row' src='/images/delete.png'>";
	edit_row_price(row_increment,true);
	
	row_increment ++;
	
	
}

function edit_quantity(row_index){	

	var quantity = document.getElementById("edit_price_row_"+row_index).getElementsByTagName("input")[2].value;
	var inputValues = [];
	var count = 1;
	var total = 0;
	
	$('#table_pricing input').each(function() {inputValues.push($(this).val());});

	for(v in inputValues){
		
		var value = inputValues[v];
		if(count == 3)
		{
			total += parseInt(value);
			count = 0;
		}
		count++;
		
	}

	document.getElementById("quantity_total_counter").innerHTML = total;
}

function remove_advance_item_pricing(index){
	
	var v = listings[index];
	var seller_receive = v['seller_receive'];
	
	document.getElementById("notification_product_price_input").innerHTML = "<div id='notification_product_display_label'>I receive: <br><br> Buyer pays: </div><div id='notification_product_display_input'><input value="+seller_receive+" step='0.01' class='input_amount' id='seller_amount' type='number' onChange='edit_item_pricing_calculate(true)'><br><br><input class='input_amount' step='0.01' id='buyer_amount' type='number' onChange='edit_item_pricing_calculate(false)'><br><span class='text_small'>(Includes "+listing_fee+"% Fee)</span><br><span style='font-size:12px;color:#fff; cursor: pointer;' onClick='create_advance_item_pricing("+index+")'>+ Advanced </span>";
	edit_item_pricing_calculate(true);
}


function edit_row_price(row_index,bool){

	var seller_amount = document.getElementById("edit_price_row_"+row_index).getElementsByTagName("input")[0];
	var buyer_amount =  document.getElementById("edit_price_row_"+row_index).getElementsByTagName("input")[1];
	
	if(bool){
		buyer_amount.value = calculate_price(seller_amount.value,true);
	}
	else{
		seller_amount.value = calculate_price(buyer_amount.value,false);
	}
	
}


function item_pricing_split_row(row_number){
	
	var table = document.getElementById("table_pricing_body");
	
	var original_row_quantity = document.getElementById("edit_price_row_"+row_number).getElementsByTagName("input")[2];
	var original_row_seller_value = document.getElementById("edit_price_row_"+row_number).getElementsByTagName("input")[0].value;
	var original_row_buyer_value = document.getElementById("edit_price_row_"+row_number).getElementsByTagName("input")[1].value;
	var original_row_quantity_value = original_row_quantity.value;

	original_row_quantity.value = Math.ceil(original_row_quantity_value/2);
	var split_row_value = Math.floor(original_row_quantity_value/2);
	var row = table.insertRow(-1);
	row.id = "edit_price_row_"+row_increment;
	var cell_1 = row.insertCell(-1);
	cell_1.innerHTML = "<input type='number' step='0.01' onChange='edit_row_price("+row_increment+",true)' value='"+original_row_seller_value+"'></input>";
	var cell_2 = row.insertCell(-1);
	cell_2.innerHTML = "<input type='number' step='0.01' onChange='edit_row_price("+row_increment+",false)' value='"+original_row_buyer_value+"'></input>";
	var cell_3 = row.insertCell(-1);
	cell_3.innerHTML = "<input type='number'onChange='edit_quantity("+row_number+")' value='"+split_row_value+"'></input>";
	var cell_4 = row.insertCell(-1);
	cell_4.innerHTML = "<img title='Split Row' onClick='item_pricing_split_row("+row_increment+")' src='/images/split.png'><img onClick='item_pricing_remove_row("+row_increment+")' title='Delete Row' src='/images/delete.png'>";
	
    row_increment ++;
	 
}

function item_pricing_remove_row(row_number){
	var el = document.getElementById( 'edit_price_row_'+row_number);
	el.parentNode.removeChild(el);
}



function submit_updated_price(index){

	document.getElementById("notification_button").style.display = "none";
	document.getElementById("notification_loader").style.display = "block";
	document.getElementById("product_display_error_callback").style.color = "#FF0000";
	
	var v = listings[index];
	var item_id = v['item_id'];
	var past_price = v['seller_receive'];
	var sell_data = [];
	var current_quantity = v['quantity'];
	
	var advanced = document.getElementById('seller_amount');
	
	if (advanced === null){
		
		var inputValues = [];
		var count = 1;
		
		var new_price = 0;
		var quantity = 0;
		
		$('#table_pricing input').each(function() {inputValues.push($(this).val());});
	
		for(v in inputValues){
	
			var value = inputValues[v];
			
			if(count == 1){
				new_price = value;			
			}
			else if(count == 3){
				quantity = value;
			}
	
			count++;
			
			if(count == 4){
				count = 1;
				sell_data.push({"new_price":new_price,"quantity":quantity});
	
			}
		}
		
	}
	else{
		var new_price = document.getElementById("seller_amount").value;
		var quantity = v['quantity'];
		sell_data.push({"new_price":new_price,"quantity":quantity});
	}
    var count_quantity = 0;
	for(var k in sell_data){

		if(0 >= sell_data[k]['new_price']){
			document.getElementById("notification_button").style.display = "block";
			document.getElementById("notification_loader").style.display = "none";
			document.getElementById("product_display_error_callback").innerHTML =  "Price field must be greater than $0";
			return;
		}
		
		if(0 >= sell_data[k]['quantity']){
			document.getElementById("notification_button").style.display = "block";
			document.getElementById("notification_loader").style.display = "none";
			document.getElementById("product_display_error_callback").innerHTML =  "Quantity field must be greater than 0";
			return;
		}
	
		count_quantity += parseInt(sell_data[k]['quantity']);
	}

	if(count_quantity > current_quantity){
		var difference = count_quantity - current_quantity;
		document.getElementById("notification_button").style.display = "block";
		document.getElementById("notification_loader").style.display = "none";
		
		document.getElementById("product_display_error_callback").innerHTML =  "Please remove "+ difference +" quantity if you wish to proceed";
		return;
	}

	$.ajax({
	 url: "/ajax/update-sell-order.php",
	 type:'POST',	
	 data: {
		 "item_id":item_id,
		 "past_price":past_price,
         "price_data": JSON.stringify(sell_data)
     },
	 success: function(r){
		 if(r){
			 r = JSON.parse(r);
			 if(r['success']){
				 close_notification();
				 get_transaction_data(1);
			 }
			 else{
				 document.getElementById("notification_button").style.display = "block";
				 document.getElementById("notification_loader").style.display = "none";
				 document.getElementById("product_display_error_callback").innerHTML =  "There was an error processing your request";
			 }
		
		 }else{
			 document.getElementById("notification_button").style.display = "block";
			 document.getElementById("notification_loader").style.display = "none";
			 document.getElementById("product_display_error_callback").innerHTML =  "There was an error processing your request";
		 }
     },
	 error: function(r){
		document.getElementById("notification_button").style.display = "block";
		document.getElementById("notification_loader").style.display = "none";
		document.getElementById("product_display_error_callback").innerHTML =  "Request timeout";
	 },
	 timeout: 6000
	});
}


function edit_item_pricing_calculate(bool){
	
	var seller_amount = document.getElementById("seller_amount");
	var buyer_amount = document.getElementById("buyer_amount");
	
	if(bool){
		buyer_amount.value = calculate_price(seller_amount.value,true);
	}else{
		var price = buyer_amount.value;
		seller_amount.value = calculate_price(buyer_amount.value,false);
	}
	
}

var returns = [];
function add_to_returns(index){
	returns.push({"item_id":listings[index]['item_id'],"seller_receive":listings[index]['seller_receive']});
	display_orders(3);
}

function remove_returns(index){
	
	var item_id = listings[index]['item_id'];
	var seller_receive = listings[index]['seller_receive'];
	
	for(k in returns){
		var v = returns[k];
		if(seller_receive == v['seller_receive'] && item_id == v['item_id']){
			returns.splice(k, 1);
			break;
		}
	}
	display_orders(3);

}

function submit_return_request(){
	if(returns.length == 0){
		create_notification("Error","Please select an item to return.");
		return;
	}
	 $.ajax({
	 url: "/ajax/item-returns.php",
	 type:'POST',
	 data: {
         "returns":JSON.stringify(returns),
		 
     },
	 success: function(r){
		if(r){
			
			r = JSON.parse(r);
			if(r['success']){
				returns = [];
				display_orders(3);
				create_notification("Return Request Successfull",r["msg"]);
			}
			else{
				create_notification("Error",r["msg"]);
			}
		}else{
			create_notification("Error","There was an error processing your request");
		}
     },
	 error: function(){
		create_notification("Error","There was an error processing your request");
	 },
	});
	 
}

function submit_collection_request(){
	
	  $.ajax({
	 url: "/ajax/item-collection.php",
	 type:'POST',
	 data: {
		 
     },
	 success: function(r){
		if(r){
			r = JSON.parse(r);
			if(r['success']){
				 create_notification("Collection Request Successfull",r["msg"]);
				 get_transaction_data(current_page);
			}else{
				create_notification("Error",r["msg"]);
			}
		}else{
			create_notification("Error","There was an error processing your request");
		}
     },
	 error: function(){
		create_notification("Error","There was an error processing your request");
	 },
	});
	 
}

function set_page(pagenumber){
	get_transaction_data(pagenumber);
}

function pagination(current,last){

	var x = document.getElementById("pagenation");
	var pagenation = "";
	
	if(current > 1){
		pagenation += "<span onclick='set_page("+(current-1)+")' class='page_highlight'>«</span>";
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
			pagenation += "<span onclick='set_page("+i+")' class='page_highlight'>"+i+"</span>";
		}
		else if(i > last){
			break;
		}
		else{
			pagenation += "<span onclick='set_page("+i+")' class='page'>"+i+"</span>";
		}
	}
	
	if(last > current){
		pagenation += "<span onclick='set_page("+(current+1)+")' class='page_highlight'>»</span>";
	}
	
	x.innerHTML = pagenation;
}
</script>
<?php footer();?>
</body>
</html>
