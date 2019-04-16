<?php

function check_fields($require_fields,$type){
	if($type = "post"){
		foreach ($require_fields as $field) {
			if (!isset($_POST[$field])) {
				return false;
			}
		}
	}else{
		foreach ($require_fields as $field) {
			if (!isset($_GET[$field])) {
				return false;
			}
		}
	}
	return true;
}

function validate_int($int){
	return ctype_digit(strval($int));
}

function validate_currency($int){
	return preg_match('/^[0-9]+(\.[0-9]{1,2})?$/', $int);
}


