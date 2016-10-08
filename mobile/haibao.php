<?php
define('IN_ECTOUCH', true);
require (dirname(__FILE__) . '/include/init.php');
require (dirname(__FILE__) . '/api/haibao_api.php');
$logger = LoggerManager::getLogger('haibao.php');

/**
 * 生成海报，在微信公众号上推送给客户
 */

$act = isset($_REQUEST['act']) ? $_REQUEST['act'] : 'list';
if($act == 'list'){
	//海报模板列表查询
	$all_row = $db->getAll("select * from wxch_haibao order by startdate desc");
	$smarty->assign('listData', $all_row);
	$smarty->display('haibao_list.htm');

}else if($act == 'creat_by_goods'){
	$logger->debug('根据产品生成海报');
	// 通过产品图片生成海报
	$goods_id = $_REQUEST['goods_id'];
	if(empty($goods_id)){
		die (json_encode(array(
				'errcode'=>1,
				'errmsg'=>'缺少商品ID'
		)));
	}
	$user_id = $_SESSION['user_id'];
	if(empty($user_id)){
		die (json_encode(array(
				'errcode'=>1,
				'errmsg'=>'请先登录或注册！'
		)));
	}

	$haibao_api = new haibao_api();
	$ret = $haibao_api->cr_user_product_haibao($user_id, $goods_id);
	echo json_encode($ret);
}

/**
 * 生成用户头像和推广二维码
 *
 * @param unknown $user_id
 */
function create_qr_headimg($user_id, $goods_id){
	$logger = LoggerManager::getLogger('haibao.php');
	$logger->debug('生成头像和二维码');
	$type = 'tj';
	$result = array();
	$user_wxid = $GLOBALS['db']->getOne("SELECT `wxid` FROM " . $GLOBALS['ecs']->table('users') . " WHERE `user_id`='$user_id'");
	$user_name = $GLOBALS['db']->getOne("SELECT `user_name` FROM " . $GLOBALS['ecs']->table('users') . " WHERE `user_id`='$user_id'");
	$scene = $user_name;
	$result['nickname'] = $GLOBALS['db']->getOne("SELECT `nickname` FROM `wxch_user` WHERE `wxid`='$user_wxid'");
// 	$qr_path = $GLOBALS['db']->getOne("SELECT `qr_path` FROM `wxch_qr_tianxin100` WHERE `scene_id`='$user_id'");
	access_token();
	$time = time();
	$logger->debug('$user_wxid = ' . $user_wxid);

	$headimgurl = $GLOBALS['db']->getOne("SELECT `headimgurl` FROM `wxch_user` WHERE `wxid`='$user_wxid'");
	$logger->debug('$headimgurl = ' . $headimgurl);

	//Add by ZhangNu 如果头像或者昵称不存成，刷新数据
	if ( empty ( $headimgurl ) || empty($result ['nickname']))
	{
		$retUserInfo =refreshWxInfo($user_wxid);
		$headimgurl = $retUserInfo[headimgurl];
		$result ['nickname'] = $retUserInfo[nickname];
	}

	if(!empty($headimgurl)){
		// 设置获取微信头像的大小
		$pic_param_pos = strrpos($headimgurl, '/');
		if(strlen($headimgurl) - $pic_param_pos < 5){
			$headimgurl = substr($headimgurl, 0, $pic_param_pos) . '/132';
		}
		$logger->debug('$headimgurl = ' . $headimgurl);
		$h_imageinfo = downloadimageformweixin($headimgurl);
		$h_local_file = fopen(ROOT_PATH . 'images/qrcode/head/' . $time . '.jpg', 'a');
		$logger->debug('$h_local_file = ' . 'images/qrcode/head/' . $time . '.jpg');
		if(false !== fwrite($h_local_file, $h_imageinfo)){
			fclose($local_file);
			$result['headimgurl'] = 'images/qrcode/head/' . $time . '.jpg';
		}else{
			$result['errcode'] = 1;
			$result['errmsg'] = "保存二维码和头像图片失败,检查fwrite函数是否生效请重试";
			return $result;
		}
	}else{
		$result['errcode'] = 1;
		$result['errmsg'] = "获取头像失败，请先登录微信商城后重试";
		return $result;
	}

// 	$logger->debug('$qr_path = ' . $qr_path);
// 	if(!empty($qr_path) && file_exists(ROOT_PATH . "images/qrcode/" . $qr_path)){
// 		$result['qrcode'] = "images/qrcode/" . $qr_path;
// 	}else{
		$logger->debug('重新生成二维码');
		$scene_param = array(
				'user_id' => $user_id,
				'goods_id' => $goods_id
		);
		$scene_param = json_encode($scene_param);
		$createtime = date("Y-m-d H:i:s", time());
		$endtime = date("Y-m-d H:i:s", time() + 604800);
		$sql = "insert into wxch_scene (param, createtime, endtime) values ('$scene_param','$createtime', '$endtime')";
		$GLOBALS['db'] -> query($sql);
		$scene_id = $GLOBALS['db'] -> insert_id();
		$GLOBALS['db'] -> query("update wxch_scene set scene_id='$scene_id' where id=$scene_id");
		$logger->debug('$scene_id = ' . $scene_id);

		$action_name = "QR_SCENE";//临时二维码
// 		$scene_id = scene_encode(array($user_id, $goods_id));
		$json_arr = array(
				'action_name' => $action_name,
				'expire_seconds' => 2592000,
				'action_info' => array(
						'scene' => array(
								'scene_id' => $scene_id
						)
				)
		);
		$data = json_encode($json_arr);
		$logger->debug('$data = ' . $data);
		$ret = $GLOBALS['db']->getRow("SELECT `access_token` FROM `wxch_config`");
		$access_token = $ret['access_token'];
		if(strlen($access_token) >= 64){
			$url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=' . $access_token;
			$res_json = curl_grab_page($url, $data);

			$json = json_decode($res_json);
		}
		$ticket = $json->ticket;

		if($ticket){
			$ticket_url = urlencode($ticket);
			$ticket_url = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . $ticket_url;
			$imageinfo = downloadimageformweixin($ticket_url);
			if(empty($imageinfo)){
				$result['errcode'] = 1;
				$result['errmsg'] = "下载二维码失败，请检查服务器环境后重试";
				return $result;
			}else{
// 				$surl = $GLOBALS['_CFG']['site_url'] . 'mobile/images/qrcode/' . $time . '.jpg';
				$local_file = fopen(ROOT_PATH . 'images/qrcode/' . $time . '.jpg', 'a');
				if(false !== $local_file){
					if(false !== fwrite($local_file, $imageinfo)){
						fclose($local_file);
						$sql = "update wxch_scene set ticket = '$ticket' where id='$scene_id'";
						$GLOBALS['db'] -> query($sql);

						$result['qrcode'] = 'images/qrcode/' . $time . '.jpg';
					}else{
						$result['errcode'] = 1;
						$result['errmsg'] = "保存二维码和头像图片失败,检查fwrite函数是否生效请重试";
						return $result;
					}
				}else{
					$result['errcode'] = 1;
					$result['errmsg'] = "保存二维码图片的路径images/qrcode或images/qrcode/head没可写权限，请修改！";
					return $result;
				}
			}
		}else{
			$result['errcode'] = 1;
			$result['errmsg'] = "获取ticket失败检查appid和appsecret是否正确";
			return $result;
		}
// 	}

	$result['errcode'] = 0;
	return $result;
}

/**
 * 生成海报
 *
 * @param unknown $img_info
 */
function create_haibao($img_info){
	$logger = LoggerManager::getLogger('haibao.php');
	$logger->debug('生成海报');
	$logger->debug('$img_info = ' . json_encode($img_info));
	// 主图
	$goods_imgsrc = ROOT_PATH . "../" . $img_info['goods_img'];
	$goods_price = $img_info['goods_price'];
	$width = 473;
	$height = 473;
	$time = time();
	$g_name = resizejpg($goods_imgsrc, $width, $height, $time);

	$imgsrc = ROOT_PATH . $img_info['qrcode'];
	$h_imgsrc = ROOT_PATH . $img_info['headimgurl'];
	// 二维码大小
	$width = 140;
	$height = 140;
	$name = resizejpg($imgsrc, $width, $height, $time . "_1");
	$imgs = $name;
	// 处理头像
	$width = 84;
	$height = 84;
	$h_imgs = resizejpg($h_imgsrc, $width, $height, $time . "_2");
	$target = ROOT_PATH . $img_info['target_img']; // 背景图片
	$target_img = Imagecreatefromjpeg($target);
	$source = Imagecreatefromjpeg($imgs);
	$h_source = Imagecreatefromjpeg($h_imgs);
	$g_source = Imagecreatefromjpeg($g_name);
	$h_bgimg = imagecreatetruecolor(90, 90);
	$h_bg_color = imagecolorAllocate($h_bgimg, 255, 255, 255);
	imagefill($h_bgimg, 0, 0, $h_bg_color);
	imagecopy($target_img, $g_source, 27, 90, 0, 0, 473, 473);
	imagecopy($target_img, $source, 348, 632, 0, 0, 140, 140);
	imagecopy($target_img, $h_bgimg, 55, 519, 0, 0, 90, 90);
	imagecopy($target_img, $h_source, 58, 522, 0, 0, 84, 84);
	$fontfile = ROOT_PATH . "wryh.ttf";
	// 水印文字
	$nickname = $img_info['nickname'];

	// 打水印
	$textcolor = imagecolorallocate($target_img, 0, 0, 0);
	$nickname_lenth = (strlen($nickname) + mb_strlen($nickname, 'UTF8')) / 2; // strlen：utf-8编码下每个中文字符所占字节为3
// 	$logger->debug("strlen($nickname) = " . $nickname_lenth);
	$nickname_show = $nickname;
	$left = $nickname_lenth * 4; // 一个字节长度的字符占8像素
	if($left > 60){
		$left = 60;
		// 调整昵称长度
		$mb_len = mb_strlen($nickname_show, 'UTF8');
		for($idx = 1; $idx < $mb_len; $idx ++){
			$temp = mb_substr($nickname_show, 0, $mb_len - $idx, 'UTF8');
			$temp_len = (strlen($temp) + mb_strlen($temp, 'UTF8')) / 2;
			if($temp_len * 4 < 50){
				$nickname_show = $temp . '**';
				break;
			}
		}
	}

	//昵称
	imagettftext($target_img, 14, 0, 94 - $left, 630, $textcolor, $fontfile, $nickname_show);
	//商品价格
	$textcolor1 = imagecolorallocate($target_img, 255, 0, 0);
	//去掉右侧的0

	$goods_price = floatval($goods_price) . '';
// 	$logger->debug("strlen($goods_price) = " . strlen($goods_price));
	if(strpos($goods_price, '.') > 0){
		$left = (strlen($goods_price) - 4) * 20 + 10;
	}else{
		$left = (strlen($goods_price) - 3) * 20;
	}
	imagettftext($target_img, 16, 0, 217 - $left, 755, $textcolor1, $fontfile, '￥');
	imagettftext($target_img, 30, 0, 240 - $left, 756, $textcolor1, $fontfile, floatval($goods_price));

	Imagejpeg($target_img, ROOT_PATH . 'images/haibao/' . $time . '.jpg', 100);

	//删除过程中的图片
	unlink(ROOT_PATH . "images/" . $time . ".jpg");
	unlink(ROOT_PATH . "images/" . $time . "_1" . ".jpg");
	unlink(ROOT_PATH . "images/" . $time . "_2" . ".jpg");
	unlink($h_imgsrc);
	unlink($imgsrc);

	return 'images/haibao/' . $time . '.jpg';
}

/**
 * 刷新token
 */
function access_token(){
	$ret = $GLOBALS['db']->getRow("SELECT * FROM `wxch_config` WHERE `id` = 1");
	$appid = $ret['appid'];
	$appsecret = $ret['appsecret'];
	$dateline = $ret['dateline'];
	$time = time();
	if(($time - $dateline) > 7200){
		$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$appsecret";
		$ret_json = curl_get_contents($url);
		$ret = json_decode($ret_json);
		if($ret->access_token){
			$GLOBALS['db']->query("UPDATE `wxch_config` SET `access_token` = '$ret->access_token',`dateline` = '$time' WHERE `wxch_config`.`id` =1;");
		}
	}
}

function curl_get_contents($url){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_TIMEOUT, 1);
	curl_setopt($ch, CURLOPT_USERAGENT, _USERAGENT_);
	curl_setopt($ch, CURLOPT_REFERER, _REFERER_);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	if(defined('CURLOPT_IPRESOLVE') && defined('CURL_IPRESOLVE_V4')){
		curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
	}
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	$r = curl_exec($ch);
	curl_close($ch);
	return $r;
}

function downloadimageformweixin($url){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_URL, $url);
	ob_start();
	curl_exec($ch);
	$return_content = ob_get_contents();
	ob_end_clean();

	$return_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	return $return_content;
}

function resizejpg($imgsrc, $imgwidth, $imgheight, $time){
	// $imgsrc jpg格式图像路径 $imgdst jpg格式图像保存文件名 $imgwidth要改变的宽度 $imgheight要改变的高度
	// 取得图片的宽度,高度值
	$arr = getimagesize($imgsrc);
// 	header("Content-type: image/jpg");
	$imgWidth = $imgwidth;
	$imgHeight = $imgheight;
	$imgsrc = imagecreatefromjpeg($imgsrc);
	$image = imagecreatetruecolor($imgWidth, $imgHeight);
	imagecopyresampled($image, $imgsrc, 0, 0, 0, 0, $imgWidth, $imgHeight, $arr[0], $arr[1]);
	$name = ROOT_PATH . "images/" . $time . ".jpg";
	Imagejpeg($image, $name, 100);
	return $name;
}

function curl_grab_page($url, $data, $proxy = '', $proxystatus = '', $ref_url = ''){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
	curl_setopt($ch, CURLOPT_TIMEOUT, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	if($proxystatus == 'true'){
		curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, true);
		curl_setopt($ch, CURLOPT_PROXY, $proxy);
	}
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_URL, $url);
	if(!empty($ref_url)){
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_REFERER, $ref_url);
	}
	if(defined('CURLOPT_IPRESOLVE') && defined('CURL_IPRESOLVE_V4')){
		curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
	}
	curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	ob_start();
	return curl_exec($ch);
	ob_end_clean();
	curl_close($ch);
	unset($ch);
}

/**
 * 推送海报
 */
function send($user_id, $haibao_dir){
	$time = time();
	$logger = LoggerManager::getLogger('haibao.php');
	$data = ROOT_PATH . $haibao_dir;
	$user_wxid = $GLOBALS['db']->getOne("SELECT `wxid` FROM " . $GLOBALS['ecs']->table('users') . " WHERE `user_id`='$user_id'");
	require_once (ROOT_PATH . 'api/weixin_api.php');
	$weixin_api = new weixin_api();
	$ret = $weixin_api->send_custom_image($user_wxid, $data);
	if($ret->errmsg == 'ok'){
		return $ret;
	}else{
		$ret['errcode'] = 1;
		$ret['errmsg'] = "请联系青山老农技术修改代码";
		return $ret;
	}
}

function https_request($url, $data = null){
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
	if(!empty($data)){
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
	}
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	$output = curl_exec($curl);
	curl_close($curl);
	return $output;
}



//重新获取更新用户的头像等微信信息
function refreshWxInfo($wxid) {
	if (! empty ( $wxid )) {
		access_token ( $db );
		$ret = $db->getRow ( "SELECT * FROM `wxch_config` WHERE `id` = 1" );
		$access_token = $ret ['access_token'];
		$url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=$access_token&openid=$wxid";
		$res_json = curl_get_contents ( $url );
		$w_user = json_decode ( $res_json, TRUE );
		if ($w_user ['errcode'] == '40001') {
			$access_token = new_access_token ( $db );
			$url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=$access_token&openid=$wxid";
			$res_json = curl_get_contents ( $url );
			$w_user = json_decode ( $res_json, TRUE );
		}

		$logger->debug('$w_user = ' . $w_user);

		$ecs_users = $ecs->prefix . 'users';
		$w_sql = "UPDATE  `wxch_user` SET  `nickname` =  '$w_user[nickname]',`sex` =  '$w_user[sex]',`city` =  '$w_user[city]',`country` =  '$w_user[country]',`province` =  '$w_user[province]',`language` =  '$w_user[language]',`headimgurl` =  '$w_user[headimgurl]',`localimgurl` = '$localimgurl', `subscribe_time` =  '$w_user[subscribe_time]' WHERE `wxid` = '$wxid';";
		$db->query ( $w_sql );
		return w_user;
	}
}

