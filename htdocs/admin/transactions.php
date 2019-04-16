<?php
require "shared/nav-menu.php";
require $_SERVER['DOCUMENT_ROOT']."/shared/transaction-code.php";
require $_SERVER['DOCUMENT_ROOT'].'/shared/app-code.php';
$title = "Manage Transactions - CashifySkin";
$css[] = "css/transactions.css";  
output_header(); 
$steamid = "";
$js_action = "";
if(!empty($_GET['steamid']) && ctype_digit(strval($_GET['steamid']))){
	$steamid = $_GET['steamid'];
	$js_action = "get_transaction_data(1)";
}
?>

<div id='basic_content_wrapper'>
<h1>Transaction Details</h1>

<div id='filter'>
<select id="type" class='filter_medium' onChange="change_type()">
<option value=0>Trade Offer</option>
<option value=1>Active Buy Order</option>
<option value=2>Purchases</option>
<option value=3>Sales</option>
<option value=4>Cash</option>
</select>
<input value='<?php echo $steamid; ?>' id='steamid' type="text" class="filter_medium" placeholder="Steam ID">
<button class='button_theme' onclick='get_transaction_data(1)'>Submit</button><br>
<input id='invoice_id' type="text" class="filter_medium" placeholder="Invoice/Payment ID">
<button class='button_theme' onclick='fetch_invoice_details()'>Submit</button>
<br>
<span id='filter_others'></span>
</span>

</div>

<div id = "transaction_table"></div>
<div id = 'pagenation'></div>


</div>

<script>

var current_page = 1;
var type = 1;
var type_list = {0:"Trade Offer",1:"Active Buy Order",2:"Purchases",3:"Sales",4:"Cash"};
var listings;

<?php echo $js_action; ?>

function sale_item_refund_confirmation(id){
	create_notification_confirmation("Issue Refund","You are about to purchase this item under cashifyskins as a refund. Click confirm to proceed.","submit_sale_item_refund(\""+id+"\")");
}

function submit_sale_item_refund(id){
	
	 $.ajax({
	 url: "ajax/sale-item-refund.php",
	 type:'POST',
	 data: {
		 "id":id
     },	
	 success: function(r){
		 if(r){
			 r = JSON.parse(r);
			 if(r['success']){
				 get_transaction_data(1);
				 close_notification();
			 }else{ 
				 create_notification("Error",r['msg']);
			 }
		 }else{
			 create_notification("Error","Transaction Failed. Please try again");
		 }
		
     },
	 error: function(r){
		 create_notification("Error", r);
	 },
	 timeout: 50000
	});

}
function issue_custom_refund(id){

	var state_drop_down = "<input type='number' min=0>";
	
	var b = "<input id='invoice_id_input' type='number' value="+id+" hidden>Refund Amount <br><input style='margin:10px 0; height:25px;' id='refund_amount_input' type='number' min=0> ";
	
	create_notification_confirmation("Issue Refund",b,"submit_custom_refund_request()");

}

function submit_custom_refund_request(){
	
	var invoice_id = document.getElementById("invoice_id_input").value;
	var refund_amount = document.getElementById("refund_amount_input").value;

	if(0 > refund_amount){
		create_notification("Error","Please input a amount");
	}
	
	 $.ajax({
	 url: "ajax/refund-funds.php",
	 type:'POST',
	 data: {
		 "invoice_id":invoice_id,
         "refund_amount":refund_amount
     },	
	 success: function(r){
		 if(r){
			 r = JSON.parse(r);
			 if(r['success']){
				 create_notification("Refund","Refund Success, now head over to paypal and issue a cash refund manually.");
			 }else{ 
				 create_notification("Error",r['msg']);
			 }
		 }else{
			 create_notification("Error","Transaction Failed. Please try again");
		 }
		
     },
	 error: function(r){
		 create_notification("Error", r);
	 },
	 timeout: 50000
	});
}

function manual_payout(){

	var state_drop_down = "<input type='number' min=0>";
	
	var b = "Payout Amount <br><input style='margin:10px 0; height:25px;' id='payout_amount_input' type='number' step='0.01' min='0'> ";
	
	create_notification_confirmation("Manual PayOut",b,"submit_manual_payout_request()");

}

function submit_manual_payout_request(){
	
	var steamid = document.getElementById("steamid").value;
	var payout_amount = document.getElementById("payout_amount_input").value;

	 $.ajax({
	 url: "ajax/manual-payout.php",
	 type:'POST',
	 data: {
		 "steamid":steamid,
         "payout_amount":payout_amount
     },	
	 success: function(r){
		 if(r){
			 r = JSON.parse(r);
			 if(r['success']){
				 create_notification("Success","Remember to deduct "+payout_fee+"% from total amount when using paypal masspay.");
				 get_transaction_data(1);
			 }else{ 
				 create_notification("Error",r['msg']);
			 }
		 }else{
			 create_notification("Error","Transaction Failed. Please try again");
		 }
		
     },
	 error: function(r){
		 create_notification("Error", r);
	 },
	 timeout: 50000
	});
}


function fetch_invoice_details(){
	var invoice_id = document.getElementById("invoice_id").value;
	 $.ajax({
	 url: "ajax/fetch-invoice-details.php",
	 type:'POST',
	 data: {
         "invoice_id":invoice_id
     },	
	 success: function(r){
		 if(r){
			  r = JSON.parse(r);
			 if(r['success']){
				 display_invoice_details(r['data']);
			 }else{
				 create_notification("Error","Transaction Failed. Please try again");
			 }
		 }else{
			 create_notification("Error","Transaction Failed. Please try again");
		 }
     },
	 error: function(r){
		 create_notification("Error", r);
	 },
	 timeout: 50000
	});
}

function display_invoice_details(data){
	
	var tablecontent = "<table><tr><th>Type</th><th>Amount</th><th>Payee/Payer</th><th>Time</th><th>Status</th><th></th></tr>";
	
	for(var key in data){
		
		var v = data[key];
		var steamid = v["steamid"];
		var payee = v["node"];
		var cash = v["amount"];
		var time = v["time"];
		var status = v["status"];
		status = cash_status_code[status];
		var type = v["type"];
		type = cash_type_code[type];
		var button = "<input id='get_poster_details_button' type='button' class='button_theme' style='color:#fff; margin:0;' onclick=\"location.href='/admin/accounts.php?steamid="+steamid+"';\" value='Poster Details'/>";
		tablecontent += "<tr><td>"+type+"</td><td>$"+cash+"</td><td>"+payee+"</td><td>"+time+"</td><td>"+status+"</td><td>"+button+"</td></tr>";
		
	}
	
	document.getElementById("transaction_table").innerHTML = tablecontent;
	
}


function trade_correction_confirmation(id) {
	var header = "Trade Correction Confirmation";
	var message = "You are about correct a trade transaction. <br> Please click confirm to proceed.";
	var confirm_button = "submit_trade_correction("+id+");";
	create_notification_confirmation(header,message,confirm_button);
}


function submit_trade_correction(id){
	toggle_notification_loader(true);
	 $.ajax({
	 url: "ajax/correct-trade.php",
	 type:'POST',
	 data: {
         "id":id
     },	
	 success: function(r){
		 if(r){
			 r = JSON.parse(r);
			 if(r['success']){
				 close_notification();
				 get_transaction_data(current_page);
			 }else{
				 create_notification("Error","Transaction Failed. Please try again");
			 }
		 }else{
			 create_notification("Error","Transaction Failed. Please try again");
		 }
     },
	 error: function(r){
		 create_notification("Error", r);
	 },
	 timeout: 50000
	});
	
}

function refund_item_confirmation(id) {

	var header = "Refund Confirmation";
	var message = "You are about refund an item for it's purchase price. <br> Please click confirm to proceed.";
	var confirm_button = "submit_item_purchase_refund("+id+");";
	create_notification_confirmation(header,message,confirm_button);
	
}

function submit_item_purchase_refund(id){
	toggle_notification_loader(true);
	 $.ajax({
	 url: "/admin/ajax/refund-purchase.php",
	 type:'POST',
	 data: {
         "id":id
     },
	 success: function(r){
		 if(r){
			 r = JSON.parse(r);
			 if(r['success']){
				close_notification();
				get_transaction_data(current_page);
			 }else{
				create_notification("Error","Transaction Failed. Please try again");
			 }
		 }else{
			create_notification("Error","Transaction Failed. Please try again");
		 }
     },
	 error: function(r){
		create_notification("Error","Transaction Failed. Please try again");
	 },
	 timeout: 50000
	});
	
}



function create_element(element,id){
	var object = document.createElement(element);
	if(id){object.id = id;}
	return object;
}

function display_trade_transaction(summary,detail){
	
	var tablecontent = "<table><tr><th>ID</th><th>Items</th><th>Type</th><th>Bot</th><th>Status</th><th>Comment</th><th>Time</th><th>Action</th></tr>";
	
	for(var k in summary){
		
		var v = summary[k];

		var id = v["id"];
		var status = v["status"];
		var time_start = v["time_start"];
		var time_end = v["time_end"];
		var type = v["type"];
		var staff_comment = (v["staff_comment"]) ? v["staff_comment"]+"*" : "";
		var status_comment = (v["status_comment"]) ? v["status_comment"] : "";	
		
		var item_count = count_items(id);
		var bot = v["name"];
		var button = "";
		
		if(type == 0){
			if(status > 3 ){
				button = "<button class='action_button_medium' onClick='trade_correction_confirmation(\""+id+"\")'>Items Deposited</button>";
			}
		}
		else{
			if(status == 3 && bot != "[CS] Refunds"){
				button = "<button class='action_button_medium' onClick='trade_correction_confirmation(\""+id+"\")'>Refund Items</button>";
			}
		}
		

		tablecontent += "<tr><td>"+id+"</td><td>"+item_count+" Items <button id='show_"+k+"' onclick ='show_detail("+k+")' class='action_button_small'>Show</button></td><td>"+trade_type_code[type]+"</td><td>"+bot+"</td><td>"+trade_status_code[status]+"</td><td>"+status_comment+"<br>"+staff_comment+"</td><td>"+time_start+"<br>"+time_end+"</td><td>"+button+"</td></tr>";
		
		for(var key2 in detail){
			
			var v2 = detail[key2];
			if(v2["id"] == id){
				var display_name = v2['display_name'];
				var quantity = v2['quantity'];
				
				tablecontent += "<tr class='detail_"+k+"'style='display:none;border:0;'><td>"+display_name+"</td><td>"+quantity+" Qty</td><td colspan='6'></td></tr>";
			}
		}
	
	}
	
	
	function count_items(id){
		var item_count = 0;
	
		for(var key in detail){
			if(detail[key]["id"] == id){
				item_count += detail[key]['quantity'];
			}
		}
		
		return item_count;
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

	var tablecontent = "<table><tr><th>Name</th><th>Price</th><th>Status</th><th>Time</th><th></th></tr>";

	for(var key in listings){
		
		var v = listings[key];
		var id = v['id'];
		var display_name = v['display_name'];
		var appid = v['appid']
		var appid_name = app_code[v['appid']]['name'];
		var price = v["price"];

		if(v["seller_receive"] && type == 3){
			price += "<br>($"+v["seller_receive"]+")";
		}
		
		var time = v["time_in"];

		if(v["time_out"]){
			time += "<br>"+v["time_out"];
		}
		
		var status = v["status"];
		var button = "";
		
		if(status == 1 && type == 3){
			if(!in_refunds(id)){
				button += "<button class='action_button_2' onClick='sale_item_refund_confirmation("+id+")'>Refund</button><br>";
			}
		
			if(!in_returns(v['item_id'],v['seller_receive'])){
				button += "<button class='action_button_2' onClick='add_to_returns("+key+")'>Add Returns</button>";
			}
			else{
				button += "<button class='action_button_2' onClick='remove_returns("+key+")'>Remove</button>";
			}
			
		}
		
		if(type == 2){
			if(status == 10){
				button += "<button class='action_button_2' onClick='refund_item_confirmation(\""+id+"\")'>Refund</button>";
			}
		}
		
		if(type == 2){
			status = purchase_status_code[status];
		}
		else{
			status = sale_status_code[status];
		}
		
		
		var img  = image_url_source+v["image"]+"/80fx80f";

		tablecontent += "<tr><td class='listing_cell'><div class='item_name_col'><img src='"+img+"'><div class='item_name_text'>"+display_name+"<br>"+appid_name+"</div></div></td><td>$"+price+"</td><td>"+status+"</td><td>"+time+"</td><td class='action_column'>"+button+"</td></tr>";
		
	}
	
 	tablecontent += "</table>";
	
	document.getElementById("transaction_table").innerHTML = tablecontent;
	
 
}

var refund_list = [];
function add_to_refunds(index){

	var id = listings[index]['id'];
	refund_list.push(id);
	
	display_orders(3);
	
}

function remove_refunds(index){
	
	var id = listings[index]['id'];
	
	for(k in refund_list){
		if(id == refund_list[k]){
			refund_list.splice(k, 1);
			break;
		}
	}
	
	display_orders(3);

}

function in_refunds(id){
	for(k in refund_list){
		if(id == refund_list[k]){
			return true;
		}
	}
	return false;
}


function item_sale_refund_confirmation(){

/*create_notification_confirmation("Manual Item Refund","Select Refund Method <br><select style='margin:10px 0; height:25px; font-size:16px;'><option value='1'>Purchase User Item</option><option value='2'>Manual Item Refund</option></select>","submit_item_refunds()");*/

create_notification_confirmation("Item Refund","You are about to mark the selected item as purchased under cashifyskins. Please click confirm to proceed.","submit_item_refunds()");
	
}

function item_sale_refunds(id){
	
	 $.ajax({
	 url: "/admin/ajax/sale-item-refund.php",
	 type:'POST',
	 data: {
         "id":id
     },
	 success: function(r){
		 if(r){
			 r = JSON.parse(r);
			 if(r['success']){
				get_transaction_data(current_page);
			 }else{
				create_notification("Error","Transaction Failed. Please try again");
			 }
		 }else{
			create_notification("Error","Transaction Failed. Please try again");
		 }
     },
	 error: function(r){
		create_notification("Error","Transaction Failed. Please try again");
	 },
	 timeout: 50000
	});
	
}


/*function submit_item_refunds(){

	var steamid = document.getElementById("steamid").value;
	
	 $.ajax({
	 url: "/admin/ajax/manual-item-refund.php",
	 type:'POST',
	 data: {
         "steamid":steamid,
		 "refunds":JSON.stringify(refund_list)
     },
	 success: function(r){
		 if(r){
			 r = JSON.parse(r);
			 if(r['success']){
				get_transaction_data(current_page);
			 }else{
				create_notification("Error","Transaction Failed. Please try again");
			 }
		 }else{
			create_notification("Error","Transaction Failed. Please try again");
		 }
     },
	 error: function(r){
		create_notification("Error","Transaction Failed. Please try again");
	 },
	 timeout: 50000
	});
	
}
*/

function display_buy_order(){

	var tablecontent = "<table><tr><th>Name</th><th>Quantity</th><th>Price</th></tr>";

	for(var key in listings){
		
		var v = listings[key];
		var display_name = v['display_name'];
		var appid_name = app_code[v['appid']]['name'];
		var price = v["price"];
		var time = v["time"];
		var quantity = v["quantity"];
		var id = v['id'];	
		var img  = "https://steamcommunity-a.akamaihd.net/economy/image/"+v["image"];

		tablecontent += "<tr><td class='listing_cell'><div class='item_name_col'><img src='"+img+"'><div class='item_name_text'>"+display_name+"<br>"+appid_name+"</div></div></td><td>"+quantity+"</td><td>$"+price+"<br>($"+(price*quantity).toFixed(2)+")</td></tr>";
	
	}
	
 	tablecontent += "</table>";
	
	document.getElementById("transaction_table").innerHTML = tablecontent;
	
 
}



function cancel_buy_order(id){

	 $.ajax({
	 url: "/ajax/cancel-buy-order.php",
	 type:'POST',
	 data: {
         "id":id
     },
	 success: function(r){
		 r = JSON.parse(r);
		 if(r['success']){
			create_notification("Success",r['msg']);
		 }else{
			 create_notification("Error",r['msg']);
		 }
		get_transaction_data(current_page);
     },
	 error: function(r){
	
	 },
	 timeout: 3000
	});
}

function display_cash_transaction(data){

	var tablecontent = "<table><tr><th>ID</th><th>Type</th><th>Amount</th><th>Payee/Payer</th><th>Status</th><th>Status Comment</th><th>Time</th><th></th></tr>";
	
	for(var key in data){
		
		var v = data[key];
		var id = v['id'];
		var payee = v["node"];
		var cash = v["amount"];
		var time = v["time"];
		var status = v["status"];
		status = cash_status_code[status];
		var status_comment = v["status_comment"];
		var staff_comment = v["staff_comment"];
		var comment = status_comment;
		if(staff_comment){
			comment += "<br>"+staff_comment;
		}
		var type = v["type"];
		type = cash_type_code[type];
		var action = "";
		if(v["type"] == 1){
			action = "<button class='action_button' onClick='issue_custom_refund("+id+")'>Refund</button>"
		}

		tablecontent += "<tr><td>"+id+"</td><td>"+type+"</td><td>$"+cash+"</td><td>"+payee+"</td><td>"+status+"</td><td>"+comment+"</td><td>"+time+"</td><td>"+action+"</td></tr>";
		
	}

	document.getElementById("transaction_table").innerHTML = tablecontent;
 
}
function item_availability_check(){
	
	var type = document.getElementById("type").value;
	var steamid = document.getElementById("steamid").value;
	 $.ajax({
	 url: "ajax/item-missing.php",
	 type:'POST',
	 data: {
		 "steamid":steamid,
         "type":type
     },
	 success: function(r){ 
		 if(r){
			handle_item_availability_check(JSON.parse(r));
		 }
     },
	 error: function(){
		
	 },
	 timeout: 6000
	});
}

function handle_item_availability_check(data){

	if(type == 2){
		var message = "issue a refund to the user";
	}else{
		var message = "check if bot are online or add items to the bot";
	}

	var tablecontent = "<span style='display:block; margin-left:20px;'>This function checks if bot can fulfill item collection/return request.* <br> If the table is empty it means that the bot can fulfill item request collection/return, otherwise please "+message+". </span> <br><table><tr><th>Name</th><th>App</th><th>Quantity</th></tr>";
	
	
	for(var k in data['missing']){
		
		var v = data['missing'][k];
		var item_id = v['item_id'];
		var appid = v['appid'];
		var app_name = app_code[v['appid']]['name'];
		var name = v["display_name"];
		var quantity = v["quantity"];
		
		tablecontent += "<tr><td>"+name+"</td><td>"+app_name+"</td><td>"+quantity+"</td></tr>";

	}
	
 	tablecontent += "</table>";
	

	document.getElementById("transaction_table").innerHTML = tablecontent;
	
}

function change_type(){ // Change Transaction history and regenerates filters for the table

	type = document.getElementById("type").value;
	
	var output = "";	

	if(type == 2){
		output += "<button class='transaction_sumbit' onClick='submit_collection_request()'>Test Item Collection »</button>";
		output += "<button class='transaction_sumbit' style='margin-right:5px;' onClick='item_availability_check(1)'>Check Item Availability</button>";
	}
	else if(type == 3){
		output += "<button class='transaction_sumbit' onClick='submit_return_request()'>Test Returns »</button>";
		output += "<button class='transaction_sumbit' style='margin-right:5px;' onClick='item_availability_check(2)'>Check Item Availability</button>";
	}
	else if(type == 4){
		output += "<button class='transaction_sumbit' onClick='manual_payout()' style='margin-bottom:10px; margin-right:0;'>Manual Payout</button>";
	}
	
	if(type ==  2 || type == 3){

		var status_array;
		
		if(type == 2){
			status_array = purchase_status_code;
		}else{
			status_array = sale_status_code;
		}

		output += "<select id='filter_status' class='filter_small' onChange='get_transaction_data(1)'>";
		output += "<option value=''>Status - All</option>";
		for(k in status_array){
			var name = status_array[k];
			output += "<option value='"+k+"'>"+name+"</option>";
		}
		
		output += "</select><br>";
		output += "<input id='search' type='text' class='filter_large' placeholder='Item Search' onChange='get_transaction_data(1)'>";
	}
	
	var filter = document.getElementById("filter_others");
	filter.innerHTML = output;

	get_transaction_data(1);
}

function get_filter_data(){

	if(type == 2 || type == 3){
		var filter_array = {};
		
		var status = document.getElementById("filter_status").value;
		var search_term = document.getElementById("search").value;
		//var appid = document.getElementById("filter_appid").value;
		
		if(status){
			filter_array["status"] = status;
		}
		if(search_term){
			filter_array["search_term"] = search_term;
		}
		/*if(appid){
			filter_array["appid"] = appid;
		}*/
		
		return JSON.stringify(filter_array);
	}
	else{
		return "";
	}
	
}

function get_transaction_data(page){
	if(!page){page = 1;}
	
	var steamid = document.getElementById("steamid").value;
	
	current_page = page;
	
	var type = document.getElementById("type").value;
	var filter = get_filter_data();
	 $.ajax({
	 url: "/ajax/transaction-history.php",
	 type:'POST',
	 data: {
         "type":type,
		 "steamid":steamid,
		 "page":page,
		 "filter":filter
     },
	 success: function(r){
		if(r){
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
			else if(type == 4){
				display_cash_transaction(r['summary']);
			}
			
			pagination(current_page,r['totalpage']);
			
		 }
     },
	 error: function(r){
	
	 },
	 timeout: 6000
	});

}


function calucate_pricing(bool){
	
	var seller_amount = document.getElementById("seller_amount");
	var buyer_amount = document.getElementById("buyer_amount");
	
	if(bool){
		var price = seller_amount.value;
		buyer_amount.value =  Math.ceil((price*100)/(100-listing_fee)*100)/100;
	}else{
		var price = buyer_amount.value;
		seller_amount.value = Math.floor((price/100)*(100-listing_fee)*100)/100;
	}
	
}

function submit_return_request(){
	if(returns.length == 0){
		create_notification("Error","Please select an item to return.");
		return;
	}
	 var steamid = document.getElementById("steamid").value;
	 
	 $.ajax({
	 url: "/ajax/item-returns.php",
	 type:'POST',
	 data: {
         "returns":JSON.stringify(returns),
		 "steamid":steamid
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
	var steamid = document.getElementById("steamid").value;
	  $.ajax({
	 url: "/ajax/item-collection.php",
	 type:'POST',
	 data: {
		 "steamid":steamid
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
