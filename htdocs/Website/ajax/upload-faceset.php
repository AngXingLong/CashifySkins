<?php

require "../secret/database.php";
require "../secret/skybiometry-creditationals.php";
require "FCClientPHP.php";

$name = $_POST['name'];
$output['success'] = 0;
$file_name = "";
$image_url = "http://mp08.bit-mp.biz/image/attendance-photo/tmp";
$storage_directory = "/home/www/mp08.bit-mp.biz/image/faceset/tmp";
	
if(!$_FILES['photo']['error'])
{
		$valid_file = true;
		
		$file_name = uniqid().".png";
		$image_url = $image_url.$file_name;
		
		if($valid_file)
		{
			move_uploaded_file($_FILES['photo']['tmp_name'], $storage_directory.$file_name);
		}

}
else
{
	$output["msg"] = 'Your upload triggered the following error:  '.$_FILES['photo']['error'];
	$output["type"] = 3;
	echo json_encode($output);
	die;
}
	
	
$detect_list[] = $image_url;

$detect_result = $bio->faces_detect(implode(",",$detect_list));
if($detect_result['status'] == "success"){


	$tid_list = array();
	foreach($detect_result['photos'] as $v){
		$tid_list[] = $v["tags"][0]['tid'];
	}
	
	//save 
	$save_result = $bio->tags_save(implode(",",$tid_list),$name."@tracking");
	if($save_result['status'] == "success"){
		//train 
		$train_result = $bio->faces_train($name,"tracking");
		$output['success'] = 1;
	}

}

echo json_encode($output);