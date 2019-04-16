<?php
	//Start session
	session_start();
 
	require_once('secret/database.php');
	require_once('GoogleAuthenticator.php');
	
	$ga = new PHPGangsta_GoogleAuthenticator();
 	
 	$username = $_SESSION['SESS_FIRST_NAME'];
 	
	try{
	$sql = "SELECT * FROM member WHERE username = '$username' ";
	echo "$username";
	$result = mysql_query($sql);
	$_SESSION['result'] = $sql;
	$member = mysql_fetch_assoc($result);
	if ($result){
		echo "Select Successfully ";
		
		$secret = $member['Secret'];
		echo "secret: ". $member['Secret'];
		if ($secret == ""){
			echo "No Secret";
			$secret = $ga->createSecret();
			echo $secret;
			$sql = "UPDATE member SET Secret = '$secret' WHERE username = '$username' ";
			$result = mysql_query($sql);
			if ($result){
				echo "New Record Updated Successfully";
			} else{
				echo "Record Updated Fails";
			}
		} else{
		$secret = $member['Secret'];
		echo "existing secret: ".$secret;
	}
	$conn = null;
	}
	}
	catch(PDOException $e)
	{
		echo $e -> getMessage();
		$_SESSION['error'] = $e;
	}
	$title = 'AEVISTRACK';
	echo "<script type='text/javascript'>alert('$title');</script>";
	$qrCodeUrl = $ga->getQRCodeGoogleUrl($title, $secret);
	$_SESSION['qrCodeUrl'] = $qrCodeUrl;
	//$oneCode = $ga->getCode($secret);
	//echo $oneCode;
	$tolerance = 2;
	$code = (isset($_POST['vericode'])) ? strtolower(trim($_POST['vericode'])) : false;
	echo "code posted: ".$code;
	$checkResult = $ga->verifyCode($secret, $code, 2);    // 2 = 2*30sec clock tolerance
	if ($checkResult) {
	    echo "Congratulations! Login successful.";
	    header("location: home.php");
	    exit();
	} else {
    	
    	
    	$message = "Incorrect Code, Please Try Again.";
    	echo "<script type='text/javascript'>alert('$message');</script>";
    	header("location: vericode.php");
    	exit();
}
?>