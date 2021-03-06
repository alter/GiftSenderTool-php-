<?php
header("Content-Type: text/html; charset=utf-8");
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
    <script src="js/jquery_1.3.min.js"></script> 
    <script src="js/jquery.table.addrow.js"></script> 
    <script src="js/add_del_buttons.js"></script> 
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
<tr><td><label for="file">Accounts list:</label></td><td><input type="file" name="file" id="file" /><td><tr>
<tr><td><input type="submit" name="activate" value="Generate code"></td></tr>
</table>
</form>
</td><td>
*Legend:
<font size="2">
<table>
<tr><td>TTL</td><td> - time to live, how much times this code maybe used(default 1)</td></tr>
<tr><td>Amount</td><td> - number of codes which will be created</td></tr>
<tr><td>Value</td><td> - maybe amount of crystals or resourceId of item</td></tr>
</table>
</td>
</table>
</font>
</pre>
</body>
</html>

<?php
$allowed_extensions = array("txt");
$file_received = 0;
$accounts_array = array();

if($_FILES['file']['name'] != ''){
  $file = $_FILES['file'];
  if ($file['tmp_name'] > '') {
    if (!in_array(end(explode(".", strtolower($file['name']))), $allowed_extensions)) {
      die($file['name'].' is an invalid file type!<br/>');
    }
    else if (($file['type'] == "text/plain") && ($file['size'] < 30000000)){
      $file_received = 1;
      if(file_exists($file['tmp_name'])){
        $accounts_array = array_map('rtrim', file($file['tmp_name'], FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
      }
    }
    else {
      die($file['name'].' has a huge size or its type not text/plain<br/>');
    }
  }
}

$accounts_array_elements = count($accounts_array);
if(isset($_POST['amount']) && ($_POST['amount'] !='')){
  if($accounts_array_elements == 0){
    $amount = intval($_POST['amount']);
  }
  else{
    $amount = $accounts_array_elements;
  }
}
else{
  if($accounts_array_elements == 0){
    $amount = 1;
  }
  else{
    $amount = $accounts_array_elements;
  }
}

$codes = array();
$account_codes = array();
$created_type = 0;

if( $amount == 0){
  $type = mysql_real_escape_string($_POST['type']);
  create_type($type);
  $type_id = get_type_id($type);
  echo "<b>Type id: $type_id</b><br>";
}

for($i = 0; $i < $amount; $i++)
{
    if(isset($_POST['type']) && ($_POST['type'] != '')){
        if(isset($_POST['ttl']) && ($_POST['ttl'] != ''))
          if ($file_received == 1){
            $ttl = 1;
          }
          else{
            $ttl = intval($_POST['ttl']);
          }
        else
            $ttl = 1;
        $type = mysql_real_escape_string($_POST['type']);
        $rule_arr = array();
        $stack_arr = array();
        $value_arr = array();
        foreach($_POST as $key => $value){
            if(strstr($key, 'rule'))
                $rule_arr["$key"] = $value;

            else if(strstr($key, 'stackcount')){
                if($value == ''){
                    echo "<font color='red'><b>".errors(8)."</b></font>";
                    return 8;
                }
                $stack_arr["$key"] = intval($value);
            }

            else if(strstr($key, 'value')){
                if($value == ''){
                    echo "<font color='red'><b>".errors(8)."</b></font>";
                    return 8;
                }
                $value_arr["$key"] = intval($value);
            }
        }
        if((count($rule_arr) < 1) || (count($value_arr) != count($rule_arr))){
            echo "<font color='red'>".errors(8)."</font>";
            return 8;
        }
        if($created_type == 0){
            create_type($type);
            reset ($rule_arr);
            reset ($stack_arr);
            reset ($value_arr);
            $type_id = get_type_id($type);
            echo "<b>Type id: $type_id</b><br>";
            while ((list($r_key, $r_value) = each($rule_arr)) && (list($v_key, $v_value) = each($value_arr)) && (list($s_key, $s_value) = each($stack_arr))){
                $insert_rules_for_types = sprintf("INSERT INTO `rules_for_types`(`types_id`,`rules_id`,`value`,`stackcount`) value('%d','%d','%d','%d')", intval($type_id), intval($r_value), intval($v_value), intval($s_value));
                commit_changes($insert_rules_for_types);
            }
        
        }
        $created_type = 1;

        $uniq_code = get_uniq_code(get_random_code(12));
        $insert_present = sprintf("INSERT INTO `presents`(`name`,`types_id`,`code`,`ttl`) value('%s','%d','%s','%d')", mysql_real_escape_string($type), intval($type_id), $uniq_code, intval($ttl));
        commit_changes($insert_present);
        if($file_received == 1){
          $account_codes[$accounts_array[$i]] = $uniq_code;
          $insert_code_for_account = sprintf("INSERT INTO `accounts`(`accountName`,`code`) value('%s','%s')", mysql_real_escape_string($accounts_array[$i]), $uniq_code);
          commit_changes($insert_code_for_account);
        }
        else{
          $codes[$i] = $uniq_code;
        }
    }
    else{
        echo "<font color='red'>".errors(8)."</font>";
        return 8;
    }
}

echo "<b>Uniq codes list:</b><br><br>";
echo '<form method="POST" action="csv.php" enctype="multipart/form-data">';
if($file_received == 1){
    foreach($account_codes as $name => $uniq_code){
      echo "$name => $uniq_code <br/>";
      echo '<input type="hidden" name="fields[]" value="'.$name.';'.$uniq_code.'"/>';
  }
}
else{
  foreach($codes as $uniq_code){
    echo "$uniq_code<br>";
    echo '<input type="hidden" name="fields[]" value="'.$uniq_code.'"/>'; 
  }
}
echo '<br/>';
echo '<input type="submit" name="submit" value="save to csv">';
echo '</form>';
mysql_close($link);
?>
