<?php
include_once("../lib/main_config.inc");
include_once("../lib/userapi.php");

db_connect($db->hostname,$db->port,$db->username,$db->password,$db->name);
$code_type_id=119;
$code = create_new_account("accountName", intval($code_type_id));
echo "$code\n";
?>
