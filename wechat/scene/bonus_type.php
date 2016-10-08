<?php
/**
 * 发放红包
 * @author liuzhy
 *
 */
class bonus_type extends scene_processor{

	function __construct(){
		$this->logger = LoggerManager::getLogger(basename(__FILE__));
		$this->weixin_api = new weixin_api();
		$this->keyword_name = "发放红包";
	}

	public function process($val = null, $fromUsername = null, $toUsername = null, $belong = null, $is_new_user = false){
		$this->logger->debug('bonus_type = ' . $val);
		$bonus = parent::$db->getRow("select FROM_UNIXTIME(use_end_date+86400,'%Y-%m-%d') as use_end_date, type_money from " . $GLOBALS['ecs']->table('bonus_type') . " where type_id='$val' and UNIX_TIMESTAMP()>send_start_date and UNIX_TIMESTAMP()<send_end_date+86400");
		if(empty($bonus)){
			$this->weixin_api->send_custom_message($fromUsername, "红包已经发放完毕，下次请早哦！", 0);
			return;
		}
		$use_end_date = $bonus['use_end_date'];
		$bonus_money = $bonus['type_money'];
		
		
		$bonus_id = parent::$db->getOne("select bonus_id from " . $GLOBALS['ecs']->table('user_bonus') . " WHERE `bonus_type_id` = '$val' and user_id = 0 order by bonus_id asc limit 0, 1");
		
		if(empty($bonus_id)){
			$this->weixin_api->send_custom_message($fromUsername, "红包已被抢完，下次请早哦！", 0);
			return;
		}else{
			$user_id = parent::$db->getOne("select user_id from " . $GLOBALS['ecs']->table('users') . " where wxid='$fromUsername'");
			parent::$db->query("UPDATE " . $GLOBALS['ecs']->table('user_bonus') . " SET `user_id` = '$user_id' WHERE `bonus_id` = '$bonus_id'");
			$this->weixin_api->send_custom_message($fromUsername, $bonus_money. "元红包已经发放到您的账户，有效期至". $use_end_date . "，请尽快使用！", 0);
		}
		
		$this->logger->debug("用户等级逻辑处理完毕");
	}

	protected function plusPoint($val, $fromUsername){
		return 0;
	}

}