<?php
  $id = '';
  require_once('secret/database.php');
  if(isset($_GET['client_Name'])){
     
     $id = $_GET['client_Name'];
     $result=mysql_query("SELECT * FROM Organisation WHERE Name='$id'");
     $row = mysql_fetch_assoc($result);
     echo '<div class="form-group">';             
     echo '<label for="txtarea1" class="col-sm-2 control-label">'.'Contact'.'</label>';
     echo '<div class="col-sm-8"><textarea name="contact" id="contact" cols="50" rows="4" class="form-control1" required>'.$row['Contact'].'</textarea></div>';
     echo '</div>';
     echo '<div class="form-group">';             
     echo '<label for="txtarea1" class="col-sm-2 control-label">'.'Address'.'</label>';
     echo '<div class="col-sm-8"><textarea name="address" id="address" cols="50" rows="4" class="form-control1" required>'.$row['Address'].'</textarea></div>';
     echo '</div>';                
                
   
     exit;
   } elseif (isset($_GET['Event_Name'])){
     $id = $_GET['Event_Name'];
     $result=mysql_query("SELECT * FROM Event WHERE Event_Name='$id'");
     $row = mysql_fetch_assoc($result);
     
     echo '<div class="form-group">';             
     echo '<label for="txtarea1" class="col-sm-2 control-label">'.'Location ID'.'</label>';
     echo '<div class="col-sm-8"><textarea name="location_id" id="location_id" cols="50" rows="4" class="form-control1" required>'.$row['Location_ID'].'</textarea></div>';
     echo '</div>';
     echo '<div class="form-group">';             
     echo '<label for="txtarea1" class="col-sm-2 control-label">'.'Host Name'.'</label>';
     echo '<div class="col-sm-8"><textarea name="host_id" id="host_id" cols="50" rows="4" class="form-control1" required>'.$row['Host_ID'].'</textarea></div>';
     echo '</div>';
     echo '<div class="form-group">';             
     echo '<label for="txtarea1" class="col-sm-2 control-label">'.'Time Start'.'</label>';
     echo '<div class="col-sm-8"><textarea name="time_start" id="time_start" cols="50" rows="4" class="form-control1" required>'.$row['Time_Start'].'</textarea></div>';
     echo '</div>';
     echo '<div class="form-group">';             
     echo '<label for="txtarea1" class="col-sm-2 control-label">'.'Time End'.'</label>';
     echo '<div class="col-sm-8"><textarea name="time_end" id="time_end" cols="50" rows="4" class="form-control1" required>'.$row['Time_End'].'</textarea></div>';
     echo '</div>';
     exit;
   } elseif (isset($_GET['location_name'])){
     $id = $_GET['location_name'];
     $result=mysql_query("SELECT * FROM Location WHERE name='$id'");
     $row = mysql_fetch_assoc($result);
     
     echo '<div class="form-group">';             
     echo '<label for="txtarea1" class="col-sm-2 control-label">'.'Street Name'.'</label>';
     echo '<div class="col-sm-8"><textarea name="street_name" id="street_name" cols="50" rows="4" class="form-control1" required>'.$row['Street_Name'].'</textarea></div>';
     echo '</div>';
     echo '<div class="form-group">';             
     echo '<label for="txtarea1" class="col-sm-2 control-label">'.'Postal Code'.'</label>';
     echo '<div class="col-sm-8"><textarea name="postal_code" id="postal_code" cols="50" rows="4" class="form-control1" required>'.$row['Postal_Code'].'</textarea></div>';
     echo '</div>';
     echo '<div class="form-group">';             
     echo '<label for="txtarea1" class="col-sm-2 control-label">'.'Longitude'.'</label>';
     echo '<div class="col-sm-8"><textarea name="longitude" id="longitude" cols="50" rows="4" class="form-control1" required>'.$row['Longitude'].'</textarea></div>';
     echo '</div>';
     echo '<div class="form-group">';             
     echo '<label for="txtarea1" class="col-sm-2 control-label">'.'Latitude'.'</label>';
     echo '<div class="col-sm-8"><textarea name="latitude" id="latitude" cols="50" rows="4" class="form-control1" required>'.$row['Latitude'].'</textarea></div>';
     echo '</div>';
     echo '<div class="form-group">';             
     echo '<label for="txtarea1" class="col-sm-2 control-label">'.'Image'.'</label>';
     echo "<div class='col-sm-8'><img class='img-responsive' src='http://mp08.bit-mp.biz/image/location-photo/".$row['image']."' width='20%' height='20%'><input type='file' name='photo'></div>";
     echo '</div>';
     exit;
   } elseif (isset($_GET['user_name'])){
     $id = $_GET['user_name'];
     $result=mysql_query("SELECT * FROM UserProfile WHERE Name='$id'");
     $row = mysql_fetch_assoc($result);
     
     echo '<div class="form-group">';             
     echo '<label for="txtarea1" class="col-sm-2 control-label">'.'NRIC'.'</label>';
     echo '<div class="col-sm-8"><textarea name="nric" id="nric" cols="50" rows="4" class="form-control1" required>'.$row['NRIC'].'</textarea></div>';
     echo '</div>';
     echo '<div class="form-group">';             
     echo '<label for="txtarea1" class="col-sm-2 control-label">'.'User Name'.'</label>';
     echo '<div class="col-sm-8"><textarea name="username" id="username" cols="50" rows="4" class="form-control1" required>'.$row['Username'].'</textarea></div>';
     echo '</div>';
     echo '<div class="form-group">';             
     echo '<label for="txtarea1" class="col-sm-2 control-label">'.'Staff ID'.'</label>';
     echo '<div class="col-sm-8"><textarea name="staffid" id="staffid" cols="50" rows="4" class="form-control1" required>'.$row['StaffID'].'</textarea></div>';
     echo '</div>';
     echo '<div class="form-group">';             
     echo '<label for="txtarea1" class="col-sm-2 control-label">'.'Password'.'</label>';
     echo '<div class="col-sm-8"><textarea name="password" id="password" cols="50" rows="4" class="form-control1" required>'.$row['Password'].'</textarea></div>';
     echo '</div>';
     echo '<div class="form-group">';             
     echo '<label for="txtarea1" class="col-sm-2 control-label">'.'Role'.'</label>';
     echo '<div class="col-sm-8"><textarea name="role" id="role" cols="50" rows="4" class="form-control1" required>'.$row['Role'].'</textarea></div>';
     echo '</div>';
     echo '<div class="form-group">';             
     echo '<label for="txtarea1" class="col-sm-2 control-label">'.'Inactive'.'</label>';
     echo '<div class="col-sm-8"><textarea name="inactive" id="inactive" cols="50" rows="4" class="form-control1" required>'.$row['Inactive'].'</textarea></div>';
     echo '</div>';
     echo '<div class="form-group">';             
     echo '<label for="txtarea1" class="col-sm-2 control-label">'.'Photo'.'</label>';
     echo "<div class='col-sm-8'><img class='img-responsive' src='http://mp08.bit-mp.biz/image/profile-photo/".$row['Photo']."' width='20%' height='20%'><input type='file' name='photo'></div>";
     echo '</div>';
     echo '<div class="form-group">';             
     echo '<label for="txtarea1" class="col-sm-2 control-label">'.'Organisation ID'.'</label>';
     echo '<div class="col-sm-8"><textarea name="organisation_id" id="organisation_id" cols="50" rows="4" class="form-control1" required>'.$row['organisation_id'].'</textarea></div>';
     echo '</div>';
     exit;
   }
   

?>
