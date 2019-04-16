$(document).ready(function(){
	$.ajax({
		 url: "/ajax/set-time-zone.php",
			type:'POST',
			tryCount : 0,
			retryLimit : 3,
			data: {
				"time_zone":moment().format("Z")
			},
			success: function(r){},
			error: function(){
			this.tryCount++;
			   if (this.tryCount <= this.retryLimit) {
				   $.ajax(this);
				   return;
			   }            
			   return;
			},
		 timeout: 10000
	});
});