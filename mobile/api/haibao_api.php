<?php
require_once ('weixin_api.php');
class haibao_api{
	private $logger;
	private $weixin_api;
	private $img_path;
	private $font_file;

	public function __construct(){
		$this->logger = LoggerManager::getLogger('haibao_api.php');
		$this->weixin_api = new weixin_api();
		$this->img_path = dirname(__FILE__) . "/../images/haibao/";
		$this->font_file = dirname(__FILE__) . "/../../wechat/wryh.ttf";
//		$this->font_file = dirname(__FILE__) . "/../../wechat/SymbolTigerExpert.ttf";
	}

	/**
	 * 生成用户专属海报
	 * @param string $user_id 用户ID
	 */
	function cr_user_haibao($user_id){
		//生成场景
		$scene_param = array(
				'user_id' => $user_id,
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
		$qrcode = $this->weixin_api->cr_qrcode($json_arr, "");
		$this->logger->debug(json_encode($qrcode));
		if($qrcode->errcode > 0){
			return $qrcode;
		}else{
			$GLOBALS['db'] -> query("update wxch_scene set ticket='" . $qrcode->ticket . "' where id=$scene_id");
			$qrcode = dirname(__FILE__) . "/../" . $qrcode->data;
		}
		
		//获取头像
		$h_path  = $this->img_path . $user_id . '.jpg';
		$this->logger->debug('$h_path = ' . $h_path);
		$h_local_file = fopen($h_path, 'a');
		$wx_user = $GLOBALS['db']->getRow("SELECT wu.`headimgurl`, wu.`nickname`, wu.`wxid` FROM `wxch_user` wu, " . 
				$GLOBALS['ecs']->table('users') . " u WHERE u.wxid=wu.wxid and u.user_id='$user_id'");
		$openid = $wx_user['wxid'];
		$retUserInfo = $this->weixin_api->refreshWxInfo($openid);
		$headimgurl = $retUserInfo['headimgurl'];
		$nickname = $retUserInfo['nickname'];
//		$headimgurl = $wx_user['headimgurl'];
//		$nickname = $wx_user['nickname'];
//		if ( empty ( $headimgurl ) || empty($nickname)) {
//			//重新获取更新用户的头像等微信信息
//			$retUserInfo = $this->weixin_api->refreshWxInfo($openid);
//			$headimgurl = $retUserInfo->headimgurl;
//			$nickname = $retUserInfo->nickname;
//		}
	
		$this->logger->debug(json_encode($wx_user));
		if(empty($headimgurl)){
			return json_decode(json_encode(array(
					"errcode" => 1,
					"errmsg" => "获取头像失败，请先登录微信商城后重试",
			)));
		}
		//设置获取微信头像的大小
		$pic_param_pos = strrpos($headimgurl, '/');
		if(strlen($headimgurl) - $pic_param_pos < 5){
			$headimgurl = substr($headimgurl, 0, $pic_param_pos) . '/132';
		}
		$h_imageinfo = $this->weixin_api -> downloadimageformweixin($headimgurl);
		if(false ===fwrite($h_local_file, $h_imageinfo)){
			return json_decode(json_encode(array(
					"errcode" => 1,
					"errmsg" => "保存二维码和头像图片失败,检查fwrite函数是否生效请重试",
			)));
		}
		fclose($h_local_file);
		
		//查询海报模板配置
		$now_date = date('Y-m-d', time());
		$sql = "select * from wxch_haibao where keyword='user_haibao' and startdate <='$now_date' and '$now_date'<=enddate order by startdate desc limit 1";
		$row = $GLOBALS['db']->getRow($sql);
		$bg_image = dirname(__FILE__) . "/../" . $row['image'];
		$this->logger->debug('$bg_image = ' . $bg_image);
		$this->logger->debug('$row = ' . json_encode($row));
		$head_size = intval($row['head_size']);
		$head_x = intval($row['head_x']);
		$head_y = intval($row['head_y']);
		$qr_size = intval($row['qr_size']);
		$qr_x = intval($row['qr_x']);
		$qr_y = intval($row['qr_y']);
		
		//转换二维码和头像尺寸
		$qr_img_name = $user_id . "_qr";
		$qr_img = $this->resizejpg($qrcode, $qr_size, $qr_size, $qr_img_name);
		$qr_img_name = $user_id . "_head";
		$h_img=$this->resizejpg($h_path, $head_size, $head_size, $qr_img_name);
		
		//图片合成
		$target_img = Imagecreatefromjpeg($bg_image);
		$qr_source = Imagecreatefromjpeg($qr_img);
		$h_source = Imagecreatefromjpeg($h_img);
		imagecopy($target_img,$qr_source,$qr_x,$qr_y,0,0,$qr_size,$qr_size);
		imagecopy($target_img,$h_source,$head_x,$head_y,0,0,$head_size,$head_size);//35, 520
		
		//昵称
		$textcolor = imagecolorallocate($target_img, 0, 0, 0);
		$nickname_lenth = (strlen($nickname) + mb_strlen($nickname,'UTF8')) / 2;	//strlen：utf-8编码下每个中文字符所占字节为3
		$this->logger->debug("strlen($nickname) = " . $nickname_lenth);
		$nickname_show = $nickname;
		$left = $nickname_lenth * 4;//一个字节长度的字符占8像素
		if($left > 60) {
			$left = 60;
			//调整昵称长度
			$mb_len = mb_strlen($nickname_show, 'UTF8');
			for ($idx = 1; $idx < $mb_len; $idx++){
				$temp = mb_substr($nickname_show, 0, $mb_len-$idx, 'UTF8');
				$temp_len = (strlen($temp) + mb_strlen($temp,'UTF8')) / 2;
				if($temp_len * 4 < 50){
					$nickname_show = $temp . '**';
					break;
				}
			}
		}
		$this->logger->debug('$nickname_show = ' . $nickname_show);
		$this->logger->debug('$this->font_file = ' . $this->font_file);
		imagettftext($target_img,14,0,$head_x + $head_size/2 -5 - $left,$head_y + $head_size + 30,
				$textcolor,$this->font_file,$nickname_show);
		
		//落地海报图片
		$target_img_path = $this->img_path . $user_id . '_haibao.jpg';
		Imagejpeg($target_img, $target_img_path);
		
		//推送海报图片
		$this->weixin_api->send_custom_image($openid, $target_img_path, 0);
		$this->weixin_api->send_custom_message($openid, 
				"提醒：个人专属海报二维码非永久二维码，有效期至 " . date('Y年m月d日', time() + 2505600) . "，超过有效期请重新生成海报！", 0);
		
		//删除生成海报过程中的图片
		$this->logger->debug("删除生成海报过程中的图片");
		$this->logger->debug ($qrcode);
		$this->logger->debug ($qr_img); 
		$this->logger->debug ($h_path); 
		$this->logger->debug ($h_img); 
		$this->logger->debug ($target_img_path); 
		@unlink ($qrcode); 
		@unlink ($qr_img); 
		@unlink ($h_path); 
		@unlink ($h_img); 
		@unlink ($target_img_path); 
		
		//将生成的二维码图片的地址放到数据库中
		$insert_sql = "INSERT INTO `wxch_qr_tianxin100` (`qr_path`,`scene`,`scene_id`, `nickname`) VALUES
				('$target_img_path','$scene_param', '$scene_id','$nickname')";
		$GLOBALS['db']->query($insert_sql);

		$ret = array(
				"errcode" => 0,
				"errmsg" => "",
				"data" => $target_img_path
		);
		return json_decode(json_encode($ret));
	}

	/**
	 * 生成用户产品海报
	 * @param string $user_id 用户ID
	 * @param string $goods_id 商品ID
	 */
	function cr_user_product_haibao($user_id, $goods_id){
		// 查询产品图片和价格
		$sql = "select
			case
				when is_promote=1 and promote_start_date <= UNIX_TIMESTAMP() and UNIX_TIMESTAMP()<promote_end_date
					then promote_price
				else shop_price END
			as shop_price, goods_img, goods_name, market_price
			from " . $GLOBALS['ecs']->table('goods') . " where goods_id = '$goods_id'";
		$row = $GLOBALS['db']->getRow($sql);
		$goods_img = $row['goods_img'];
		$goods_price = $row['shop_price'];
		$goods_name = $row['goods_name'];
		$market_price = $row['market_price'];
		$this->logger->debug('$goods_img = ' . $goods_img);
		$this->logger->debug('$goods_price = ' . $goods_price);
	
		//生成场景
		$scene_param = array(
				'user_id' => $user_id,
				'goods_id' => $goods_id
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
		$qrcode = $this->weixin_api->cr_qrcode($json_arr, "");
		$this->logger->debug(json_encode($qrcode));
		if($qrcode->errcode > 0){
			return $qrcode;
		}else{
			$GLOBALS['db'] -> query("update wxch_scene set ticket='" . $qrcode->ticket . "' where id=$scene_id");
			$qrcode = dirname(__FILE__) . "/../" . $qrcode->data;
		}
		
		//获取头像
		$h_path  = $this->img_path . $user_id . '.jpg';
		$this->logger->debug('$h_path = ' . $h_path);
		$h_local_file = fopen($h_path, 'a');
		$wx_user = $GLOBALS['db']->getRow("SELECT wu.`headimgurl`, wu.`nickname`, wu.`wxid` FROM `wxch_user` wu, " . 
				$GLOBALS['ecs']->table('users') . " u WHERE u.wxid=wu.wxid and u.user_id='$user_id'");
		$openid = $wx_user['wxid'];
		$retUserInfo = $this->weixin_api->refreshWxInfo($openid);
		if($retUserInfo['subscribe'] == 0){
			return json_decode(json_encode(array(
					"errcode" => 1,
					"errmsg" => "操作失败，请先关注青山老农公众号",
			)));
		}
		$headimgurl = $retUserInfo['headimgurl'];
		$nickname = $retUserInfo['nickname'];
//		$headimgurl = $wx_user['headimgurl'];
//		$nickname = $wx_user['nickname'];
//		$this->logger->debug(json_encode($wx_user));
//		if ( empty ( $headimgurl ) || empty($nickname)) {
//			//重新获取更新用户的头像等微信信息
//			$retUserInfo = $this->weixin_api->refreshWxInfo($openid);
//			$headimgurl = $retUserInfo->headimgurl;
//			$nickname = $retUserInfo->nickname;
//		}
		if(empty($headimgurl)){
			return json_decode(json_encode(array(
					"errcode" => 1,
					"errmsg" => "获取头像失败，请先登录微信商城后重试",
			)));
		}
		//设置获取微信头像的大小
		$pic_param_pos = strrpos($headimgurl, '/');
		if(strlen($headimgurl) - $pic_param_pos < 5){
			$headimgurl = substr($headimgurl, 0, $pic_param_pos) . '/132';
		}
		$this->logger->debug('$headimgurl = ' . $headimgurl);
		$h_imageinfo = $this->weixin_api -> downloadimageformweixin($headimgurl);
		if(false ===fwrite($h_local_file, $h_imageinfo)){
			return json_decode(json_encode(array(
					"errcode" => 1,
					"errmsg" => "保存二维码和头像图片失败,检查fwrite函数是否生效请重试",
			)));
		}
		fclose($h_local_file);
		
		//查询海报模板配置
		$now_date = date('Y-m-d', time());
		$sql = "select * from wxch_haibao where keyword='product_haibao' and startdate <='$now_date' and '$now_date'<=enddate order by startdate desc limit 1";
		$this->logger->debug('$sql = ' . $sql);
		$row = $GLOBALS['db']->getRow($sql);
		$bg_image = dirname(__FILE__) . "/../" . $row['image'];
		$this->logger->debug('$bg_image = ' . $bg_image);
		$this->logger->debug('$row = ' . json_encode($row));
		$head_size = intval($row['head_size']);
		$head_x = intval($row['head_x']);
		$head_y = intval($row['head_y']);
		$qr_size = intval($row['qr_size']);
		$qr_x = intval($row['qr_x']);
		$qr_y = intval($row['qr_y']);
		$goods_size = intval($row['product_size']);
		$goods_x = intval($row['product_x']);
		$goods_y = intval($row['product_y']);
		
		//转换二维码和头像尺寸，产品图尺寸
		$qr_img_name = $user_id . "_qr";
		$qr_img = $this->resizejpg($qrcode, $qr_size, $qr_size, $qr_img_name);
		$qr_img_name = $user_id . "_head";
		$h_img=$this->resizejpg($h_path, $head_size, $head_size, $qr_img_name);
		$goods_img_name = $user_id . "_g_" . $goods_id;
		$goods_img_src = dirname(__FILE__) . "/../../" . $goods_img;
		$goods_img=$this->resizejpg($goods_img_src, $goods_size, $goods_size, $goods_img_name);
		
		//图片合成
		$target_img = Imagecreatefromjpeg($bg_image);
		$g_img = Imagecreatefromjpeg($goods_img);
		$qr_source = Imagecreatefromjpeg($qr_img);
		$h_source = Imagecreatefromjpeg($h_img);
		imagecopy($target_img,$g_img,$goods_x,$goods_y,0,0,$goods_size,$goods_size);
		$h_bg_img = imagecreatetruecolor($head_size +6, $head_size +6); //头像背景
		$h_bg_color = imagecolorAllocate($h_bg_img, 255, 255, 255);		//头像背景白色
		imagefill($h_bg_img, 0, 0, $h_bg_color);
		imagecopy($target_img, $h_bg_img, $head_x -3, $head_y -3, 0, 0, $head_size +6, $head_size +6);
		imagecopy($target_img,$qr_source,$qr_x,$qr_y,0,0,$qr_size,$qr_size);
		imagecopy($target_img,$h_source,$head_x,$head_y,0,0,$head_size,$head_size);//35, 520
		
		//昵称
		$textcolor = imagecolorallocate($target_img, 0, 0, 0);
		$nickname_lenth = (strlen($nickname) + mb_strlen($nickname,'UTF8')) / 2;	//strlen：utf-8编码下每个中文字符所占字节为3
		$this->logger->debug("strlen($nickname) = " . $nickname_lenth);
		$nickname_show = $nickname;
		$left = $nickname_lenth * 4;//一个字节长度的字符占8像素
		if($left > 60) {
			$left = 60;
			//调整昵称长度
			$mb_len = mb_strlen($nickname_show, 'UTF8');
			for ($idx = 1; $idx < $mb_len; $idx++){
				$temp = mb_substr($nickname_show, 0, $mb_len-$idx, 'UTF8');
				$temp_len = (strlen($temp) + mb_strlen($temp,'UTF8')) / 2;
				if($temp_len * 4 < 50){
					$nickname_show = $temp . '**';
					break;
				}
			}
		}
		$this->logger->debug('$nickname_show = ' . $nickname_show);
		$this->logger->debug('$this->font_file = ' . $this->font_file);
		imagettftext($target_img,14,0,$head_x + $head_size/2 -5 - $left,$head_y + $head_size + 30,
				$textcolor,$this->font_file,$nickname_show);
				
		//商品名称
		$textcolor2 = imagecolorallocate($target_img, 100, 100, 100);
		$goods_name_lenth = (strlen($goods_name) + mb_strlen($goods_name,'UTF8')) / 2;
		$goods_name_show = $goods_name;
		$left = $goods_name_lenth * 7.8;//一个字节长度的字符占8像素
		if($left > 300) {
			$left = 300;
			//调整商品名称长度
			$mb_len = mb_strlen($goods_name_show, 'UTF8');
			for ($idx = 1; $idx < $mb_len; $idx++){
				$temp = mb_substr($goods_name_show, 0, $mb_len-$idx, 'UTF8');
				$temp_len = (strlen($temp) + mb_strlen($temp,'UTF8')) / 2;
				if($temp_len * 7.8 < 290){
					$goods_name_show = $temp . '...';
					break;
				}
			}
		}
		$this->logger->debug('$goods_name_show = ' . $goods_name_show);
		$this->logger->debug('$left = ' . $left);
		imagettftext($target_img, 12, 0, 470 - $left, 595, $textcolor2, $this->font_file, $goods_name_show);
				
		//商品价格
		$textcolor1 = imagecolorallocate($target_img, 255, 0, 0);
		//去掉右侧的0
		$goods_price = floatval($goods_price) . '';
	// 	$this->logger->debug("strlen($goods_price) = " . strlen($goods_price));
		if(strpos($goods_price, '.') > 0){
			$left = (strlen($goods_price) - 4) * 20 + 10;
		}else{
			$left = (strlen($goods_price) - 3) * 20;
		}
		imagettftext($target_img, 16, 0, 217 - $left, 755, $textcolor1, $this->font_file, '￥');
		imagettftext($target_img, 30, 0, 240 - $left, 756, $textcolor1, $this->font_file, floatval($goods_price));
		
		if(floatval($goods_price) != floatval($market_price)){
			//市场价格
			$textcolor3 = imagecolorallocate($target_img, 100, 100, 100);
			//去掉右侧的0
			$market_price = '原价：' . floatval($market_price) . '';
			$market_price_lenth = (strlen($market_price) + mb_strlen($market_price,'UTF8')) / 2;
			$left = $market_price_lenth * 8;
			imagettftext($target_img, 16, 0, 180 - $left, 754, $textcolor3, $this->font_file, $market_price);
			imageline ($target_img , 180 - $left, 746, 205, 746 , 100);
		}
		
		//落地海报图片
		$target_img_path = $this->img_path . $user_id . '_g_' . $goods_id . '_haibao.jpg';
		Imagejpeg($target_img, $target_img_path);
		
		//推送海报图片
		$this->weixin_api->send_custom_image($openid, $target_img_path, 0);
		$this->weixin_api->send_custom_message($openid, 
				"提醒：产品海报二维码非永久二维码，有效期至 " . date('Y年m月d日', time() + 2505600) . "，超过有效期请重新生成海报！", 0);
		
		//删除生成海报过程中的图片
		$this->logger->debug("删除生成海报过程中的图片");
		$this->logger->debug ($qrcode);
		$this->logger->debug ($qr_img); 
		$this->logger->debug ($h_path); 
		$this->logger->debug ($h_img); 
		$this->logger->debug ($target_img_path);  
		$this->logger->debug ($goods_img); 
		@unlink ($qrcode); 
		@unlink ($qr_img); 
		@unlink ($h_path); 
		@unlink ($h_img); 
		@unlink ($target_img_path); 
		@unlink ($goods_img); 
		
		//将生成的二维码图片的地址放到数据库中
		$insert_sql = "INSERT INTO `wxch_qr_tianxin100` (`qr_path`,`scene`,`scene_id`, `nickname`) VALUES
				('$target_img_path','$scene_param', '$scene_id','$nickname')";
		$GLOBALS['db']->query($insert_sql);

		$ret = array(
				"errcode" => 0,
				"errmsg" => "",
				"data" => $target_img_path
		);
		return json_decode(json_encode($ret));
	}

	/**
	 * 调整图片大小
	 * @param string $imgsrc 源图片地址
	 * @param int $imgwidth 图片宽度
	 * @param int $imgheight 图片高度
	 * @param timestamp $time 时间戳
	 * @return string 调整大小后的图片地址
	 */
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
		$name = $this->img_path . $time . ".jpg";
		Imagejpeg($image, $name, 100);
		return $name;
	}
	
}