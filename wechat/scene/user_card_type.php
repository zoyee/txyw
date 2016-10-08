<?php
/**
 * 绑定会员卡
 * @author liuzhy
 *
 */
class user_card_type extends scene_processor{

	function __construct(){
		$this->logger = LoggerManager::getLogger(basename(__FILE__));
		$this->weixin_api = new weixin_api();
		$this->keyword_name = "绑定会员卡";
	}

	public function process($val = null, $fromUsername = null, $toUsername = null, $belong = null, $is_new_user = false){
		$this->logger->debug('user_card_type_id = ' . $val);
		$ct_name = parent::$db->getOne("select ct_name from " . $GLOBALS['ecs']->table('user_card_type') . " where ct_id='$val'");
		if(empty($ct_name)){
			$this->weixin_api->send_custom_message($fromUsername, "未绑定会员卡，此二维码对应会员卡不可用，请联系客服！", 0);
			return;
		}
		
		$card = parent::$db->getRow("select c.card_no, c.user_money, c.pay_points, c.rank_points from " . $GLOBALS['ecs']->table('user_card') . " c, " . $GLOBALS['ecs']->table('users') . 
				" u where u.wxid='$fromUsername' and u.user_id = c.user_id");
		if($card){
			$card_no = $card['card_no'];
			$this->weixin_api->send_custom_message($fromUsername, "您已经绑定" . $ct_name . "，卡号" . $card_no, 0);
			return;
		}		
		
		$card = parent::$db->getRow("select card_no, user_money, pay_points, rank_points from " . $GLOBALS['ecs']->table('user_card') . " WHERE `ct_id` = '$val' and user_id = 0 order by id asc limit 0, 1");	
		
		if(empty($card)){
			$this->weixin_api->send_custom_message($fromUsername, $ct_name . "已经发放完毕，请联系客服！", 0);
			return;
		}else{
			$card_no = $card['card_no'];
			$user_money = $card['user_money'];
			$pay_points = $card['pay_points'];
			$rank_points = $card['rank_points'];
		
			$user_id = parent::$db->getOne("select user_id from " . $GLOBALS['ecs']->table('users') . " where wxid='$fromUsername'");
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
		}
		
		$this->logger->debug("绑定会员卡逻辑处理完毕");
	}

	protected function plusPoint($val, $fromUsername){
		return 0;
	}

}