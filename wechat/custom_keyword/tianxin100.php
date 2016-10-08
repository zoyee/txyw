<?php
/**
 * 用户推广个人专属海报
 * @author liuzhy
 *
 */
class tianxin100 extends keyword_processor{
	protected $haibao_api;

	function __construct(){
		$this->logger = LoggerManager::getLogger(basename(__FILE__));
		$this->weixin_api = new weixin_api();
		$this->keyword_name = "个人专属海报";
		if(!$this->haibao_api){
			$this->haibao_api = new haibao_api();
		}
	}

	public function process($processor = null, $fromUsername = null, $toUsername = null, $belong = null){
		$user_id = $GLOBALS['db']->getOne("SELECT u.user_id FROM " . 
				$GLOBALS['ecs']->table('users') . " u WHERE u.wxid='$fromUsername'");
		$this->weixin_api->send_custom_message($fromUsername, "正在为您生成您的专属推广海报，请稍候！", 0);
		$this->haibao_api->cr_user_haibao($user_id);
		exit;
	}

	protected function plusPoint($child_processor, $fromUsername){
		$this->common_plusPoint($fromUsername);
	}
}