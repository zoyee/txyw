<?php
/**
 * 推荐商品
 * @author liuzhy
 *
 */
class goods_id extends scene_processor{

	function __construct(){
		$this->logger = LoggerManager::getLogger(basename(__FILE__));
		$this->weixin_api = new weixin_api();
		$this->keyword_name = "推荐商品";
	}

	public function process($val = null, $fromUsername = null, $toUsername = null, $belong = null, $is_new_user = false){
		$this->logger->debug('goods_id = ' . $val);
		$goods = parent::$db->getRow("select goods_name, goods_thumb from ecs_goods where goods_id = '$val'");
		$good_name = $goods['goods_name'];
		$good_img = $goods['goods_thumb'];
		$this->weixin_api->send_custom_single_news($fromUsername, $good_name, '',
				parent::$base_url . $good_img,
				parent::$m_url . 'goods.php?id='.$val);
		$this->logger->debug("推荐商品逻辑处理完毕");
	}

	protected function plusPoint($val, $fromUsername){
		return 0;
	}

}