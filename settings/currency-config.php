<?php

$listing_fee = 5;
$min_listing_price = 0.01;
$max_listing_price = 10000;
$cashout_fee = 5;

function calculate_listing_price($price,$switch){

global $listing_fee;
		
		if($switch){
			$price =  ceil(($price*100)/(100-$listing_fee)*100)/100;
		}else{
			$price = floor(($price/100)*(100-$listing_fee)*100)/100;
		}
		return $price;
}
