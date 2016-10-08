<?php
define('IN_ECTOUCH', true);

require(dirname(__FILE__) . '/include/init.php');
require_once(ROOT_PATH . 'lang/' .$_CFG['lang']. '/sms.php');
$logger = LoggerManager::getLogger('index.php');

$user_id = $_SESSION['user_id'];
if(empty($user_id)){
	$result['error'] = 1;
	$result['message'] = "用户未登录！";
    die(json_encode($result));
}

$mobile_phone = $_REQUEST['mobile'];
$weixin = $_REQUEST['weixin'];
$verifycode = $_REQUEST['verify_code'];
if(empty($mobile_phone) || empty($verifycode)){
	$result['error'] = 1;
	$result['message'] = "手机号和验证码不能为空！";
    die(json_encode($result));
}

$preg = '/^1[0-9]{10}$/';
if (!preg_match($preg, $mobile_phone)){
	$result['error'] = 1;
	$result['message'] = "手机号必须为11位数字！";
    die(json_encode($result));
}

/* 提交的手机号是否已经绑定帐号 */
$sql = "SELECT COUNT(user_id) FROM " . $ecs->table('users') . " WHERE mobile_phone = '$mobile_phone' and `user_id` <>".$user_id;
$logger->debug($sql);
if ($db->getOne($sql) > 0){
	$result['error'] = 1;
	$result['message'] = $_LANG['mobile_phone_binded'];
    die(json_encode($result));
}

/* 验证手机号验证码和IP */
$sql = "SELECT COUNT(id) FROM " . $ecs->table('verifycode') ." WHERE mobile='$mobile_phone' AND verifycode='$verifycode' AND status=4 AND dateline>'" . gmtime() ."'-".$_CFG['ecsdxt_smsgap'];
$logger->debug($sql);
if ($db->getOne($sql) == 0){
	$result['error'] = 1;
	$result['message'] = $_LANG['verifycode_mobile_phone_notmatch'];
    die(json_encode($result));
}

/* 更新验证码表更新用户手机字段 */
$sql = "UPDATE " . $ecs->table('verifycode') . " SET reguid=" . $user_id . ",regdateline='" . gmtime() ."',status=5 WHERE mobile='$mobile_phone' AND verifycode='$verifycode'  AND status=4 AND dateline>'" . gmtime() ."'-".$_CFG['ecsdxt_smsgap'];
$db->query($sql);
$sql = "UPDATE " . $ecs->table('users') . " SET mobile_phone='" . $mobile_phone ."', wxh='".$weixin."' WHERE user_id=" . $user_id . "";
$db->query($sql);
$result['error'] = 0;
$result['message'] = "操作成功！";
echo json_encode($result);