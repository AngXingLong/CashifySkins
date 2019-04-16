<?php
global $appid_code;
$appid_code = array(
	753=>"Steam", 
	730=>"Counter-Strike: Global Offensive", 
	570=>"Dota 2", 
	440=>"Team Fortress 2" 
);

function validate_appid($appid){
	global $appid_code;
	return array_key_exists($appid,$appid_code);
}
