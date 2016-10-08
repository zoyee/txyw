<?php
/**
 * 绑定会员
 * @author liuzhy
 *
 */
class bd extends keyword_processor{

	function __construct(){
		$this->logger = LoggerManager::getLogger(basename(__FILE__));
		$this->keyword_name = "绑定会员";
	}

	public function process($processor = null, $fromUsername = null, $toUsername = null, $belong = null){
		$time = time();
		$bd_url = '<a href="' . self::$m_url . 'user_wxch.php?wxid=' . $fromUsername . '">点击绑定会员</a>';
		$contentStr = $bd_url . self::$keyword_lang['bd'] . self::$m_url;
		$resultStr = sprintf(Template::$textTpl, $fromUsername, $toUsername, $time, $contentStr);
		$this->insert_wmessage($fromUsername, $resultStr, $belong);
		echo $resultStr;
		$this->logger->debug($resultStr);
	}

	protected function plusPoint($child_processor, $fromUsername){
		$this->common_plusPoint($fromUsername);
	}
}
