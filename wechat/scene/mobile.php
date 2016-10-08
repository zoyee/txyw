<?php
/**
 * 设置用户手机号码
 * @author liuzhy
 *
 */
class mobile extends scene_processor{

	function __construct(){
		$this->logger = LoggerManager::getLogger(basename(__FILE__));
		$this->weixin_api = new weixin_api();
		$this->keyword_name = "用户手机号码";
	}

	public function process($val = null, $fromUsername = null, $toUsername = null, $belong = null, $is_new_user = false){
		$this->logger->debug('mobile = ' . $val);
		parent::$db->query("UPDATE " . $GLOBALS['ecs']->table('users') . " SET `mobile_phone` = '$val' WHERE `wxid` = '$fromUsername'");
		$this->logger->debug("设置用户手机号码处理完毕");
	}

	protected function plusPoint($val, $fromUsername){
		return 0;
	}

}