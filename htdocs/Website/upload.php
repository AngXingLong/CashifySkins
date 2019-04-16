<?php
try{
	$filename = tempnam('image/user-photo/2/', 'MP');
	$new_image_name = basename(preg_replace('"\.tmp$"', '.jpg', $filename));
	unlink($filename);
	
	//print_r($_FILES);
	move_uploaded_file($_FILES["file"]["tmp_name"], "image/user-photo/2/" . $new_image_name. '.jpg');
	$json_out = "[" . json_encode(array("result"=>$new_image_name)) . "]";
	echo $json_out;
}
catch(Exception $e) {
	$json_out =  "[".json_encode(array("result"=>0))."]";
	echo $json_out;
}
?>