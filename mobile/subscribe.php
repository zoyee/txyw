<?php
define('IN_ECTOUCH', true);

require(dirname(__FILE__) . '/include/init.php');
$logger = LoggerManager::getLogger('subscribe.php');
$affiliate_conf = unserialize($GLOBALS['_CFG']['affiliate']);
$affiliate = get_affiliate();
$buy_flag = $_REQUEST['buy_flag'];
$pintuan_id = $_REQUEST['pintuan_id'];
$smarty->assign('user_id', $_SESSION['user_id']);
$smarty->assign('affiliate', $affiliate);
$smarty->assign('affiliate_conf', $affiliate_conf);

$logger->debug('user_id = ' . $_SESSION['user_id']);
$logger->debug('affiliate = ' . $affiliate);

if($affiliate_conf['on'] && empty($_SESSION['user_id'])){
//	$str = $smarty->fetch("qrcode.php?act=user&id=" . $affiliate);
	ini_set('default_socket_timeout', 60); 
	$url = $config['site_url']. 'mobile/' . "qrcode.php?";
	$sql = "select goods_id from " . $ecs->table('cart') . " where session_id='" . SESS_ID . "'";
	$cart_goods = $db->getOne($sql);
	if($cart_goods){
		$url .= "act=user_cart&cart_sid=" . SESS_ID . "&user_id=" . $affiliate;
		$logger->debug($url);
		$result = file_get_contents($url);
		$logger->debug('$result = ' . $result);
		$json = json_decode($result);
		if($json->errcode == 0){
			$smarty->assign('user_qr', $json->data);
		}else{
			$smarty->assign('ecsAlert', '网络不给力，请点击确定刷新页面');
			$smarty->assign('after', "location.href=\'".$_SERVER['REQUEST_URI']."';");
			$smarty->display('ecsAlert.dwt');
			exit;
		}
	}elseif($pintuan_id){
		$url .= "act=pintuan&pintuan_id=" . $pintuan_id . "&user_id=" . $affiliate;
		$logger->debug($url);
		$result = file_get_contents($url);
		$logger->debug('$result = ' . $result);
		$json = json_decode($result);
		if($json->errcode == 0){
			$smarty->assign('user_qr', $json->data);
		}else{
			$smarty->assign('ecsAlert', '网络不给力，请点击确定刷新页面');
			$smarty->assign('after', "location.href=\'".$_SERVER['REQUEST_URI']."';");
			$smarty->display('ecsAlert.dwt');
			exit;
		}
	}else if($affiliate){
		$url .= "act=user&id=" . $affiliate;
		$logger->debug($url);
		$result = file_get_contents($url);
		$logger->debug('$result = ' . $result);
		$json = json_decode($result);
		if($json->errcode == 0){
			$smarty->assign('user_qr', $json->data);
		}else{
			$smarty->assign('ecsAlert', '网络不给力，请点击确定刷新页面');
			$smarty->assign('after', "location.href=\'".$_SERVER['REQUEST_URI']."';");
			$smarty->display('ecsAlert.dwt');
			exit;
		}
	}
	
}

$now_date = date('Y-m-d');
$smarty->assign('date', $now_date);
if($buy_flag){
	$smarty->assign('buy_flag', $buy_flag);
}
$smarty->display('subscribe.dwt');