<?php
/**
 * 账号密码找回
 * @author liuzhy
 *
 */
class getback extends keyword_processor{

	function __construct(){
		$this->logger = LoggerManager::getLogger(basename(__FILE__));
		$this->keyword_name = "账号密码找回";
	}

	public function process($processor = null, $fromUsername = null, $toUsername = null, $belong = null){
		$time = time();
		$row =  parent::$db->getRow("SELECT user_name, password_tianxin FROM " . $GLOBALS['ecs']->table('users')."WHERE `wxid` = '$fromUsername'");
		$password_tianxin = $row['password_tianxin'];
		$user_name =  $row['user_name'];
		$contentStr = '您的账号是：' . $user_name . "\r\n密码是：" . $password_tianxin;
		$resultStr = sprintf(Template::$textTpl, $fromUsername, $toUsername, $time, $contentStr);
		$this->insert_wmessage($fromUsername, $resultStr, $belong);
		echo $resultStr;
		$this->logger->debug($resultStr);
		exit;
	}

	protected function plusPoint($child_processor, $fromUsername){
		return 0;
	}
}