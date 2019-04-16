<?php
  //Start session
  session_start();
  $username = $_SESSION['SESS_NAME'];
 
  require_once('secret/database.php');
  require_once('GoogleAuthenticator.php');
  
  $ga = new PHPGangsta_GoogleAuthenticator();
  
  
  //$secret = $_SESSION['secret'];
  
  //Array to store validation errors
  $errmsg_arr = array();
 
  //Validation error flag
  $errflag = false;
 
  //Function to sanitize values received from the form. Prevents SQL injection
  function clean($str) {
    $str = @trim($str);
    if(get_magic_quotes_gpc()) {
      $str = stripslashes($str);
    }
    return mysql_real_escape_string($str);
  }

  $code = (isset($_POST['vericode'])) ? strtolower(trim($_POST['vericode'])) : false;
  
 
  //If there are input validations, redirect back to the verification form
  if($errflag) {
    $_SESSION['ERRMSG_ARR'] = $errmsg_arr;
    session_write_close();
    header("location: vericode.php");
    exit();
  }
  $secret="";
  $query = "SELECT Secret FROM UserProfile WHERE Username = '$username'";
  $result = mysql_query($query);
  $row = mysql_fetch_assoc($result);
   //$sql = "SELECT * FROM UserProfile WHERE Username = '$username' ";
   
   //$result = mysql_query($sql);
   //$value = mysql_fetch_object($result);
   $secret = $row['Secret'];
  if (isset($secret) && !empty($secret)){
    $secret = $secret;
  } else{
    $secret_new = $ga->createSecret();
    $sql = "UPDATE UserProfile SET Secret = '$secret_new' WHERE Username = '$username' ";
    //$result = mysql_query($sql);
    $secret = $secret_new;
  }
  $title = 'AEVISTRACK';
  //echo "<script type='text/javascript'>alert('$title');</script>";
  $qrCodeUrl = $ga->getQRCodeGoogleUrl($title, $secret);
  //$_SESSION['qrCodeUrl'] = $qrCodeUrl;
  //$oneCode = $ga->getCode($secret);
  //echo $oneCode;
  $tolerance = 2;
  
  //echo "code posted: ".$code;
  if (!empty($code)){
    $checkResult = $ga->verifyCode($secret, $code, 2);    // 2 = 2*30sec clock tolerance
    if ($checkResult) {
        echo "Congratulations! Login successful.";
        header("location: home.php");
        exit();
    } else {
        $errmsg_arr[] = 'Incorrect Code, Please Try Again.';
        $errflag = true;
        if($errflag) {
          $_SESSION['ERRMSG_ARR'] = $errmsg_arr;
          session_write_close();
          header("location: vericode.php");
          exit();
        }
    }
  }
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
<script>
logout(){
  header("location: index.php");
}
</script>
</head> 
<body>
<button style="margin-bottom: 30px;" onclick="logout();">Log out</button>

<form name="veriform" method="post">
<h1> 2-Step Verification  </h1>
<div class="inset">
  <td colspan="2">
    <!--the code bellow is used to display the message of the input validation-->
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
  </td>
  <tr>
    <td width="116" style="color: #fff;"><label for="vericode">Please scan QR Code below using Google Authenticator App for verfication code</label></td>
    <img alt="qrcode" style="display: block; margin: 20px auto; border:0; outline:0; " width="166" height="166" src="<?php echo $qrCodeUrl ?>"/>
    <td width="116" style="color: #fff;"><label for="vericode">Enter the 6-digit Code</label></td>
    <td width="177"><input name="vericode" type="vericode" /></td>
  </tr>
  <tr>
    <td><div align="right"></div></td>
    <input type="submit" value="verify" name="verify" target="verify"></td>
  </tr>
</div>

</form>
            
</div>
<br>
<br>
<br>
<br>
<br>
<br>
<div id="footer" style="margin: 10px;">
   <p>Copyright 2015 by AEVISTRACK Pte Ltd. All rights reserved.</p>
</div> 

</body>
</html>