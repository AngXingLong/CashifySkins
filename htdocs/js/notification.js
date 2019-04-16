
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

/*var confirm_button = document.createElement("button");
	confirm_button.textContent = "Confirm";
	confirm_button.className = "notifcation_button";
	confirm_button.onclick = function(){confirm_function();}
	
	var cancel_button = document.createElement("button");
	cancel_button.textContent = "Cancel";
	cancel_button.className = "notifcation_button";
    cancel_button.onclick =  function(){close_notification();}
	
	var error_msg = document.createElement("div");
	error_msg.id = "notification_error_msg";
	
	var notification_loader = document.createElement("img");
	notification_loader.id = "notification_loader";
	notification_loader.src = "images/loader-3.GIF";
	
	notification_buttons.appendChild(cancel_button);
	notification_buttons.appendChild(confirm_button);
	
*/
	notification.appendChild(notification_close);
	
	notification.appendChild(notification_body);
	notification.appendChild(notification_footer);
	
	document.body.appendChild(background);
	document.body.appendChild(notification);
	
	document.getElementById("notification").style.top = (window.pageYOffset+150)+"px";
	
	 
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