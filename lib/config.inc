<?php
/* This part should be configured by IT specialist */
$db = new DBConfig();
$db->hostname = "192.168.198.67";
$db->port = 3306;
$db->username = "present";
$db->password = "present123";
$db->name = "presents";

$bp1 = new BasePartConfig();
$bp1->basepart_name = "BasePart 1";
$bp1->billing_api_host = "192.168.198.64";
$bp1->billing_api_port = 8080;
$bp1->master_server_host = "192.168.198.64";
$bp1->master_server_port = 10500;

$bp2 = new BasePartConfig();
$bp2->basepart_name = "BasePart 2";
$bp2->billing_api_host = "192.168.198.67";
$bp2->billing_api_port = 8080;
$bp2->master_server_host = "192.168.198.66";
$bp2->master_server_port = 10500;
$bp2->senderName = "Система раздачи подарков";
$bp2->subject = "Подарок!";
$bp2->body = "Этот прекрасный подарок специально для тебя !";
/* don't hesitate to add base parts if you have a lot of them. Just use copy-paste from code above */
?>
