<?php

include_once('hessian/HessianClient.php');

include_once('AvatarOnShard.inc.php');
include_once('GiveItemStatus.inc.php');
include_once('GiveItemResult.inc.php');
include_once('CancelItemResult.inc.php');
include_once('ItemActionInfo.inc.php');
include_once('RecentCCU.inc.php');
include_once('CCUSample.inc.php');
include_once('GametoolResource.inc.php');
include_once('ItemToSend.inc.php');
include_once('SendItemResult.inc.php');
include_once('SendItemStatus.inc.php');

function registerGametoolMethods($fullPath) {
  $methods = array(
    'getAllShards',
    'getAvatars',
    'giveItemToAvatar',
    'getGivenAvatarItem',
    'getGivenAvatarItems',
    'cancelPendingAvatarItem',
    'getShardCCU',
    'sendItemToAvatarByMail',
    'mutisendItemToAvatarByMail',
    'getShardCCUTimeline'
    );
  foreach ($methods as $method) {
    Hessian::remoteMethod($fullPath, $method);
  }
}

function registerGametoolResourceMethods($fullPath) {
  $methods = array(
    'getResource',
    );
  foreach ($methods as $method) {
    Hessian::remoteMethod($fullPath, $method);
  }
}

?>
