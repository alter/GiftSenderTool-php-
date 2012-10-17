<?
include_once('ttl.inc');

function get_transaction_id(){
	$insert_transaction_id = "insert into `transactionsId`(`id`) value('0')";
    commit_changes($insert_transaction_id);
	$select_transaction_id = "select id from `transactionsId` order by id desc limit 1";
	$result = mysql_query($select_transaction_id);
	$result = mysql_fetch_array($result, MYSQL_NUM);
	return $result[0];
}

function check_path($path){
	if (is_null($path))
                return -1;
        else
                return 0;

}

function check_avatar_in_history($avatarId, $code, $shardName){
	$select_unique = sprintf("select id from `history` where `code` = '%s' and `avatarId` = '%d' and `shard` = '%s'", mysql_real_escape_string($code), intval($avatarId), $shardName);
	$result = mysql_query($select_unique) or die(mysql_error());
	$rows = mysql_num_rows($result);
        return $rows;		
}

function check_account_in_history($accountName, $code){
	$select_unique = sprintf("select id from `history` where `code` = '%s' and `accountName` = '%s'", mysql_real_escape_string($code), mysql_real_escape_string($accountName));
	$result = mysql_query($select_unique) or die(mysql_error());
	$rows = mysql_num_rows($result);
        return $rows;		
}

function db_connect($db_hostname,$db_port,$db_username,$db_password,$db_name){
    global $link;
    $link = mysql_connect($db_hostname.":".$db_port,$db_username,$db_password);
    if (!$link) {
        die('Connection refused: '.mysql_error());
        return -2;
    }
    mysql_set_charset('utf8',$link); 
    mysql_select_db($db_name) or die(mysql_error());
}

function get_random_code($length = 12) {
    $validCharacters = "abcdefghijklmnopqrstuxyvwzABCDEFGHIJKLMNOPQRSTUXYVWZ0123456789";
    $validCharNumber = strlen($validCharacters);
    $result = "";
    $groups = intval($length/4);
    for ($i = 0; $i < $groups; $i++){
        $pre_result = "";
        for ($y = 0; $y < 4; $y++) {
            $index = mt_rand(0, $validCharNumber - 1);
            $pre_result .= $validCharacters[$index];
        }
        if( $i == ($groups-1))
            $result .= $pre_result;
        else
            $result .= "$pre_result-";
    }
    return $result;
}

function get_uniq_code($code){
    $select_code = sprintf("select code from `presents` where `code` = '%s'", mysql_real_escape_string($code));
    $result = mysql_query($select_code) or die(mysql_error());
    if(mysql_num_rows($result) < 1)
        return $code;
    else
        get_uniq_code(get_random_code($length = 12));
}

function create_type($type){
    $insert_type = sprintf("INSERT INTO `types`(`name`) value('%s')",mysql_real_escape_string($type));
    commit_changes($insert_type);
}

function get_type_id($type){
    $select_type_id = sprintf("SELECT id FROM `types` where  name='%s'",mysql_real_escape_string($type));
    $result = mysql_query($select_type_id);
    $type_id = mysql_fetch_array($result, MYSQL_NUM);
    return $type_id[0];

}

function commit_changes($name){
    mysql_query($name) or die(mysql_error());;
    mysql_query("COMMIT");

}

function connect_to_ms_api($master_server_host,$master_server_port){
    $url = "http://$master_server_host:$master_server_port/masterServer";
    registerAdminToolMethods($url);
    $ms_api = new HessianClient($url);
    return $ms_api;
}

?>
