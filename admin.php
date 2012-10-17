<?php
//test git!
set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__.'/lib');
include_once('main_config.inc');
include_once('functions.inc');

db_connect($db->hostname,$db->port,$db->username,$db->password,$db->name);
$select_rules = "SELECT id,name from rules";
$rules = mysql_query($select_rules) or die (mysql_error());
while ($result = mysql_fetch_array($rules))
    $array["$result[0]"] = $result[1];
?>
<html>
<head>
    <title>Code generator</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"> 
    <style type="text/css">
      td { font-size:12px; }    
    </style>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.3/jquery.min.js"></script>
    <script src="js/jquery.table.addrow.js"></script>
    <script type="text/javascript">
        (function($){
            $(document).ready(function(){
                $(".addRow-ignoreClass").btnAddRow({maxRow:50,ignoreClass:"noClone"});
                $(".delRow").btnDelRow();
            });
        })(jQuery);
    </script>
</head>
<body>
<pre>
<h1>Generate new code</h1>
<table width="100%" border="0">
<tr valign="top"><td>
<form id="myForm" action="" method="post" enctype="multipart/form-data">
<table>
<tr><td class="noClone">Description:</td><td class="noClone"><input type="text" name="type" size="15"/></td></tr>
<tr><td class="noClone">TTL*:</td><td class="noClone"><input type="text" name="ttl" value="1" size="15"/></td></tr>
<tr><td class="noClone">Amount*:</td><td class="noClone"><input type="text" name="amount" value="1" size="15"</td></tr>
<tr><td>Rule: <select name="rule1" id="rule1">
<?
foreach($array as $key => $value)
    echo "<option value=\"".$key."\">".$value."</option>\n ";
?>
</select></td>
<td>Value*:<input type="text" name="value1" id="value1" size="10"/></td>
<td>StackCount:<input type="text" name="stackcount1" id="stackcount1" value="1" size="1"/><input type="button" class="delRow" value="Delete"/></td></tr>
<tr><td><input type="button" class="addRow-ignoreClass" value="add rule" /></td></tr>
<tr><td><label for="file">Filename:</label></td><td><input type="file" name="file" id="file" /><td><tr>
<tr><td><input type="submit" name="activate" value="Generate code"></td></tr>
</table>
</form>
</td><td>
*Legend:
<font size="2">
<table>
<tr><td>TTL</td><td> - time to live, how much times this code maybe used(default 50)</td></tr>
<tr><td>Amount</td><td> - number of codes which will be created</td></tr>
<tr><td>Value</td><td> - maybe amount of crystals or resourceId of item</td></tr>
</table>
</td>
</table>
</font>
</pre>
</body>
</html>

<?
//print_r($_POST);
if(isset($_POST['amount']) && ($_POST['amount'] !=''))
    $amount = intval($_POST['amount']);
else
    $amount = 1;
$codes = array();
$created_type = 0;
for($i = 0; $i < $amount; $i++)
{
    if(isset($_POST['type']) && ($_POST['type'] != '')){
        if(isset($_POST['ttl']) && ($_POST['ttl'] != ''))
            $ttl = intval($_POST['ttl']);
        else
            $ttl = 50;
        $type = $_POST['type'];
        $rule_arr = array();
        $stack_arr = array();
        $value_arr = array();
        foreach($_POST as $key => $value){
            if(strstr($key, 'rule'))
                $rule_arr["$key"] = $value;

            else if(strstr($key, 'stackcount')){
                if($value == ''){
                    echo "<font color='red'><b>Please fill in all fields</b></font>";
                    return -1;
                }
                $stack_arr["$key"] = intval($value);
            }

            else if(strstr($key, 'value')){
                if($value == ''){
                    echo "<font color='red'><b>Please fill in all fields</b></font>";
                    return -1;
                }
                $value_arr["$key"] = intval($value);
            }
        }
        if((count($rule_arr) < 1) || (count($value_arr) != count($rule_arr))){
            echo "<font color='red'>Please fill in all fields</font>";
            return -1;
        }

        if($created_type == 0){
            create_type($type);
            reset ($rule_arr);
            reset ($stack_arr);
            reset ($value_arr);
            $type_id = get_type_id($type);
            while ((list($r_key, $r_value) = each($rule_arr)) && (list($v_key, $v_value) = each($value_arr)) && (list($s_key, $s_value) = each($stack_arr))){
                $insert_rules_for_types = sprintf("INSERT INTO `rules_for_types`(`types_id`,`rules_id`,`value`,`stackcount`) value('%d','%d','%d','%d')", intval($type_id), intval($r_value), intval($v_value), intval($s_value));
                commit_changes($insert_rules_for_types);
            }
        
        }
        $created_type = 1;

        $uniq_code = get_uniq_code(get_random_code(12));

        $insert_present = sprintf("INSERT INTO `presents`(`name`,`types_id`,`code`,`ttl`) value('%s','%d','%s','%d')", mysql_real_escape_string($type),intval($type_id),$uniq_code, intval($ttl));
        commit_changes($insert_present); 
        $codes[$i] = $uniq_code;
    }
    else{
        echo "<font color='red'>Please fill in all fields</font>";
        return -1;
    }
}
echo "<b>Uniq codes list:</b><br><br>";

echo '<form method="POST" action="csv.php">';
foreach($codes as $uniq_code){
    echo $uniq_code."<br>";
    echo '<input type="hidden" name="fields[]" value="'.$uniq_code.'"/>'; 
}
echo '<input type="submit" name="submit" value="save(csv)">';
echo '</form>';
mysql_close($link);
?>
