<?php
$cash_status_code = [
			0=>"Pending",
			1=>"Processing",
			2=>"Complete",
			3=>"Denied",
			4=>"Canceled",
			5=>"Transaction Error"
];

$cash_type_code = [
			0=>"Cashout",
			1=>"Funds Purchase",
			2=>"Purchase Refunds"
];

$trade_type_code = [
			0 => "Item Deposit",
			1 => "Item Returns",
			2 => "Item Collection",
			3 => "Specific Purchase"
];

$trade_status_code = [
			0=>"In Queue",
			1=>"Sending Offer",
			2=>"Trade Offer Sent",
			3=>"Complete",
			4=>"Issue With User",
			5=>"Issue With Bot",
			6=>"Escrow Cooldown",
];


// do not touch 1,3,8,10,11 if item is to collected

$sale_status_code = [
			0=>"Awaiting Deposit", 
			1=>"On Sale",
			2=>"Sold",
			3=>"Return Inititated",
			4=>"Returned",
			5=>"Expired",
			6=>"Deposit Failed",
			7=>"Escrow Cooldown",
			8=>"Awaiting Delivery To Buyer",
			//anything above 8 is treated as sold by sales
];

$purchase_status_code = [ // Actual item status to buyer
			"8"=>"Collection Initiated",
			10=>"Awaiting Collection",
			11=>"Collection Initiated",
			12=>"Collected",
			13=>"Refunded",
			14=>"Expired",
			15=>"Purchase Failed",
];
$purchase_set_code = [
			0=>"System",
			1=>"User"
];


