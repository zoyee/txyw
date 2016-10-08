<?php
/**
 * 帮助
 * @author liuzhy
 *
 */
class help extends keyword_processor{

	function __construct(){
		$this->logger = LoggerManager::getLogger(basename(__FILE__));
		$this->keyword_name = "帮助";
	}

	public function process($processor = null, $fromUsername = null, $toUsername = null, $belong = null){
		$time = time();
		$contentStr = parent::$keyword_lang['help'];
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