<?php
header("Content-Type: text/html; charset=utf-8");
set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__.'/lib');
include_once('main_config.inc');
include_once('functions.inc');
include_once('hessian/HessianClient.php');
include_once('adminTool.inc.php');


if(isset($_POST['basePartName']) && $_POST['basePartName']!='')
    $basePartName = $_POST['basePartName'];

foreach($baseparts as $basepart)
    if( $basepart['basepart_name'] == "$basePartName"){
        $master_server_host = $basepart['master_server_host'];
        $master_server_port = $basepart['master_server_port'];
    }
try{
    $ms_api = connect_to_ms_api($master_server_host,intval($master_server_port));
    $shards = $ms_api->getShards();
}
catch(Exception $ex){
    echo $ex->getMessage()."<br>Check that you have access to masterServer: $master_server_host:$master_server_port";
    return -1;
}

?>

<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8"> 
<link rel="stylesheet" type="text/css" href="css/style.css">
<script src="js/jquery_1.7.js" type="application/javascript"></script> 
<script src="js/jquery.form.js" type="application/javascript"></script> 
<script src="js/activate_div.js" type="application/javascript"></script> 
</head>
<body>
<form class="activate_post" action="activate.php" method="post">
<div  class="login_div">
  <label for="an">Enter account name:</label> <input type="text" name="account" value="test01" size="20" /><br>
  <label for="si">Choose shard:</label> <select name="shardId">
  <?
  foreach ($shards as $shard)
      printf("<option value=\"%d\">%s</option>\n ", $shard['id'], $shard['name']);
  ?>
  </select>
  <input type="hidden" name="master_server_host" value="<? echo $master_server_host; ?>" size="20" />
  <input type="hidden" name="master_server_port" value="<? echo $master_server_port; ?>" size="20" />
  <input type="hidden" name="basePartName" value="<? echo $basePartName; ?>" size="20" />
  <br>
  <input type="submit" name="submit" value="login" />
</div>
</form>
</body>
</html>
