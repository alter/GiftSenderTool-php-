<?php

require_once('../HessianClient.php'); 

$options = array('keep-alive' => true);

$proxy = new HessianClient("http://127.0.0.1:8080/tests", $options); 

for ($i = 0; $i < 100; $i++) {
 echo $proxy->getSum($i, $i*2) . "\n";
}

?>