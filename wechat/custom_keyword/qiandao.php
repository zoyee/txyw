<?php
/**
 * 签到
 * @author liuzhy
 *
 */
class qiandao extends keyword_processor{

	function __construct(){
		$this->logger = LoggerManager::getLogger(basename(__FILE__));
		$this->keyword_name = "每日签到";
	}

	public function process($processor = null, $fromUsername = null, $toUsername = null, $belong = null){
		$time = time();
		$contentStr = $this->plusPoint($processor, $fromUsername);
		$resultStr = sprintf(Template::$textTpl, $fromUsername, $toUsername, $time, $contentStr);
		$this->insert_wmessage($fromUsername, $resultStr, $belong);
		echo $resultStr;
		$this->logger->debug($resultStr);
		exit;
	}

	protected function plusPoint($child_processor, $fromUsername){
		$this->logger->debug("计算积分");
		$keyword = get_class($this);
		$ret = $this->common_plusPoint($fromUsername);
		switch ($ret){
			case -1 : return "签到送积分功能未设置！";
			case -2 : return parent::$keyword_lang['qdstop'];
			case 0  : return parent::$keyword_lang['qdno'];
			case 1  : return parent::$keyword_lang['qdok'];
			default :
				$points = parent::$db -> getOne("SELECT `point_value` FROM `wxch_point` WHERE `point_name` = '$keyword'");
				return parent::$keyword_lang['qdok'] . "，积分加" . $points;
		}
	}
}