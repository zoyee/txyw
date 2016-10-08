<?php
/**
 * 获取账户密码
 * @author liuzhy
 *
 */
class get_account extends scene_processor{

	function __construct(){
		$this->logger = LoggerManager::getLogger(basename(__FILE__));
		$this->weixin_api = new weixin_api();
		$this->keyword_name = "获取账户密码";
	}

	public function process($scene_id = null, $fromUsername = null, $toUsername = null, $belong = null, $is_new_user = false){
		$time = time();
		$row =  parent::$db->getRow("SELECT user_name, password_tianxin FROM " . $GLOBALS['ecs']->table('users')."WHERE `wxid` = '$fromUsername'");
		$password_tianxin = $row['password_tianxin'];
		$user_name =  $row['user_name'];
		$contentStr = '您的账号是：' . $user_name . "\r\n密码是：" . $password_tianxin;
		$this->weixin_api->send_custom_message($fromUsername, $contentStr, 0);
		return 0;
	}

	protected function plusPoint($val, $fromUsername){
		return 0;
	}

}