function quick_purchase(item_id,name,price){

	if(csrf == 0){create_notification("Login required","You must login to place your order"); return;}
	if(price == 0){create_notification("Purchase Failed","This item is out of stock or had it's price changed."); return;}
	
	var header = "Purchase Confirmation";
	var message = "You are about to purchase "+name+" for $"+price+". Please confirm your purchase before continuing.";
	var confirm_button = "quick_purchase_submit("+item_id+","+price+");";
	create_notification_confirmation(header,message,confirm_button);
}

function quick_purchase_submit(item_id,price){
	
	toggle_notification_loader(true);

	 $.ajax({
	 url: "/ajax/buy-order.php",
	 type:'POST',
	 data: {
		  "id":item_id,
		  "quantity":1,
		  "price":price,
		  "type":2
     },
	 success: function(r){
		toggle_notification_loader(false);
		 if(r){
	 		 r = JSON.parse(r);
			 if(r['success']){
				document.getElementById("notification_header").innerHTML = "Item Purchased";
				document.getElementById("notification_body").innerHTML = "Your item has been purchased do you wish to collect your item now?";
				document.getElementById("notification_confirm_button").innerHTML = "Collect Now";
				document.getElementById("notification_confirm_button").onclick =  function(){submit_collection_request();};
				fetch_user_funds();
			 }
			 else{
				 create_notification("Purchase Error",r['msg']);
			 }
		 }else{
			 create_notification("Purchase Error","No response from server please try again.");
		 }
     },
	 error: function(){
		 toggle_notification_loader(false);
		 create_notification("Purchase Error","No response from server please try again.");
	 },
	});
	
}

function submit_collection_request(){
	toggle_notification_loader(true);
	 $.ajax({
	 url: "/ajax/item-collection.php",
	 type:'POST',
	 success: function(r){
		toggle_notification_loader(false);
		if(r){
			r = JSON.parse(r);
			if(r['success']){
				create_notification("Item Collection Initiated",r["msg"]);
			}else{
				create_notification("Error",r["msg"]);
			}
		}else{
			create_notification("Purchase Error","No response from server please try again.");
		}
     },
	 error: function(){
		 toggle_notification_loader(false);
		create_notification("Purchase Error","No response from server please try again.");
	 },
	});
	 
}
