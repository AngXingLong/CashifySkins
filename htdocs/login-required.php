<?php
require $_SERVER['DOCUMENT_ROOT'].'/shared/nav-menu.php';
$title = "Login Required - CashifySkins";
output_header(); 

?>

<div id="basic_content_wrapper">
<h1>Please login to access this page</h1><br>
<form action="?login"  id='login' method="post"> <input class='login_button' type="image" src="/images/steam-login.png"></form>

</div>
<style>
#basic_content_wrapper{
	text-align:center;
	font-size:20px;
	height:100vh;
}
#login {
    display: inline-block;
    text-align: center;
}
#login input{
	height:46px;
}

</style>

<?php footer();?>
</body>
</html>