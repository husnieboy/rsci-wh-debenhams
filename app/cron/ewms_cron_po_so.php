#!/usr/local/bin/php -q
<?php

chdir(dirname(__FILE__));
include_once('ewms_cron_class.php');
include_once('db2_cron_class.php');

$ewms 		= new cronEWMS();
$db2 		= new cronDB2();


/*$po 		= $db2->purchaseOrder();
$ewms->purchaseOrder();			sleep(2);
$poDetail 	= $db2->purchaseOrderDetails();
$ewms->purchaseOrderDetails();	sleep(2);*/
$so 		= $db2->storeOrder();
$ewms->storeOrder();			sleep(2);
//$soDetail 	= $db2->storeOrderDetails();
//$ewms->storeOrderDetails();		sleep(2);


$db2->close();
$ewms->close();