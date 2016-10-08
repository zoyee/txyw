<?php

define('IN_ECTOUCH', true);

require(dirname(__FILE__) . '/includes/init.php');

$sql = 'select param from wxch_scene where id = 12345678';
$param = json_decode($GLOBALS['db']->getOne($sql),true);
var_dump($param); 
var_dump('数组长度：'.count($param));
?>