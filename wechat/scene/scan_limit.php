<?php
/**
 * 二维码限扫
 * @author liuzhy
 *
 */
class scan_limit extends scene_processor{

	function __construct(){
		$this->logger = LoggerManager::getLogger(basename(__FILE__));
		$this->weixin_api = new weixin_api();
		$this->keyword_name = "二维码限扫";
	}

	public function process($val = null, $fromUsername = null, $toUsername = null, $belong = null, $is_new_user = false){
		$this->logger->debug('scene_id = ' . $val);
		$sql = "select scan from wxch_scene where scene_id = '$val'";
		$scan_count = parent::$db->getOne($sql);
		$scene = parent::$db->getRow("select * from wxch_scene where scene_id='$val' order by id desc LIMIT 0 , 1");
		$param = json_decode($scene['param']);
		$scan_limit = $param->scan_limit;
		
		if($scan_count >= $scan_limit){
			$this->weixin_api->send_custom_message($fromUsername, "操作无效，此二维码只能扫描" . $scan_limit . "次！", 0);
			return 1;
		}
		return 0;
	}

	protected function plusPoint($val, $fromUsername){
		return 0;
	}

}