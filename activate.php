<?php

header("Content-Type: text/html; charset=utf-8");
set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__.'/lib');
include_once('main_config.inc');
include_once('functions.inc');
include_once('hessian/HessianClient.php');
include_once('billingApi.inc.php');
include_once('ServerVersion.inc.php');
include_once('ItemToSend.inc.php');
include_once('gametool.inc.php');
include_once('adminTool.inc.php');

if(isset($_POST['basePartName']) && $_POST['basePartName']!='')
   $basePartName = $_POST['basePartName'];

if(isset($_POST['master_server_host']) && $_POST['master_server_host']!='')
    $master_server_host = $_POST['master_server_host'];

if(isset($_POST['master_server_port']) && $_POST['master_server_port']!='')
    $master_server_port = intval($_POST['master_server_port']);

if(isset($_POST['account']) && $_POST['account']!='')
    $account = $_POST['account'];

if(isset($_POST['shardId']) && $_POST['shardId']!='')
    $shardId = intval($_POST['shardId']);


$ms_api = connect_to_ms_api($master_server_host, $master_server_port);

$shards = $ms_api->getShards();
foreach($shards as $shard){
    if( $shard['id'] == $shardId ){
        $shardName = $shard['name'];
    }
}
$sconfig = $ms_api->getShardConfig($shardId);
$xml = new SimpleXMLElement($sconfig['config']);
$gametool_host = $xml->gametoolEAR->web['host'];
$gametool_port = $xml->gametoolEAR->web['port'];
$gametool_url  = "http://$gametool_host:$gametool_port/gametool";

try{
    $gametool_serverVer = new ServerVersion($gametool_url, 'hessian/account.api', $db->gametool_options);
    $gametool_path = $gametool_serverVer->getVersionPath($db::gametool_version);
}
catch(Exception $ex){
    echo $ex->getMessage()."\nCheck that you have access to billing and gametool servers";
    return -1;
}

if(check_path($gametool_path) != 0){
    echo "gametool api version isn't supported";
    return -1;
}

$gametool_proxy = new HessianClient($gametool_url . $gametool_path, $db->gametool_options);
registerGametoolMethods($gametool_url . $gametool_path);

db_connect($db->hostname,$db->port,$db->username,$db->password,$db->name);
$shards = $gametool_proxy->getAllShards();
$avatars = $gametool_proxy->getAvatarsIgnoringCase($account);
?>
<html>
<head>
<title>Activation Code</title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8"> 
<link rel="stylesheet" type="text/css" href="css/style.css">
<script src="js/jquery_1.7.js"></script> 
<script src="js/jquery.form.js"></script> 
<script src="js/presents_div.js"></script> 
</head>
<body>
<form class="presents_post" action="presents.php" method="post">
<div class="activate_div">
  <input type="hidden" name="accountName" value="<?echo $account;?>">
  <input type="hidden" name="shardName" value="<?echo $shardName;?>">
  <input type="hidden" name="gametool_host" value="<?echo $gametool_host;?>">
  <input type="hidden" name="gametool_port" value="<?echo $gametool_port;?>">
  <input type="hidden" name="basePartName" value="<?echo $basePartName;?>">
  <label for="ai">Choose avatar:</label> <select name="avatarId">
  <?
  foreach ($avatars as $avatar){
    print_r($avatar);
    if( 0 == sprintf("%d",$avatar["deleted"]) ){
      if( $shardName == $avatar["shard"] )
      printf("<option value=\"%d\">%s</option>\n ", $avatar["avatarId"], $avatar["avatar"]);
    }
  }
  ?>
  </select>
  <br>
  <label for="c">Enter code:</label><input type="text" name="code" size="20">
 <br>
 <input type="submit" name="activate" value="Activate">
</div>
</form>
</body>
</html>
<?
mysql_close($link);
?>
