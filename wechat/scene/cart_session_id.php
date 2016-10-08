<?php
/**
 * 购物车session，购物车衔接
 * @author liuzhy
 *
 */
class cart_session_id extends scene_processor{

	function __construct(){
		$this->logger = LoggerManager::getLogger(basename(__FILE__));
		$this->weixin_api = new weixin_api();
		$this->keyword_name = "购物车衔接";
	}

	public function process($val = null, $fromUsername = null, $toUsername = null, $belong = null, $is_new_user = false){
		$this->logger->debug('cart_session_id = ' . $val);
		$user_id = parent::$db -> getOne("SELECT user_id FROM " . $GLOBALS['ecs']->table('users') . " WHERE `wxid` = '$fromUsername'");
		
		$cart_list = parent::$db -> getAll("select c.goods_name, g.goods_thumb from " . $GLOBALS['ecs']->table('cart') . 
				" c, " . $GLOBALS['ecs']->table('goods') . " g where c.goods_id=g.goods_id and session_id='$val' order by rec_id");
				
		parent::$db->query("update " . $GLOBALS['ecs']->table('cart') . " set user_id='$user_id' where session_id='$val'");
		
		$picurl = "";
		$description = "";
		$title = "直达购物车";
		$url = parent::$m_url . 'flow.php';
		if(count($cart_list) > 0){
			$picurl = parent::$base_url . $cart_list[0]['goods_thumb'];
			foreach ( $cart_list as $row ) {
       			$description .= $row['goods_name'] . '/';
			}
			$description = substr($description, 0, strlen($description) - 1);
		}else{
			return 0;
		}
		$param = array();
		array_push($param, array(
				'title' => $title,
				'description' => $description,
				'url' => $url,
				'picurl' => $picurl
		));
				
		$this->weixin_api->send_custom_multi_news($fromUsername, $param);
		$this->logger->debug("购物车衔接逻辑处理完毕");
		return 1;
	}

	protected function plusPoint($val, $fromUsername){
		return 0;
	}

}