<?php
/**
 * PC端商城二维码扫码登录
 * @author liuzhy
 *
 */
class login_uuid extends scene_processor{

	function __construct(){
		$this->logger = LoggerManager::getLogger(basename(__FILE__));
		$this->weixin_api = new weixin_api();
		$this->keyword_name = "PC端商城二维码扫码登录";
	}

	public function process($uuid = null, $fromUsername = null, $toUsername = null, $belong = null, $is_new_user = false){
		$this->logger->debug('$uuid = ' . $uuid);
		$time = date('Y-m-d H:i:s');
		$sql = "select u.user_id from " . $GLOBALS['ecs']->table('users') . " u where u.wxid='$fromUsername'";
		$user_id = $GLOBALS['db']->getOne($sql);
		
		if($user_id){
			$sql = "update " . $GLOBALS['ecs']->table('qr_event') . " set user_id='$user_id', `status`='1', wxid='$fromUsername', scan_time='$time' where uuid='$uuid'";
			$this->logger->debug('$sql = ' . $sql);
			$GLOBALS['db']->query($sql);
			$this->logger->debug("用户$user_id扫码登录PC商城(uuid=$uuid)");
			$this->weixin_api->send_custom_message($fromUsername, "老农商城PC端扫码登录成功，如非本人操作，请及时联系客服！", 0);
			return 0;
		}
		return 0;
	}

	protected function plusPoint($val, $fromUsername){
		return 0;
	}

}