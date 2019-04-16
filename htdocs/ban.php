<?php

require $_SERVER['DOCUMENT_ROOT']."/shared/nav-menu.php";
$title = "Banned - Cashify Skins";

output_header(); 

?>

<div id="basic_content_wrapper">
<?php
if(!empty($_GET['reason'])){
	echo "<h1>You have been banned for ".filter_var($_GET['reason'], FILTER_SANITIZE_STRING)."</h1>";
}
else{
	echo "<h1>You have been banned</h1>";
}
?>

<h2>Please submit a appeal under steam forum appeal section if you wish to be unban.</h2>

</div>

<style>

#basic_content_wrapper{
	width:1140px;
	padding:30px;
	padding-top:0; 
}

h1{
	font-size:40px;
}

</style>

<?php footer();?>
</body>
</html>