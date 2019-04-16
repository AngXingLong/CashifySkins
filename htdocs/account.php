<?php 
session_start();
session_regenerate_id(true);

require 'shared/nav-menu.php';

if(empty($_SESSION['steamid'])){
	header("Location: /login-required.php");
	die;
}

require "shared/trust-score-code.php";
require "shared/app-code.php";

$title = "Account - CashifySkins";
$css[] = "/css/account.css";

	output_header(); 

	$result = select("select steamid32, offer_token, game_preference, trust_score, credit FROM user where steamid = ? ;", array($_SESSION['steamid']));
	$result = $result[0];
	$usersid32 = $result['steamid32'];
	$tradetoken = $result['offer_token'];
	$credit = $result['credit'];
	$game_preference = $result['game_preference'];
	$url = "";
	if (!empty($tradetoken) && !empty($usersid32)){
		$url = "https://steamcommunity.com/tradeoffer/new/?partner=".$usersid32."&token=".$tradetoken;
	}
	
	$trust = $trust_score_code[$result["trust_score"]];
	
	
echo "<div id='basic_content_wrapper'>";
echo "<div class='profilecontent'>";
echo "<img class='avatar_image' src=".$_SESSION['steam_avatarfull']."/>"; 
echo "<div class='profileinfo'><h1>".$_SESSION['steam_personaname']."</h1>";
echo "Trust Score: ".$trust; 
echo "<br><br>Credit Balance: $".number_format ($user_details["credit"],2); 
echo "</div>";
echo "<a href='".$_SESSION['steam_profileurl']."/tradeoffers/privacy' class='url' >Get Trade Url</a>

<input id='trade_token' type='text' class='input_input' value='".$url."'>";

$game_select = array(1=>"None") + $appid_code;

echo "<br><span class='text'>Game Preference</span>
<select class='input_select' id='game_preference' name='game_preference'>";
foreach($game_select as $appid => $name){
	if($game_preference == $appid){
		echo "<option value='$appid' selected>$name</option>";
	}else{
		echo "<option value='$appid'>$name</option>";
	}
}
echo"</select>";

echo "<p><button id='submit' class='button_orange' onclick='update_account_info()'>Update</button></p>";

echo "</div>";

?>
<div class='header'>Trasaction History</div>
<div id='filter'>
<select id="type" class='filter_medium' onChange="change_type()">
<option value=0>Trade Offer</option>
<option value=2>Purchases</option>
<option value=3>Sales</option>
<option value=4>Billing</option>

</select><span id='filter_others'><select id='filter_status' class="filter_small"></select><br>
<input id='search' type="text" class="filter_large" placeholder="Item search">
</span>
<div id='return_action'></div>
</div>
<div id = "transaction_table"></div>

<div id = 'pagenation'></div>

</div>
<script>

var current_page = 1;
var type = 1;
var type_list = {0:"Trade Offer",2:"Purchases",3:"Sales",4:"Cash"};
var listings;

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

	var tablecontent = "<table><thead><tr><th>ID</th><th>Items</th><th>Type</th><th>Bot</th><th>Status</th><th>Status Comment</th><th>Token</th><th>Time</th></thead></tr>";
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
		
		tablecontent += "<tr><td>"+id+"</td><td style='width:140px;'>"+item_count+" Items <button id='show_"+index+"' style ='float:right;' onclick ='show_detail("+index+")' class='action_button_small'>Show</button></td><td>"+type+"</td><td>"+bot+"</td><td>"+status+"</td><td>"+status_comment+"</td><td>"+security_token+"</td><td>"+time_start+"<br>"+time_end+"</td></tr>";
		
		for(var key2 in detail){
			
			var v2 = detail[key2];
			if(v2["id"] == id){
				var display_name = v2['display_name'];
				var quantity = v2['quantity'];
				
				tablecontent += "<tr class='detail_"+index+"'style='display:none;border:0;'><td>"+display_name+"</td><td>"+quantity+" Qty</td><td colspan='6'></td></tr>";
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
		
		for (i = 0; i < x.length; i++){
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

function display_orders(type){

	var tablecontent = "<table><thead><tr><th>Name</th><th>Price</th><th>Status</th><th>Time</th></thead></tr>";
	var index = 0;
	for(var key in listings){
		
		var v = listings[key];
		var display_name = v['display_name'];
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
		
		if(type == 2){
			status = purchase_status_code[status];
		}else{
			status = sale_status_code[status];
		}
		
		var color = v["color"];
		var img  = image_url_source+v["image"]+"/80fx80f";

		tablecontent += "<tr><td class='listing_cell'><div class='item_name_col'><div class='item_name_col_image_wrapper'><img src='"+img+"'></div><div class='item_name_text'><b style='color:#"+color+";'> "+display_name+"</b><br>"+appid_name+"</div></div></td><td>$"+price+"</td><td>"+status+"</td><td>"+time+"</td></tr>";
		index ++;
		
	}
	
 	tablecontent += "</table>";
	
	document.getElementById("transaction_table").innerHTML = tablecontent;
	
 
}


function display_cash_transaction(data){

	var tablecontent = "<table><tr><th>Description</th><th>Amount</th><th>Payee</th><th>Status</th><th>Status Comment</th><th>Time</th></tr>";
	for(var key in data){
		
		var v = data[key];

		var payee = v["node"];
		var amount = v["amount"];
		var time = v["time"];
		var status = v["status"];
		var status_comment = "";
		if( v["status_comment"]){
			status_comment = v["status_comment"];
		}
		
		status = cash_status_code[status];
		var type = v["type"];
		if(type == 0){
			amount = "-$"+amount;
		}else{
			amount = "$"+amount;
		}
		type = cash_type_code[type];

		tablecontent += "<tr><td>"+type+"</td><td>"+amount+"</td><td>"+payee+"</td><td>"+status+"</td><td>"+status_comment+"</td><td>"+time+"</td></tr>";
		
	}
	

	document.getElementById("transaction_table").innerHTML = tablecontent;
 
}

change_type();

function change_type(){ // Change Transaction history and regenerates filters for the table

	type = document.getElementById("type").value;
	
	var output = "";	

	if(type ==  2 || type == 3){

		var status_array;
		
		if(type == 2){
			status_array = purchase_status_code;
		}else{
			status_array = sale_status_code;
		}

		output += "<select id='filter_status' class='filter_small' onChange='get_transaction_data()'>";
		output += "<option value=''>Status - All</option>";
		for(k in status_array){
			var name = status_array[k];
			output += "<option value='"+k+"'>"+name+"</option>";
		}
		
		output += "</select><br>";
		
		output += "<select id='filter_appid' class='filter_large' onChange='get_transaction_data()'>";
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
		var filter_array = {};
		
		var status = document.getElementById("filter_status").value;
		var search_term = document.getElementById("search").value;
		var appid = document.getElementById("filter_appid").value;
		
		if(status){
			filter_array["status"] = status;
		}
		if(search_term){
			filter_array["search_term"] = search_term;
		}
		if(appid){
			filter_array["appid"] = appid;
		}
		
		return JSON.stringify(filter_array);
	}
	else{
		return "";
	}
	
}

var delayTimer;

function term_search() {
    clearTimeout(delayTimer);
    delayTimer = setTimeout(function(){get_transaction_data(1);}, 1000); 
}

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
			
			pagination(page,r['totalpage']);
			
		 }
     },
	 error: function(r){
	
	 },
	 timeout: 10000
	});

}

function update_account_info(){
	
	var game_preference = document.getElementById("game_preference").value;
	var trade_token = document.getElementById("trade_token").value;
	
	$.ajax({
		type: "POST",
		url: "/ajax/update-account.php",
		data: {"trade_token":trade_token,"game_preference":game_preference},
		success: function(r) {
			if(r){
				r = JSON.parse(r);
				if(r['success']){
					create_notification("Update Successfull",r["msg"]);
				}else{
					create_notification("Error",r["msg"]);
				}
			}else{
				create_notification("Error","There was an error processing your request");
			}
		},
		error: function() {
			create_notification("Error","There was an error processing your request");
		}
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
