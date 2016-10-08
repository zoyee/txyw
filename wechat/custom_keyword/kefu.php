<?php
/**
 * 客服转接
 * @author liuzhy
 *
 */
class kefu extends keyword_processor{

	function __construct(){
		$this->logger = LoggerManager::getLogger(basename(__FILE__));
		$this->weixin_api = new weixin_api();
		$this->keyword_name = "客服转接";
	}

	public function process($processor = null, $fromUsername = null, $toUsername = null, $belong = null){
		$time = time();
        $this->weixin_api->send_custom_message($fromUsername, "正在为您转接在线客服，请稍候！", 0);
		$resultStr = sprintf(Template::$serviceTpl, $fromUsername, $toUsername, $time);
		$this->insert_wmessage($fromUsername, $resultStr, $belong);
		echo $resultStr;
		$this->logger->debug($resultStr);
		exit;
	}

	protected function plusPoint($child_processor, $fromUsername){
		return 0;
	}
}