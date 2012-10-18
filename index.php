<?php

header("Content-Type: text/html; charset=utf-8");
set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__.'/lib');
include_once('main_config.inc');
include_once('functions.inc');
include_once('hessian/HessianClient.php');
include_once('adminTool.inc.php');
?>

<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8"> 
<link rel="stylesheet" type="text/css" href="css/style.css">
<script src="js/jquery_1.7.js"></script> 
<script src="js/jquery.form.js"></script> 
<script src="js/login_div.js"></script> 
</head>
<body>
<h1>Activate code</h1>

<div id="main">
<form class="login_post" action="login.php" method="post">
<div  class="index_div">
  <label for="bp">Choose base part:</label> <select name="basePartName">
  <?
  foreach($baseparts as $basepart)
      printf("<option value=\"%s\">%s</option>\n ", $basepart['basepart_name'], $basepart['basepart_name']);
  ?>
  </select>
  <br>
  <input type="submit" name="submit" value="Enter" />
</div>
</form>
<div  class="login_div">
</div>
<div  class="activate_div">
</div>
<div  class="presents_div">
</div>
</div>
</body>
</html>
