<?php

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
include_once('includes/cls_json.php');
require(ROOT_PATH . 'includes/lib_sms.php');

require_once(ROOT_PATH . 'languages/' .$_CFG['lang']. '/sms.php');
$logger = LoggerManager::getLogger('sms.php');

if (!isset($_REQUEST['step']))
{
    $_REQUEST['step'] = "";
}

$result = array('error' => 0, 'message' => '');
$json = new JSON;

$mobile = trim($_REQUEST['mobile']);

/* 注册时手机号码验证 */
if ($_REQUEST['step'] == 'getverifycode1')
{
	/* 是否开启手机短信验证注册 */
	if($_CFG['ecsdxt_mobile_reg'] == '0') {
		$result['error'] = 1;
		$result['message'] = $_LANG['ecsdxt_mobile_reg_closed'];
        die($json->encode($result));
	}

	/* 提交的手机号是否正确 */
	if (!ismobile($mobile))
	{
		$result['error'] = 2;
		$result['message'] = $_LANG['invalid_mobile_phone'];
        die($json->encode($result));
	}

	/* 提交的手机号是否已经注册帐号 */
    $sql = "SELECT COUNT(user_id) FROM " . $ecs->table('users') ." WHERE mobile_phone = '$mobile'";

    if ($db->getOne($sql) > 0)
    {
        $result['error'] = 3;
		$result['message'] = $_LANG['mobile_phone_registered'];
        die($json->encode($result));
    }

	/* 获取验证码请求是否获取过 */
	$sql = "SELECT COUNT(id) FROM " . $ecs->table('verifycode') ." WHERE status=1 AND mobile='".$mobile."' AND dateline>'" . gmtime() ."'-".$_CFG['ecsdxt_smsgap'];

    if ($db->getOne($sql) > 0)
    {
        $result['error'] = 4;
		$result['message'] = sprintf($_LANG['get_verifycode_excessived'], $_CFG['ecsdxt_smsgap']);
        die($json->encode($result));
    }

	$verifycode = getverifycode();

    $smarty->assign('shop_name',	$_CFG['shop_name']);
    $smarty->assign('user_mobile',	$mobile);
    $smarty->assign('verify_code',  $verifycode);
    $smarty->assign('valid_min', intval($_CFG['ecsdxt_smsgap']) / 60);

    $content = $smarty->fetch('str:' . $_CFG['ecsdxt_mobile_reg_value']);
	$logger->debug('短信内容:'. $content);
	/* 发送注册手机短信验证 */
	$ret = sendsms($mobile, $content);

	if($ret === true)
	{
		//插入获取验证码数据记录
		$sql = "INSERT INTO " . $ecs->table('verifycode') . "(mobile, getip, verifycode, dateline) VALUES ('" . $mobile . "', '" . real_ip() . "', '$verifycode', '" . gmtime() ."')";
		$db->query($sql);

		$result['error'] = 0;
		$result['message'] = $_LANG['send_mobile_verifycode_successed'];
		die($json->encode($result));
	}
	else
	{
		$result['error'] = 5;
		$result['message'] = $_LANG['send_mobile_verifycode_failured'] . $ret;
		die($json->encode($result));
	}
}
/* 修改资料时手机号码验证 */
elseif ($_REQUEST['step'] == 'getverifycode2')
{
	$logger->debug('手机号码验证'. $_CFG['ecsdxt_mobile_bind']);
	/* 是否开启手机绑定 */
	if($_CFG['ecsdxt_mobile_bind'] == '0') {
		$result['error'] = 1;
		$result['message'] = $_LANG['ecsdxt_mobile_bind_closed'];
        die($json->encode($result));
	}

	/* 提交的手机号是否正确 */
	$preg = '/^1[0-9]{10}$/';
	if (!preg_match($preg, $mobile))
	{
		$result['error'] = 2;
		$result['message'] = $_LANG['invalid_mobile_phone'];
        die($json->encode($result));
	}

	/* 提交的手机号是否已经绑定帐号 */
    $sql = "SELECT COUNT(user_id) FROM " . $ecs->table('users') ." WHERE mobile_phone = '$mobile' and `user_id` <>".$_SESSION['user_id'];

    if ($db->getOne($sql) > 0)
    {
        $result['error'] = 3;
		$result['message'] = $_LANG['mobile_phone_binded'];
        die($json->encode($result));
    }

	/* 获取验证码请求是否获取过 */
	$sql = "SELECT COUNT(id) FROM " . $ecs->table('verifycode') ." WHERE (status=4 or status=5) AND mobile='".$mobile."' AND dateline>'" . gmtime() ."'-".$_CFG['ecsdxt_smsgap'];

    if ($db->getOne($sql) > 0)
    {
        $result['error'] = 4;
		$result['message'] = sprintf($_LANG['get_verifycode_excessived'], $_CFG['ecsdxt_smsgap']);
        die($json->encode($result));
    }

	$verifycode = getverifycode();

    $smarty->assign('shop_name',	$_CFG['shop_name']);
    $smarty->assign('user_mobile',	$mobile);
    $smarty->assign('verify_code',  $verifycode);
    $smarty->assign('valid_min', intval($_CFG['ecsdxt_smsgap']) / 60);

    $content = $smarty->fetch('str:' . $_CFG['ecsdxt_mobile_bind_value']);
    $logger->debug('短信内容:'. $content);
	/* 发送注册手机短信验证 */
	//$ret = sendsms($mobile, $content);
	$ret = true;

	if($ret === true)
	{
		//插入获取验证码数据记录
		$sql = "INSERT INTO " . $ecs->table('verifycode') . "(mobile, getip, verifycode, dateline, status) VALUES ('" . $mobile . "', '" . real_ip() . "', '$verifycode', '" . gmtime() ."', 4)";
		$db->query($sql);

		$result['error'] = 0;
		$result['message'] = $_LANG['bind_mobile_verifycode_successed'] . $verifycode;
		die($json->encode($result));
	}
	else
	{
		$result['error'] = 5;
		$result['message'] = $_LANG['bind_mobile_verifycode_failured'] . $ret;
		die($json->encode($result));
	}
}
/* 密码找回时手机号码验证 */
else if($_REQUEST['step'] == 'crtVerifyCodeForGetPwd') {
	/* 提交的手机号是否正确 */
	if (!ismobile($mobile))
	{
		$result['error'] = 2;
		$result['message'] = $_LANG['invalid_mobile_phone'];
		die($json->encode($result));
	}

	/* 提交的手机号是否已经绑定帐号 */
	$sql = "SELECT COUNT(user_id) FROM " . $ecs->table('users') ." WHERE mobile_phone = '$mobile'";
	if ($db->getOne($sql) == 0)
	{
		$result['error'] = 3;
		$result['message'] = '没有匹配手机号的账号！';
		die($json->encode($result));
	}

	/* 获取验证码请求是否获取过 */
	$sql = "SELECT COUNT(id) FROM " . $ecs->table('verifycode') ." WHERE status=6 AND mobile='".$mobile."' AND dateline>'" . gmtime() ."'-".$_CFG['ecsdxt_smsgap'];

	if ($db->getOne($sql) > 0)
	{
		$result['error'] = 4;
		$result['message'] = sprintf($_LANG['get_verifycode_excessived'], $_CFG['ecsdxt_smsgap']);
		die($json->encode($result));
	}

	$verifycode = getverifycode();

	$smarty->assign('shop_name',	$_CFG['shop_name']);
	$smarty->assign('user_mobile',	$mobile);
	$smarty->assign('verify_code',  $verifycode);
	$smarty->assign('valid_min', intval($_CFG['ecsdxt_smsgap']) / 60);

	$content = $smarty->fetch('str:' . $_CFG['ecsdxt_mobile_bind_value']);

	/* 发送注册手机短信验证 */
	$ret = sendsms($mobile, $content);
// 	$ret = true;

	if($ret === true)
	{
		//插入获取验证码数据记录
		$sql = "INSERT INTO " . $ecs->table('verifycode') . "(mobile, getip, verifycode, dateline, status) VALUES ('" . $mobile . "', '" . real_ip() . "', '$verifycode', '" . gmtime() ."', 6)";
		$db->query($sql);

		$result['error'] = 0;
		$result['message'] = $_LANG['send_mobile_verifycode_successed'];
		die($json->encode($result));
	}
	else
	{
		$result['error'] = 5;
		$result['message'] = $_LANG['send_mobile_verifycode_failured'] . $ret;
		die($json->encode($result));
	}
}
?>