<?php
/**
 * ECSHOP 二维码登录
 * ============================================================================
 * 扫描二维码登录
 * ============================================================================
 * $Author: liuzhy $
 * $Id: qr_login.php $
 */
 
 define('IN_ECS', true);
 require(dirname(__FILE__) . '/includes/init.php');
 /* 载入语言文件 */
require_once(ROOT_PATH . 'languages/' .$_CFG['lang']. '/user.php');
include_once('includes/cls_json.php');
$json = new JSON;
$logger = LoggerManager::getLogger('qr_event.php');
$act = $_REQUEST['act'];

$uuid = $_REQUEST['uuid'];
$time = $_REQUEST['time'];
$back_act = $_REQUEST['back_act'];

if($act == 'qr_login'){
	//PC端扫微信二维码登录（注册）商城
	$continue = 1;
	$time_tmp = 0;
	while($continue>0){
		$qr_event = $db->getRow("select * from " . $ecs->table('qr_event') . " where uuid='$uuid'");
		if($qr_event['status']){
			$user_id = $qr_event['user_id'];
			$_SESSION['user_id'] = $user_id;
			$time = gmtime() + 3600 * 24 * 30;
			$nickname = $db->getOne("select w.nickname from wxch_user w, " . $ecs->table('users') . " u where w.uname=u.user_name and u.user_id='$user_id' limit 1");
			setcookie("ECS[nickname]",  $nickname,   $time, $GLOBALS['cookie_path'], $GLOBALS['cookie_domain']);
			$logger->debug('user_id = ' . $user_id);
			update_user_info();
			recalculate_price();
			$result['error']   = 0;
            $result['content'] = $_LANG['login_success'];
            //删除二维码图片文件
            $logger->debug(ROOT_PATH . $qr_event['qr_path']);
            @unlink (ROOT_PATH . $qr_event['qr_path']);
            die($json->encode($result));
		}
		
		sleep(1);
		$time_tmp = $time_tmp + 1000;
		if($time_tmp > $time){
			$continue = 0;
		}
	}
}elseif($act == 'bind_oath_user'){
	//PC端扫微信二维码绑定联合登录用户(qq)和微信用户，合并用户及其订单
	$continue = 1;
	$time_tmp = 0;
	while($continue>0){
		$qr_event = $db->getRow("select * from " . $ecs->table('qr_event') . " where uuid='$uuid'");
		if($qr_event['status']){
			$user_id = $qr_event['user_id'];
			$oath_user_id = $qr_event['oath_user_id'];
			$_SESSION['user_id'] = $user_id;
			$logger->debug('user_id = ' . $user_id);
			update_user_info();
			recalculate_price();
			$result['error']   = 0;
            $result['content'] = $_LANG['login_success'];
            
            $oath_bind_user_id = $db->getOne("select user_id from " . $ecs->table('oath_user') . " where id='$oath_user_id'");
            $oath_bind_user = $db->getRow("select * from " . $ecs->table('users') . " where user_id='$oath_bind_user_id'");
            if($oath_bind_user_id && $user_id && $oath_bind_user_id != $user_id){
            	//合并用户，舍弃oath绑定的用户，保留微信绑定的用户
            	$logger->debug("合并联合登录用户".$oath_bind_user_id."到".$user_id."");
            	$db->query("update " . $ecs->table('account_log') . " set user_id='$user_id' where user_id='$oath_bind_user_id'");
            	$db->query("update " . $ecs->table('pintuan') . " set user_id='$user_id' where user_id='$oath_bind_user_id'");
            	$db->query("update " . $ecs->table('pintuan_orders') . " set act_user='$user_id' where act_user='$oath_bind_user_id'");
            	$db->query("update " . $ecs->table('pintuan_orders') . " set follow_user='$user_id' where follow_user='$oath_bind_user_id'");
            	$db->query("update " . $ecs->table('sessions') . " set userid='$user_id' where userid='$oath_bind_user_id'");
            	$db->query("update " . $ecs->table('order_info') . " set user_id='$user_id' where user_id='$oath_bind_user_id'");
            	$db->query("update " . $ecs->table('user_address') . " set user_id='$user_id' where user_id='$oath_bind_user_id'");
            	$db->query("update " . $ecs->table('user_bonus') . " set user_id='$user_id' where user_id='$oath_bind_user_id'");
            	$db->query("update " . $ecs->table('user_account') . " set user_id='$user_id' where user_id='$oath_bind_user_id'");
            	$db->query("update " . $ecs->table('user_card') . " set user_id='$user_id' where user_id='$oath_bind_user_id'");
            	$db->query("update " . $ecs->table('verifycode') . " set reguid='$user_id' where reguid='$oath_bind_user_id'");
            	
            	//转移积分余额
            	if($oath_bind_user['user_money'] > 0 || $oath_bind_user['frozen_money'] > 0
            		|| $oath_bind_user['pay_points'] > 0 || $oath_bind_user['rank_points'] > 0){
            		$desc = "合并联合登录用户".$oath_bind_user_id."到".$user_id.",转移余额与积分";
            		$logger->debug($desc);
            		log_account_change($user_id, $oath_bind_user['user_money'], $oath_bind_user['frozen_money'], $oath_bind_user['rank_points'], $oath_bind_user['pay_points'], $desc);
            	}
            	$db->query("update " . $ecs->table('users') . " set sales1_id='$user_id' where sales1_id='$oath_bind_user_id'");
            	$db->query("update " . $ecs->table('users') . " set sales2_id='$user_id' where sales2_id='$oath_bind_user_id'");
            	$db->query("update " . $ecs->table('users') . " set sales3_id='$user_id' where sales3_id='$oath_bind_user_id'");
            	$db->query("update " . $ecs->table('users') . " set sales4_id='$user_id' where sales4_id='$oath_bind_user_id'");
            	$db->query("update " . $ecs->table('users') . " set sales5_id='$user_id' where sales5_id='$oath_bind_user_id'");
            	$db->query("update " . $ecs->table('users') . " set affiliate_id='$user_id' where affiliate_id='$oath_bind_user_id'");
            	$db->query("update " . $ecs->table('users') . " set parent_id='$user_id' where parent_id='$oath_bind_user_id'");
            	if($oath_bind_user['mobile_phone']){
            		$db->query("update " . $ecs->table('users') . " set mobile_phone='$oath_bind_user[mobile_phone]' where user_id='$user_id' and mobile_phone=''");
            	}
            	$db->query("delete from " . $ecs->table('users') . " where user_id='$oath_bind_user_id'");
            	$logger->debug("合并联合登录用户".$oath_bind_user_id."到".$user_id."完成");
            }
            $db->query("update " . $ecs->table('oath_user') . " set user_id='$user_id' where id='$oath_user_id'");
            
            //删除二维码图片文件
            $logger->debug(ROOT_PATH . $qr_event['qr_path']);
            @unlink (ROOT_PATH . $qr_event['qr_path']);
            die($json->encode($result));
		}
		
		sleep(1);
		$time_tmp = $time_tmp + 1000;
		if($time_tmp > $time){
			$continue = 0;
		}
	}
}
