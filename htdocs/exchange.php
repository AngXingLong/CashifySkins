<?php

require $_SERVER['DOCUMENT_ROOT']."/shared/nav-menu.php";
$title = "Banned - Cashify Skins";

output_header(); 

?>




<div id='basic_content_wrapper'>
<select id='game_type_selection'>
<option>All Games</option>
<option>All Games</option>
<option>All Games</option>
<option>All Games</option>
</select>
<div class='exchange_selection'><div class='exchange_image'>Test</div><div class='exchange_description'></div></div>
</div>
<style>

#game_type_selection{
	margin:20px auto;
	width:600px;
	display:block;
	height:30px;
	font-size:16px;
	text-align:center;
}
.exchange_selection{
	width:800px;
	margin:0 auto;
	background:#333;
	height:120px;
}
.exchange_image{
}
.exchange_description{
	width:800px;
	margin:0 auto;
	background:#333;
	height:120px;
}
</style>

<?php footer();?>
</body>
</html>