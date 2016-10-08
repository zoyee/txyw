<?php
require_once ('api/weixin_api.php');
$weixin_api = new weixin_api();
$logger = LoggerManager::getLogger('mobile/wxch_order.php');
$time = time();
if(empty($wxch_order_name)){
	$wxch_order_name = 'reorder';
}

$wxch_user_id = $_SESSION['user_id'];
if(empty($wxch_user_id)){
	$wxch_user_id=$order['user_id'];
}

$nickname = "";
if($wxch_user_id > 0){
// 	$access_token = access_token($db);
// 	$url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$access_token;
	$query_sql = "SELECT * FROM " . $ecs->table('users') . " WHERE user_id = '$wxch_user_id'";
	$ret_w = $db->getRow($query_sql);
	$wxid = $ret_w['wxid'];
	if($wxid){
		$query_sql = "SELECT nickname FROM wxch_user WHERE wxid = '$wxid'";
		$nickname = $db->getOne($query_sql);
	}
	if(empty($order['order_id'])){
		$order['order_id'] = $order_id;
	}
	if(empty($order_id)){
		$order_id = $order['order_id'];
	}

// 	if($wxch_order_name == 'pay'){
// 		$orders_sql = "SELECT * FROM " . $ecs->table('order_info') . " WHERE `order_id` = '$order_id'";
// 		$orders = $db->getRow($orders_sql);
// 		$order_goods = $db->getAll("SELECT * FROM " . $ecs->table('order_goods') . "  WHERE `order_id` = '$order_id'");
// 	} else {
		$orders = $db->getRow("SELECT * FROM " . $ecs->table('order_info') . " WHERE `order_id` = '$order_id' ");
		$order_goods = $db->getAll("SELECT * FROM " . $ecs->table('order_goods') . "  WHERE `order_id` = '$order_id'");
// 	}

	$shopinfo = '';
	if(!empty($order_goods)) {
		foreach($order_goods as $v) {
			if(empty($v['goods_attr'])) {
				$shopinfo .= $v['goods_name'].'('.$v['goods_number'].'),';
			} else {
				$shopinfo .= $v['goods_name'].'【'.$v['goods_attr'].'】'.'('.$v['goods_number'].'),';
			}
		}
		$shopinfo = substr($shopinfo, 0, strlen($shopinfo)-1);
	}
	/*店   铺   地  址：         http://           we10.taobao.     com*/
// 	$sql = "SELECT * FROM wxch_order WHERE order_name = '$wxch_order_name'";
// 	$cfg_order = $db->getRow($sql);
 	$cfg_baseurl = $db->getOne("SELECT cfg_value FROM wxch_cfg WHERE cfg_name = 'baseurl'");
 	$cfg_murl = $db->getOne("SELECT cfg_value FROM wxch_cfg WHERE cfg_name = 'murl'");
	if($orders['pay_status'] == 0) {
// 		$pay_status = '支付状态：未付款';
		$pay_status = '未付款';
	} elseif($orders['pay_status'] == 1) {
// 		$pay_status = '支付状态：付款中';
		$pay_status = '付款中';
	} elseif($orders['pay_status'] == 2) {
// 		$pay_status = '支付状态：已付款';
		$pay_status = '已付款';
	}
// 	$wxch_address = "\r\n收件地址：".$orders['address'];
// 	$wxch_consignee = "\r\n收件人：".$orders['consignee'];
// 	$wxch_address = $orders['address'];
// 	$wxch_consignee = $orders['consignee'];
// 	$w_title = $cfg_order['title'];
	if($orders['order_amount'] == '0.00') {
		$orders['order_amount'] = $orders['surplus'];
	}
// 	$w_description = '订单号：'.$orders['order_sn']."\r\n".'商品信息：'.$shopinfo."\r\n总金额：".$orders['order_amount']."\r\n".$pay_status.$wxch_consignee.$wxch_address;
// 	$w_url = $cfg_baseurl.$cfg_murl.'user.php?act=order_detail&order_id='.$order['order_id'].'&wxid='.$wxid;
// 	$http_ret1 = stristr($cfg_order['image'],'http://');
// 	$http_ret2 = stristr($cfg_order['image'], 'http:\\');
// 	if($http_ret1 or $http_ret2)
// 	{
// 		$w_picurl = $cfg_order['image'];
// 	}
// 	else
// 	{
// 		$w_picurl = $cfg_baseurl."mobile/".$cfg_order['image'];

// 	}

// 	$post_msg = '{
//        "touser":"'.$wxid.'",
//        "msgtype":"news",
//        "news":{
//            "articles": [
//             {
//                 "title":"'.$w_title.'",
//                 "description":"'.$w_description.'",
//                 "url":"'.$w_url.'",
//                 "picurl":"'.$w_picurl.'"
//             }
//             ]
//        }
//    }';
// 	$ret_json = curl_grab_page($url, $post_msg);
// 	$ret = json_decode($ret_json);
// 	$logger->debug('微信推送 $url = ' . $url);
// 	$logger->debug('$post_msg = ' . $post_msg);
// 	$logger->debug('$ret_json = ' . $ret_json);
// 	if($ret->errmsg != 'ok')
// 	{
// 		$access_token = new_access_token($db);
// 		$url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$access_token;
// 		$ret_json = curl_grab_page($url, $post_msg);
// 		$ret = json_decode($ret_json);
// 	}

	$logger->debug("订单提醒");
	$weixin_api->send_template_msg("pay", array(
			"touser" => $wxid,
			"url" => $w_url,
			"order_id" => $orders['order_sn'],
			"goods_name" => $shopinfo,
			"tatol_amount" => $orders['order_amount'],
			"pay_status" => $pay_status,
			"consignee" => $orders['consignee'],
			"address" => $orders['address']
	));
	
	$prize = $GLOBALS['db']->getRow("SELECT * FROM wxch_prize WHERE `is_show` = 1 ");
	$site_url=$config['site_url'];
	$gwcj_url = $site_url."http://shop.byhill.com/wechat/dzp/index.php?pid=3&wxid=".$wxid;
	if($prize['price'] <=$orders['money_paid'] && $prize['is_show'] == 1 && $wxid == 'oQ5ZLs3Y3sam9SBFHC0Na3rVWby8'){
		$weixin_api->send_template_msg("gwcj", array(
				"touser" => $wxid,
				"url" => $gwcj_url
		));
	}

	/* 分成提醒 */
	$affiliate = unserialize($GLOBALS['_CFG']['affiliate']);
	$num = count($affiliate['item']);	//分销层级
	$money = $orders['fencheng'];		//分成金额
	$row['user_id'] = $wxch_user_id;
	$logger->debug("分成提醒");
    for ($i=0; $i < $num; $i++) {
		$wxid=0;
		$row = $db->getRow("SELECT o.parent_id as user_id,u.user_name FROM " . $GLOBALS['ecs']->table('users') . " o" .
                        " LEFT JOIN" . $GLOBALS['ecs']->table('users') . " u ON o.parent_id = u.user_id".
                        " WHERE o.user_id = '$row[user_id]'" );
		$up_uid = $row['user_id'];
		if(empty($up_uid)) break;

	   	$query_sql = "SELECT wxid FROM " . $ecs->table('users') . " WHERE user_id = '$up_uid'";
		$ret_w = $db->getRow($query_sql);
		$wxid = $ret_w['wxid'];
		$num_tianxin100 = $i + 1;
// 		if($wxch_order_name == 'pay') {
// 			$w_title= "有笔银子进入口袋";//"窝友".$nickname."付款了";//"您的".$num_tianxin100."级会员".$nickname."付款了";
// 		} else {
// 			$w_title= "窝友".$nickname."下单了";//"您的".$num_tianxin100."级会员".$nickname."下单了";
// 		}
		$affiliate['item'][$i]['level_money'] = (float)$affiliate['item'][$i]['level_money'];
		if ($affiliate['item'][$i]['level_money']) {
           $affiliate['item'][$i]['level_money'] /= 100;
        }
		$yongjin_tianxin100 = round($money * $affiliate['item'][$i]['level_money'], 2);
// 		$w_description= "订单号：".$orders['order_sn']."\r\n总金额：".$orders['order_amount']."您将获得奖励：".$yongjin_tianxin100."\r\n".$pay_status;
        $wp_url = $cfg_baseurl.$cfg_murl."distribute.php?act=myorder&user_id=".$wxch_user_id."&level=".$num_tianxin100;

//        $weixin_api->send_template_msg("reorder", array(
//        		"touser" => $wxid,
//        		"url" => $w_url,
//        		"nick_name" => $nickname,
//        		"order_id" => $orders['order_sn'],
//        		"tatol_amount" => $orders['order_amount'],
//        		"yongjin" => $yongjin_tianxin100
//        ));
		$first = "您的会员下单了";
		if($i == 0){
			$first = "您的金牌会员下单了";
		}
		if($i == 1){
			$first = "您的银牌会员下单了";
		} 
        $weixin_api->send_template_msg("reorder", array(
        		'touser' => $wxid,
        		'url' => $wp_url,
        		'first' => $first,
        		'keyword1' => $nickname,
				'keyword2' => $orders['order_sn'],
				'keyword3' => $orders['goods_amount'],
				'keyword4' => $orders['money_paid'],
				'remark' => '我们正在快马加鞭筹备新品，记得常回来看看哦',
        ));

// 		$post_msg = '{
//        "touser":"'.$wxid.'",
//        "msgtype":"news",
//        "news":{
//            "articles": [
//             {
//                 "title":"'.$w_title.'",
//                 "description":"'.$w_description.'",
//                 "url":"'.$wp_url.'",
//                 "picurl":"'.$w_picurl.'"
//             }
//             ]
//        }
//    }';
// 	$ret_json = curl_grab_page($url, $post_msg);
// 	$ret = json_decode($ret_json);
// 	$logger->debug('微信推送分享 $url = ' . $url);
// 	$logger->debug('$post_msg = ' . $post_msg);
// 	$logger->debug('$ret_json = ' . $ret_json);
// 	if($ret->errmsg != 'ok')
// 	{
// 		$access_token = new_access_token($db);
// 		$url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$access_token;
// 		$ret_json = curl_grab_page($url, $post_msg);
// 		$logger->debug('微信推送分享NO $url = ' . $url);
// 		$logger->debug('$post_msg = ' . $post_msg);
// 		$logger->debug('$ret_json = ' . $ret_json);
// 		$ret = json_decode($ret_json);
// 	}


	}

	//PRINCE新增拼团提醒 Start
	if($wxch_order_name == 'pay' && $orders['extension_code'] == 'pintuan'){
		$logger->debug("拼团提醒");
			$sql = "SELECT * FROM " . $GLOBALS['ecs']->table('pintuan_orders') . " WHERE order_id = '$order_id' ";
			$pintuan = $GLOBALS['db']->getRow($sql);
			$pt_id = $pintuan['pt_id'];
			$act_user = $pintuan['act_user'];
			$follow_user = $pintuan['follow_user'];

			$tuan = $GLOBALS['db']->getRow("select * from " . $GLOBALS['ecs']->table('pintuan') . " where pt_id='$pt_id'");
			$need_num = $tuan['need_people'] - $tuan['avaliable_people'];

			$query_sql_1 = "SELECT * FROM " . $GLOBALS['ecs']->table('users') . " WHERE user_id = '$act_user'";
			$ret_w_1 = $GLOBALS['db']->getRow($query_sql_1);
			$act_wxid = $ret_w_1['wxid'];

			$query_sql_2 = "SELECT * FROM " . $GLOBALS['ecs']->table('users') . " WHERE user_id = '$follow_user'";
			$ret_w_2 = $GLOBALS['db']->getRow($query_sql_2);
			$follow_wxid = $ret_w_2['wxid'];

			$cfg_baseurl = $GLOBALS['db']->getOne("SELECT cfg_value FROM wxch_cfg WHERE cfg_name = 'baseurl'");
			$cfg_murl = $GLOBALS['db']->getOne("SELECT cfg_value FROM wxch_cfg WHERE cfg_name = 'murl'");

			$w1_url = $cfg_baseurl.$cfg_murl.'pintuan.php?act=pt_view&pt_id='.$pt_id.'&u='.$act_user;
			$w2_url = $cfg_baseurl.$cfg_murl.'pintuan.php?act=pt_view&pt_id='.$pt_id.'&u='.$follow_user;
			$w_picurl = $cfg_baseurl."mobile/images/weixin/wxch_pt.jpg";
// 			$w_description="快快点击进入分享给朋友们参团吧";

		    if($act_user == $follow_user){
// 				$w_title="成功发起拼团";
		    	$weixin_api->send_template_msg('launch_pintuan', array(
		    			"touser" => $act_wxid,
		    			"url" => $w1_url,
		    			"need_num" => $need_num
		    	));
			}else{
// 				$w_title="成功参与拼团";
				$weixin_api->send_template_msg('pintuan_remind', array(
		    			"touser" => $follow_wxid,
		    			"url" => $w2_url,
		    			"need_num" => $need_num
		    	));

				$weixin_api->send_template_msg('pintuan_remind', array(
						"touser" => $act_wxid,
						"url" => $w1_url,
						"need_num" => $need_num
				));
			}

// 			$post_msg = '{
// 			   "touser":"'.$follow_wxid.'",
// 			   "msgtype":"news",
// 			   "news":{
// 				   "articles": [
// 					{
// 						"title":"'.$w_title.'",
// 						"description":"'.$w_description.'",
// 						"url":"'.$w2_url.'",
// 						"picurl":"'.$w_picurl.'"
// 					}
// 					]
// 			   }
// 		    }';
// 			$ret_json = curl_grab_page($url, $post_msg);
// 			$ret = json_decode($ret_json);

// 		    if($act_user!=$follow_user){
// 				$w_title="有新朋友参加您的拼团啦";
// 				$w_description="快快点击进入分享更多朋友参团吧";
// 				$post_msg = '{
// 				   "touser":"'.$act_wxid.'",
// 				   "msgtype":"news",
// 				   "news":{
// 					   "articles": [
// 						{
// 							"title":"'.$w_title.'",
// 							"description":"'.$w_description.'",
// 							"url":"'.$w1_url.'",
// 							"picurl":"'.$w_picurl.'"
// 						}
// 						]
// 				   }
// 				}';
// 				$ret_json = curl_grab_page($url, $post_msg);
// 				$ret = json_decode($ret_json);
// 			}

	}
	//PRINCE新增拼团提醒  End
	$logger->debug("提醒结束1");
}

// function get_award_scale($user_id)
// {
// 	$sql = "select rank_points from ".$GLOBALS['ecs']->table('users')." where user_id = ".$user_id;
// 	$rank_points = $GLOBALS['db']->getOne($sql);
// 	$sql = "select award_scale,award_on from ".$GLOBALS['ecs']->table('user_rank')." where min_points <= " . intval($rank_points) . ' AND max_points > ' . intval($rank_points);
// 	$data = $GLOBALS['db']->getRow($sql);
// 	if ($data['award_on'] > 0){
// 		$award_scale = $data['award_scale'];
// 	}else {
// 		$award_scale = 0;
// 	}

// 	return $award_scale;
// }

?>