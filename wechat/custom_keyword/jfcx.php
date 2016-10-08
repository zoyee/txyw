<?php
/**
 * 积分查询
 * @author liuzhy
 *
 */
class jfcx extends keyword_processor{

	function __construct(){
		$this->logger = LoggerManager::getLogger(basename(__FILE__));
		$this->keyword_name = "积分查询";
	}

	public function process($processor = null, $fromUsername = null, $toUsername = null, $belong = null){
		$time = time();
		$row =  parent::$db->getRow("SELECT pay_points, user_money FROM " . $GLOBALS['ecs']->table('users')."WHERE `wxid` = '$fromUsername'");
		$money = $row['user_money'];
		$pay_points =  $row['pay_points'];
		$integral_name = parent::$db->getOne("select `value` from " . $GLOBALS['ecs']->table('touch_shop_config') . " where `code` = 'integral_name'");
		$contentStr = "您的余额：￥$money\r\n" . $integral_name . "：$pay_points";
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