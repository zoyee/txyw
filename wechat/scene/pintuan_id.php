<?php
/**
 * 拼团分享
 * @author liuzhy
 *
 */
class pintuan_id extends scene_processor{

	function __construct(){
		$this->logger = LoggerManager::getLogger(basename(__FILE__));
		$this->weixin_api = new weixin_api();
		$this->keyword_name = "拼团分享";
	}

	public function process($val = null, $fromUsername = null, $toUsername = null, $belong = null, $is_new_user = false){
		$this->logger->debug('pintuan_id = ' . $val);
		$user_id = parent::$db -> getOne("SELECT user_id FROM " . $GLOBALS['ecs']->table('users') . " WHERE `wxid` = '$fromUsername'");
		
		$pintuan_info = $this->pintuan_detail_info($val);
//		$this->logger->debug('$pintuan_info = ' . json_encode($pintuan_info));
		$url = $pintuan_info['url'] . "&u=" . $user_id;
		$param = array();
		array_push($param, array(
				'title' => "拼团在这里",
				'description' => $pintuan_info['act_name'],
				'url' => $url,
				'picurl' => $pintuan_info['goods_thumb']
		));
				
		$this->weixin_api->send_custom_multi_news($fromUsername, $param);
		$this->logger->debug("拼团分享衔接逻辑处理完毕");
		return 1;
	}

	protected function plusPoint($val, $fromUsername){
		return 0;
	}


	private function pintuan_detail_info($pintuan_id) {
		$sql = "SELECT ga.*,IFNULL(g.goods_thumb, '') AS goods_thumb, pt.*,g.* " . "FROM  " . 
				$GLOBALS ['ecs']->table ( 'pintuan' ) . " AS pt  " . "LEFT JOIN " . 
				$GLOBALS ['ecs']->table ( 'goods_activity' ) . " AS ga ON pt.act_id  = ga.act_id LEFT JOIN " . 
				$GLOBALS ['ecs']->table ( 'goods' ) . " AS g ON ga.goods_id = g.goods_id " . "WHERE pt.pt_id=" . $pintuan_id;
		$pintuan = parent::$db->getRow ( $sql );
		$ext_info = unserialize ( $pintuan ['ext_info'] );
		$pintuan = array_merge ( $pintuan, $ext_info );
		if ($pintuan ['goods_thumb']) {
			$pintuan ['goods_thumb'] = parent::$base_url . $pintuan ['goods_thumb'];
		}
		$pintuan ['url'] = parent::$m_url . 'pintuan.php?act=view&act_id=' . $pintuan ['act_id'];
		return $pintuan;
	}
}