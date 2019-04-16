<?php 
	session_start();
	require "admin-access.php";
	require "../../shared/database.php";
	require "../../shared/transaction-code.php";

	//$_POST['steamid'] = "76561198075337308";
	//$_POST['type'] = 3;
	if(empty($_POST['steamid']) || empty($_POST['type'])){die;}
	
	$usersid = $_POST['steamid'];
	
	if($_POST['type'] == 3){
		
		$missing_items = select("select pl.display_name, pl.appid, count(*) as quantity from item_transaction it inner join pricelist pl on it.item_id = pl.id where it.seller_sid = ? and it.status = 1 and NOT EXISTS (SELECT null FROM inventory i WHERE i.assetid = it.assetid and i.item_id = it.item_id and i.botsid = it.botsid) group by it.item_id",array($usersid));
	
	}
	else
	{
		$missing_items = select("select  pl.display_name, pl.appid, count(*) as quantity from item_transaction it inner join pricelist pl on it.item_id = pl.id where it.seller_sid = ? and it.status = 10 and NOT EXISTS (SELECT null FROM inventory i WHERE i.assetid = it.assetid and i.item_id = it.item_id and i.botsid = it.botsid) group by it.item_id;",array($usersid));
	
	}
	
	$output['missing'] = $missing_items;
	
	end:
	echo json_encode($output);
	