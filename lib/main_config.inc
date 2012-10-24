<?php
function __autoload($class_name) {
    include $class_name . '.class';
}

set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__);

include_once('config.inc');
/* don't touch following code */
$i=1;
$baseparts = array();
while($i){
    $bpx="bp".$i;   
    if ( isset($$bpx) ){
        $base_options="base".$i."_options";
        $$base_options=array(
            'basepart_name' => $$bpx->basepart_name,
            'billing_api_host' => $$bpx->billing_api_host,
            'billing_api_port' => $$bpx->billing_api_port,
            'master_server_host' => $$bpx->master_server_host,
            'master_server_port' => $$bpx->master_server_port,
            'localization_senderName' => $$bpx->senderName,
            'localization_subject' => $$bpx->subject,
            'localization_body' => $$bpx->body
        );
        $baseparts[] = $$base_options;
        ++$i;  
    }
    else{
         break;
    }
}
//var_dump($baseparts);
?>
