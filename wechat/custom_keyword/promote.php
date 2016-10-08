<?php
/**
 * 促销商品
 * @author liuzhy
 *
 */
class promote extends keyword_processor {

	function __construct(){
		$this->logger = LoggerManager::getLogger(basename(__FILE__));
		$this->keyword_name = "促销商品";
	}

	public function process($processor = null, $fromUsername = null, $toUsername = null, $belong = null){
		$time = time();
		$data = $this->get_goods($fromUsername);
		$resultStr = sprintf(Template::$newsTpl, $fromUsername, $toUsername, $time, $data['ArticleCount'], $data['items']);
		$this->insert_wmessage($fromUsername, $resultStr, $belong);
		echo $resultStr;
		$this->logger->debug($resultStr);
	}

	protected function plusPoint($child_processor, $fromUsername){
		$this->common_plusPoint($fromUsername);
	}

	private function get_goods($fromUsername){
		return $this->get_recommend_goods('promote', $fromUsername);
	}
}