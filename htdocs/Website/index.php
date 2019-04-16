<?php
	//Start session
	session_start();	
	//Unset the variables stored in session
	unset($_SESSION['SESS_ID']);
	unset($_SESSION['SESS_NAME']);
	
?>
<html>
<head>
<title>AEVISTRACK Pte Ltd | Sign In</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="Easy Admin Panel Responsive web template, Bootstrap Web Templates, Flat Web Templates, Android Compatible web template, 
Smartphone Compatible web template, free webdesigns for Nokia, Samsung, LG, SonyEricsson, Motorola web design" />
<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
 <!-- Bootstrap Core CSS -->
<link href="css/bootstrap.min.css" rel='stylesheet' type='text/css' />
<!-- Custom CSS -->
<link href="css/style.css" rel='stylesheet' type='text/css' />
<!-- Graph CSS -->
<link href="css/font-awesome.css" rel="stylesheet"> 
<!-- jQuery -->	
<!-- lined-icons -->
<link rel="stylesheet" href="css/icon-font.min.css" type='text/css' />
<!-- //lined-icons -->
<!-- chart -->
<script src="js/Chart.js"></script>
<!-- //chart -->
<!--animate-->
<link href="css/animate.css" rel="stylesheet" type="text/css" media="all">
<script src="js/wow.min.js"></script>
	<script>
		 new WOW().init();
	</script>
<!--//end-animate-->
<!----webfonts--->
<link href='//fonts.googleapis.com/css?family=Cabin:400,400italic,500,500italic,600,600italic,700,700italic' rel='stylesheet' type='text/css'>
<!---//webfonts---> 
 <!-- Meters graphs -->
<script src="js/jquery-1.10.2.min.js"></script>
<!-- Placed js at the end of the document so the pages load faster -->

</head> 
   
 <body class="sign-in-up">
    <section>
			<div id="page-wrapper" class="sign-in-wrapper">
				<div class="graphs">
					<div class="sign-in-form">
						<div class="sign-in-form-top">
							<p><span>Sign In to Admin</span></p>
						</div>
						<div class="signin">
							<div class="signin-rit">
							<form name="loginform" action="login_exec.php" method="post">
							<?php
								if( isset($_SESSION['ERRMSG_ARR']) && is_array($_SESSION['ERRMSG_ARR']) && count($_SESSION['ERRMSG_ARR']) >0 ) {
								echo '<ul class="err">';
								foreach($_SESSION['ERRMSG_ARR'] as $msg) {
									echo '<li>',$msg,'</li>'; 
									}
								echo '</ul>';
								unset($_SESSION['ERRMSG_ARR']);
								}
							?>
							<div class="log-input">
								<div class="log-input-left">
								    <label for="username=">User Name</label>
									<input name="username" type="text" />
								</div>
								<span class="checkbox2">
									 <label class="checkbox"><input type="checkbox" name="checkbox" checked=""><i> </i></label>
								</span>
								<div class="clearfix"> </div>
							</div>
							<div class="log-input">
								<div class="log-input-left">
								    <label for="password">Password</label>
									<input name="password" type="password" />
								</div>
								<span class="checkbox2">
									 <label class="checkbox"><input type="checkbox" name="checkbox" checked=""><i> </i></label>
								</span>
							</div>
							<input type="submit" value="Login">
						</form>	 
						</div>
					</div>
				</div>
			</div>
		<!--footer section start-->
			<footer>
			   <p>Copyright 2015 by AEVISTRACK Pte Ltd. All Rights Reserved | Design by <a href="https://w3layouts.com/" target="_blank">w3layouts.</a></p>
			</footer>
        <!--footer section end-->
	</section>
<script src="js/jquery.nicescroll.js"></script>
<script src="js/scripts.js"></script>
<!-- Bootstrap Core JavaScript -->
   <script src="js/bootstrap.min.js"></script>
</body>
</html>