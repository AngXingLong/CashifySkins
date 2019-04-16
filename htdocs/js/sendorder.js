function quick_trade(name,price,appid){
	var order = [];
	order.push({name:name,price:price,quantity:1});
	submit_buy_order(order,2,appid);
}

function submit_buy_order(order,type,appid){
	
	if(csrf = 0){create_notification("Login Required","You must login to place your order"); return;}

	 $.ajax({
	 url: "/ajax/buy-order.php",
	 type:'POST',
	 data: {
          "order": JSON.stringify(order),
		  "type":type,
		  "appid":appid,
		  
     },
	 success: function(r){
			alert(r);
     },
	 error: function(){
		
	 },
	 timeout: 6000
	});
	
}
