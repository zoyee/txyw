<?php
/**
 * 自动注册
 * @author liuzhy
 *
 */
class auto_reg extends keyword_processor {

	function __construct(){
		$this->logger = LoggerManager::getLogger(basename(__FILE__));
		$this->weixin_api = new weixin_api();
		$this->keyword_name = "自动注册";
	}

	public function process($keyword = null, $fromUsername = null, $toUsername = null, $belong = null){
		$time = time();
		//查询用户的微信信息
		$sql = "select * from " . $GLOBALS['ecs']->table('users') . " where wxid='$fromUsername'";
		$ecs_user = parent::$db->getRow($sql);
		$this->logger->debug('$ecs_user = ' . json_encode($ecs_user));
		
		if(! $ecs_user){
			$auto_reg_conf = parent::$db->getRow('select * from wxch_autoreg WHERE `autoreg_id` = 1');
			$this->logger->debug('$auto_reg_conf = ' . json_encode($auto_reg_conf));
			if(empty($auto_reg_conf['state'])){
				//自动注册未开启
			}else{
				//目前先不考虑用户中心UCenter同步的问题
				$name_prefix = $auto_reg_conf['autoreg_name'];
				$autoreg_rand = $auto_reg_conf['autoreg_rand'];
				$pwd_prefix = parent::$wxch_cfg['userpwd'];
				$pwd = $pwd_prefix . $this->randomkeys($autoreg_rand);
				$pwd_md5 = md5($pwd);
				
				parent::$db->autoExecute($GLOBALS['ecs']->table('users'), array(
						'user_name' => $fromUsername,
						'password' => $pwd_md5,
						'wxid' => $fromUsername,
						'user_rank' => 0,
						'wxch_bd' => 'yes',
						'password_tianxin' => $pwd,
						'reg_time' => $time
				), 'INSERT');
				
				$user_id = parent::$db -> insert_id();
				$this->logger->debug('$user_id = ' . $user_id);
				$user_name = $name_prefix . $user_id;
				parent::$db->autoExecute($GLOBALS['ecs']->table('users'), array(
						'user_name' => $user_name
				), 'UPDATE', " user_id='$user_id'");
				parent::$db->autoExecute('wxch_user', array(
						'uname' => $user_name,
						'step' => 3
				), 'UPDATE', " wxid = '$fromUsername'");
								
				//注册送红包				
				$sql = "select * from wxch_user where wxid = '$fromUsername'";
				$wx_user = parent::$db->getRow($sql);
				if($wx_user['coupon']){
					$bonus_type = $wx_user['coupon'];
					
					$bonus = parent::$db->getRow("select FROM_UNIXTIME(use_end_date+86400,'%Y-%m-%d') as use_end_date, type_money from " . $GLOBALS['ecs']->table('bonus_type') . " where type_id='$bonus_type' and UNIX_TIMESTAMP()>send_start_date and UNIX_TIMESTAMP()<send_end_date+86400");
					if(empty($bonus)){
						$this->weixin_api->send_custom_message($fromUsername, "红包发放活动已经结束，下次请早哦！", 0);
					}else{
						$sql = "select bonus_id from " . $GLOBALS['ecs']->table('user_bonus') . " where bonus_type_id='$bonus_type' and user_id=0 order by bonus_id asc limit 0, 1";
						$this->logger->debug($sql);
						$bonus_id = parent::$db->getOne($sql);
						$use_end_date = $bonus['use_end_date'];
						$bonus_money = $bonus['type_money'];
						if($bonus_id){
							parent::$db->query("update " . $GLOBALS['ecs']->table('user_bonus') . " set user_id = " . $user_id . " where bonus_id = " . $bonus_id);
							$this->logger->debug('给' . $user_name . "(" . $user_id . ")发放红包");
							$this->weixin_api->send_custom_message($fromUsername, $bonus_money. "元红包已经发放到您的账户，有效期至". $use_end_date . "，请尽快使用！", 0);
						}else{
							$this->logger->debug('没有足够的红包发放');
							$this->weixin_api->send_custom_message($fromUsername, "红包已被抢完，下次请早哦！", 0);
						}
					}
				}
				
				//关注(注册)送积分
				$sql = "select point_value from wxch_point where autoload='yes' and point_name='g_point'";
				$point = parent::$db->getOne($sql);
				if($point){
					log_account_change($user_id, 0, 0, 0, $point, '注册送积分，消费积分+'.$point);
					$this->weixin_api->send_custom_message($fromUsername, "关注送积分\r\n积分+" . $point, 0);
				}
					
				//关注送红包(优惠券)
				$ret = parent::$db->getRow("SELECT `type_id` FROM `wxch_coupon` WHERE `id` = 1");
				$type_id = $ret['type_id'];
				if($type_id){
					parent::$db->autoExecute('wxch_user', array(
							'coupon' => $type_id
					), 'UPDATE', " wxid = '$fromUsername'");
				}
			}
		}
		
	}

	protected function plusPoint($child_processor, $fromUsername){
		$this->common_plusPoint($fromUsername);
	}

	function randomkeys($length){
		$pattern='1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ';
		for($i=0;$i<$length;$i++){
			$key .= $pattern{mt_rand(0,35)};    //生成php随机数
		}
		return $key;
	}
}