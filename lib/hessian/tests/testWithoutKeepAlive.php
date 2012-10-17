<?php

require_once('../HessianClient.php'); 

$proxy = new HessianClient("http://127.0.0.1:8080/tests"); 

for ($i = 0; $i < 100; $i++) {
 echo $proxy->getSum($i, $i*2) . "\n";
}

?>