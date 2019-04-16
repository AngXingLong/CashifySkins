<?php
require "../secret/database.php";
require "../secret/skybiometry-creditationals.php";
require "FCClientPHP.php";
	
	
$bio = new FCClientPHP($bio_api_key,$bio_api_secret);
$img_name = "";

//$tagged_image = "http://mp08.bit-mp.biz/image/profile-photo/orginal_test.jpg";
//$tagged_image = "http://mp08.bit-mp.biz/image/profile-photo/compare_1.jpg";
//$tagged_image = "http://mp08.bit-mp.biz/image/profile-photo/compare_2.jpg";
//$tagged_image = "http://mp08.bit-mp.biz/image/profile-photo/compare_3.jpg";

//
//http://img01.taobaocdn.com/bao/uploaded/i1/12366022014845446/T1wz9gXBdgXXXXXXXX_!!2-item_pic.png
//$name = "xinglong";
//print_r($bio->tags_add($test_image,50,40,90,80,"john","john@tracking"));

//echo "<pre>"; print_r($result); echo "</pre>";
//$confirmed = $result["photos"][0]["tags"][0]["confirmed"];

//print_r($bio->faces_train("PNG","test",null,"$file_name",$file_name));

//$image_person_identifier = ""; 
//$bio->faces_recognize($image_url,$image_person_identifier,"","$file_name",$file_name);

//first step

//try{
//image_url,center_of_face_percentage_x,center_of_face_percentage_y,width_detch_in_percentage,height_to_detech_in_percentage,optional_label, id_of_user@track
	//echo "<pre>"; print_r($bio->tags_add($tagged_image,50,50,85,85,$name,"$name@tracking")); echo "</pre>";
	//echo "<pre>"; print_r($bio->faces_train($name,"tracking")); echo "</pre>";
//}
//catch(Exception $e){
//	echo "error inserting";
//}

//

//echo "<pre>"; print_r($result); echo "</pre>";
//$compare_image = "http://mp08.bit-mp.biz/image/faceset/xuying/train-10.jpg";
$name = 50;


$detect_list[] = "http://mp08.bit-mp.biz/image/faceset/sidra/train-1.JPG";
$detect_list[] = "http://mp08.bit-mp.biz/image/faceset/sidra/train-2.JPG";
$detect_list[] = "http://mp08.bit-mp.biz/image/faceset/sidra/train-3.JPG";
$detect_list[] = "http://mp08.bit-mp.biz/image/faceset/sidra/train-4.JPG";
$detect_list[] = "http://mp08.bit-mp.biz/image/faceset/sidra/train-5.JPG";


$detect_result = $bio->faces_detect(implode(",",$detect_list));
echo "<pre>"; print_r($detect_result); echo "</pre>";

$tid_list = array();
foreach($detect_result['photos'] as $v){
	$tid_list[] = $v["tags"][0]['tid'];
}


//save 
$save_result = $bio->tags_save(implode(",",$tid_list),$name."@tracking");
echo "<pre>"; print_r($save_result); echo "</pre>";

//train 
$train_result = $bio->faces_train($name,"tracking");
echo "<pre>"; print_r($train_result); echo "</pre>";


//verfication
/*
$verfication_result = $bio->faces_recognize($compare_image,$name,"tracking",null,null,null,"aggressive");
echo "<pre>"; print_r($verfication_result); echo "</pre>";


$confidence = $verfication_result['photos'][0]["tags"][0]["uids"];
	
if(!empty($confidence)){
	$confidence = $confidence[0]["confidence"];
}else{
	$confidence  = 0;
}
echo $confidence;
*/