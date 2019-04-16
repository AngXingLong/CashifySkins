<?php
set_time_limit(300);
require "cron-job-settings.php";
require $file_root."/composer/PPBootStrap.php";
require $file_root."/settings/currency-config.php";
require $server_root."/shared/database.php";

use PayPal\CoreComponentTypes\BasicAmountType;
use PayPal\PayPalAPI\MassPayReq;
use PayPal\PayPalAPI\MassPayRequestItemType;
use PayPal\PayPalAPI\MassPayRequestType;
use PayPal\Service\PayPalAPIInterfaceServiceService;
use PayPal\Auth\PPSignatureCredential;
use PayPal\Auth\PPTokenAuthorization;

$conn->beginTransaction();

$conn->exec("LOCK TABLES user write, user u write, ban read, ban b read, trade_transaction read, trade_transaction tt read, cash_transaction write, cash_transaction ct write;");

$stmt = $conn->prepare("update cash_transaction ct set ct.status = 3, ct.status_comment = 'Insufficient Credits' where exists (select null from user u  where ct.amount > u.credit and ct.steamid = u.steamid) and ct.type = 0 and ct.status = 0;");
$stmt->execute();

//No payouts to banned users
$stmt = $conn->prepare("update cash_transaction ct set ct.status = 3, ct.status_comment = 'User Banned' where exists (select null from ban b  where ct.steamid = b.steamid) and ct.type = 0 and ct.status = 0;");
$stmt->execute();

$stmt = $conn->prepare("update cash_transaction ct set status = 3, status_comment = 'User in trade' where exists (SELECT null from trade_transaction tt where tt.type = 3 and tt.status in (0,1,2) and tt.usersid = ct.steamid) and ct.type = 0 and ct.status = 0;");
$stmt->execute();

$conn->commit();

$currency = "USD";
$paypalService = new PayPalAPIInterfaceServiceService(Configuration::getAcctAndConfig());

while(true){
	$conn->beginTransaction();
	
	$data = select("select ct.id, ct.steamid, ct.amount, ct.node from cash_transaction ct where ct.status = 0 and ct.type = 0 limit 240");
	
	if(empty($data)){break;}
	
	$massPayRequest = new MassPayRequestType();
	$massPayRequest->MassPayItem = array();
	
	foreach($data as $k => $value){
		
		$user_receive = round(($value['amount']/100) * (100-$cashout_fee), 2, PHP_ROUND_HALF_UP);

		$masspayItem = new MassPayRequestItemType();
		$masspayItem->UniqueId = $value['id'];
		$masspayItem->Amount = new BasicAmountType($currency, $user_receive);
		$masspayItem->ReceiverEmail = $value['node'];
		$massPayRequest->MassPayItem[] = $masspayItem;
		
	}

	$massPayReq = new MassPayReq();
	$massPayReq->MassPayRequest = $massPayRequest;
	
	$success = false; 
        $error_msg = "";
	try{
		$massPayResponse = $paypalService->MassPay($massPayReq);

		if($massPayResponse->Ack == "Success"){
			$success = true;
		}
		else{
			$error_msg = "Error Code:".$massPayResponse->Errors[0]->ErrorCode;
		}
	}
	catch(exception $e){
		 $error_msg = $e;
	}

	if($success){
		$stmt = $conn->prepare("update cash_transaction ct left join user u on u.steamid = ct.steamid set ct.status = 2, u.credit = 
		(u.credit - ct.amount), ct.time = now() where ct.id = ? ;");
				
		foreach($data as $value){
			$stmt->execute(array($value['id']));
		}
	}
	else{
		$stmt = $conn->prepare("update cash_transaction set status = 5, time = now(), staff_comment = ? where id = ? ");	
		foreach($data as $value){
			$stmt->execute(array($error_msg,$value['id']));
		}
	}
	
	$conn->commit();

}

