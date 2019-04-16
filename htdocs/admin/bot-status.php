<?php
require "shared/nav-menu.php";
require "../shared/bot-type-code.php";

$css[] = "css/bot-status.css";
$js[] = "/js/moment.js";
output_header(); 
?>
<div id='basic_content_wrapper'>
<span class='content_header'><h1>Bot Status</h1><span class='manage_all_bot_button_wrapper'><button onclick='start_all_bot()' class="manage_all_bots_button">Start All</button>
<button class="manage_all_bots_button" onclick='stop_all_bot()' >Stop All</button></span></span>
<table>
<tr><th>Name</th><th>SteamID</th><th>Type</th><th>Expected State</th><th>Status</th><th>Last Reported</th><th></th></tr>
<tbody id='table_content'></tbody>
</table>
</div>
<script>
<?php

echo "var status_code = ".json_encode($bot_status_code).";";
echo "var bot_type_code = ".json_encode($bot_type_code).";";

?>
fetch_bot_status();
setInterval(function(){ fetch_bot_status(); }, 2000);

function populate_table(data){
	var table = document.getElementById("table_content");
	table.innerHTML = "";
	for(k in data){
		var v = data[k];
		var name = v['name'];
		var steamid = v['steamid'];
		var type = bot_type_code[v['type']];
		var status = v['status'];
		var expected_status = v['expected_status'];
		var last_reported = v['last_reported'];
		last_reported = moment(v['last_reported']).fromNow();
	
		var button = "";
	
		if(status == 0){	
			button = "<button class='button_theme' onClick='start_bot(\""+steamid+"\")'>Start Bot</button>";
		}
		else if(status == 1 || status == 2){
			button = "<button class='button_theme' onClick='test_bot_responsiveness(\""+steamid+"\")'>Ping Bot</button> ";
			button += "<button class='button_theme' onClick='stop_bot(\""+steamid+"\")'>Stop Bot</button>";
		}
		
		status = status_code[status];
		expected_status = status_code[expected_status];
		
		table.innerHTML += "<tr><td>"+name+"</td><td>"+steamid+"</td><td>"+type+"</td><td>"+expected_status+"</td><td>"+status+"</td><td>"+last_reported+"</td><td>"+button+"</td></tr>";
	}
	
}

function fetch_bot_status(){
	$.ajax({
	 url: "ajax/bot-status-data.php",
	 type:'POST',
	 data: {
		 
     },
	 success: function(r){
		 if(r){
			populate_table(JSON.parse(r));
		 }
     },
	 error: function(r){
		 
	 },
	 timeout: 50000
	});
}

function start_bot(steamid){
	$.ajax({
	 url: "ajax/start-bot.php",
	 type:'POST',
	 data: {
		 "steamid":steamid
     },
	 success: function(r){
		 if(r){
			r = JSON.parse(r);
			if(r['success']){
				create_notification("Success","An order has been sent to start the bot may take up to a min to start up");
				return;
			}
		 }
		 create_notification("Error","Unable to resolve your request please try again");
     },
	 error: function(r){
		 create_notification("Error","Unable to resolve your request please try again");
	 },
	 timeout: 50000
	});
}

function stop_bot(steamid){
	$.ajax({
	 url: "ajax/stop-bot.php",
	 type:'POST',
	 data: {
		 "steamid":steamid
     },
	 success: function(r){
		 if(r){
			r = JSON.parse(r);
			if(r['success']){
				create_notification("Success","An order has been sent to stop the bot may take up to 5 mins to stop the bot");
				return;
			}
		 }
		 create_notification("Error","Unable to resolve your request please try again");
     },
	 error: function(r){
		 create_notification("Error","Unable to resolve your request please try again");
	 },
	 timeout: 50000
	});
}


function start_all_bot(){
	$.ajax({
	 url: "ajax/start-all-bot.php",
	 type:'POST',
	 data: {
		 
     },
	 success: function(r){
		 if(r){
			r = JSON.parse(r);
			if(r['success']){
				create_notification("Success","An order to start all bot. This will take a while to start up");
				return;
			}
		 }
		 create_notification("Error","Unable to resolve your request please try again");
     },
	 error: function(r){
		 create_notification("Error","Unable to resolve your request please try again");
	 },
	 timeout: 50000
	});
}

function stop_all_bot(){
	$.ajax({
	 url: "ajax/stop-all-bot.php",
	 type:'POST',
	 data: {
		 
     },
	 success: function(r){
		 if(r){
			r = JSON.parse(r);
			if(r['success']){
				create_notification("Success","An order has been issues to stop all bots. This will take up to 5 mins to stop");
				return;
			}
		 }
		 create_notification("Error","Unable to resolve your request please try again");
     },
	 error: function(r){
		 create_notification("Error","Unable to resolve your request please try again");
	 },
	 timeout: 50000
	});
}

function test_bot_responsiveness (steamid){
	$.ajax({
	 url: "ajax/test-bot.php",
	 type:'POST',
	 data: {
		 "steamid":steamid
     },
	 success: function(r){
		 if(r){
			r = JSON.parse(r);
			if(r['success']){
				create_notification("Success","A test packet has been sent. Please wait a bit before refreshing the page to see if the bot response.");
				return;
			}
		 }
		 create_notification("Error","Unable to resolve your request please try again");
     },
	 error: function(r){
		 create_notification("Error","Unable to resolve your request please try again");
	 },
	 timeout: 50000
	});
}

</script>

<?php footer();?>
</body>
</html>