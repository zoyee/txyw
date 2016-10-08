<?php
define('IN_ECTOUCH', true);
require(dirname(__FILE__) . '/include/init.php');
$smarty->assign('shop_name', $_CFG['shop_name']);
$smarty->assign('ecsAlert', '功能维护中，请稍后再来');
$smarty->assign('after', "history.back(-1)");
$smarty->display('ecsAlert.dwt');
exit;