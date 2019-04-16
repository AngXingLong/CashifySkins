<?php
session_start();
require "admin-access.php";
require "../../shared/database.php";

$output = array("success"=>"0");

if(!empty($_POST['invoice_id'])){		

	$invoice_id = $_POST['invoice_id'];
	$data = select("select * from cash_transaction where id = ? or payment_id = ? ;", array($invoice_id, $invoice_id));
	$output['success'] = 1;
	$output['data'] = $data;
}

echo json_encode($output);