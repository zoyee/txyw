<?php
/**
 * 用户等级
 * @author liuzhy
 *
 */
class user_rank extends scene_processor{

	function __construct(){
		$this->logger = LoggerManager::getLogger(basename(__FILE__));
		$this->weixin_api = new weixin_api();
		$this->keyword_name = "用户等级";
	}

	public function process($val = null, $fromUsername = null, $toUsername = null, $belong = null, $is_new_user = false){
		$this->logger->debug('user_rank = ' . $val);
		$user_rank = parent::$db->getOne("select user_rank from " . $GLOBALS['ecs']->table('users') . " WHERE `wxid` = '$fromUsername'");
		$rank_name = parent::$db->getOne("select rank_name from " . $GLOBALS['ecs']->table('user_rank') . " where rank_id='$val'");
		if(empty($rank_name)){
			$this->weixin_api->send_custom_message($fromUsername, "会员等级不可用，请联系客服！", 0);
			return;
		}
		if(!empty($user_rank) && $user_rank != $val){
			$this->weixin_api->send_custom_message($fromUsername, "您不能变更您的会员级别，如需变更请联系客服！", 0);
		}elseif($user_rank == $val){
			//已经是等级用户
			$this->logger->debug("已经是等级用户");
		}else{
			parent::$db->query("UPDATE " . $GLOBALS['ecs']->table('users') . " SET `user_rank` = '$val' WHERE `wxid` = '$fromUsername'");
			$this->weixin_api->send_custom_message($fromUsername, "恭喜您成为".$rank_name."！", 0);
		}
		$this->logger->debug("用户等级逻辑处理完毕");
	}

	protected function plusPoint($val, $fromUsername){
		return 0;
	}

}