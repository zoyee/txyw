<?php
/**
 * 用户推广二维码
 * @author liuzhy
 *
 */
class qrcode extends keyword_processor{
	protected $weixin_api;

	function __construct(){
		$this->logger = LoggerManager::getLogger(basename(__FILE__));
		$this->keyword_name = "二维码";
		if(!$this->weixin_api){
			$this->weixin_api = new weixin_api();
		}
	}

	public function process($processor = null, $fromUsername = null, $toUsername = null, $belong = null){
		$user_id = $GLOBALS['db']->getOne("SELECT u.user_id FROM " . 
				$GLOBALS['ecs']->table('users') . " u WHERE u.wxid='$fromUsername'");
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
			$qrcode = dirname(__FILE__) . "/../../mobile/" . $qrcode->data;
		}
		
		$this->logger->debug($qrcode);
		$this->logger->debug('$fromUsername = ' . json_encode($fromUsername));
		$this->weixin_api->send_custom_image($fromUsername, $qrcode, 0);
		$this->weixin_api->send_custom_message($fromUsername, 
				"提醒：个人专属二维码非永久二维码，有效期至 " . date('Y年m月d日', time() + 2505600) . "，超过有效期请重新生成二维码！", 0);
				
		//将生成的二维码图片的地址放到数据库中
		$dateline = time();
		$endtime = time() + 2505600;
		$insert_sql = "INSERT INTO `wxch_qr` (`type`,`action_name`, `scene_id`, `scene` ,`qr_path`,`endtime`,`dateline`) VALUES
				('tj','QR_SCENE', '$scene_id', '$scene_param' ,'$qrcode','$endtime','$dateline')";
		$GLOBALS['db']->query($insert_sql);
		
		//删除生成海报过程中的图片
		$this->logger->debug("删除生成二维码过程中的图片");
		$this->logger->debug ($qrcode);
		@unlink ($qrcode); 
		exit;
	}

	protected function plusPoint($child_processor, $fromUsername){
		$this->common_plusPoint($fromUsername);
	}
}