<?php


/**
 * 关注公众号
 * @author liuzhy
 *
 */
class g_point extends keyword_processor {

	function __construct() {
		$this->logger = LoggerManager :: getLogger(basename(__FILE__));
		$this->weixin_api = new weixin_api();
		$this->keyword_name = "关注公众号";
	}

	public function process($processor = null, $fromUsername = null, $toUsername = null, $belong = null) {
		$time = time();
		$sql = "select * from wxch_user where wxid = '$fromUsername'";
		$wx_user = parent :: $db->getRow($sql);
		$send_welcome_flag = 0;	//是否显示欢迎消息
		$bonus_flag = 0;		//是否送关注红包
		if ($wx_user) {
//			if($time - $wx_user['subscribe_time'] > 5 * 24 * 3600){
				$this->weixin_api->refreshWxInfo($fromUsername);
//			}
			if(empty($wx_user['subscribe'])){
				parent::$db->autoExecute('wxch_user', array(
						'subscribe' => 1
				), 'UPDATE', " wxid = '$fromUsername'");
				$send_welcome_flag = 1;
			}
		}else{
			$this->weixin_api->loadWxInfo($fromUsername);
			parent::$db->autoExecute('wxch_user', array(
					'subscribe' => 1,
					'subscribe_time' => $time
			), 'UPDATE', " wxid = '$fromUsername'");
			
			$send_welcome_flag = 1;
		}
		
		
		
		
		//推送关注回复消息
		if($send_welcome_flag){
			$ret = parent :: $db->getRow("SELECT * FROM `wxch_keywords1` where is_start = 1");
			$this->logger->debug(json_encode($ret));
			if ($ret['type'] == '3') {
				//文本消息
				$this->weixin_api->send_custom_message($fromUsername, $ret['contents'], 0);
			} elseif ($ret['type'] == '4') {
				//图文消息
				$articles = $this -> get_keywords_articles($ret['id']);
				$param = array();
				foreach($articles as $art) {
					if (!empty($art['file_url'])) {
						$picurl = parent::$base_url . $art['file_url'];
					} else {
						$picurl = parent::$base_url . 'themes/default/images/logo.gif';
						if (!is_null($GLOBALS['_CFG']['template'])) {
							$picurl = $base_url . 'themes/' . $GLOBALS['_CFG']['template'] . '/images/logo.gif';
						}
					}
					$gourl = parent::$article_url . $art['article_id'];
					
					array_push($param, array(
						'title' => $art['title'],
						'description' => $art['description'],
						'url' => $gourl,
						'picurl' => $picurl
					));
				}
				$this->logger->debug('$param = ' . json_encode($param));
				$this->weixin_api->send_custom_multi_news($fromUsername, $param);
			}
		}
		
		
	}
	
	protected function plusPoint($child_processor, $fromUsername) {
		//关注送积分比较特殊，用户还没生成，需要在callback-ent里添加送积分逻辑
	}
}