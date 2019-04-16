<?php
	session_start();
	$Username = $_SESSION['SESS_NAME'];

	$options = '';
	require_once('secret/database.php');
	$sql = "SELECT * FROM Event WHERE Time_Start > NOW()";
	$result = mysql_query($sql);
	while ($rows = mysql_fetch_array($result)){
		$options .= "<option>". $rows['Event_Name']."</option>";
	}
	$option_default = "<option selected disabled>"."Please select by event name to update information"."</option>";
?>

<!DOCTYPE HTML>
<html>
<head>
<title>AEVISTRACK Pte Ltd | Update Event</title>
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
<link rel='icon' type='image/png' href='image/logo.png'>
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
<script language="javascript">
function fetch(str)
{
    var xmlhttp;
 
      if (window.XMLHttpRequest)
      {// code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp=new XMLHttpRequest();
      }
      else
      {// code for IE6, IE5
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
      }	
 
      xmlhttp.onreadystatechange = function() {
        if(xmlhttp.readyState == 4 && xmlhttp.status == 200)
        {
          document.getElementById("fetch").innerHTML = xmlhttp.responseText;
        }
      }
 
      xmlhttp.open("GET","update_fetch.php?Event_Name="+str, true);
      xmlhttp.send();
}
</script>
</head> 
   
 <body class="sticky-header left-side-collapsed"  onload="initMap()">
    <section>
    <!-- left side start-->
		<div class="left-side sticky-left-side">

			<!--logo and iconic logo start-->
			<div class="logo">
				<h1><a href="home.php">Easy <span>Admin</span></a></h1>
			</div>
			<div class="logo-icon text-center">
				<a href="home.php"><i class="lnr lnr-home"></i> </a>
			</div>

			<!--logo and iconic logo end-->
			<div class="left-side-inner">

				<!--sidebar nav start-->
					<ul class="nav nav-pills nav-stacked custom-nav">
						<li class="active"><a href="home.php"><i class="lnr lnr-power-switch"></i><span>Dashboard</span></a></li>
						<li class="menu-list"><a href="#"><i class="lnr lnr-book"></i>  <span>Reports</span></a> 
							<ul class="sub-menu-list">
								<li><a href="attendance.php">Attendance</a> </li>
								<li><a href="attendee.php">Attendee Profile</a> </li>
								<li><a href="client.php">Client List</a> </li>
								<li><a href="audit.php">Audit Trail</a> </li>
								<li><a href="event.php">Event</a> </li>
							</ul>
						</li>
						<li class="menu-list"><a href="#"><i class="lnr lnr-spell-check"></i> <span>New</span></a>
							<ul class="sub-menu-list">
								<li><a href="add_client.php">New Client</a> </li>
								<li><a href="add_event.php">New Event</a> </li>
								<li><a href="add_location.php">New Location</a> </li>
								<li><a href="add_user.php">New User</a> </li>
							</ul>
						</li>
						<li class="menu-list"><a href="#"><i class="lnr lnr-pencil"></i> <span>Update</span></a>
							<ul class="sub-menu-list">
								<li><a href="update_client.php">Update Client</a> </li>
								<li><a href="update_event.php">Update Event</a> </li>
								<li><a href="update_location.php">Update Location</a> </li>
								<li><a href="update_user.php">Update User</a> </li>
							</ul>
						</li>
						<li class="menu-list"><a href="#"><i class="lnr lnr-select"></i> <span>Delete</span></a>
							<ul class="sub-menu-list">
								<li><a href="delete_client.php">Delete Client</a> </li>
								<li><a href="delete_event.php">Delete Event</a> </li>
								<li><a href="delete_location.php">Delete Location</a> </li>
								<li><a href="delete_user.php">Delete User</a> </li>
							</ul>
						</li>
					</ul>
				<!--sidebar nav end-->
			</div>
		</div>
		<!-- left side end-->
    
		<!-- main content start-->
		<div class="main-content">
			<!-- header-starts -->
			<div class="header-section">
			 
			<!--toggle button start-->
			<a class="toggle-btn  menu-collapsed"><i class="fa fa-bars"></i></a>
			<!--toggle button end-->

			<!--notification menu start -->
			<div class="menu-right">
				<div class="user-panel-top">  	
					<div class="profile_details_left">
					</div>
					<div class="profile_details">		
						<ul>
							<li class="dropdown profile_details_drop">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
									<div class="profile_img">	
										<span style="background:url(images/1.jpg) no-repeat center"> </span> 
										 <div class="user-name">
											<p><?php echo $Username;?><span>Administrator</span></p>
										 </div>
										 <i class="lnr lnr-chevron-down"></i>
										 <i class="lnr lnr-chevron-up"></i>
										<div class="clearfix"></div>	
									</div>	
								</a>
								<ul class="dropdown-menu drp-mnu"> 
									<li> <a href="index.php"><i class="fa fa-sign-out"></i> Logout</a> </li>
								</ul>
							</li>
							<div class="clearfix"> </div>
						</ul>
					</div>		
				</div>
			  </div>
			<!--notification menu end -->
			</div>
	<!-- //header-ends -->
			<div id="page-wrapper">
				<div class="graphs">
					<h3 class="blank1">Update New Event</h3>
						<div class="tab-content">
						<div class="tab-pane active" id="horizontal-form">
							<form class="form-horizontal" method="post" action="update_submit.php"  onsubmit="return updateConfirm();">
								<div class="form-group">
									<label for="txtarea1" class="col-sm-2 control-label">Name</label>
									<div class="col-sm-8"><select  name="Event_Name" id="Event_Name" cols="50" rows="4" class="form-control1" onchange="fetch(this.value)"><?php echo $option_default;?><?php echo $options; ?></select></div>
								</div>	
								<div id = "fetch"></div>
						</div>
					</div>
						  <div class="panel-footer">
							<div class="row">
								<div class="col-sm-8 col-sm-offset-2">
									<button class="btn-success btn" type="submit" name="submit_event">Submit</button>
									<button class="btn-default btn">Cancel</button>
									<button class="btn-inverse btn">Reset</button>
								</div>
							</div>
						 </div>
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
<script>
	function updateConfirm(){
    var result = confirm("Update this Event?");
    if(result){
        return true;
    }else{
        return false;
    }
}

</script>
	
<script src="js/jquery.nicescroll.js"></script>
<script src="js/scripts.js"></script>
<!-- Bootstrap Core JavaScript -->
   <script src="js/bootstrap.min.js"></script>
</body>
</html>