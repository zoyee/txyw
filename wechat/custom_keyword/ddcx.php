<?php
/**
 * 订单查询
 * @author liuzhy
 *
 */
class ddcx extends keyword_processor {

	function __construct(){
		$this->logger = LoggerManager::getLogger(basename(__FILE__));
		$this->keyword_name = "订单查询";
	}

	public function process($processor = null, $fromUsername = null, $toUsername = null, $belong = null){
		$time = time();
		$data = $this->get_orders($fromUsername);
		$this->logger->debug('$data = ' . json_encode($data));
		if($data['ArticleCount'] == 0){
			$resultStr = sprintf(Template::$textTpl, $fromUsername, $toUsername, $time, "您还没有订单");
		}else{
			$resultStr = sprintf(Template::$newsTpl, $fromUsername, $toUsername, $time, $data['ArticleCount'], $data['items']);
		}
		$this->insert_wmessage($fromUsername, $resultStr, $belong);
		echo $resultStr;
		$this->logger->debug($resultStr);
	}

	protected function plusPoint($child_processor, $fromUsername){
		$this->common_plusPoint($fromUsername);
	}

	private function get_orders($fromUsername){
		$user_id = parent::$db -> getOne("SELECT `user_id` FROM " . $GLOBALS['ecs']->table('users') . " WHERE  `wxid` ='$fromUsername'");
		$base_img_path = parent::$base_url;

		if(!empty($user_id)){
			$orders = parent::$db -> getAll("SELECT * FROM " . $GLOBALS['ecs']->table('order_info') . " WHERE `user_id` = '$user_id' ORDER BY `order_id` DESC LIMIT 0,5");
			$ArticleCount = count($orders);
			$items = '';
			if ($ArticleCount >= 1) {
				foreach($orders as $k => $v) {
					$order_id = $v['order_id'];
					$order_goods = parent::$db -> getAll("select og.goods_name, g.goods_img from " . $GLOBALS['ecs']->table('order_goods') . " og left join " . $GLOBALS['ecs']->table('goods') . " g on og.goods_id=g.goods_id where og.order_id='$order_id'");
					$goods_info = '';
					foreach($order_goods as $vv) {
						$goods_info .= $vv['goods_name'] . ',';
					}
					$goods_info = substr($goods_info, 0, strlen($goods_info)-1);

					$picUrl = "";
					if(count($order_goods) > 0){
						if(parent::$wxch_cfg['imgpath'] == 'local'){
							$picUrl = $base_img_path . $vv['goods_img'];
						}elseif(parent::$wxch_cfg['imgpath'] == 'server'){
							$picUrl = $vv['goods_img'];
						}
					}

					$url = "";
					if(parent::$oauth_state == 'true'){
						$url = parent::$oauth_location .  parent::$m_url . 'user.php?act=order_detail&order_id=' . $v['order_id'];
					} else {
						$url = 'user.php?act=order_detail&order_id=' . $v['order_id'] . '&wxid=' . $fromUsername;
					}
					$title = $v['order_sn'] . "|" . $goods_info;

					$items .= "<item>
		                 <Title><![CDATA[" . $title . "]]></Title>
		                 <PicUrl><![CDATA[" . $picUrl . "]]></PicUrl>
		                 <Url><![CDATA[" . $url . "]]></Url>
		                 </item>";
				}
			}
			$data = array();
			$data['ArticleCount'] = $ArticleCount;
			$data['items'] = $items;
			return $data;
		} else {
			$data = array();
			$data['ArticleCount'] = 0;
			$data['items'] = "";
			return $data;
		}
	}
}