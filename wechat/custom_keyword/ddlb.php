<?php
/**
 * 订单列表
 * @author liuzhy
 *
 */
class ddlb extends keyword_processor {

	function __construct(){
		$this->logger = LoggerManager::getLogger(basename(__FILE__));
		$this->keyword_name = "订单列表";
	}

	public function process($processor = null, $fromUsername = null, $toUsername = null, $belong = null){
		$time = time();
		$data = $this->get_orders($fromUsername);
		$resultStr = sprintf(Template::$textTpl, $fromUsername, $toUsername, $time, $data);
		$this->insert_wmessage($fromUsername, $resultStr, $belong);
		echo $resultStr;
		$this->logger->debug($resultStr);
	}

	protected function plusPoint($child_processor, $fromUsername){
		$this->common_plusPoint($fromUsername);
	}

	private function get_orders($fromUsername){
		$user_id = parent::$db -> getOne("SELECT `user_id` FROM " . $GLOBALS['ecs']->table('users') . " WHERE  `wxid` ='$fromUsername'");

		if(!empty($user_id)){
			$orders = parent::$db -> getAll("SELECT * FROM " . $GLOBALS['ecs']->table('order_info') . " WHERE `user_id` = '$user_id' ORDER BY `order_id` DESC LIMIT 0,5");
			$ArticleCount = count($orders);
			if ($ArticleCount >= 1) {
				$items = '';
				foreach($orders as $k => $v) {
					$order_id = $v['order_id'];
					$order_goods = parent::$db -> getAll("SELECT * FROM " . $GLOBALS['ecs']->table('order_goods') . "  WHERE `order_id` = '$order_id'");
					$goods_info = '';
					foreach($order_goods as $vv) {
						if (empty($v['goods_attr'])) {
							$goods_info .= $vv['goods_name'] . '(' . $vv['goods_number'] . '),';
						} else {
							$goods_info .= $vv['goods_name'] . '（' . $vv['goods_attr'] . '）' . '(' . $vv['goods_number'] . '),';
						}
					}
					$goods_info = substr($goods_info, 0, strlen($goods_info)-1);
					if ($k != 0) {
						if (parent::$oauth_state == 'true') {
							$title = "\r\n" . '------------------' . "\r\n" . '订单号：<a href="' . parent::$oauth_location . parent::$m_url . 'user.php?act=order_detail&order_id=' . $v['order_id'] . '">' . $v['order_sn'] . "</a>";
						} else {
							$title = "\r\n" . '------------------' . "\r\n" . '订单号：<a href="' . parent::$m_url . 'user.php?act=order_detail&order_id=' . $v['order_id'] . '&wxid=' . $fromUsername . '">' . $v['order_sn'] . "</a>";
						}
					} else {
						if (parent::$oauth_state == 'true') {
							$title = '订单号：<a href="' . parent::$oauth_location . parent::$m_url . 'user.php?act=order_detail&order_id=' . $v['order_id'] . '">' . $v['order_sn'] . "</a>\r\n";
						} else {
							$title = '订单号：<a href="' . parent::$m_url . 'user.php?act=order_detail&order_id=' . $v['order_id'] . '&wxid=' . $fromUsername . '">' . $v['order_sn'] . "</a>\r\n";
						}
					}
					if ($v['order_amount'] == 0.00) {
						if ($v['money_paid'] > 0) {
							$v['order_amount'] = $v['money_paid'];
						}
					}
					$description = "\r\n商品信息：" . $goods_info . "\r\n总金额：" . $v['order_amount'] . "\r\n物流公司：" . $v['shipping_name'] . "\r\n物流单号：" . $v['invoice_no'];
					$items .= $title . $description;
				}
				if (parent::$oauth_state == 'true') {
					$items_oder_list = '<a href="' . parent::$oauth_location . parent::$m_url . 'user.php?act=order_list">"我的订单"</a>';
				} else {
					$items_oder_list = '<a href="' . parent::$m_url . 'user.php?act=order_list&wxid=' . $fromUsername . '">"我的订单"</a>';
				}
				$items_more = "\r\n" . '更多详细信息请点击' . $items_oder_list . '';
				$contentStr = $items . $items_more;
				return $contentStr;
			} else {
				$contentStr = "您还没有订单";
				return $contentStr;
			}
		} else {
			$contentStr = "没有找到匹配的用户信息！请联系在线客服！";
			return $contentStr;
		}
	}
}