<?php
/**
 * 限制只能新用户可以扫码
 * @author liuzhy
 *
 */
class new_user_limit extends scene_processor{

	function __construct(){
		$this->logger = LoggerManager::getLogger(basename(__FILE__));
		$this->weixin_api = new weixin_api();
		$this->keyword_name = "新用户可以扫码";
	}

	public function process($scene_id = null, $fromUsername = null, $toUsername = null, $belong = null, $is_new_user = false){
		$this->logger->debug('$scene_id = ' . $scene_id);
		$scene = parent::$db->getRow("select * from wxch_scene where scene_id='$scene_id' order by id desc LIMIT 0 , 1");
		$param = json_decode($scene['param']);
		
		if(!$is_new_user && $param->new_user_limit){
			$this->weixin_api->send_custom_message($fromUsername, "此二维码只限新用户使用！", 0);
			return 1;
		}
		return 0;
	}

	protected function plusPoint($val, $fromUsername){
		return 0;
	}

}