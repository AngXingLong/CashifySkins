var max_seller_listing_price = 950; // sale price $1000
var min_seller_listing_price = 0.01; //  sale price $0.02
var listing_fee = 5;
var payout_fee = 5;
var image_url_source = "https://steamcommunity-a.akamaihd.net/economy/image/";

var app_code = {
	753:{"name":"Steam","icon":"/images/apps/icons/753.jpg"}, 
	730:{"name":"Counter-Strike: Global Offensive","icon":"/images/apps/icons/730.jpg"}, 
	570:{"name":"Dota 2","icon":"/images/apps/icons/570.jpg"}, 
	440:{"name":"Team Fortress 2","icon":"/images/apps/icons/440.jpg"}, 
};

var cash_status_code = {
			0:"Pending",
			1:"Processing",
			2:"Complete",
			3:"Denied",
			4:"Canceled",
			5:"Transaction Error"
};

var cash_type_code = {
			0:"Withdrawals",
			1:"Funds Purchase",
			2:"Purchase Refunds",
			3:"Promo"
};

var trade_type_code = {
			0:"Item Deposit",
			1:"Item Returns",
			2:"Item Collection",
			3:"Specific Purchase"
};

var trade_status_code = {
			0:"In Queue",
			1:"Sending Offer",
			2:"Trade Offer Sent",
			3:"Complete",
			4:"Issue With User",
			5:"Issue With Bot",
			6:"Escrow Cooldown",
};

var sale_status_code = {
			0:"Awaiting Deposit", 
			1:"On Sale",
			2:"Sold",
			3:"Return Inititated",
			4:"Returned",
			5:"Expired",
			6:"Deposit Failed",
			8:"Awaiting Delivery To Buyer",
};

var purchase_status_code = {
			8:"Specific Collection",
			10:"Awaiting Collection",
			11:"Collection Initiated",
			12:"Collected",
			13:"Refunded",
			14:"Expired",
			15:"Purchase Failed",
};
// Function to get sell order price
// set true to get buyer amount , false for seller amount

function calculate_price(amount,toogle) {
	if(toogle){
		return Math.ceil((amount*100)/(100-listing_fee)*100)/100;
	}
	else{
		return Math.floor((amount/100)*(100-listing_fee)*100)/100;
	}
}

function fetch_user_funds(){
	
	$.ajax({
	 url: "/ajax/user-funds.php",
	 type:'POST',
	 tryCount : 0,
     retryLimit :2,
	 data: {},
	 success: function(r){
		 if(r || r == 0){
			document.getElementById("user_funds").textContent = r;
		 } 
     },
	 error : function() {
         this.tryCount++;
         if(this.tryCount <= this.retryLimit) {
             $.ajax(this);
             return;
         }            
    },
	 timeout: 10000
	});
}

// Notification Creator

function create_notification(header,message){
	
	if (document.getElementById('notification') !== null) {
		
		if(document.getElementById("notification_button_wrapper").style.display == "none"){
			toggle_notification_loader(false);
		}
		
		document.getElementById("notification_header").innerHTML = header;
		document.getElementById("notification_body").innerHTML = message;
		document.getElementById("notification_button_wrapper").innerHTML = "<button class='notifcation_button' onClick='close_notification();'>Ok</button>";
		return;
	}
	
	var background = document.createElement("div");
	background.id = "background_fade";
	background.onclick = close_notification;	
	
	var notification = document.createElement("div");
	notification.id = "notification";
	notification.className = "notification_default";
	
	var notification_close = document.createElement("div");
	notification_close.id = "notification_close";
	notification_close.onclick = close_notification;
	notification_close.textContent = "X";
	
	if(header){
		var notification_header = document.createElement("div");
		notification_header.id = "notification_header";
		notification_header.className = "notification_header_default";
		notification_header.innerHTML = header;
		notification.appendChild(notification_header);
	}else{
		var notification_header = document.createElement("div");
		notification_header.id = "notification_header";
		notification.appendChild(notification_header);
	}
	
	var notification_body = document.createElement("div");
	notification_body.id = "notification_body";
	notification_body.className = "notification_body_default";
	notification_body.innerHTML = message; 
	
	var notification_footer = document.createElement("div");
	notification_footer.id = "notification_footer";
	notification_footer.className= "notification_footer_default";
	notification_footer.innerHTML = "<button id='close_notification_button' class='notifcation_button' onClick='close_notification()'>Ok</button>";

	notification.appendChild(notification_close);
	
	notification.appendChild(notification_body);
	notification.appendChild(notification_footer);
	
	
	if (document.getElementById('background_fade') == null) {document.body.appendChild(background);}
	document.body.appendChild(notification);
	
	document.getElementById("notification").style.top = (window.pageYOffset+150)+"px";
	
	
	if(window.innerWidth >= 700) {
		document.getElementById("notification").style.top = (window.pageYOffset+150)+"px";
	} 
	
	
}


function create_notification_confirmation(header,message,confirm_function){
	
	if (document.getElementById('notification') !== null) {close_notification();}
	
	var background = document.createElement("div");
	background.id = "background_fade";
	background.onclick = close_notification;
	
	var notification = document.createElement("div");
	notification.id = "notification";
	notification.className = "notification_default";
	
	var notification_close = document.createElement("div");
	notification_close.id = "notification_close";
	notification_close.onclick = close_notification;
	notification_close.textContent = "X";
	
	if(header){
		var notification_header = document.createElement("div");
		notification_header.id = "notification_header";
		notification_header.className = "notification_header_default";
		notification_header.innerHTML = header;
		notification.appendChild(notification_header);
	}else{
		var notification_header = document.createElement("div");
		notification_header.id = "notification_header";
		notification.appendChild(notification_header);
	}
	
	var notification_body = document.createElement("div");
	notification_body.id = "notification_body";
	notification_body.className = "notification_body_default";
	notification_body.innerHTML = message; 
	
	var notification_footer = document.createElement("div");
	notification_footer.id = "notification_footer";
	notification_footer.className = "notification_footer_default";
	notification_footer.innerHTML = "<div id='notification_button_wrapper'><button class='notifcation_button' onClick='close_notification()'>Cancel</button> <button class='notifcation_button' onClick='"+confirm_function+"' id='notification_confirm_button'>Confirm</button><div id='notification_error_msg'></div></div><img id='notification_loader' class='notification_loader_right' src='/images/loader-3.GIF'>";

	notification.appendChild(notification_close);
	
	notification.appendChild(notification_body);
	notification.appendChild(notification_footer);
	
	document.body.appendChild(background);
	document.body.appendChild(notification);
	
	if(window.innerWidth >= 700) {
		document.getElementById("notification").style.top = (window.pageYOffset+150)+"px";
	} 
	
	 
}

function toggle_notification_loader(toggle){
	
	if(toggle){
		document.getElementById("notification_button_wrapper").style.display = "none";
		document.getElementById("notification_loader").style.display = "block";
	}else{
		document.getElementById("notification_button_wrapper").style.display = "block";
		document.getElementById("notification_loader").style.display = "none";
	}
	
}

function close_notification(){
	var r = document.getElementById("notification");
	r.parentNode.removeChild(r);
	r = document.getElementById("background_fade");
	r.parentNode.removeChild(r);
}

function close_notification_only(){
	var r = document.getElementById("notification");
	r.parentNode.removeChild(r);
}
