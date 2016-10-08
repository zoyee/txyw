<?php
define('IN_ECTOUCH', true);
require(dirname(__FILE__) . '/include/init.php');
require(ROOT_PATH . 'include/lib_weixintong.php');
$user_id = $_SESSION['user_id'];
if(empty($user_id)){
	$user_id = $wechat->get_userid();
}

$act = $_REQUEST['act'];
$user_shop = $db->getRow("select * from " . $ecs->table('user_shop') . " where user_id='$user_id'");
if(!empty($user_id) && empty($user_shop)){
	$time = time();
	$db->autoExecute($ecs->table('user_shop'), array(
		'user_id' => $user_id,
		'open_flag' => 1,
		'dateline' => $time
	), 'INSERT');
	$user_shop = $db->getRow("select * from " . $ecs->table('user_shop') . " where user_id='$user_id'");
}

if($act == 'edit_shop_desc'){
	$smarty->assign('user_shop', $user_shop);
	$smarty->display('edit_user_shop.dwt');
}
elseif($act == 'save_shop_desc'){
	$shop_desc = $_REQUEST['shop_desc'];
	$db->autoExecute($ecs->table('user_shop'), array(
		'shop_desc' => $shop_desc
	), 'UPDATE', "user_id='$user_id'");
	
	$smarty->assign('ecsAlert', '保存成功');
	$smarty->assign('after', "location.href=\'user.php';");
	$smarty->display('ecsAlert.dwt');
	exit;
}