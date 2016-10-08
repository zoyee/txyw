<?php
/**
 * 二维码生成：产品二维码、用户专属二维码
 */
define('IN_ECTOUCH', true);
define('IN_ECS', true);
require(dirname(__FILE__) . '/include/init.php');
require_once(dirname(__FILE__) . '/include/cls_image.php');
$logger = LoggerManager::getLogger('qrcode.php');
$logger->debug('收到二维码生成请求');
require_once (ROOT_PATH . 'api/weixin_api.php');
$weixin_api = new weixin_api();

if($_REQUEST['act'] == 'goods'){
	$gid = $_REQUEST['id'];
	if(file_exists(dirname(__FILE__) . '/images/qrcode/goods/' . $gid . '.jpg')){
		//二维码已存在
		$result['errcode'] = 0;
		$result['qrcode'] = 'images/qrcode/goods/' . $gid . '.jpg';
		echo json_encode($result);
	}else{
		//生成产品永久二维码
		$scene_param = array(
				'goods_id' => $gid
		);
		$scene_param = json_encode($scene_param);
		$createtime = date("Y-m-d H:i:s", time());
		$sql = "insert into wxch_scene (`type`,param, createtime) values (2, '$scene_param','$createtime')";
		$GLOBALS['db'] -> query($sql);
		$rec_id = $GLOBALS['db'] -> insert_id();
		$scene_id = $weixin_api->cr_limit_scene_id();
		$GLOBALS['db'] -> query("update wxch_scene set scene_id='$scene_id' where id=$rec_id");
		$logger->debug('$scene_id = ' . $scene_id);

		$action_name = "QR_LIMIT_STR_SCENE";//永久二维码
		// 		$scene_id = scene_encode(array($user_id, $goods_id));
		$json_arr = array(
				'action_name' => $action_name,
				'action_info' => array(
						'scene' => array(
								'scene_str' => $scene_id
						)
				)
		);
		$data = json_encode($json_arr);
		$logger->debug('$data = ' . $data);
		$qr_code = $weixin_api->cr_qrcode($json_arr, "user_card_type");
		$logger->debug(json_encode($qr_code));
		if($qr_code->errcode == 0){
			$GLOBALS['db'] -> query("update wxch_scene set ticket='" . $qr_code->ticket . "' where id=$rec_id");
			$result['errcode'] = 0;
			$target_file = ROOT_PATH . 'images/qrcode/goods/' . $gid . '.jpg';
			rename(ROOT_PATH . $qr_code->data, $target_file);
			$result['qrcode'] = 'images/qrcode/goods/' . $gid . '.jpg';
			echo json_encode($result);
		}else{
			echo json_encode($qr_code);
		}
		
	}
}
//会员卡二维码
elseif($_REQUEST['act'] == 'user_card_type'){
	
	$id = $_REQUEST['id'];
	if(file_exists(dirname(__FILE__) . '/images/qrcode/user_card_type/' . $id . '.jpg')){
		//二维码已存在
		$result['errcode'] = 0;
		$result['qrcode'] = 'images/qrcode/user_card_type/' . $id . '.jpg';
		echo json_encode($result);
	}else{
		//查询会员卡是否需要绑定红包
		$user_card_type = $GLOBALS['db'] -> getRow("select bonus_type, user_rank from " . $GLOBALS['ecs']->table('user_card_type') . " where ct_id='$id'");
		$scene_param = array(
				'user_card_type' => $id,
//				'user_scan_limit' => 1,
//				'new_user_limit' => 1
		);
		if(!empty($user_card_type['bonus_type'])){
			$scene_param['bonus_type'] = $user_card_type['bonus_type'];
		}
		if(!empty($user_card_type['user_rank'])){
			$scene_param['user_rank'] = $user_card_type['user_rank'];
		}
		
		
		//生成临时二维码, 每次请求都重新生成
		$scene_param = json_encode($scene_param);
		$time = time();
		$createtime = date('Y-m-d H:i:s', $time);
		$sql = "insert into wxch_scene (`type`,param, createtime) values (2, '$scene_param','$createtime')";
		$GLOBALS['db'] -> query($sql);
		$rec_id = $GLOBALS['db'] -> insert_id();
		$scene_id = $weixin_api->cr_limit_scene_id();
		$GLOBALS['db'] -> query("update wxch_scene set scene_id='$scene_id' where id=$rec_id");
		$logger->debug('$scene_id = ' . $scene_id);
	
		$json_arr = array(
				'action_name' => "QR_LIMIT_STR_SCENE",
				'action_info' => array(
						'scene' => array(
								'scene_str' => $scene_id
						)
				)
		);
		$data = json_encode($json_arr);
		$logger->debug('$data = ' . $data);
		$qr_code = $weixin_api->cr_qrcode($json_arr, "user_card_type");
		$logger->debug(json_encode($qr_code));
		
		if($qr_code->errcode == 0){
			$GLOBALS['db'] -> query("update wxch_scene set ticket='" . $qr_code->ticket . "' where id=$rec_id");
			$result['errcode'] = 0;
			$qr_path = ROOT_PATH . $qr_code->data;
			$base_name = basename($qr_path, ".jpg");
			$new_path = str_replace($base_name, $id, $qr_path);
			rename($qr_path, $new_path);
			$qr_path = str_replace(ROOT_PATH, "", $new_path);
			$result['qrcode'] = $qr_path;
	//		$result['valid_date'] = date('Y-m-d', $time + 2505600);
	//		$sql = "update " . $GLOBALS['ecs']->table('user_card_type') . " set qr_code='" . $result['qrcode'] . "', qr_code_valid_time='" . $result['valid_date'] . "'" .
	//				" where ct_id='$id'";
	//		$logger->debug($sql);
	//		$GLOBALS['db'] -> query($sql);
			$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('user_card_type'), array(
					"qr_code" => $result['qrcode']
			), 'UPDATE', " ct_id='$id'");
			echo json_encode($result);
		}else{
			echo json_encode($qr_code);
		}
	}
}
//用户推荐二维码
elseif($_REQUEST['act'] == 'user'){
	$id = $_REQUEST['id'];
	//生成场景
	$scene_param = array(
			'user_id' => $id,
	);
	$scene_param = json_encode($scene_param);
	$createtime = date("Y-m-d H:i:s", time());
	$endtime = date('Y-m-d H:i:s', time() + 2505600);
	$sql = "insert into wxch_scene (`type`, param, createtime, endtime) values (1, '$scene_param','$createtime', '$endtime')";
	$GLOBALS['db'] -> query($sql);
	$scene_id = $GLOBALS['db'] -> insert_id();
	$GLOBALS['db'] -> query("update wxch_scene set scene_id='$scene_id' where id=$scene_id");

	//临时二维码
	$json_arr = json_decode(json_encode(array(
			'action_name'=>"QR_SCENE",
			'expire_seconds'=>2592000,
			'action_info'=>array(
					'scene'=>array(
							'scene_id'=>$scene_id
					)
			)
	)));
	
	$data = json_encode($json_arr);
	$ret = $weixin_api->cr_qrcode($json_arr, "user");
	$logger->debug(json_encode($ret));
	if($ret->errcode == 0){
		$GLOBALS['db'] -> query("update wxch_scene set ticket='" . $ret->ticket . "' where id=$scene_id");
	}
	echo json_encode($ret);
}
//找回密码二维码
elseif($_REQUEST['act'] == 'get_account'){
	if(file_exists(dirname(__FILE__) . '/images/qrcode/qr_get_account.jpg')){
		//二维码已存在
		$result['errcode'] = 0;
		$result['qrcode'] = 'images/qrcode/qr_get_account.jpg';
		echo json_encode($result);
	}else{
		//生成场景
		$scene_param = array(
				'get_account' => 1,
		);
		$scene_param = json_encode($scene_param);
		$createtime = date("Y-m-d H:i:s", time());
		$sql = "insert into wxch_scene (`type`, param, createtime) values (2, '$scene_param','$createtime')";
		$GLOBALS['db'] -> query($sql);
		$rec_id = $GLOBALS['db'] -> insert_id();
		$scene_id = $weixin_api->cr_limit_scene_id();
		$GLOBALS['db'] -> query("update wxch_scene set scene_id='$scene_id' where id=$rec_id");
	
		//永久二维码
		$json_arr = json_decode(json_encode(array(
				'action_name'=>"QR_LIMIT_STR_SCENE",
				'action_info'=>array(
						'scene'=>array(
								'scene_str'=>$scene_id
						)
				)
		)));
		
		$data = json_encode($json_arr);
		$qr_code = $weixin_api->cr_qrcode($json_arr, "");
		$logger->debug(json_encode($qr_code));
		
		if($qr_code->errcode == 0){
			$GLOBALS['db'] -> query("update wxch_scene set ticket='" . $qr_code->ticket . "' where id=$rec_id");
			$result['errcode'] = 0;
			$qr_path = ROOT_PATH . $qr_code->data;
			$base_name = basename($qr_path, ".jpg");
			$new_path = str_replace($base_name, 'qr_get_account', $qr_path);
			rename($qr_path, $new_path);
			$qr_path = str_replace(ROOT_PATH, "", $new_path);
			$result['qrcode'] = $qr_path;
			echo json_encode($result);
		}else{
			echo json_encode($qr_code);
		}
	}
}
//用户推荐永久二维码，直接推送给客户
elseif($_REQUEST['act'] == 'user_limit_qr'){
	$id = $_REQUEST['id'];
	$wxid = $GLOBALS['db']->getOne("SELECT u.wxid FROM " . 
				$GLOBALS['ecs']->table('users') . " u WHERE u.user_id='$id'");
	if(file_exists(dirname(__FILE__) . '/images/qrcode/user/' . $id . '.jpg')){
		//二维码已存在
		$qr_path = ROOT_PATH . 'images/qrcode/user/' . $id . '.jpg';
		$send = $weixin_api->send_custom_image($wxid, $qr_path, 0);
		$ret->errcode = 0;
		$ret->qrcode = 'images/qrcode/user/' . $id . '.jpg';
		if($send->errcode == 0){
			$ret->message = '二维码已通过公众号推送给用户';
			$weixin_api->send_custom_message($wxid, "此用户专属推广二维码为永久二维码，请另存到手机以免遗失");
		}
		echo json_encode($ret);
	}else{
		//生成场景
		$scene_param = array(
				'user_id' => $id,
		);
		$scene_param = json_encode($scene_param);
		$createtime = date("Y-m-d H:i:s", time());
		$endtime = date('Y-m-d H:i:s', time() + 2505600);
		$sql = "insert into wxch_scene (`type`, param, createtime) values (2, '$scene_param','$createtime')";
		$GLOBALS['db'] -> query($sql);
		$rec_id = $GLOBALS['db'] -> insert_id();
		$scene_id = $weixin_api->cr_limit_scene_id();
		$GLOBALS['db'] -> query("update wxch_scene set scene_id='$scene_id' where id=$rec_id");
	
		//永久二维码
		$json_arr = json_decode(json_encode(array(
				'action_name'=>"QR_LIMIT_STR_SCENE",
				'action_info'=>array(
						'scene'=>array(
								'scene_str'=>$scene_id
						)
				)
		)));
		
		$data = json_encode($json_arr);
		$ret = $weixin_api->cr_qrcode($json_arr, "user");
		$logger->debug(json_encode($ret));
		if($ret->errcode == 0){
			//重命名二维码图片文件
			$qr_path = ROOT_PATH . $ret->data;
			$base_name = basename($qr_path, ".jpg");
			$new_path = str_replace($base_name, $id, $qr_path);
			rename($qr_path, $new_path);
			
			$GLOBALS['db'] -> query("update wxch_scene set ticket='" . $ret->ticket . "' where id=$rec_id");
			$send = $weixin_api->send_custom_image($wxid, $new_path, 0);
			$qr_path = str_replace(ROOT_PATH, "", $new_path);
			$ret->qrcode = $qr_path;
			
			if($send->errcode == 0){
				$ret->message = '二维码已通过公众号推送给用户';
				$weixin_api->send_custom_message($wxid, "此用户专属推广二维码为永久二维码，请另存到手机以免遗失");
			}
		}
		echo json_encode($ret);
	}
}
//购物流程注册二维码
elseif($_REQUEST['act'] == 'user_cart'){
	$user_id = $_REQUEST['user_id'];
	$cart_sid = $_REQUEST['cart_sid'];
	
	//生成场景
	$scene_param = array(
			'cart_session_id' => $cart_sid,
			'scan_limit' => 1
	);
	if(!empty($user_id)){
		$scene_param['user_id'] = $user_id;
	}
	//生成临时二维码, 每次请求都重新生成
	$scene_param = json_encode($scene_param);
	$createtime = date("Y-m-d H:i:s", time());
	$endtime = date('Y-m-d H:i:s', time() + 2505600);
	$sql = "insert into wxch_scene (`type`, param, createtime, endtime) values (1, '$scene_param','$createtime', '$endtime')";
	$GLOBALS['db'] -> query($sql);
	$scene_id = $GLOBALS['db'] -> insert_id();
	$GLOBALS['db'] -> query("update wxch_scene set scene_id='$scene_id' where id=$scene_id");

	//临时二维码
	$json_arr = json_decode(json_encode(array(
			'action_name'=>"QR_SCENE",
			'expire_seconds'=>2592000,
			'action_info'=>array(
					'scene'=>array(
							'scene_id'=>$scene_id
					)
			)
	)));
	
	$data = json_encode($json_arr);
	$ret = $weixin_api->cr_qrcode($json_arr, "user");
	$logger->debug(json_encode($ret));
	if($ret->errcode == 0){
		$GLOBALS['db'] -> query("update wxch_scene set ticket='" . $ret->ticket . "' where id=$scene_id");
	}
	echo json_encode($ret);
}
//拼团二维码
elseif($_REQUEST['act'] == 'pintuan'){
	$user_id = $_REQUEST['user_id'];
	$pintuan_id = $_REQUEST['pintuan_id'];
	
	//生成场景
	$scene_param = array(
			'pintuan_id' => $pintuan_id
	);
	if(!empty($user_id)){
		$scene_param['user_id'] = $user_id;
	}
	//生成临时二维码, 每次请求都重新生成
	$scene_param = json_encode($scene_param);
	$createtime = date("Y-m-d H:i:s", time());
	$endtime = date('Y-m-d H:i:s', time() + 2505600);
	$sql = "insert into wxch_scene (`type`, param, createtime, endtime) values (1, '$scene_param','$createtime', '$endtime')";
	$GLOBALS['db'] -> query($sql);
	$scene_id = $GLOBALS['db'] -> insert_id();
	$GLOBALS['db'] -> query("update wxch_scene set scene_id='$scene_id' where id=$scene_id");

	//临时二维码
	$json_arr = json_decode(json_encode(array(
			'action_name'=>"QR_SCENE",
			'expire_seconds'=>2592000,
			'action_info'=>array(
					'scene'=>array(
							'scene_id'=>$scene_id
					)
			)
	)));
	
	$data = json_encode($json_arr);
	$ret = $weixin_api->cr_qrcode($json_arr, "user");
	$logger->debug(json_encode($ret));
	if($ret->errcode == 0){
		$GLOBALS['db'] -> query("update wxch_scene set ticket='" . $ret->ticket . "' where id=$scene_id");
	}
	echo json_encode($ret);
}
//PC端二维码注册、登录
elseif($_REQUEST['act'] == 'qr_event'){
	$logger->debug("qr_event");
	$user_id = $_REQUEST['user_id'];
	$uuid = $_REQUEST['uuid'];
	$type = $_REQUEST['type'];
	
	//生成场景
	$scene_param = array('scan_limit' => 1);
	if(!empty($user_id)){
		$scene_param['user_id'] = $user_id;
	}
	if($type == 1){
		$scene_param['login_uuid'] = $uuid;
	}elseif($type == 2){
		$scene_param['register_uuid'] = $uuid;
	}elseif($type == 3){
		$scene_param['bind_oath_user_uuid'] = $uuid;
	}
	//生成临时二维码, 每次请求都重新生成
	$scene_param = json_encode($scene_param);
	$createtime = date("Y-m-d H:i:s", time());
	$endtime = date('Y-m-d H:i:s', time() + 2505600);
	$sql = "insert into wxch_scene (`type`, param, createtime, endtime) values (1, '$scene_param','$createtime', '$endtime')";
	$GLOBALS['db'] -> query($sql);
	$scene_id = $GLOBALS['db'] -> insert_id();
	$GLOBALS['db'] -> query("update wxch_scene set scene_id='$scene_id' where id=$scene_id");

	//临时二维码
	$json_arr = json_decode(json_encode(array(
			'action_name'=>"QR_SCENE",
			'expire_seconds'=>2592000,
			'action_info'=>array(
					'scene'=>array(
							'scene_id'=>$scene_id
					)
			)
	)));
	
	$data = json_encode($json_arr);
	$ret = $weixin_api->cr_qrcode($json_arr, "user");
	$logger->debug(json_encode($ret));
	if($ret->errcode == 0){
		$GLOBALS['db'] -> query("update wxch_scene set ticket='" . $ret->ticket . "' where id=$scene_id");
	}
	echo json_encode($ret);
}
//分享文章附带的二维码
elseif($_REQUEST['act'] == 'article'){
	$logger->debug("article");
	$user_id = $_REQUEST['user_id'];
	$article_id = $_REQUEST['id'];
	
	//生成场景
	$scene_param = array();
	if(!empty($user_id)){
		$scene_param['user_id'] = $user_id;
	}
	if(!empty($article_id)){
		$scene_param['article_id'] = $article_id;
	}
	//生成临时二维码, 每次请求都重新生成
	$scene_param = json_encode($scene_param);
	$createtime = date("Y-m-d H:i:s", time());
	$endtime = date('Y-m-d H:i:s', time() + 2505600);
	$sql = "insert into wxch_scene (`type`, param, createtime, endtime) values (1, '$scene_param','$createtime', '$endtime')";
	$GLOBALS['db'] -> query($sql);
	$scene_id = $GLOBALS['db'] -> insert_id();
	$GLOBALS['db'] -> query("update wxch_scene set scene_id='$scene_id' where id=$scene_id");

	//临时二维码
	$json_arr = json_decode(json_encode(array(
			'action_name'=>"QR_SCENE",
			'expire_seconds'=>2592000,
			'action_info'=>array(
					'scene'=>array(
							'scene_id'=>$scene_id
					)
			)
	)));
	
	$data = json_encode($json_arr);
	$ret = $weixin_api->cr_qrcode($json_arr, "user");
	$logger->debug(json_encode($ret));
	if($ret->errcode == 0){
		$GLOBALS['db'] -> query("update wxch_scene set ticket='" . $ret->ticket . "' where id=$scene_id");
	}
	echo json_encode($ret);
}
//代理认证二维码
elseif($_REQUEST['act'] == 'suppliers_agent'){
	$logger->debug("suppliers_agent");
	$suppliers_id = $_REQUEST['suppliers_id'];
	
	//生成场景
	$scene_param = array();
	if(!empty($suppliers_id)){
		$scene_param['suppliers_agent'] = $suppliers_id;
	}
	
	//生成临时二维码, 每次请求都重新生成
	$scene_param = json_encode($scene_param);
	$createtime = date("Y-m-d H:i:s", time());
	$endtime = date('Y-m-d H:i:s', time() + 2505600);
	$sql = "insert into wxch_scene (`type`, param, createtime, endtime) values (1, '$scene_param','$createtime', '$endtime')";
	$GLOBALS['db'] -> query($sql);
	$scene_id = $GLOBALS['db'] -> insert_id();
	$GLOBALS['db'] -> query("update wxch_scene set scene_id='$scene_id' where id=$scene_id");

	//临时二维码
	$json_arr = json_decode(json_encode(array(
			'action_name'=>"QR_SCENE",
			'expire_seconds'=>2592000,
			'action_info'=>array(
					'scene'=>array(
							'scene_id'=>$scene_id
					)
			)
	)));
	
	$data = json_encode($json_arr);
	$ret = $weixin_api->cr_qrcode($json_arr, "suppliers");
	$logger->debug(json_encode($ret));
	if($ret->errcode == 0){
		$GLOBALS['db'] -> query("update wxch_scene set ticket='" . $ret->ticket . "' where id=$scene_id");
	}
	echo json_encode($ret);
}