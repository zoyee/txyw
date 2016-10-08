<?php
/**
 * 转移用户积分
 * @author liuzhy
 *
 */
class transfer_credits_url extends scene_processor{

	function __construct(){
		$this->logger = LoggerManager::getLogger(basename(__FILE__));
		$this->weixin_api = new weixin_api();
		$this->keyword_name = "转移用户积分";
	}

	public function process($val = null, $fromUsername = null, $toUsername = null, $belong = null, $is_new_user = false){
		$this->logger->debug('transfer_credits_url = ' . $val);
		if($val){
			$result = file_get_contents($val . '&step=1');
			$this->logger->debug('$result = ' . $result);
			$json = json_decode($result);
			if($json->errcode == 0){
				$this->logger->error('积分迁移失败step=1，返回信息:' . $result);
				$this->weixin_api->send_custom_message($fromUsername, "对不起，积分迁移失败，请联系客服手工处理！", 0);
				return 1;
			}
			$pay_points = $json->pay_points;
			
			if($pay_points > 0){
				log_account_change($json->user_id, 0, 0, 0, $pay_points, "积分商城转移青豆" . $pay_points);
				
				$result = file_get_contents($val . '&step=2&pay_points=' . $pay_points);
				$this->logger->debug('$result = ' . $result);
				$json = json_decode($result);
				if($json->errcode == 0){
					$this->logger->error('积分迁移失败step=2，返回信息:' . $result);
					$pay_points = 0 - $pay_points;
					log_account_change($json->user_id, 0, 0, 0, $pay_points, "撤销积分商城转移青豆" . $pay_points);
					$this->weixin_api->send_custom_message($fromUsername, "对不起，积分迁移失败，请联系客服手工处理！", 0);
					return 1;
				}
				$this->weixin_api->send_custom_message($fromUsername, "从分商城转移青豆\r\n积分+" . $pay_points, 0);
				$this->logger->debug("用户" . $json->user_id. "从分商城转移青豆+" . $pay_points);
			}
		}
		
		return 0;
	}

	protected function plusPoint($val, $fromUsername){
		return 0;
	}

}