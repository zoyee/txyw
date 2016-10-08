<?php
/**
 * 520专题图片
 * @author liuzhy
 *
 */
class kefu_qr extends keyword_processor{

	function __construct(){
		$this->logger = LoggerManager::getLogger(basename(__FILE__));
		$this->weixin_api = new weixin_api();
		$this->keyword_name = "客服二维码";
	}

	public function process($child_processor = null, $fromUsername = null, $toUsername = null, $belong = null){
		$this->logger->debug('$key = kefu_qr');
		$target_img_path = dirname(__FILE__) . "/../../mobile/images/kefu_qr.jpg";
		$this->weixin_api->send_custom_image($fromUsername, $target_img_path, 0);
		exit;
	}

	protected function plusPoint($child_processor, $fromUsername){
		return 0;
	}
}