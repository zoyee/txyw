<?php
/**
 * 绑定微信二维码事件
 * @author liuzhy
 *
 */
class bind_oath_user_uuid extends scene_processor{

	function __construct(){
		$this->logger = LoggerManager::getLogger(basename(__FILE__));
		$this->weixin_api = new weixin_api();
		$this->keyword_name = "绑定微信二维码事件";
	}

	public function process($uuid = null, $fromUsername = null, $toUsername = null, $belong = null, $is_new_user = false){
		$this->logger->debug('$uuid = ' . $uuid);
		$time = date('Y-m-d H:i:s');
		$sql = "select u.user_id from " . $GLOBALS['ecs']->table('users') . " u where u.wxid='$fromUsername'";
		$user_id = $GLOBALS['db']->getOne($sql);
		
		if($user_id){
			$sql = "update " . $GLOBALS['ecs']->table('qr_event') . " set user_id='$user_id', `status`='1', wxid='$fromUsername', scan_time='$time' where uuid='$uuid' and type=3 and `status`=0";
			$this->logger->debug('$sql = ' . $sql);
			$GLOBALS['db']->query($sql);
			$this->logger->debug("用户$user_id扫码绑定PC商城联合登录用户(uuid=$uuid)");
			$this->weixin_api->send_custom_message($fromUsername, "微信绑定操作成功，如非本人操作，请及时联系客服！", 0);
			return 0;
		}
		return 0;
	}

	protected function plusPoint($val, $fromUsername){
		return 0;
	}

}