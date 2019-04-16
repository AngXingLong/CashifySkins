var created = false; 
var interval;
var queue_page = 0;
var queue_data;
var queue_notification_sent = [];

getqueuedata(0);

function select_queue_page(page){

	var number_of_offer = queue_data.length;
	
	if(page > number_of_offer -1){
		queue_page = number_of_offer - 1;
	}
	else if(0 > page){
		queue_page = 0;
	}
	else{
		queue_page = page;
	}
	
	update_status(queue_data);
}

function update_status(){

	var number_of_offer = queue_data.length;
	data = queue_data[queue_page];
	document.getElementById("bar").style.display = "block";
	
	var botname = data["botname"];
	var position = data["position"];
	var status = data["status"];
	var botsid = data["botsid"];
	if(status == 0){
		status = "In Queue | "+position;
	}
	else if(status == 1){
		status = "Sending Trade Offer";
	}
	else if(status == 2){
		status = "Trade Offer Sent";
	}
	
	var security_token = data["security_token"];

	if(number_of_offer > 1){
		document.getElementById("queue_counter").innerHTML = queue_page+1;
		document.getElementById("bot_toggle").style.display = "block";
	}else{
		document.getElementById("bot_toggle").style.display = "none";
	}
	
	document.getElementById("bot_name").innerHTML = "<a href='http://steamcommunity.com/profiles/"+botsid+"' >Bot: "+ botname+"</a>";
	document.getElementById("statustext").innerHTML = "Status: "+status;
	document.getElementById("token").innerHTML = "Token: "+ security_token;
	queue_notification();
	
}

function queue_notification(){
	for(key in queue_data){
		var status = queue_data[key]["status"];
		if(status == 2){
			var botname = queue_data[key]["botname"];
			if(queue_notification_sent.indexOf(botname) == -1){
				alert(botname + " has send you a trade offer");
				queue_notification_sent.push(botname);
			}
		}
	} 
}


function createstatus(){
	
	created = true;
	
	var queuebar = document.createElement("div");  
	queuebar.setAttribute("id", "bar");  

	document.body.appendChild(queuebar);

	var queue_bar = document.getElementById("bar");
	
	queue_bar.innerHTML =  "<div id='description'><div id='bot_name'></div><div id='statustext'></div><div id='token'></div></div>"+
	"<button id='cancel' onclick='remove_queue()'>Cancel Trade X</button>"+
	"<div id = 'bot_toggle'>Toggle Queue<br><br><div><span class='queue_arrows' onclick='select_queue_page(queue_page-1)'>◄ </span><span id ='queue_counter'>4</span><span class='queue_arrows' onclick='select_queue_page(queue_page+1)'> ►</span><div></div>";
							
}

function remove_queue(){
	getqueuedata(1);
}


function getqueuedata(type){
var xmlhttp;
if (window.XMLHttpRequest){xmlhttp=new XMLHttpRequest();}
	else{xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");}
	xmlhttp.onreadystatechange=function(){
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200)
		{	
			//alert(xmlhttp.responseText);
			var response = JSON.parse(xmlhttp.responseText);
				
			if (response) {
				
				if(!created){
					createstatus();
				}
				queue_data = response['queue_details'];
				update_status();
				interval = setTimeout(function(){ getqueuedata(0);}, 5000);
				
			}
			else{
				if(created){
					document.getElementById("bar").style.display = "none";
				}
			}	
		}
	}
	
	xmlhttp.timeout = 5000;
    xmlhttp.ontimeout = function () { getqueuedata(); }
	xmlhttp.open("POST","/ajax/queue.php",true);
	xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	xmlhttp.send("type="+type);
	
}
