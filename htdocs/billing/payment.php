<?php

	session_start();
	
	if(empty($_SESSION['steamid']) || empty($_SESSION['csrf_token']) || empty($_POST['csrf']) ||$_SESSION['csrf_token'] != $_POST['csrf']){
		die;
	}
	
	if(empty($_POST['type'])){die;}
	
	require $_SERVER['DOCUMENT_ROOT']."/../composer/PPBootStrap.php";
	require $_SERVER['DOCUMENT_ROOT']."/shared/database.php";
	
	use PayPal\CoreComponentTypes\BasicAmountType;
	use PayPal\EBLBaseComponents\AddressType;
	use PayPal\EBLBaseComponents\BillingAgreementDetailsType;
	use PayPal\EBLBaseComponents\PaymentDetailsItemType;
	use PayPal\EBLBaseComponents\PaymentDetailsType;
	use PayPal\EBLBaseComponents\SetExpressCheckoutRequestDetailsType;
	use PayPal\PayPalAPI\SetExpressCheckoutReq;
	use PayPal\PayPalAPI\SetExpressCheckoutRequestType;
	use PayPal\Service\PayPalAPIInterfaceServiceService;
	
	$amount = 0;
	
	switch ($_POST['type']) {
		case 1: 
		$amount = 5;
		break;  
		case 2:
		$amount = 10;
		break; 
		case 3:
		$amount = 20;
		break; 
		case 4:
		$amount = 40;
		break; 
		case 5:
		$amount = 50;
		break; 
		case 6:
		$amount = 100;
		break; 
		case 7:
		$amount = 250;
		break; 
		case 8:
		$amount = 500;
		break; 
		default:
       	die();
	}

	$used_quota = select("select sum(amount) as amount from cash_transaction where steamid = ? and date_format(time, '%Y-%m') = date_format(now(), '%Y-%m') and type = 1 and status in (1,2);",array($_SESSION['steamid']));
	
	$used_quota = !empty($used_quota[0]['amount']) ? $used_quota[0]['amount'] : 0;
	
	if($used_quota + $amount > 700){
		header("Location: https://$domain_name/billing/payment-status.php?status=3");
		die;
	}
	//$invoice_id = bin2hex(openssl_random_pseudo_bytes(10));

	$returnUrl = "https://$domain_name/billing/payment-redirect.php?status=success";
	$cancelUrl = "https://$domain_name/billing/payment-status.php?status=1";
	
	$currency = "USD";
	$paymentDetails = new PaymentDetailsType();
		
	$itemDetails = new PaymentDetailsItemType();
	$itemDetails->Name = "$".number_format($amount,2)." CashifySkins Wallet Funds";
	$itemDetails->Amount = $amount;
	$itemDetails->Quantity = 1;
	$itemDetails->ItemCategory = "Digital";
		
	$paymentDetails->PaymentDetailsItem[0] = $itemDetails;	
	$paymentDetails->ItemTotal = new BasicAmountType($currency, $amount);
	$paymentDetails->OrderTotal = new BasicAmountType($currency, $amount);
	$paymentDetails->PaymentAction = "Sale";
	$paymentDetails->AllowedPaymentMethod = "InstantFundingSource";
	//InstantFundingSource
	//$paymentDetails->InvoiceID = $invoice_id;

	$setECReqDetails = new SetExpressCheckoutRequestDetailsType();
	$setECReqDetails->PaymentDetails[0] = $paymentDetails;
	$setECReqDetails->CancelURL = $cancelUrl;
	$setECReqDetails->ReturnURL = $returnUrl;
	$setECReqDetails->NoShipping = 1;
	$setECReqDetails->AllowNote = 0;
	
	$billingAgreementDetails = new BillingAgreementDetailsType("MerchantInitiatedBilling");
	
	$setECReqType = new SetExpressCheckoutRequestType();
	$setECReqType->SetExpressCheckoutRequestDetails = $setECReqDetails;
	$setECReq = new SetExpressCheckoutReq();
	$setECReq->SetExpressCheckoutRequest = $setECReqType;
	
	$paypalService = new PayPalAPIInterfaceServiceService(Configuration::getAcctAndConfig());
	try {
		$setECResponse = $paypalService->SetExpressCheckout($setECReq);
	} catch (Exception $ex) {
		
	}
	
	if($setECResponse->Ack =='Success') {		
			$token = $setECResponse->Token;
			$_SESSION['payment'] = json_encode(array('token'=>$token,'amount'=>$amount));
			
			if($environment_settings == "sandbox"){
				$redirect = 'https://www.sandbox.paypal.com/webscr?cmd=_express-checkout&token=' . $token;
			}
			else{
				$redirect = 'https://www.paypal.com/webscr?cmd=_express-checkout&token=' . $token;
			}
			
			header("Location: ".$redirect);
			die;
	}
	
	header("Location: https://$domain_name/billing/payment-status.php?status=1");
