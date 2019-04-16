<?php
require $_SERVER['DOCUMENT_ROOT']."/../composer/PPBootStrap.php";
require $_SERVER['DOCUMENT_ROOT']."/shared/database.php";

session_start();

if(empty($_SESSION['steamid'])){
	$status = 0; goto end;	
}

use PayPal\CoreComponentTypes\BasicAmountType;
use PayPal\EBLBaseComponents\AddressType;
use PayPal\EBLBaseComponents\BillingAgreementDetailsType;
use PayPal\EBLBaseComponents\PaymentDetailsItemType;
use PayPal\EBLBaseComponents\DoExpressCheckoutPaymentRequestDetailsType;
use PayPal\EBLBaseComponents\PaymentDetailsType;
use PayPal\PayPalAPI\DoExpressCheckoutPaymentReq;
use PayPal\PayPalAPI\DoExpressCheckoutPaymentRequestType;
use PayPal\PayPalAPI\GetExpressCheckoutDetailsReq;
use PayPal\PayPalAPI\GetExpressCheckoutDetailsRequestType;
use PayPal\Service\PayPalAPIInterfaceServiceService;

$status = 1;

if(empty($_GET['token']) || empty($_GET['PayerID'])){
	$status = 0; goto end;
}

$token = $_GET['token'];
$payerId = $_GET['PayerID'];

$getExpressCheckoutDetailsRequest = new GetExpressCheckoutDetailsRequestType($token);
$getExpressCheckoutReq = new GetExpressCheckoutDetailsReq();
$getExpressCheckoutReq->GetExpressCheckoutDetailsRequest = $getExpressCheckoutDetailsRequest;

$paypalService = new PayPalAPIInterfaceServiceService(Configuration::getAcctAndConfig());

try {
	$getECResponse = $paypalService->GetExpressCheckoutDetails($getExpressCheckoutReq);
} catch (Exception $ex) {
	$status = 0; goto end;
}

if($getECResponse->Ack != "Success"){$status = 0;goto end;}

$payer_email = $getECResponse->GetExpressCheckoutDetailsResponseDetails->PayerInfo->Payer; //Email
$payer_id = $getECResponse->GetExpressCheckoutDetailsResponseDetails->PayerInfo->PayerID; //ID
$payer_verfied =$getECResponse->GetExpressCheckoutDetailsResponseDetails->PayerInfo->PayerStatus; //paypal verfied
//$getECResponse->GetExpressCheckoutDetailsResponseDetails->InvoiceID;
//$getECResponse->GetExpressCheckoutDetailsResponseDetails->PayerInfo->ContactPhone; 
$invoice_amount = $getECResponse->GetExpressCheckoutDetailsResponseDetails->PaymentDetails[0]->OrderTotal->value;

$verification = json_decode($_SESSION['payment'],true);
$amount = $verification['amount'];

if($verification['token'] != $token || $verification['amount'] != $invoice_amount){$status = 0;goto end;}
//if($payer_verfied != "verified"){$status = 2;goto end;} //check if paypal veifed

$user_check = select("select 
(select trust_score from user where steamid = ?) as trust_score,
(select sum(amount) from cash_transaction where steamid = ? and date_format(time, '%Y-%m') = date_format(now(), '%Y-%m') and type = 1 and status in (1,2)) as amount, 
(select count(*) from cash_transaction where steamid <> ? and node_id = ? and type = 1 and DATE_SUB(NOW(), INTERVAL 1 MONTH) >= time) as used, 
(select count(*) from cash_transaction ct inner join ban b on ct.steamid = b.steamid where ct.node_id = ? limit 1) as ban ",
array($_SESSION['steamid'], $_SESSION['steamid'], $_SESSION['steamid'],$payer_id,$payer_id));

$user_check = $user_check[0];
$used_quota = !empty($user_check['amount']) ? $user_check['amount'] : 0;
$total_amount = $user_check['amount'] + $amount;

if($user_check['ban'] || $user_check['used']){ // baned paypal or one payment method used for mutiple accounts
	$status = 0;goto end;
}
if($total_amount > 700){ //cannot accept too much, what happens if this person chargedback
	$status = 3; goto end;
}
if($total_amount > 50){
	if($user_check['trust_score'] < 2){// || $payer_verfied != "verified"
		$status = 2;goto end;
	}
}

$stmt = $conn->prepare("insert into cash_transaction (steamid, node, node_id, amount, status, type, time) values (?,?,?,?,1,1,now()) ;");
$stmt->execute(array($_SESSION['steamid'], $payer_email, $payer_id, $amount));
$invoice_id = $conn->lastInsertId();

$currency = 'USD';
$paymentDetails= new PaymentDetailsType();
$itemDetails = new PaymentDetailsItemType();
$itemDetails->Name = "$".number_format($amount,2)." CashifySkins Wallet Funds";
$itemDetails->Amount = $amount;
$itemDetails->Quantity = 1;
$itemDetails->ItemCategory = "Digital";
$paymentDetails->PaymentDetailsItem[0] = $itemDetails;	

$paymentDetails->ItemTotal = new BasicAmountType($currency, $amount);
$paymentDetails->OrderTotal = new BasicAmountType($currency, $amount);
$paymentDetails->PaymentAction = "Sale";
$paymentDetails->AllowedPaymentMethod = "InstantPaymentOnly";
$paymentDetails->InvoiceID = $invoice_id;

$DoECRequestDetails = new DoExpressCheckoutPaymentRequestDetailsType();
$DoECRequestDetails->PayerID = $payerId;
$DoECRequestDetails->Token = $token;
$DoECRequestDetails->PaymentAction = "Sale";
$DoECRequestDetails->PaymentDetails[0] = $paymentDetails;

$DoECRequest = new DoExpressCheckoutPaymentRequestType();
$DoECRequest->DoExpressCheckoutPaymentRequestDetails = $DoECRequestDetails;

$DoECReq = new DoExpressCheckoutPaymentReq();
$DoECReq->DoExpressCheckoutPaymentRequest = $DoECRequest;


try {
	$DoECResponse = $paypalService->DoExpressCheckoutPayment($DoECReq);
} catch (Exception $e) {
	$stmt = $conn->prepare("update cash_transaction status = 5, staff_comment = ? where id = ? ;");
    $stmt->execute(array("Paypal Error",$invoice_id));	
	$status = 0;
	goto end;
}


if(isset($DoECResponse->DoExpressCheckoutPaymentResponseDetails->PaymentInfo)) 

//$time = $DoECResponse->DoExpressCheckoutPaymentResponseDetails->Timestamp;
$paypal_payment_id = $DoECResponse->DoExpressCheckoutPaymentResponseDetails->PaymentInfo[0]->TransactionID;

$conn->beginTransaction();
$stmt = $conn->prepare("update cash_transaction set status = 2, payment_id = ?, time = now() where id = ? ;");
$stmt->execute(array($paypal_payment_id, $invoice_id));	

$stmt = $conn->prepare("update user set credit = credit + ? where steamid = ? ;");
$stmt->execute(array($amount,$_SESSION['steamid']));	
$conn->commit();

$referrer_details= select("select referrer_sid, amount from referals where paid = 0 and referent_sid = ?;",array($_SESSION['steamid']));

if(!empty($referrer_details)){
	$referrer_sid  = $referrer_details[0]['referrer_sid'];
	$referrer_reward = $referrer_details[0]['amount'];
	
	$stmt = $conn->prepare("update referals set paid = 1 where referent_sid = ?;");
	$stmt->execute(array($_SESSION['steamid']));
	
	$stmt = $conn->prepare("update user set credit = credit + ? where steamid = ?;");
	$stmt->execute(array($referrer_reward,$referrer_sid));
}

end:
header("Location: https://$domain_name/billing/payment-status.php?status=".$status);
die;
