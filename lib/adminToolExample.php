<?php

include_once('../hessian/HessianClient.php'); 
include_once('./adminTool.inc.php');

function conv($textInWin) {
  return iconv('Windows-1251', 'UTF-8', $textInWin);
}

$url = 'http://127.0.0.1:10500/masterServer';
registerAdminToolMethods($url);

$api = new HessianClient($url);

try {
  print "Requesting base part servers...\n";

  $servers = $api->sendRequestGetResponse(new BasePartServersRequest());

  print "Base part servers:\n";

  foreach ($servers['servers'] as $server) {
    print " " . $server['id'] . ": " . $server['name'] . " at " . $server['hostName']
      . ", status = " . $server['status']['name'] . "\n";

    if ($server['name'] == 'itemMallServer') {
      print "Start itemMall...\n";

      $startResponse = $api->sendRequestGetResponse(new StartBasePartRequest($server['id'], AddonParams::getEmpty()));
      print_r($startResponse);
    }
  }

  print "Requesting shards...\n";

  $shards = $api->getShards();

  print "Shards:\n";

  $shardIds = array();

  foreach ($shards as $shard) {
    print " " . $shard['id'] . ": " . $shard['name'] . ", " . $shard['status']['name'] . "\n";

    print "  Requesting applications...\n";
    $apps = $api->getApplications($shard['id']);
    print "  Shard applications: " . count($apps) . "\n";
    foreach ($apps as $app) {
      print "    " . $app['id'] . ": " . $app['name'] . " at " . $app['host']
                   . ", " . $app['status']['name'] . "\n";  
    }

    $shardIds[]= $shard['id'];
  }

  $stopResponse = $api->sendRequestGetResponse(
    new DeferredStopStartRequest(conv('administration'), 
                                 conv('Maintenance will be performed at 17:33'),
                                 15,
                                 $shardIds,
                                 ShardHandlingMode::Everything(),
                                 true,
                                 AddonParams::getEmpty()));

  print_r($stopResponse);

} catch (HessianError $e) {
  print "Error occured: " . $e->getMessage() . "\n";
}

?>