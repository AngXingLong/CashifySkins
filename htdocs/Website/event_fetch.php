<?php
  $location = '';
  require_once('secret/database.php');
     
     $name = $_GET['location_name'];
     $result=mysql_query("SELECT ID FROM Location WHERE name='$name'");
	 print mysql_error();
     $row = mysql_fetch_assoc($result);
     echo '<div class="form-group">';
     echo '<label for="txtarea1" class="col-sm-2 control-label">'.'Location ID'.'</label>';
     echo '<div class="col-sm-8"><textarea name="location_id" id="location_id" cols="50" rows="4" class="form-control1" readonly>'.$row['ID'].'</textarea></div>';
     echo '</div>';
	 exit;
	 
?>