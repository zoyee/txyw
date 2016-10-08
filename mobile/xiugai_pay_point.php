<?php
/**
 * 
 */
define('IN_ECTOUCH', true);
require (dirname(__FILE__) . '/include/init.php');
set_time_limit(0);
$sql = "select * from ecs_account_log where change_type=99 and pay_points>0 and rank_points=0 
	and FROM_UNIXTIME(change_time,'%Y-%m-%d')='2016-05-06' and change_desc like '积分商城转移青豆%' order by log_id";
$all = $GLOBALS['db']->getAll($sql);
foreach ($all as $row){
	$log_id = $row['log_id'];
	$user_id = $row['user_id'];
	$point = $row['pay_points'];
	$GLOBALS['db']->query("update ecs_users set pay_points = pay_points - $point where user_id='$user_id'");
	$GLOBALS['db']->query("delete from ecs_account_log where log_id=$log_id");
}
echo "finish";