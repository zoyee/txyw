<?php
/**
 * 供货商代理认证
 * @author liuzhy
 *
 */
class suppliers_agent extends scene_processor{

	function __construct(){
		$this->logger = LoggerManager::getLogger(basename(__FILE__));
		$this->weixin_api = new weixin_api();
		$this->keyword_name = "供货商代理认证";
	}

	public function process($val = null, $fromUsername = null, $toUsername = null, $belong = null, $is_new_user = false){
		$this->logger->debug('suppliers_id = ' . $val);
		$time = date('Y-m-d H:i:s', time());
		$user_id = parent::$db -> getOne("SELECT `user_id` FROM " . $GLOBALS['ecs']->table('users') . " WHERE `wxid` = '$fromUsername'");
		
		$sql = "select id from " . $GLOBALS['ecs']->table('suppliers_user') . " where suppliers_id = '$val' and user_id='$user_id'";
		$id = parent::$db -> getOne($sql);
		if($id){
			parent::$db -> query("update " . $GLOBALS['ecs']->table('suppliers_user') . " set status = 5, reply_time='$time' where id = $id");
		}else{
			parent::$db -> query("insert into " . $GLOBALS['ecs']->table('suppliers_user') . " (suppliers_id, user_id, status, add_time, reply_time) values" .
					"('$val', '$user_id', 5, '$time', '$time')");
		}
		
		$contentStr = "恭喜，代理认证成功！";
		$this->insert_wmessage($fromUsername, $contentStr, $belong);
		$this->weixin_api->send_custom_message($fromUsername, $contentStr, false);
		$this->logger->debug($contentStr);
		$this->logger->debug("供货商代理认证逻辑处理完毕");
	}

	protected function plusPoint($val, $fromUsername){
		return 0;
	}
	
}