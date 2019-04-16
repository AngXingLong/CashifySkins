<?php
header("HTTP/1.1 503 Service Temporarily Unavailable");
header("Status: 503 Service Temporarily Unavailable");
header("Retry-After: 3600");
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Down For Maintenance</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
     
    </head>
    <body>
    <img class='banner'src="/images/cashifyskins-banner.png">
    	<h1>Down For Maintenance</h1>
	<p>Sorry for the inconvenience, but we’re performing a maintenance at the moment.</p>
	<p>We’ll be back online shortly!</p>
    </body>
       <style>
        h1 { 
			font-size: 50px; 
		}
        body { 
			text-align:center; 
			font: 20px Helvetica, sans-serif; 
			color: #ddd;  
			background-image:url(/images/bg_footer.jpg);
			background-color:#000;
			background-position: center; 
			background-repeat:no-repeat;
		}
		.banner{
			margin-top:40px;
			margin-right:20px;
		}
        </style>
    
    
</html>