<?php
	require $_SERVER['DOCUMENT_ROOT'].'/shared/nav-menu.php';
	require $_SERVER['DOCUMENT_ROOT']."/../settings/currency-config.php";
	require 'shared/app-code.php';
	
	$title = "Buy CS:GO, Dota 2, Tf2, and other various steam economy items - CashifySkins";
	$css[] = "/css/buy.css";  
	$js[] ="/js/quick-purchase.js";
	$js[] ="/js/filter-parameters.js";
	$appid_code = array("1" => "All Games") + $appid_code;
	 
$game_select = array(
	"id"=>"appid",
	"class"=>"filter_medium",
	"onchange"=>"change_appid()",
	"value"=>$appid_code
);

$sort_by = array(
	"id"=>"sort",
	"class"=>"filter_small",
	"onchange"=>"fetch_listing(1)",
	"value"=>array(""=>"Sort By:",1=>"Highest Price",2=>"Lowest Price",3=>"A - Z",4=>"Z - A")
);

if(!empty($_SESSION['steamid'])){
	$user_preferences = select("select game_preference from user where steamid = ?",array($_SESSION['steamid']));
}

$game_select_output = drop_down_no_value($game_select);
$sort_by_output = drop_down_no_value($sort_by);

function drop_down_no_value($parameters){
	
	$selected = !empty($_GET[$parameters["id"]]) ? $_GET[$parameters["id"]] : "";
	$output = "<select ";
	$output .= "id='".$parameters["id"]."'";
	$output .= "class='".$parameters["class"]."'";
	$output .= "onchange='".$parameters["onchange"]."'";
	$output .= ">";
	
	foreach($parameters['value'] as $k => $v){
		if($k == $selected){
			$output .= "<option value='$k' selected>$v</option>";
		}else{
			$output .= "<option value='$k'>$v</option>";
		}
	}
	$output .= "</select>";
	return $output;
}

output_header(); 

?>


<div id = "contentfilter">
<div id = "fixed_filter">

<?php 
echo $game_select_output;
echo $sort_by_output; 
echo "<input id='search_term' name='search' class='filter_large' placeholder='Search'>";
?>
</div> 
<div id ="custom_filter">
</div>
<div>
<button id='search' onclick="fetch_listing(1)">Search</button> 
<button onclick="clear_filter()">Clear Filter</button>
</div>

</div>
<div id = "table">
<div id = "tablecontent"></div>
<div id = "pagenation"></div>
</div>
<script>
<?php
$santized_search = "";
if(!empty($_GET['search_term'])){
	require $_SERVER['DOCUMENT_ROOT']."/../composer/htmlpurifier/library/HTMLPurifier.auto.php";
	$santized_search = $purifier->purify($_GET['search_term']);
}
?>

var santized_search = "<?php echo $santized_search; ?>";
var default_appid = '<?php echo !empty($user_preferences[0]['game_preference']) ? $user_preferences[0]['game_preference'] : 1 ?>';
update_filter_values();
fetch_listing(1);

var typingTimer;                
var $input = $('#search_term');

$input.on('keyup', function () {
  clearTimeout(typingTimer);
  typingTimer = setTimeout(function(){fetch_listing(1);}, 800);
});

$input.on('keydown', function () {
  clearTimeout(typingTimer);
});


function clear_filter(){
	location.href = "/buy.php";
}

function addtosearch(v){
	document.getElementById("search_term").value = v;	
	fetch_listing(1);
}


function change_appid(){
	generate_custom_filter(document.getElementById("appid").value);
	document.getElementById("search_term").value = "";
	document.getElementById("sort").value = "";
	fetch_listing(1);
}

function generate_custom_filter(appid){
	var output = "";
	var appid = document.getElementById("appid").value;
	
	if(filters[appid]){
		for(k2 in filters[appid]){
			var v = filters[appid][k2];
			var type = v['e_type'];
			if(type == 1){
				output += drop_down(v);
			}
			else if(type == 2){
				output += drop_down_menu(v);
			}
			else if(type == 3){
				output += "<br>";
			}
		}
	}
	document.getElementById("custom_filter").innerHTML = output;
}




function update_filter_values(){

	document.getElementById("sort").value = getParameterByName("sort");
	
	document.getElementById("search_term").value = santized_search;
	
	var appid = getParameterByName("appid");

	if(appid == ""){
		document.getElementById("appid").value = default_appid;
	}
	else if(!filters[appid]){
		document.getElementById("appid").value = 1;
	}
	else{
		document.getElementById("appid").value = getParameterByName("appid");
	}

	var appid = document.getElementById("appid").value;
	
	if(filters[appid]){
		generate_custom_filter();
	}

}


function getParameterByName(name) {
    var url = window.location.href;
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
        results = regex.exec(url);
    if (!results) return '';
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
}

function drop_down_menu(parameters){
	 
	var output = "<div class='"+parameters['e_class']+"' ><ul>";
	
	for(k in parameters['e_value']){
		output += "<li>"+k+"<ul>";
		for(k2 in parameters['e_value'][k]){
			output += "<li onClick=\"addtosearch('"+parameters['e_value'][k][k2]+"') \">"+parameters['e_value'][k][k2]+"</li>";
		}
		output += "</ul></li>";
	}
	
	output +=  "</ul></div>";
			
	return output;
}

function drop_down(parameters){
	var output = "<select ";
	output += "id = '"+parameters["e_id"]+"'";
	output += "class = '"+parameters["e_class"]+"'";
	
	if(parameters["e_onchange"]){
		output += "onchange='"+parameters["e_onchange"]+"'";
	}else{
		output += "onchange='fetch_listing(1)'";
	}
	
	output += ">";
	
	if(parameters["e_identifer"]){
		output += "<option value=''>"+parameters["e_identifer"]+"</option>";
	}
	
	var selected = getParameterByName(parameters["e_id"]);

	for(k in parameters['e_value']){
		var v = parameters['e_value'][k];
		if(v == selected){
			output += "<option value='"+v+"' selected>"+v+"</option>";
		}else{
			output += "<option value='"+v+"'>"+v+"</option>";
		}
	}
	output += "</select>";
	return output;
}

function drop_down_no_value(parameters){
	
	var output = "<select ";
	output += "id='"+parameters["id"]+"'";
	output += "class='"+parameters["class"]+"'";
	output += "onchange='"+parameters["onchange"]+"'";
	output += ">";
	
	for(k in parameters['value']){
		var v = parameters['value'][k];
		if(k == selected){
			output += "<option value='"+k+"' selected>"+v+"</option>";
		}else{
			output += "<option value='"+k+"'>"+v+"</option>";
		}
	}
	output += "</select>";
	return output;
}

function fetch_filter_parameters(){
	
	var url_parameters = {};
	
	var appid = document.getElementById("appid").value;
	if(appid > 1){url_parameters["appid"] = appid;}
	
	var search_term = document.getElementById("search_term").value;
	if(search_term){url_parameters["search_term"] = search_term;}
	
	var sort_by = document.getElementById("sort").value;
	if(sort_by){url_parameters["sort"] = sort_by;}
	
	
	for(k in filters){
		if(k == appid){
			for(k2 in filters[k]){
				var v = filters[k][k2];
				var id = v["e_id"];
				if(id && document.getElementById(id).value){
					url_parameters[id] = document.getElementById(id).value;
				}
			}
		}
	}
	
	return url_parameters;
	
}

function push_url_state(parameters){
	
	/*var i = 0;
	for(k in parameters){
		if(k == "appid"){
			if(parameters[k] == default_appid){
				delete parameters[k];
			}
			break;
		}
		i++;
	}*/
	if(!jQuery.isEmptyObject(parameters)){
		history.pushState({},"","buy.php?"+jQuery.param(parameters));
	}else{
		history.pushState({},"","buy.php");
	}
}

function fetch_listing(page){
	var filter_parameters = fetch_filter_parameters();
	
	if(page > 1){
		filter_parameters["page"] = page;
	}
	
	 $.ajax({
	 url: "/ajax/buy-listings.php",
	 type:'GET',
	 data: filter_parameters,
	 success: function(r){
		 r = JSON.parse(r);
		 generate_table_listings(r['data']);
		 pagination(page,r['totalpage']);
		 push_url_state(filter_parameters);
     },
	 error: function(){
		fetch_listing();
	 },
	 timeout: 10000
	});
}

function generate_table_listings(data){
	var table = document.getElementById("tablecontent");
	table.innerHTML = "";

	for(k in data){
		var v = data[k];

		var item_id = v['id'];
		var name = v['name'];
		var display_name = v['display_name'];
		var picture_url = v['image'];
		var picture_url = "<img class='product_image' src='https://steamcommunity-a.akamaihd.net/economy/image/"+picture_url+"/220fx220f'>";
		var price = v['price'];
		var quantity = v['quantity'];
		var appid = v['appid'];
		
		color = v['color'];
		border_color = "";
		if(color){
			border_color = "style='border-bottom-color: #"+color+";'";
		}
		//app_icon = app_code[appid]['icon'];
		var app_icon = app_code[appid]["icon"];
		
		var quick_purchase = "<div onClick=\"quick_purchase('"+item_id+"','"+display_name.replace(/'/g, "\\'")+"','"+price+"')\" class='quick'>";
		table.innerHTML  += "<div class='card'><a href=\"/listings.php?name="+name+"&appid="+appid+"\" class='card_img' "+border_color+">"+picture_url+" <img class='card_app_icon' src='"+app_icon+"'></a><div class='description'>"+display_name+"</div><div class='card_bottom'>$"+price+" <span class='divider'>|</span> <img height='12' src='/images/stock.png'> "+quantity+" </div>"+quick_purchase+"<img src='images/thunder.png'/> Quick Buy</div></div>";

	}
	
}

function pagination(current,last){

	var x = document.getElementById("pagenation");
	var pagenation = "";
	
	if(current == 0){
		x.innerHTML = "";
		return;
	}
	
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


$("input").keypress(function(event) {
    if (event.which == 13) {
      fetch_listing(1);
    }
});

</script>
<?php footer();?>
</body>
</html>
