<?php
 		
	 $id = '';
	 if(isset($_GET['Name'])){
     $id = $_GET['Name'];
	 require_once('secret/database.php');
     $result=mysql_query("SELECT * FROM Organisation WHERE Name='$id'");
     $row = mysql_fetch_assoc($result);
    
	
	 echo '<div class="form-group mb-n">
				<label class="col-md-2 control-label">'.'Contact'.'</label>
				<div class="col-md-8">
					<input type="text" class="form-control1" placeholder="'.$row['Contact'].'" readonly=""></div>
				</div>';
     echo '<div class="form-group mb-n">
				<label class="col-md-2 control-label">'.'Address'.'</label>
				<div class="col-md-8">
					<input type="text" class="form-control1" placeholder="'.$row['Address'].'" readonly=""></div>
				</div>';
	 			
	 exit; }
	 elseif(isset($_GET['User_Name'])){
	 $id = '';
     $id = $_GET['User_Name'];
	 require_once('secret/database.php');
     $result=mysql_query("SELECT * FROM UserProfile WHERE User_Name='$id'");
     $row = mysql_fetch_assoc($result);
    
	 echo '<div class="form-group mb-n">
				<label class="col-md-2 control-label">'.'Name'.'</label>
				<div class="col-md-8">
					<input type="text" class="form-control1" placeholder="'.$row['Name'].'" readonly=""></div>
				</div>';
	 echo '<div class="form-group mb-n">
				<label class="col-md-2 control-label">'.'NRIC'.'</label>
				<div class="col-md-8">
					<input type="text" class="form-control1" placeholder="'.$row['NRIC'].'" readonly=""></div>
				</div>';
     echo '<div class="form-group mb-n">
				<label class="col-md-2 control-label">'.'Username'.'</label>
				<div class="col-md-8">
					<input type="text" class="form-control1" placeholder="'.$row['Username'].'" readonly=""></div>
				</div>';
	 echo '<div class="form-group mb-n">
				<label class="col-md-2 control-label">'.'Address'.'</label>
				<div class="col-md-8">
					<input type="text" class="form-control1" placeholder="'.$row['StaffID'].'" readonly=""></div>
				</div>';
	 echo '<div class="form-group mb-n">
				<label class="col-md-2 control-label">'.'Secret'.'</label>
				<div class="col-md-8">
					<input type="text" class="form-control1" placeholder="'.$row['Secret'].'" readonly=""></div>
				</div>';
	 echo '<div class="form-group mb-n">
				<label class="col-md-2 control-label">'.'Role'.'</label>
				<div class="col-md-8">
					<input type="text" class="form-control1" placeholder="'.$row['Role'].'" readonly=""></div>
				</div>';
     echo '<div class="form-group mb-n">
				<label class="col-md-2 control-label">'.'Inactive'.'</label>
				<div class="col-md-8">
					<input type="text" class="form-control1" placeholder="'.$row['Inactive'].'" readonly=""></div>
				</div>';
     echo '<div class="form-group mb-n">
				<label class="col-md-2 control-label">'.'Photo'.'</label>
				<div class="col-md-8">
					<input type="text" class="form-control1" placeholder="'.$row['Photo'].'" readonly=""></div>
				</div>';
     echo '<div class="form-group mb-n">
				<label class="col-md-2 control-label">'.'Organisation ID'.'</label>
				<div class="col-md-8">
					<input type="text" class="form-control1" placeholder="'.$row['organisation_id'].'" readonly=""></div>
				</div>';				
	 			
	 exit;}
	elseif(isset($_GET['Location_name'])){
	 $id = '';
     $id = $_GET['Location_name'];
	 require_once('secret/database.php');
     $result=mysql_query("SELECT * FROM Location WHERE Location_name='$id'");
     $row = mysql_fetch_assoc($result);
    
	 echo '<div class="form-group mb-n">
				<label class="col-md-2 control-label">'.'Street Name'.'</label>
				<div class="col-md-8">
					<input type="text" class="form-control1" placeholder="'.$row['Street_Name'].'" readonly=""></div>
				</div>';
     echo '<div class="form-group mb-n">
				<label class="col-md-2 control-label">'.'Postal Code'.'</label>
				<div class="col-md-8">
					<input type="text" class="form-control1" placeholder="'.$row['Postal_Code'].'" readonly=""></div>
				</div>';
	 echo '<div class="form-group mb-n">
				<label class="col-md-2 control-label">'.'Longitude'.'</label>
				<div class="col-md-8">
					<input type="text" class="form-control1" placeholder="'.$row['Longitude'].'" readonly=""></div>
				</div>';
     echo '<div class="form-group mb-n">
				<label class="col-md-2 control-label">'.'Latitude'.'</label>
				<div class="col-md-8">
					<input type="text" class="form-control1" placeholder="'.$row['Latitude'].'" readonly=""></div>
				</div>';
     echo '<div class="form-group mb-n">
				<label class="col-md-2 control-label">'.'Image'.'</label>
				<div class="col-md-8">
					<input type="text" class="form-control1" placeholder="'.$row['image'].'" readonly=""></div>
				</div>';				
	 			
	exit;}
	elseif(isset($_GET['Event_Name'])){	
	 $id = '';
     $id = $_GET['Event_Name'];
	 require_once('secret/database.php');
     $result=mysql_query("SELECT * FROM Event WHERE Event_Name='$id'");
     $row = mysql_fetch_assoc($result);
    
	 echo '<div class="form-group mb-n">
				<label class="col-md-2 control-label">'.'Location ID'.'</label>
				<div class="col-md-8">
					<input type="text" class="form-control1" placeholder="'.$row['Location_ID'].'" readonly=""></div>
				</div>';
     echo '<div class="form-group mb-n">
				<label class="col-md-2 control-label">'.'Host ID'.'</label>
				<div class="col-md-8">
					<input type="text" class="form-control1" placeholder="'.$row['Host_ID'].'" readonly=""></div>
				</div>';
	 echo '<div class="form-group mb-n">
				<label class="col-md-2 control-label">'.'Time Start'.'</label>
				<div class="col-md-8">
					<input type="text" class="form-control1" placeholder="'.$row['Time_Start'].'" readonly=""></div>
				</div>';
	 echo '<div class="form-group mb-n">
				<label class="col-md-2 control-label">'.'Time End'.'</label>
				<div class="col-md-8">
					<input type="text" class="form-control1" placeholder="'.$row['Time_End'].'" readonly=""></div>
				</div>';
	 echo '<div class="form-group mb-n">
				<label class="col-md-2 control-label">'.'Strict'.'</label>
				<div class="col-md-8">
					<input type="text" class="form-control1" placeholder="'.$row['strict'].'" readonly=""></div>
				</div>';
	 			
exit;
	}
?>
