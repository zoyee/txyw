<?php
/**
 * 卖家查询
 */
define('IN_ECTOUCH', true);

require(dirname(__FILE__) . '/include/init.php');
require_once(ROOT_PATH . 'lang/' .$_CFG['lang']. '/user.php');
$logger = LoggerManager::getLogger('suppliers.php');
$user_id = $_SESSION['user_id'];
//$logger->debug('$user_id = ' . $user_id);
$ectouch_themes = $config['site_url'] . 'mobile/themes/' . $_CFG['template'];

$act = !empty($_REQUEST['act']) ? $_REQUEST['act'] : 'order_list_page';
//$logger->debug('$act = ' . $act);
if(empty($user_id)){
	
}
//判断用户是否是卖家
$user = $db->getRow("select * from " . $ecs->table('users') . " where user_id='$user_id'");
if(empty($user['suppliers_id'])){
	
}
$suppliers_id = $user['suppliers_id'];
//$suppliers_id = 4; //DEBUG
$suppliers = $db->getRow("select * from " . $ecs->table('suppliers'). " where suppliers_id='$suppliers_id'");

$smarty->assign('lang',       $_LANG);

if ($act == 'original_goods_page'){
	//商品定义列表页面
	$goods_type = $_REQUEST['goods_type'];
	$smarty->assign('goods_type', $goods_type);
	$smarty->display("original_goods_page.dwt");
} 
elseif($act == 'order_list_page'){
	//卖家订单列表页面
	$order_type = $_REQUEST['order_type'];
	$smarty->assign('order_type', $order_type);
	$smarty->display("order_list_page.dwt");
}
elseif($act == 'query_original_goods'){
	//ajax查询商品定义
	$goods_type = $_REQUEST['goods_type'];
    $size = isset($_REQUEST['amount']) ? $_REQUEST['amount'] : 6;
	$start = isset($_REQUEST['last']) ? $_REQUEST['last'] : 0;
	
	$sql = "select IFNULL(g.goods_id, go.goods_id) as goods_id, go.goods_sn, go.goods_guide, go.goods_name, go.goods_thumb, go.market_price, g.shop_price, IFNULL(g.is_on_sale, 0) as on_sale, g.suppliers_id from " . $ecs->table('goods_original') . 
				" go left join " . $ecs->table('goods') . " g on go.goods_sn = g.goods_sn and g.suppliers_id='$suppliers_id'";
//	$logger->debug($sql);
	if($goods_type == 'onsale'){
		//已上架
		$sql .= " where g.goods_id is not null";
	}elseif($goods_type == 'unOnsale'){
		//未上架
		$sql .= " where g.goods_id is null";
	}
	$sql .= " order by go.goods_id desc limit $start, $size";
	
//	$logger->debug($sql);
	$goods = $db->getAll($sql);
//	$logger->debug(json_encode($goods));
	
	if(is_array($goods)){
        foreach($goods as $vo){
        	$vo['order_time'] = local_date($GLOBALS['_CFG']['time_format'], $vo['add_time']);
        	$vo['total_fee'] = price_format($vo['total_fee'], false);
        	$goods_info = $vo['goods_name'];
        	if($vo['goods_guide']){
        		$goods_info .= '<br/>' . $vo['goods_guide'];
        	}
        	if($vo['goods_brief']){
        		$goods_info .= '<br/>' . $vo['goods_brief'];
        	}
        	
        	$goods_price = "";
        	$button = "";
        	if($vo['on_sale'] && $vo['suppliers_id']){
				//已上架
				$goods_price = "单价：<span class=\"price\">" . price_format($vo['shop_price'], false) . "</span>";
				$button = "<button onclick=\"location.href='suppliers.php?act=org_goods_detail&goods_id=$vo[goods_id]'\">变更上架</button>";
			}else{
				//未上架
				$goods_price = "参考价：<span class=\"price\">" . price_format($vo['market_price'], false) . "</span>";
				$button = "<button onclick=\"location.href='suppliers.php?act=org_goods_detail&goods_id=$vo[goods_id]'\">我要上架</button>";
			}
        	
            $asyList[] = array(
            	'goods_image' => '<img src="' . $config['site_url'] . $vo['goods_thumb'] . '">',
            	'goods_name' => $vo['goods_name'],
            	'goods_guide' => $vo['goods_guide'],
            	'goods_price' => $goods_price,
            	'original-goods-button' => $button
            );
        }
    }
    echo json_encode($asyList);
}	
elseif($act == 'query_orders'){
	//查询卖家订单列表
	$order_type = $_REQUEST['order_type'];
    $size = isset($_REQUEST['amount']) ? $_REQUEST['amount'] : 6;
	$start = isset($_REQUEST['last']) ? $_REQUEST['last'] : 0;
	
	$sql = "SELECT o.order_id, o.order_sn, o.order_status, o.shipping_id,o.invoice_no, o.shipping_status, o.pay_status, o.add_time, " .
           "(o.goods_amount + o.shipping_fee + o.insure_fee + o.pay_fee + o.pack_fee + o.card_fee + o.tax - o.discount) AS total_fee ".
           " FROM " .$GLOBALS['ecs']->table('order_info') . " o, " . $ecs->table('order_goods') . " og, " . $ecs->table('goods') . 
		   " g where o.order_id = og.order_id and og.goods_id = g.goods_id and o.order_status in(" . OS_UNCONFIRMED . "," . OS_CONFIRMED . 
			", " . OS_RETURNED . ", " . OS_SPLITED . ", " . OS_SPLITING_PART . ")";
    if($order_type == 'unConfirm'){
		//未付款
		$sql .= " and o.pay_status=" . PS_UNPAYED;
	}elseif($order_type == 'unShipping'){
		//未发货
		$sql .= " and o.shipping_status=" . SS_UNSHIPPED;
	}elseif($order_type == 'unReceive'){
		//未收货
		$sql .= " and o.shipping_status=" . SS_SHIPPED;
	}
	$sql .= " and g.suppliers_id = '$suppliers_id' ORDER BY o.add_time DESC limit $start, $size";
	
//	$logger->debug($sql);
	$orders = $db->getAll($sql);
	
	if(is_array($orders)){
        foreach($orders as $vo){
        	$vo['order_time'] = local_date($GLOBALS['_CFG']['time_format'], $vo['add_time']);
        	$vo['total_fee'] = price_format($vo['total_fee'], false);
        	
        	if($vo['pay_status'] == PS_PAYED){
        		$pay_status = "<span class=\"green\">已付款</span>";
        	}elseif($vo['pay_status'] == PS_UNPAYED){
        		$pay_status = "<span class=\"red\">未付款</span>";
        	}elseif($vo['pay_status'] == PS_PAYING){
        		$pay_status = "<span class=\"yellow\">付款中</span>";
        	}
        	
        	if($vo['shipping_status'] == SS_SHIPPED){
        		$shipping_status = "<span class=\"yellow\">已发货</span>";
        		$button = "<button onclick=\"location.href='suppliers.php?act=order_detail&order_id=$vo[order_id]'\">查&nbsp;&nbsp;看</button>";
        	}elseif($vo['shipping_status'] == SS_UNSHIPPED){
        		$shipping_status = "<span class=\"red\">未发货</span>";
        		if($vo['pay_status'] == PS_PAYED){
        			$button = "<button onclick=\"location.href='suppliers.php?act=order_detail&order_id=$vo[order_id]'\">去发货</button>";
        		}else{
        			$button = "<button onclick=\"cancel_order('$vo[order_id]')\">取消订单</button>";
        		}
        		
        	}elseif($vo['shipping_status'] == SS_RECEIVED){
        		$shipping_status = "<span class=\"green\">已收货</span>";
        		$button = "<button onclick=\"location.href='suppliers.php?act=order_detail&order_id=$vo[order_id]'\">查&nbsp;&nbsp;看</button>";
        	}
        	
        	
            //获取订单第一个商品的图片
            $img = $db->getOne("SELECT g.goods_thumb FROM " .$ecs->table('order_goods'). " as og left join " .$ecs->table('goods'). " g on og.goods_id = g.goods_id WHERE og.order_id = ".$vo['order_id']." limit 1");
            $goodsNum = $db->getOne("select sum(goods_number) from " .$ecs->table('order_goods'). " where  order_id = ".$vo['order_id']);
            
            $asyList[] = array(
            	'order_status' => $pay_status . "&nbsp;&nbsp;" . $shipping_status,
            	'goods_image' => '<img src="'.$config['site_url'].$img.'" />',
            	'order_sn' => $vo['order_sn'],
            	'invoice_no' => $vo['invoice_no'],
            	'order_time' => $vo['order_time'],
            	'goodsNum' => $goodsNum,
            	'total_fee' => $vo['total_fee'],
            	'order-list-button' => $button
            );
        }
    }
    echo json_encode($asyList);
}
elseif($act == "cancel_order"){
	//取消订单
	$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
	$db->autoExecute($ecs->table('order_info'), array(
		'order_status' => OS_CANCELED,
	), 'UPDATE', "order_id=$order_id");
	
	echo json_encode(array(
		'error' => 0,
		'message' => '操作成功！'
	));
}
elseif($act == "order_detail"){
	//查看订单详情
    include_once(ROOT_PATH . 'include/lib_transaction.php');
    include_once(ROOT_PATH . 'include/lib_payment.php');
    include_once(ROOT_PATH . 'include/lib_order.php');
    include_once(ROOT_PATH . 'include/lib_clips.php');

    $order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

    /* 订单详情 */
    $order = get_order_detail_new($order_id, $user_id);

    if ($order === false)
    {
        $err->show($_LANG['back_home_lnk'], './');
        exit;
    }

    /* 是否显示添加到购物车 */
    if ($order['extension_code'] != 'group_buy' && $order['extension_code'] != 'exchange_goods')
    {
        $smarty->assign('allow_to_cart', 1);
    }

    /* 订单商品 */
    $goods_list = order_goods($order_id);
    foreach ($goods_list AS $key => $value)
    {
        $goods_list[$key]['market_price'] = price_format($value['market_price'], false);
        $goods_list[$key]['goods_price']  = price_format($value['goods_price'], false);
        $goods_list[$key]['subtotal']     = price_format($value['subtotal'], false);
    }

     /* 设置能否修改使用余额数 */
    if ($order['order_amount'] > 0)
    {
        if ($order['order_status'] == OS_UNCONFIRMED || $order['order_status'] == OS_CONFIRMED)
        {
            $user = user_info($order['user_id']);
            if ($user['user_money'] + $user['credit_line'] > 0)
            {
                $smarty->assign('allow_edit_surplus', 1);
                $smarty->assign('max_surplus', sprintf($_LANG['max_surplus'], $user['user_money']));
            }
        }
    }
    

    /* 未发货，未付款时允许更换支付方式 */
    if ($order['order_amount'] > 0 && $order['pay_status'] == PS_UNPAYED && $order['shipping_status'] == SS_UNSHIPPED)
    {
        $payment_list = available_payment_list(false, 0, true);

        /* 过滤掉当前支付方式和余额支付方式 */
        if(is_array($payment_list))
        {
            foreach ($payment_list as $key => $payment)
            {
                if ($payment['pay_id'] == $order['pay_id'] || $payment['pay_code'] == 'balance')
                {
                    unset($payment_list[$key]);
                }
            }
        }
        $smarty->assign('payment_list', $payment_list);
    }

    
    /* 订单 支付 配送 状态语言项 */
    $order['order_status'] = $_LANG['os'][$order['order_status']];
    $order['pay_status'] = $_LANG['ps'][$order['pay_status']];
    $order['shipping_status'] = $_LANG['ss'][$order['shipping_status']];

    
    $url_ce="/mobile/flow.php?step=ok&order_id=".$order['order_sn']."&state=STATE";
    $order['pay_online']="<div style='width:90px;height:30px;'><a href='".$url_ce."' style='width:10px;margin-top:8px;padding:5px;background-color:#00D20D;color:#fff;'>微信支付</a></div>";
    $order['to_buyer'] = null;
    
    require_once "wxjs/jssdk.php";
    $ret = $db->getRow("SELECT  *  FROM `wxch_config`");
	$jssdk = new JSSDK($appid=$ret['appid'], $ret['appsecret']);
	$signPackage = $jssdk->GetSignPackage();
//	$logger->debug(json_encode($signPackage));
	$smarty->assign('signPackage',  $signPackage);
	
	//查询订单发货图片
	$sql = "select * from " . $ecs->table('order_image') . " where order_id='$order_id' and type=1";
	$order_shipping_img = $db->getAll($sql);
	$smarty->assign('order_shipping_img', $order_shipping_img);
    
    $smarty->assign('order',      $order);
    $smarty->assign('goods_list', $goods_list);
    $smarty->display('order_detail.dwt');
	
}
elseif($act == 'deliver_goods'){
	//设置为已发货
	$order_id = $_REQUEST['order_id'];
	$shipping_name = $_REQUEST['shipping_name'];
	$invoice_no = $_REQUEST['invoice_no'];
	$shipping_imgs = $_REQUEST['shipping_imgs'];
	
	if(empty($order_id)){
		$smarty->assign('ecsAlert', '缺少订单号');
		$smarty->assign('after', "location.href=\'suppliers.php?act=order_list_page';");
		$smarty->display('ecsAlert.dwt');
		exit;
	}
	
	//更新订单发货图片
	$db->query("delete from " . $ecs->table('order_image') . " where order_id='$order_id'");
	foreach($shipping_imgs as $k => $img_path){
		$db->autoExecute($ecs->table('order_image'), array(
			'order_id' => $order_id,
			'type' => 1,
			'img_path' => $shipping_imgs[$k]
		), 'INSERT');
	}
	
	$sql = "update " . $ecs->table('order_info') . " set shipping_status=" . SS_SHIPPED . ", shipping_name='$shipping_name', invoice_no='$invoice_no' where order_id='$order_id'";
	$db->query($sql);
	
	//发消息通知
	require_once ('api/weixin_api.php');
	$weixin_api = new weixin_api();
	$url = $config['site_url'] . "mobile/user.php?act=order_detail&order_id=" . $order_id;
	$order = $db->getRow("select u.wxid, o.order_sn from " . $ecs->table('order_info') . " o, " . $ecs->table('users') . " u where o.user_id = u.user_id and o.order_id='$order_id'");
//	$logger->debug('wxid = ' . $wxid);
	if($order){
		$weixin_api->send_template_msg("order", array(
    		'touser' => $order['wxid'],
    		'url' => $url,
//    		'first' => $first,
    		'order_id' => $order['order_sn'],
			'url' => $url
   		));
	}
	
	$smarty->assign('ecsAlert', '操作成功');
	$smarty->assign('after', "location.href=\'suppliers.php?act=order_list_page';");
	$smarty->display('ecsAlert.dwt');
}
elseif($act == 'org_goods_detail'){
	//查看商品详情
	$goods_id = $_REQUEST['goods_id'];
	if(empty($goods_id)){
		$smarty->assign('ecsAlert', '缺少商品ID');
		$smarty->assign('after', "location.href=\'suppliers.php?act=original_goods_page';");
		$smarty->display('ecsAlert.dwt');
		exit;
	}
	
//	$logger->debug("select goods_id, is_on_sale from " . $ecs->table('goods') . " where goods_id ='$goods_id'");
	$check = $db->getRow("select goods_id, is_on_sale from " . $ecs->table('goods') . " where goods_id ='$goods_id'");
	$smarty->assign('is_on_sale', $check['is_on_sale']);
	if($check){
		$goods = get_goods_info($goods_id);
	}else{
		$goods = get_original_goods_info($goods_id);
	}
	
	if ($goods === false) {
        $smarty->assign('ecsAlert', '没有找到对应的商品');
		$smarty->assign('after', "location.href=\'suppliers.php?act=original_goods_page';");
		$smarty->display('ecsAlert.dwt');
		exit;
    }
    
    
    if ($goods['brand_id'] > 0) {
        $goods['goods_brand_url'] = build_uri('brand', array('bid'=>$goods['brand_id']), $goods['goods_brand']);
    }

    $shop_price   = $goods['shop_price'];

    $goods['goods_style_name'] = add_style($goods['goods_name'], $goods['goods_name_style']);

    /* 购买该商品可以得到多少钱的红包 */
    if ($goods['bonus_type_id'] > 0) {
        $time = gmtime();
        $sql = "SELECT type_money FROM " . $ecs->table('bonus_type') .
                " WHERE type_id = '$goods[bonus_type_id]' " .
                " AND send_type = '" . SEND_BY_GOODS . "' " .
                " AND send_start_date <= '$time'" .
                " AND send_end_date >= '$time'";
        $goods['bonus_money'] = floatval($db->getOne($sql));
        if ($goods['bonus_money'] > 0) {
            $goods['bonus_money'] = price_format($goods['bonus_money']);
        }
    }
    /* 检查是否已经存在于用户的收藏夹 */
    $sql = "SELECT COUNT(*) FROM " .$GLOBALS['ecs']->table('collect_goods') .
        " WHERE user_id='$_SESSION[user_id]' AND goods_id = '$goods_id'";
    if ($GLOBALS['db']->GetOne($sql) > 0) {
		$goods['is_collet'] = 1;
	} else {
		$goods['is_collet'] = 0;
	}

	$goods['count'] = selled_count($goods_id);//商品销量    lw 2015-1-21
	//计算购买该商品赠送的消费积分

	$goods['give_integral_2'] = $goods['give_integral'];

	if($goods['give_integral'] > -1) {
		$goods['give_integral'] = $goods['give_integral'];
	}else{
		if($goods['promote_price']!=0) {
			$goods['give_integral'] = intval($goods['promote_price']);
		}else{
			$goods['give_integral'] = intval($goods['shop_price']);
		}
	}
	$smarty->assign('url',              $_SERVER["REQUEST_URI"]);
	//$smarty->assign('goods',              $goods);
	$smarty->assign('volume_price',       $goods_volume_price);

    $goods['goods_desc'] = str_replace('src="/images/', 'src="'.$config['site_url'].'images/', $goods['goods_desc']); //修复产品详情的图片 by wang
   
//    $logger->debug(json_encode($goods));
    $smarty->assign('goods',              $goods);
    $smarty->assign('goods_id',           $goods['goods_id']);
    $smarty->assign('promote_end_time',   $goods['gmt_end_time']);

    /* meta */
    $smarty->assign('keywords',           htmlspecialchars($goods['keywords']));
    $smarty->assign('description',        htmlspecialchars($goods['goods_brief']));


    $catlist = array();
    foreach(get_parent_cats($goods['cat_id']) as $k=>$v) {
        $catlist[] = $v['cat_id'];
    }

    assign_template('c', $catlist);

    $position = assign_ur_here($goods['cat_id'], $goods['goods_name']);

    /* current position */
    $smarty->assign('page_title',          $position['title']);                    // 页面标题
    $smarty->assign('ur_here',             $position['ur_here']);                  // 当前位置

    $properties = get_goods_properties($goods_id);  // 获得商品的规格和属性

    $smarty->assign('properties',          $properties['pro']);                              // 商品属性

	$smarty->assign('promotion',       get_promotion_info($goods_id));//促销信息
    $smarty->assign('specification',       $properties['spe']);                              // 商品规格
    $smarty->assign('attribute_linked',    get_same_attribute_goods($properties));           // 相同属性的关联商品
    $smarty->assign('related_goods',       $linked_goods);                                   // 关联商品
    $smarty->assign('fittings',            get_goods_fittings(array($goods_id)));                   // 配件

	//甜心添加判断该商品是否被收藏过
	$is_collect=0;
	$user_id=$_SESSION['user_id'];
	$sql = "SELECT * FROM " .$GLOBALS['ecs']->table('collect_goods'). " WHERE user_id = '$user_id' and goods_id='$goods_id'";
	$is_collect = $GLOBALS['db']->getRow($sql);

	if(!empty($is_collect))	{
		$smarty->assign('is_collect', 1 );
	}else{
		$smarty->assign('is_collect', 0 );
	}
	//甜心添加判断该商品是否被收藏过
	// 会员等级价格
    $smarty->assign('pictures',            get_goods_gallery($goods_id));                    // 商品相册
	$smarty->assign('best_goods',          get_recommend_goods('best',$goods['supplier_id']));     				 // 最新商品
	//tianx  in   100   添加start 多少人评价
	$count1 = $GLOBALS['db']->getOne("SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('comment') . " where comment_type=0 and id_value ='$goods_id' and status=1");
    $smarty->assign('review_count',       $count1);
	//甜心   多少人付款
	$smarty->assign('order_num',			 intval(selled_count($goods_id))+intval(sales_volume_base($goods_id)));
	//tianx  in   100添加end添加start 多少人评价
    //获取tag
    $tag_array = get_tags($goods_id);
    $smarty->assign('tags',                $tag_array);                                       // 商品的标记
	if($goods['is_buy'] == 1) {
		 if($goods['buymax_start_date'] < gmtime() && $goods['buymax_end_date'] > gmtime()) {
			  if($goods['buymax'] > 0) {
				  $tag = 1;
			  } else {
				  $tag = 0;
			  }
		 } else {
			 $tag = 0;
		 }
	} else {
		$tag = 0;
	}
	$smarty->assign('tag',$tag);


    assign_dynamic('goods');
    $volume_price_list = get_volume_price_list($goods['goods_id'], '1');
    $smarty->assign('volume_price_list',$volume_price_list);    // 商品优惠价格区间

    //限购信息
    $time_xg_now = gmtime();
    if($goods['buymax'] > 0 && $goods['buymax_start_date'] <= $time_xg_now  && $goods['buymax_end_date'] + 86400 > $time_xg_now){
    	$smarty->assign('limit_buy', 1);
    	$smarty->assign('buymax_start_date', date("Y年m月d日",strtotime(local_date('Y-m-d', $goods['buymax_start_date']))));
    	$smarty->assign('buymax_end_date', date("Y年m月d日",strtotime(local_date('Y-m-d', $goods['buymax_end_date']))));
    }
    
    $smarty->display('original_goods_detail.dwt');
}
elseif($act == 'download_image_wx'){
	//下载微信图片(发货)
	$serverId = $_REQUEST['serverId'];
	require_once(ROOT_PATH . 'api/weixin_api.php');
	$weixin_api = new weixin_api();
	$ret = $weixin_api->download_media($serverId, 'image');
	echo json_encode($ret);
}
elseif($act == 'agent_page'){
	//代理认证页面
	$type = $_REQUEST['type'];
	$smarty->assign('type', $type);
	$smarty->display("suppliers_agent_page.dwt");
}
elseif($act == 'query_suppliers_agent'){
	//查询我的认证代理
	$size = isset($_REQUEST['amount']) ? $_REQUEST['amount'] : 6;
	$start = isset($_REQUEST['last']) ? $_REQUEST['last'] : 0;
	$sql = "select u.user_id, wu.nickname, wu.headimgurl, su.status, us.shop_desc from " . $ecs->table('suppliers_user') . " su inner join " . $ecs->table('users') . 
			" u on su.user_id=u.user_id inner join wxch_user wu on u.user_name=wu.uname " .
			" left join " . $ecs->table('user_shop') . " us on su.user_id= us.user_id " .
			" where su.suppliers_id='$suppliers_id' order by su.id desc limit $start, $size";
//	$logger->debug($sql);
	$users = $db->getAll($sql);
	if(is_array($users)){
        foreach($users as $vo){
        	//查询订单数量和金额
        	$sql = "select IFNULL(o.user_id,'$vo[user_id]'), count(o.order_sn) as order_count, IFNULL(sum(o.order_amount),0) as total_money from ecs_order_info o, " .
        			$ecs->table('order_goods') . " og, " . $ecs->table('goods') . " g where o.order_id = og.order_id and og.goods_id = g.goods_id and g.suppliers_id='$suppliers_id' and" .
					" user_id='$vo[user_id]' and order_status=1 and pay_status=2";
			$user_buy = $db->getRow($sql);
        	
        	if(empty($vo['headimgurl'])){
        		$vo['headimgurl'] = $ectouch_themes . '/images/get_avatar.png';
        	}
        	$button = "";
        	if($vo['status'] == 5){
        		$status = "<span class=\"green\">已认证</span>";
        		$button = "<button onclick=\"del_agent($vo[user_id])\">取消认证</button>";
        	}elseif($vo['status'] == 1){
        		$status = "<span class=\"yellow\">邀请中</span>";
        		$button = "<button onclick=\"cancel_invite($vo[user_id])\">取消邀请</button>&nbsp;&nbsp;<button onclick=\"invite_agent($vo[user_id])\">再次邀请</button>";
        	}elseif($vo['status'] == 2){
        		$status = "<span class=\"red\">被拒绝</span>";
        		$button = "<button onclick=\"invite_agent($vo[user_id])\">再次邀请</button>";
        	}elseif($vo['status'] == 3){
        		$status = "<span class=\"yellow\">申请中</span>";
        		$button = "<button onclick=\"refuse_agent($vo[user_id])\">拒绝申请</button>&nbsp;&nbsp;<button onclick=\"agent_pass($vo[user_id])\">认证通过</button>";
        	}elseif($vo['status'] == 4){
        		$status = "<span class=\"red\">已拒绝</span>";
        		$button = "<button onclick=\"invite_agent($vo[user_id])\">邀请认证</button>";
        	}
        	
        	$shop_img = "";
        	if(!empty($vo['shop_desc'])){
        		$shop_img = '<a href="suppliers.php?act=see_user_shop&user_id=' . $vo[user_id] . '"><img src="' . $ectouch_themes . '/images/user_shop.png"/></a>';
        	}
        	
        	$asyList[] = array(
                'headimgurl' => '<img src="' . $vo['headimgurl'] . '"  border="0"/>',
                'nickname' => $vo['nickname'],
                'order_count' => $user_buy['order_count'],
            	'total_money' => $user_buy['total_money'],
            	'supplier-agent-status' => $status,
            	'supplier-agent-button' => $button,
            	'shop-img' => $shop_img
            );
        }
	}
	echo json_encode($asyList);
}
elseif($act == 'not_agent_page'){
	//代理认证页面
	$smarty->display("suppliers_not_agent_page.dwt");
}
elseif($act == 'query_not_agent'){
	//查询没有认证的用户以供发起认证代理邀请
	$size = isset($_REQUEST['amount']) ? $_REQUEST['amount'] : 6;
	$start = isset($_REQUEST['last']) ? $_REQUEST['last'] : 0;
	$sql = "select u.user_id, wu.nickname, wu.headimgurl, us.shop_desc
				from " . $ecs->table('users') . " u 
				INNER JOIN wxch_user wu on u.user_name=wu.uname 
				LEFT JOIN " . $ecs->table('user_shop') . " us on u.user_id= us.user_id 
				where  u.user_id not in (select user_id from " . $ecs->table('suppliers_user') . " where suppliers_id='$suppliers_id')
				order by u.user_id desc limit $start, $size";
//	$logger->debug($sql);
	$users = $db->getAll($sql);
	if(is_array($users)){
        foreach($users as $vo){
        	//查询订单数量和金额
        	$sql = "select IFNULL(o.user_id,'$vo[user_id]'), count(o.order_sn) as order_count, IFNULL(sum(o.order_amount),0) as total_money from ecs_order_info o " .
					" where user_id='$vo[user_id]' and order_status=1 and pay_status=2";
//			$logger->debug($sql);
			$user_buy = $db->getRow($sql);
        	
        	if(empty($vo['headimgurl'])){
        		$vo['headimgurl'] = $ectouch_themes . '/images/get_avatar.png';
        	}
        	$button = "<button onclick=\"invite_agent($vo[user_id])\">邀请认证</button>";
        	
        	$shop_img = "";
        	if(!empty($vo['shop_desc'])){
        		$shop_img = '<a href="suppliers.php?act=see_user_shop&user_id=' . $vo[user_id] . '"><img src="' . $ectouch_themes . '/images/user_shop.png"/></a>';
        	}
        	
        	$asyList[] = array(
                'headimgurl' => '<img src="' . $vo['headimgurl'] . '"  border="0"/>',
                'nickname' => $vo['nickname'],
                'order_count' => $user_buy['order_count'],
            	'total_money' => $user_buy['total_money'],
            	'supplier-agent-button' => $button,
            	'shop-img' => $shop_img
            );
        }
	}
	echo json_encode($asyList);
}
elseif($act == 'f2f_agent_page'){
	//面对面代理认证页面
	$url = $config['site_url']. 'mobile/' . "qrcode.php?act=suppliers_agent&suppliers_id=" . $suppliers_id;
//	$logger->debug($url);
	$result = file_get_contents($url);
//	$logger->debug('$result = ' . $result);
	$json = json_decode($result);
	if($json->errcode == 0){
		$smarty->assign('user_qr', $json->data);
	}
	$smarty->display("suppliers_f2f_agent_page.dwt");
}
elseif($act == 'do_operate_agent'){
	require_once(ROOT_PATH . 'api/weixin_api.php');
	$weixin_api = new weixin_api();
	$time = date('Y-m-d H:i:s', time());
	$opt = $_REQUEST['opt'];
	$to_user_id = $_REQUEST['user_id'];
	$wxid = $db->getOne('select wxid from ' . $ecs->table('users') . " where user_id='$to_user_id'");
	$sql = "select id from " . $GLOBALS['ecs']->table('suppliers_user') . " where suppliers_id = '$suppliers_id' and user_id='$to_user_id'";
	$id = $db->getOne($sql);
	
	if($opt == 'del'){
		$db->query("delete from " . $ecs->table('suppliers_user') . " where id='$id'");
		$message = "您的供应商【$suppliers[suppliers_name]】认证代理身份被解除！";
	}elseif($opt == 'cancel'){
		$db->query("delete from " . $ecs->table('suppliers_user') . " where id='$id'");
		$message = "供应商【$suppliers[suppliers_name]】取消邀请您成为认证代理！";
	}elseif($opt == 'pass'){
		$db->query("update " . $ecs->table('suppliers_user') . " set status = 5, reply_time='$time' where id='$id'");
		$message = "您的供应商【$suppliers[suppliers_name]】认证代理请求审核通过了！";
	}elseif($opt == 'refuse'){
		$db->query("update " . $ecs->table('suppliers_user') . " set status = 4, reply_time='$time' where id='$id'");
		$message = "供应商【$suppliers[suppliers_name]】认证代理请求被拒绝了！";
	}elseif($opt == 'invite'){
		if($id){
			$db->query("update " . $ecs->table('suppliers_user') . " set status = 1, add_time='$time' where id='$id'");
		}else{
			$db->query("insert into " . $ecs->table('suppliers_user') . " (suppliers_id, user_id, status, add_time) values" .
					"('$suppliers_id', '$to_user_id', 1, '$time')");
		}
		$message = "供应商【$suppliers[suppliers_name]】邀请您成为认证代理，长按识别二维码即可同意邀请";
		//生成二维码
		$url = $config['site_url']. 'mobile/' . "qrcode.php?act=suppliers_agent&suppliers_id=" . $suppliers_id;
		$logger->debug($url);
		$result = file_get_contents($url);
		$logger->debug('$result = ' . $result);
		$json = json_decode($result);
		if($json->errcode == 0){
			$qr_image = ROOT_PATH . $json->data;
//			$logger->debug('$qr_image = ' . $qr_image);
			
//			$logger->debug('wxid = ' . $wxid);
			$weixin_api->send_custom_image($wxid, $qr_image, 0);
		}
	}
	
//	$logger->debug($message);
	$weixin_api->send_custom_message($wxid, $message, false);
}
elseif($act == 'on_shelves_goods'){
	//商品上架
	$goods_sn = $_REQUEST['goods_sn'];
	$goods_number = $_REQUEST['goods_number'];
	$goods_price = $_REQUEST['goods_price'];
	$goods_brief = $_REQUEST['goods_brief'];
	
	//判断商品是否已经存在
	$sql = "select goods_id from " . $ecs->table('goods') . " where goods_sn='$goods_sn' and suppliers_id='$suppliers_id'";
//	$logger->debug($sql);
	$goods_id = $db->getOne($sql);
	if($goods_id){
		$sql = "update " . $ecs->table('goods') . " set is_on_sale = 1, goods_number = $goods_number, shop_price=$goods_price, goods_brief='$goods_brief' where goods_id=$goods_id";
		$db->query($sql);
	}else{
		//复制商品
		$original_goods = $db->getRow("select * from " . $ecs->table('goods_original') . " where goods_sn='$goods_sn'");
		$original_goods['goods_id'] = null;
		$original_goods['is_on_sale'] = 1;
		$original_goods['goods_number'] = $goods_number;
		$original_goods['shop_price'] = $goods_price;
		$original_goods['suppliers_id'] = $suppliers_id;
		$original_goods['goods_brief'] = $goods_brief;
		$db->autoExecute($ecs->table('goods'), $original_goods, 'INSERT');
        $original_goods['goods_id'] = $db->insert_id();
        
        //复制商品属性
        $org_goods_id = $db->getOne("select goods_id from " . $ecs->table('goods_original') . " where goods_sn='$goods_sn'");
        $goods_attrs = $db->getAll("select * from " . $ecs->table('goods_attr') . " where goods_id='$org_goods_id'");
        foreach($goods_attrs as $k => $vo){
        	$vo['goods_id'] = $original_goods['goods_id'];
        	$vo['goods_attr_id'] = null;
        	$db->autoExecute($ecs->table('goods_attr'), $vo, 'INSERT');
        }
        
        $goods_articles = $db->getAll("select * from " . $ecs->table('goods_article') . " where goods_id='$org_goods_id'");
        foreach($goods_articles as $k => $vo){
        	$vo['goods_id'] = $original_goods['goods_id'];
        	$db->autoExecute($ecs->table('goods_article'), $vo, 'INSERT');
        }
        
        $goods_cats = $db->getAll("select * from " . $ecs->table('goods_cat') . " where goods_id='$org_goods_id'");
        foreach($goods_cats as $k => $vo){
        	$vo['goods_id'] = $original_goods['goods_id'];
        	$db->autoExecute($ecs->table('goods_cat'), $vo, 'INSERT');
        }
        
        $goods_galleries = $db->getAll("select * from " . $ecs->table('goods_gallery') . " where goods_id='$org_goods_id'");
        foreach($goods_galleries as $k => $vo){
        	$vo['goods_id'] = $original_goods['goods_id'];
        	$vo['img_id'] = null;
        	$db->autoExecute($ecs->table('goods_gallery'), $vo, 'INSERT');
        }
	}
	$ret = array(
		'error' => 0,
		'message' => '商品上架成功！'
	);
	echo json_encode($ret);
}
elseif($act == 'down_shelves_goods'){
	//商品下架
	$goods_sn = $_REQUEST['goods_sn'];
	
	//判断商品是否已经存在
	$db->getOne("update " . $ecs->table('goods') . " set is_on_sale=0 where goods_sn='$goods_sn' and suppliers_id='$suppliers_id'");
	$ret = array(
		'error' => 0,
		'message' => '商品下架成功！'
	);
	echo json_encode($ret);
}
elseif($act == 'apply_agent'){
	//用户申请认证
	require_once(ROOT_PATH . 'api/weixin_api.php');
	$weixin_api = new weixin_api();
	$time = date('Y-m-d H:i:s', time());
	$suppliers_id = $_REQUEST['suppliers_id'];
	$sql = "select id, status from " . $GLOBALS['ecs']->table('suppliers_user') . " where suppliers_id = '$suppliers_id' and user_id='$_SESSION[user_id]'";
	$record = $db->getRow($sql);
	$user = $db->getRow('select u.wxid, w.nickname from ' . $ecs->table('users') . " u, wxch_user w where u.user_name=w.uname and u.user_id='$_SESSION[user_id]'");
	$wxid = $user['wxid'];
	$suppliers = $db->getRow("select * from " . $ecs->table('suppliers'). " where suppliers_id='$suppliers_id'");
	$suppliers_users = $db->getAll("select wxid from " . $ecs->table('users') . " where suppliers_id='$suppliers_id'");
	$status = 0;
	$ret_message = "";
	
	if($record){
		if($record['status'] == 5){
			//已经是认证代理
			$message = "您已经是($suppliers[suppliers_name])的认证代理！";
			$ret_message = $message;
			$weixin_api->send_custom_message($wxid, $message, false);
			$status = 5;
		}elseif($record['status'] == 1){
			//供应商也邀请了，直接通过
			$db->query("update " . $ecs->table('suppliers_user') . " set status = 5, reply_time='$time' where id='$record[id]'");
			$message = "恭喜您成为供应商【$suppliers[suppliers_name]】的认证代理！";
			$ret_message = $message;
			$weixin_api->send_custom_message($wxid, $message, false);
			if($suppliers_users){
				$message = "用户$user[nickname]成为您的认证代理！";
				foreach($suppliers_users as $k => $row){
					$weixin_api->send_custom_message($row['wxid'], $message, false);
				}
			}
			$status = 5;
		}else{
			$db->query("update " . $ecs->table('suppliers_user') . " set status = 3, add_time='$time', reply_time='' where id='$record[id]'");
			if($suppliers_users){
				$message = "用户$user[nickname]申请成为认证代理，请及时处理！";
				foreach($suppliers_users as $k => $row){
					$weixin_api->send_custom_message($row['wxid'], $message, false);
				}
			}
			$status = 3;
		}
	}else{
		$db->query("insert into " . $ecs->table('suppliers_user') . " (suppliers_id, user_id, status, add_time) values" .
				"('$suppliers_id', '$_SESSION[user_id]', 3, '$time')");
		if($suppliers_users){
			$message = "用户$user[nickname]申请成为认证代理，请及时处理！";
			foreach($suppliers_users as $k => $row){
				$weixin_api->send_custom_message($row['wxid'], $message, false);
			}
		}
		$status = 3;
	}
	
	if(empty($ret_message)){
		$ret_message = "认证代理申请已发送，请耐心等待！";
	}
	exit(json_encode(array(
		'errcode' => 0,
		'status' => $status,
		'message' => $ret_message
	)));
}
/**
 * 查询运营商介绍
 */
elseif($act == 'show_desc'){
	$suppliers_id = $_REQUEST['id'];
	$suppliers = $db->getRow("select * from " . $ecs->table('suppliers'). " where suppliers_id='$suppliers_id'");
	$smarty->assign('suppliers', $suppliers);
	$smarty->display('suppliers_desc.dwt');
}
/**
 * 查询并发送供应商联系方式
 */
elseif($act == 'get_contact'){
	if(empty($user_id)){
		exit(json_encode(array(
			'errcode' => 1,
			'message' => "登录失效"
		)));
	}
	$suppliers_id = $_REQUEST['id'];
	$suppliers = $db->getRow("select * from " . $ecs->table('suppliers'). " where suppliers_id='$suppliers_id'");
	$wxid = $db->getOne("select w.wxid from wxch_user w, " . $ecs->table('users') . " u where u.user_name=w.uname and u.user_id='$user_id'");
	if($wxid){
		require_once ('api/weixin_api.php');
		$weixin_api = new weixin_api();
		$message = "供应商【$suppliers[suppliers_name]】的联系方式\r\n联系电话：$suppliers[phone]\r\n微信号：$suppliers[weixin]";
		$weixin_api->send_custom_message($wxid, $message, false);
		exit(json_encode(array(
			'errcode' => 0,
			'message' => "操作成功！"
		)));
	}else{
		exit(json_encode(array(
			'errcode' => 1,
			'message' => "没找到微信openid"
		)));
	}
}

elseif($act == 'see_user_shop'){
	$user_id = $_REQUEST['user_id'];
	$user_info = $db->getRow("select u.*, w.nickname,w.headimgurl from " . $ecs->table('users') . " u left join wxch_user w on u.user_name=w.uname where u.user_id='$user_id'");
	$user_shop = $db->getRow("select * from " . $ecs->table('user_shop') . " where user_id='$user_id'");
	$smarty->assign('user_shop', $user_shop);
	$smarty->assign('user_info', $user_info);
	$smarty->display('user_shop.dwt');
}
?>