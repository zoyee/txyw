<?php
/**
 * 根据名称查询商品
 * @author liuzhy
 *
 */
class query_goods extends keyword_processor {

	function __construct(){
		$this->logger = LoggerManager::getLogger(basename(__FILE__));
		$this->weixin_api = new weixin_api();
		$this->keyword_name = "查询商品";
	}

	public function process($keyword = null, $fromUsername = null, $toUsername = null, $belong = null){
		$time = time();
		$param = $this->get_goods($keyword, $fromUsername);
		$this->logger->debug('$param = ' . json_encode($param));
		if($param){
			$this->weixin_api->send_custom_multi_news($fromUsername, $param);
			return 1;
		}else{
			$this->weixin_api->send_custom_message($fromUsername, "没有找到匹配的商品，请重新输入商品名称进行查询！", 0);
			return 0;
		}
	}

	protected function plusPoint($child_processor, $fromUsername){
		return 0;
	}

	private function get_goods($keyword, $fromUsername){
		$affiliate_id = parent::$db -> getOne("SELECT `affiliate` FROM `wxch_user` WHERE `wxid` = '$fromUsername'");
		if ($affiliate_id >= 1) {
			$affiliate = '&u=' . $affiliate_id;
		}
		
		$goods_is = '';
		if (parent::$wxch_cfg['goods'] == 'false') {
			$goods_is = ' AND is_delete = 0 AND is_on_sale = 1';
		}
		
		$search_sql = "SELECT * FROM  " . $GLOBALS['ecs']->table('goods') . " WHERE  `goods_name` LIKE '%$keyword%' $goods_is ORDER BY sort_order, last_update DESC LIMIT 0,6";
		$this->logger->debug('$search_sql = ' . $search_sql);
		$ret = parent::$db -> getAll($search_sql);
		$ArticleCount = count($ret);
		if ($ArticleCount >= 1) {
			$param = array();
			foreach($ret as $v) {
				if(parent::$wxch_cfg['imgpath'] == 'local'){
					$v['thumbnail_pic'] = parent::$base_url . $v['goods_img'];
				}elseif(parent::$wxch_cfg['imgpath'] == 'server'){
					$v['thumbnail_pic'] = $v['goods_img'];
				}
				if(parent::$oauth_state == 'true'){
					$goods_url = parent::$oauth_location . parent::$m_url . 'goods.php?id=' . $v['goods_id'];
				}elseif(parent::$oauth_state == 'false'){
					$goods_url = parent::$m_url . 'goods.php?id=' . $v['goods_id'] . '&wxid=' . $fromUsername . $affiliate;
				}
				
            	array_push($param, array(
					'title' => $v['goods_name'],
					'description' => $v['goods_guide'],
					'url' => $goods_url,
					'picurl' => $v['thumbnail_pic']
				));
			}
			return $param;
		} else {
			return null;
		}
	}
}