<?php
header("Content-Type: text/html; charset=utf-8");
set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__.'/lib');
/*
return codes:
0 - success
-1 - connection to billing or gametool server refused
-2 - mysql connection refuse
-3 - not supported billing api version
-4 - not supported gametool api version
1 - not valid code
2 - this account or avatar has used this key earlie
3 - this is not code of this account
*/
include_once('main_config.inc');
include_once('functions.inc');
include_once('hessian/HessianClient.php');
include_once('billingApi.inc.php');
include_once('ServerVersion.inc.php');
include_once('ItemToSend.inc.php');
include_once('gametool.inc.php');

if(isset($_POST['basePartName']) && $_POST['basePartName']!='')
   $basePartName = $_POST['basePartName'];

if(isset($_POST['code']) && $_POST['code'] != '')
    $code = mysql_escape_string(htmlspecialchars(strip_tags(trim($_POST['code']))));

if(isset($_POST['accountName']) && $_POST['accountName'] != '')
    $accountName = mysql_escape_string(htmlspecialchars(strip_tags(trim($_POST['accountName']))));

if(isset($_POST['shardName']) && $_POST['shardName'] != '')
    $shardName = mysql_escape_string(htmlspecialchars(strip_tags(trim($_POST['shardName']))));

if(isset($_POST['avatarId']) && $_POST['avatarId'] != '')
    $avatarId   = intval($_POST["avatarId"]); 

if(isset($_POST['gametool_host']) && $_POST['gametool_host'] != '')
    $gametool_host = mysql_escape_string(htmlspecialchars(strip_tags(trim($_POST["gametool_host"])))); 

if(isset($_POST['gametool_port']) && $_POST['gametool_port'] != '')
    $gametool_port   = intval($_POST['gametool_port']); 

foreach($baseparts as $basepart)
    if( $basepart['basepart_name'] == "$basePartName"){
        $billing_api_host = $basepart['billing_api_host'];
        $billing_api_port = $basepart['billing_api_port'];
    }
$billing_url  = "http://$billing_api_host:$billing_api_port";
$gametool_url = "http://$gametool_host:$gametool_port/gametool";

try{
	$billing_serverVer = new ServerVersion($billing_url, 'BillingServerAPI');
	$billing_path = $billing_serverVer->getVersionPath($db::billing_version);
}
catch(Exception $ex){
    echo $ex->getMessage()."<p class='err'>Check that you have access to billing server</p>";
    return -1;
}

try{
	$gametool_serverVer = new ServerVersion($gametool_url, 'hessian/account.api', $db->gametool_options);
	$gametool_path = $gametool_serverVer->getVersionPath($db::gametool_version);
}
catch(Exception $ex){
	echo $ex->getMessage()."<p class='err'>Check that you have access to gametool server</p>";
	return -1;
}

if(check_path($billing_path) != 0){
	echo "<p class='err'>billing api version isn't supported</p>";
	return -3;
}

if(check_path($gametool_path) != 0){
	echo "<p class='err'>gametool api version isn't supported</p>";
	return -4;
}


$billing_proxy = new HessianClient($billing_url . $billing_path); 
registerBillingMethods($billing_url . $billing_path);

$gametool_proxy = new HessianClient($gametool_url . $gametool_path, $db->gametool_options);
registerGametoolMethods($gametool_url . $gametool_path);

db_connect($db->hostname,$db->port,$db->username,$db->password,$db->name);

if ( (get_ttl_rows(get_ttl($code)) < 1)||(get_ttl_value(get_ttl($code)) < 1)){
	echo "<p class='err'>This is not valid code or it has been expired</p>";
	return 1;
}

if((check_account_in_history($accountName, $code) > 0 ) || (check_avatar_in_history($avatarId, $code, $shardName) > 0)){
    echo "<p class='err'>You have used this code earlier</p>";
    return 2;
}


if((check_code_for_belonging($code) >  0) && (check_code_for_belonging_to_account($accountName, $code) < 1)){
  echo "<p class='err'>This is not your code</p>";
  return 3;  
}

$select_rules_for_type = sprintf("SELECT rules_id,value,stackcount FROM rules_for_types WHERE types_id = (SELECT types_id from `presents` where code='%s')", $code);
$results = mysql_query($select_rules_for_type) or die (mysql_error());
$array = array();

while ($result = mysql_fetch_array($results)){
    $spam = ereg_replace(' .*', '', microtime());
    $array["$result[0]!$spam"] = "$result[1]!$result[2]"; // it's a beautiful crutch
}

foreach($array as $key => $value){
    $stackcount = ereg_replace('.*!', '', $value);
    $value = ereg_replace('!.*', '', $value);
    $key = ereg_replace('!.*', '', $key);
    $select_rules = sprintf("SELECT name from rules where id='%d'", intval($key));
    $result = mysql_query($select_rules) or die (mysql_error());
    $result = mysql_fetch_array($result);
    switch($result[0]){
        case "add_premium_crystals":
            $billing_proxy->addMoneyWithCurrency($accountName, CurrencyValue::valueOf(ItemMallCurrency::HAPPY(), intval($value)),6,intval(get_transaction_id()));
            break;
        case "add_crystals":
            $billing_proxy->addMoneyWithCurrency($accountName, CurrencyValue::valueOf(ItemMallCurrency::MAIN(), intval($value)),6,intval(get_transaction_id()));
            break;
        case "add_item":
        $item = new ItemToSend();
            $item->shard = $shardName;
            $item->avatarId = intval($avatarId);
            $item->itemResourceId = intval($value); // int
            $item->runeResourceId = 0; // int
            $item->stackCount = intval($stackcount); // int
            $item->counter = 0; // int
            $item->senderName = "ActivationCodeSystem"; // String
            $item->subject = "Gift"; // String
            $item->body = sprintf("Hello, it's gift for you from code '%s'", $code); // String
            $items = array($item);
            $gametool_proxy->multisendItemToAvatarByMail($items);
            $item = null; $items = null;
            break;
    }
}
update_ttl($code);

if(isset($avatarId) && isset($accountName)){
    $insert_history = sprintf("insert into `history`(`accountName`,`avatarId`,`code`,`shard`,`date`) value('%s','%d','%s','%s',now())", $accountName, $avatarId, $code, $shardName); 
    commit_changes($insert_history);
}
else if(isset($avatarId)){
    $insert_history = sprintf("insert into `history`(`avatarId`,`code`,`shard`,`date`) value('%d','%s','%s',now())", $avatarId, $code, $shardName); 
    commit_changes($insert_history);
}
else if(isset($accountName)){
    $insert_history = sprintf("insert into `history`(`accountName`,`avatarId`,`code`,`shard`,`date`) value('%s','%s','%s',now())", $accountName, $code, $shardName); 
    commit_changes($insert_history);
}

echo "<p class='ok'>Gift has been sent</p>";
return 0;
mysql_close($link);
?>
