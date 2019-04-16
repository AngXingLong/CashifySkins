<?php 

set_time_limit(300);
require "cron-job-settings.php";
require $server_root."/shared/database.php";

// Notes
// Bot must be online and report every 10 mins
// Time must be greater than 5
// Must select all bots as incase of assetid has jumbled up users may  collect  their items

$conn->beginTransaction();  
$conn->exec("LOCK tables item_transaction write, item_transaction it write, inventory i read, inventory read");  

$wrong_asset_ids =  select("select id, item_id, botsid from item_transaction it where status in (1,10) and NOT EXISTS (SELECT null FROM inventory i WHERE i.assetid = it.assetid and i.item_id = it.item_id and i.botsid = it.botsid)");

if(empty($wrong_asset_ids)){
	die;
}
		
$surplus_asset_ids = select("select assetid, item_id, botsid from inventory i where NOT EXISTS (SELECT null FROM item_transaction it WHERE i.assetid = it.assetid and i.item_id = it.item_id and i.botsid = it.botsid and it.status in (1,3,8,10,11)); ");
//print_r($surplus_asset_ids);
if(empty($surplus_asset_ids)){
	die;
}

$updated_asset_id = array();

// Chances are higher that the assetid will be reassigned to the origial if assetid is assigned from its origin
foreach($wrong_asset_ids as $k => $v){ 

	$id = $v['id'];
	$item_id = $v['item_id'];
	$botsid = $v['botsid'];
	
	foreach($surplus_asset_ids as $k2 => $v2){
		if($item_id == $v2['item_id'] && $botsid == $v2['botsid']){
			$updated_asset_id[] = array("id"=>$id,"assetid"=>$v2['assetid'],"botsid"=>$botsid);
			unset($surplus_asset_ids[$k2]);
			break;
		}
		
		if($item_id == $v2['item_id']){
			$updated_asset_id[] = array("id"=>$id,"assetid"=>$v2['assetid'],"botsid"=>$v2['botsid']);
			unset($surplus_asset_ids[$k2]);
			break;
		}
	}
}

$stmt = $conn->prepare("update item_transaction set assetid = ?, botsid = ? where id = ? ");

foreach($updated_asset_id as $v){
	$stmt->execute(array($v['assetid'],$v['botsid'],$v['id']));	
}

$conn->commit();