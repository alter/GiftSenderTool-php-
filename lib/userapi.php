<?php
include_once("functions.inc");

function create_new_account($account_name, $code_type_id){
    $uniq_code = get_uniq_code(get_random_code(12));

    $select_type_name = sprintf("select name from type where id='%d'", intval($code_type_id));
    $result = mysql_query($select_type_name) or die (mysql_error());
    $result = mysql_fetch_array($result);
    $type_name = $result[0];

    $insert_present = sprintf("INSERT INTO `presents`(`name`,`types_id`,`code`,`ttl`) value('%s','%d','%s','%d')", mysql_real_escape_string($type_name), intval($code_type_id), $uniq_code, 1);
    commit_changes($insert_present);

    $insert_code_for_account = sprintf("INSERT INTO `accounts`(`accountName`,`code`) value('%s','%s')", mysql_real_escape_string($accounts_name), $uniq_code);
    commit_changes($insert_code_for_account);
    
    return $uniq_code;
}

create_new_account("testX", 1);

?>
