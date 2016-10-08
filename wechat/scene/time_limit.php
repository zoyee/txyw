<?php
/**
 * 二维码时效
 * @author liuzhy
 *
 */
class time_limit extends scene_processor{

	function __construct(){
		$this->logger = LoggerManager::getLogger(basename(__FILE__));
		$this->weixin_api = new weixin_api();
		$this->keyword_name = "二维码时效";
	}

	public function process($scene_id = null, $fromUsername = null, $toUsername = null, $belong = null, $is_new_user = false){
		$this->logger->debug('scene_id = ' . $scene_id);
		$scene = parent::$db->getRow("select * from wxch_scene where scene_id='$scene_id' order by id desc LIMIT 0 , 1");
		$param = json_decode($scene['param']);
		$this->logger->debug('$param = ' . $scene['param']);
		$limit_time = strtotime($param->time_limit);
		$time = time();
		$this->logger->debug('$limit_time = ' . $limit_time);
		$this->logger->debug('$time = ' . $time);
		
		if($time > $limit_time){
			$this->weixin_api->send_custom_message($fromUsername, "此二维码已经失效，不能享受优惠福利！", 0);
			return 1;
		}
		return 0;
	}

	protected function plusPoint($val, $fromUsername){
		return 0;
	}

}