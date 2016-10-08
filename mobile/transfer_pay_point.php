<?php
/**
 * 转移积分商城的积分到老农商城，手机号匹配相同用户
 */
define('IN_ECTOUCH', true);
require (dirname(__FILE__) . '/include/init.php');
set_time_limit(0);
$sql = "select user_id, mobile, pay_point from " . $GLOBALS['ecs']->table('users') . " u, tmp_pay_point t" .
		" where u.mobile_phone=t.mobile";
$all = $GLOBALS['db']->getAll($sql);
foreach ($all as $row){
	log_account_change($row['user_id'], 0, 0, 0, $row['pay_point'], "积分商城转移青豆" .$row['pay_point']);
	$GLOBALS['db']->query("update tmp_pay_point set flag=1 where mobile='" . $row['mobile'] . "'");
	echo $row['mobile']. "积分商城转移青豆" .$row['pay_point'] . "<br/>";
}
echo "finish";