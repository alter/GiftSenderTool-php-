<?php
function get_ttl($code){
        $select_ttl = sprintf("SELECT ttl FROM `presents` WHERE code='%s'", mysql_real_escape_string($code));
        $result = mysql_query($select_ttl) or die(mysql_error()); 
        return $result;
}

function get_ttl_value($result){
        $result = mysql_fetch_array($result);
        return $result[0];
}

function get_ttl_rows($result){
        $rows = mysql_num_rows($result);
        return $rows;
}

function update_ttl($code){
	$update_ttl = sprintf("UPDATE `presents` SET ttl=ttl-1 WHERE code='%s'", mysql_real_escape_string($code));
        commit_changes($update_ttl);
}
?>
