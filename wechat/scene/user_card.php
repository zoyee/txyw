<?php
/**
 * 绑定会员卡
 * @author liuzhy
 *
 */
class user_card extends scene_processor{

	function __construct(){
		$this->logger = LoggerManager::getLogger(basename(__FILE__));
		$this->weixin_api = new weixin_api();
		$this->keyword_name = "绑定会员卡";
	}

	public function process($val = null, $fromUsername = null, $toUsername = null, $belong = null, $is_new_user = false){
		$this->logger->debug('user_card = ' . $val);
		$card = parent::$db->getRow("select * from " . $GLOBALS['ecs']->table('user_card') . " where card_no='$val'");
		if(! $card){
			$this->weixin_api->send_custom_message($fromUsername, "未绑定会员卡，此二维码对应会员卡不存在！", 0);
			return;
		}
		
		$ct_name = parent::$db->getOne("select ct_name from " . $GLOBALS['ecs']->table('user_card_type') . " where ct_id='$card[ct_id]'");
		$bind_user_id = $card['user_id'];
		$user_id = parent::$db->getOne("select user_id from " . $GLOBALS['ecs']->table('users') . " where wxid='$fromUsername'");
//		$sql = "SELECT u.user_id, k.rank_id FROM " . $GLOBALS['ecs']->table('users') . 
//				" u left join " . $GLOBALS['ecs']->table('user_rank') . " k on u.user_rank = k.rank_id and k.special_rank = 1 WHERE u.wxid='$fromUsername'";
//		$this->logger->debug($sql);
//		$ecs_user = parent::$db->getRow($sql);
//		$user_id = $ecs_user['user_id'];
//		$user_rank = $ecs_user['rank_id'];
		if($bind_user_id == $user_id){
			$this->weixin_api->send_custom_message($fromUsername, "您已经绑定" . $ct_name . "，卡号" . $val, 0);
			return;
		}elseif(!empty($bind_user_id) && $bind_user_id != $user_id){
			$this->weixin_api->send_custom_message($fromUsername, "此会员卡已经被他人绑定！" . $val, 0);
			return;
		}		
				
		$card_no = $card['card_no'];
		$user_money = $card['user_money'];
		$pay_points = $card['pay_points'];
		$rank_points = $card['rank_points'];
	
		parent::$db->query("UPDATE " . $GLOBALS['ecs']->table('user_card') . " SET `user_id` = '$user_id' WHERE `card_no` = '$card_no'");
		log_account_change($user_id, $user_money, 0, $rank_points, $pay_points, '绑定会员卡'.$card_no.'充值余额'.$user_money.'等级积分:'.$rank_points.',消费积分'.$pay_points);
		$msg = "恭喜您成为" . $ct_name . "会员。";
		if(!empty($user_money)){
			$msg .= "\r\n".'充值余额'.$user_money.'元';
		}
		if(!empty($rank_points)){
			$msg .= "\r\n".'等级积分+'.$rank_points;
		}
		if(!empty($pay_points)){
			$msg .= "\r\n".'消费积分+'.$pay_points;
		}
		$this->weixin_api->send_custom_message($fromUsername, $msg , 0);
		
		$this->logger->debug("绑定会员卡逻辑处理完毕");
	}

	protected function plusPoint($val, $fromUsername){
		return 0;
	}

}