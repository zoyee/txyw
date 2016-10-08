<?php

/**
 * ECSHOP 首页文件
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: index.php 17217 2011-01-19 06:29:08Z liubo $
*/
//
define('IN_ECTOUCH', true);

require(dirname(__FILE__) . '/include/init.php');
require(ROOT_PATH . 'include/lib_weixintong.php');
$logger = LoggerManager::getLogger('index.php');
$user_id = $_SESSION['user_id'];
//if(empty($user_id)){
//	$user_id = $wechat->get_userid();
//}


if ((DEBUG_MODE & 2) != 2)
{
    $smarty->caching = true;
}
// $logger->debug('[line 26]' . json_encode($_SESSION));
// $logger->debug('act = ' . $_GET['act']);
//判断是否有ajax请求
$act = !empty($_GET['act']) ? $_GET['act'] : '';
if ($act == 'cat_rec')
{
    $rec_array = array(1 => 'best', 2 => 'new', 3 => 'hot');
    $rec_type = !empty($_REQUEST['rec_type']) ? intval($_REQUEST['rec_type']) : '1';
    $cat_id = !empty($_REQUEST['cid']) ? intval($_REQUEST['cid']) : '0';
    include_once('include/cls_json.php');
    $json = new JSON;
    $result   = array('error' => 0, 'content' => '', 'type' => $rec_type, 'cat_id' => $cat_id);

    $children = get_children($cat_id);
    $smarty->assign($rec_array[$rec_type] . '_goods',      get_category_recommend_goods($rec_array[$rec_type], $children));    // 推荐商品
    $smarty->assign('cat_rec_sign', 1);
    $result['content'] = $smarty->fetch('library/recommend_' . $rec_array[$rec_type] . '.lbi');
    die($json->encode($result));
}

/*$jump_perfect_flag = $_REQUEST['jump_perfect_flag'];
if($jump_perfect_flag){
	setcookie('jump_perfect_flag', 1, gmtime() + 3600 * 24 * 3); // 过期时间为 3 天
}else{
	if($user_id){
		$mobile = $db->getOne("select mobile_phone from " . $ecs->table('users') . " where user_id='$user_id'");
		if(empty($mobile)){
			$jump_perfect_flag = $_COOKIE['jump_perfect_flag'];
			if(empty($jump_perfect_flag)){
				$smarty->display("perfect_mobile.dwt");
				exit(0);
			}
		}
	}
}*/



/*------------------------------------------------------ */
//-- 判断是否存在缓存，如果存在则调用缓存，反之读取相应内容
/*------------------------------------------------------ */
/* 缓存编号 */
$cache_id = sprintf('%X', crc32($_SESSION['user_rank'] . '-' . $_CFG['lang']));

if (!$smarty->is_cached('index.dwt', $cache_id))
{
    assign_template();
    
    $position = assign_ur_here();
    $smarty->assign('page_title',      $position['title']);    // 页面标题
    $smarty->assign('ur_here',         $position['ur_here']);  // 当前位置

    /* meta information */
    $smarty->assign('keywords',        htmlspecialchars($_CFG['shop_keywords']));
    $smarty->assign('description',     htmlspecialchars($_CFG['shop_desc']));
    $smarty->assign('flash_theme',     $_CFG['flash_theme']);  // Flash轮播图片模板

    $smarty->assign('feed_url',        ($_CFG['rewrite'] == 1) ? 'feed.xml' : 'feed.php'); // RSS URL

    $smarty->assign('categories',      get_categories_tree()); // 分类树
    $smarty->assign('helps',           get_shop_help());       // 网店帮助
    $smarty->assign('top_goods',       get_top10());           // 销售排行

    $smarty->assign('recommend_lib', array('/library/recommend_promotion.lbi',
    		'/library/recommend_new.lbi',
    		'/library/recommend_best.lbi',
    		'/library/recommend_hot.lbi'
    ));
//     $smarty->assign('best_goods',      get_recommend_goods('best'));    // 推荐商品
//     $smarty->assign('new_goods',       get_recommend_goods('new'));     // 最新商品
//     $smarty->assign('hot_goods',       get_recommend_goods('hot'));     // 热点文章
//     $smarty->assign('promotion_goods', get_promote_goods()); // 特价商品

    $sql = "select * from " . $ecs->table('touch_template') . " where type = 0 and display=1 and theme='" . $_CFG['template'] . "' order by sort_order";
    $temp_list = $db->getAll($sql);
    $recommond_array = array();
    $recommond_title = array();
    for($idx = 0 , $size = sizeof($temp_list); $idx < $size; $idx++){
		if(preg_match("/^.*_hot\.lbi$/", $temp_list[$idx]['library'])){
			$recommond_array[$idx] = $temp_list[$idx]['display'] == 0 ? null : get_recommend_goods('hot');
			$recommond_title[$idx] = $_LANG['hot_goods'];
		} else if(preg_match("/^.*_new\.lbi$/", $temp_list[$idx]['library'])){
			$recommond_array[$idx] = $temp_list[$idx]['display'] == 0 ? null : get_recommend_goods('new');
			$recommond_title[$idx] = $_LANG['new_goods'];
		} else if(preg_match("/^.*_best\.lbi$/", $temp_list[$idx]['library'])){
			$recommond_array[$idx] = $temp_list[$idx]['display'] == 0 ? null : get_recommend_goods('best');
			$recommond_title[$idx] = $_LANG['best_goods'];
		} else if(preg_match("/^.*_promotion\.lbi$/", $temp_list[$idx]['library'])){
			$recommond_array[$idx] = $temp_list[$idx]['display'] == 0 ? null : get_promote_goods();
			$recommond_title[$idx] = $_LANG['promotion_goods'];
		} else {
			$recommond_array[$idx] = null;
		}
    }
    $smarty->assign('recommond_array', $recommond_array);
    $smarty->assign('recommond_title', $recommond_title);
//    $logger->debug(json_encode($recommond_array));
//    $logger->debug(json_encode($recommond_title));

    $smarty->assign('brand_list',      get_brands());
    $smarty->assign('promotion_info',  get_promotion_info()); // 增加一个动态显示所有促销信息的标签栏

    $smarty->assign('invoice_list',    index_get_invoice_query());  // 发货查询
    $smarty->assign('new_articles',    index_get_new_articles());   // 最新文章
    $smarty->assign('group_buy_goods', index_get_group_buy());      // 团购商品
    $smarty->assign('auction_list',    index_get_auction());        // 拍卖活动
    $smarty->assign('shop_notice',     $_CFG['shop_notice']);       // 商店公告
	//yyy添加start
	$smarty->assign('wap_index_ad',get_wap_advlist('手机版首页Banner', 5));  //wap首页幻灯广告位
	//print_r(get_wap_advlist('手机版首页Banner', 5));
	$smarty->assign('wap_index_icon',get_wap_advlist('wap端首页8个图标', 8));  //wap首页幻灯广告位
    $smarty->assign('wap_index_img',get_wap_advlist('手机端首页精品推荐广告', 5));  //wap首页幻灯广告位


	//$smarty->assign('menu_list',get_menu());


	//yyy添加end

    /* 首页主广告设置 */
    $smarty->assign('index_ad',     $_CFG['index_ad']);
    if ($_CFG['index_ad'] == 'cus')
    {
        $sql = 'SELECT ad_type, content, url FROM ' . $ecs->table("ad_custom") . ' WHERE ad_status = 1';
        $ad = $db->getRow($sql, true);
        $smarty->assign('ad', $ad);
    }

    /* links */
    $links = index_get_links();
    $smarty->assign('img_links',       $links['img']);
    $smarty->assign('txt_links',       $links['txt']);
    $smarty->assign('data_dir',        DATA_DIR);       // 数据目录


	/*jdy add 0816 添加首页幻灯插件*/
$smarty->assign("flash",get_flash_xml());
$smarty->assign('flash_count',count(get_flash_xml()));


    /* 首页推荐分类 */
    $cat_recommend_res = $db->getAll("SELECT c.cat_id, c.cat_name, cr.recommend_type FROM " . $ecs->table("cat_recommend") . " AS cr INNER JOIN " . $ecs->table("category") . " AS c ON cr.cat_id=c.cat_id");
    if (!empty($cat_recommend_res))
    {
        $cat_rec_array = array();
        foreach($cat_recommend_res as $cat_recommend_data)
        {
            $cat_rec[$cat_recommend_data['recommend_type']][] = array('cat_id' => $cat_recommend_data['cat_id'], 'cat_name' => $cat_recommend_data['cat_name']);
        }
        $smarty->assign('cat_rec', $cat_rec);
    }
    
    //查询卡客车轮胎品牌和轿车品牌
    $kkc_lt = get_cat_id_goods_list(235, 20);
    $logger->debug(json_encode($kkc_lt));
    $brand_arr = array();
    foreach($kkc_lt as $i => $goods){
    	if($goods['brand_id'] && !in_array($goods['brand_id'], $brand_arr)){
    		array_push($brand_arr, $goods['brand_id']);
    	}
    }
    $brand_list1 = array();
    if($brand_arr){
    	 $sql = "select brand_id, brand_name, brand_logo, site_url from " . $ecs->table('brand') . " where is_show=1 and brand_id " . db_create_in($brand_arr);
    	 $brand_list1 = $db->getAll($sql);
    }
    
    $kkc_lt = get_cat_id_goods_list(236, 20);
    $brand_arr = array();
    foreach($kkc_lt as $i => $goods){
    	if($goods['brand_id'] && !in_array($goods['brand_id'], $brand_arr)){
    		array_push($brand_arr, $goods['brand_id']);
    	}
    }
    $brand_list2 = array();
    if($brand_arr){
    	 $sql = "select brand_id, brand_name, brand_logo, site_url from " . $ecs->table('brand') . " where is_show=1 and brand_id " . db_create_in($brand_arr);
    	 $brand_list2 = $db->getAll($sql);
    }
    $smarty->assign('brand_list1', $brand_list1);
    $smarty->assign('brand_list2', $brand_list2);

    /* 页面中的动态内容 */
    assign_dynamic('index');
}

		/*青山老农修改*/
		$userid=$_SESSION['user_id'];
		if(!empty($userid)){
			$affiliate = unserialize($GLOBALS['_CFG']['affiliate']);
			$level_register_up = (float)$affiliate['config']['level_register_up'];
			$rank_points =  $GLOBALS['db']->getOne("SELECT rank_points FROM " . $GLOBALS['ecs']->table('users')."where user_id=".$_SESSION["user_id"]);
			if($rank_points>$level_register_up||$rank_points==$level_register_up){
			$url=$config['site_url']."mobile/index.php?u=".$userid;
			//20141204新增分享返积分
			$dourl=$config['site_url']."mobile/re_url.php?user_id=".$userid;
			}else{
					$url="";
					//20141204新增分享返积分
					$dourl="";

			}
		}else{
			$url="";
			//20141204新增分享返积分
			$dourl="";
		}
		require_once "wxjs/jssdk.php";
		$ret = $db->getRow("SELECT  *  FROM `wxch_config`");
		$jssdk = new JSSDK($appid=$ret['appid'], $ret['appsecret']);
		$signPackage = $jssdk->GetSignPackage();
		$smarty->assign('signPackage',  $signPackage);
		$smarty->assign('userid',  $userid);
		$smarty->assign('share_info',  $share_info);
		$smarty->assign('dourl',  $dourl);
		$smarty->assign('url',  $url);
		/*青山老农修改*/
	/*青山老农开发显示店铺名称*/
	if(!empty($u)){
		$sql = 'SELECT nicheng FROM ' . $ecs->table("users") . ' where user_id='.$u.'';
		$name = $db->getOne($sql);

		}

	if(!empty($user_id)){
		$sql = 'SELECT nicheng FROM ' . $ecs->table("users") . ' where user_id='.$user_id.'';
		$name = $db->getOne($sql);
		}
		/*青山老农  修复开发*/

		$tianxin_url = $db->getOne("SELECT cfg_value  FROM `wxch_cfg` WHERE `cfg_name` = 'tianxin_url'");
		$smarty->assign('tianxin_url',  $tianxin_url);

	//添加提醒图片功能 青山老农  Mr.lu
	$sql = "SELECT * FROM wxch_order WHERE id = 5";
	$cfg_order = $db->getRow($sql);
	$cfg_baseurl = $db->getOne("SELECT cfg_value FROM wxch_cfg WHERE cfg_name = 'baseurl'");
	$http_ret1 = stristr($cfg_order['image'],'http://');
	$http_ret2 = stristr($cfg_order['image'], 'http:\\');
	$w_picurl = $cfg_baseurl."/mobile/".$cfg_order['image'];
	if($http_ret1 or $http_ret2)
	 {
	$w_picurl = $cfg_order['image'];
	}
	else
	{
	$w_picurl = $cfg_baseurl."/mobile/".$cfg_order['image'];

	}
	
	//获取当前会员信息 zmh start
	$sql = "SELECT wxid FROM " .$GLOBALS['ecs']->table('users'). " WHERE user_id = '".$_SESSION['user_id']."'";
	$wxid = $GLOBALS['db']->getOne($sql);
	$info = array();
	if(!empty($wxid)){
		$weixinInfo = $GLOBALS['db']->getRow("SELECT nickname, headimgurl, subscribe_time FROM wxch_user WHERE wxid = '$wxid'");
		$info['avatar'] = empty($weixinInfo['headimgurl']) ? '':$weixinInfo['headimgurl'];
		$info['nickname'] = $weixinInfo['nickname'];
		$info['subscribe_time'] = date("Y-m-d",$weixinInfo['subscribe_time']);
	}
	$smarty->assign('info',$info);
	//获取当前会员信息 zmh end
$smarty->assign('w_picurl', $w_picurl);
$smarty->assign('name', $name);
$index_share_title = $db->getOne("SELECT value FROM ecs_touch_shop_config WHERE code = 'index_share_title'");
$index_share_content = $db->getOne("SELECT value FROM ecs_touch_shop_config WHERE code = 'index_share_content'");
$index_autoload = $db->getOne("select autoload from wxch_point where point_id = 11");
$smarty->assign('share_title', str_replace("#userName", $info['nickname'] , $index_share_title));
$smarty->assign('share_content', str_replace("#userName", $info['nickname'] ,$index_share_content));
$smarty->assign('autoload', $index_autoload=='yes'?'1':'');
$smarty->display('index.dwt', $cache_id);
echo insert_query_info();
/*------------------------------------------------------ */
//-- PRIVATE FUNCTIONS
/*------------------------------------------------------ */

/**
 * 调用发货单查询
 *
 * @access  private
 * @return  array
 */
function index_get_invoice_query()
{
    $sql = 'SELECT o.order_sn, o.invoice_no, s.shipping_code FROM ' . $GLOBALS['ecs']->table('order_info') . ' AS o' .
            ' LEFT JOIN ' . $GLOBALS['ecs']->table('touch_shipping') . ' AS s ON s.shipping_id = o.shipping_id' .
            " WHERE invoice_no > '' AND shipping_status = " . SS_SHIPPED .
            ' ORDER BY shipping_time DESC LIMIT 10';
    $all = $GLOBALS['db']->getAll($sql);

    foreach ($all AS $key => $row)
    {
        $plugin = ROOT_PATH . 'include/modules/shipping/' . $row['shipping_code'] . '.php';

        if (file_exists($plugin))
        {
            include_once($plugin);

            $shipping = new $row['shipping_code'];
            $all[$key]['invoice_no'] = $shipping->query((string)$row['invoice_no']);
        }
    }

    clearstatcache();

    return $all;
}

/**
 * 获得最新的文章列表。
 *
 * @access  private
 * @return  array
 */
function index_get_new_articles()
{
    $sql = 'SELECT a.article_id, a.title, ac.cat_name, a.add_time, a.file_url, a.open_type, ac.cat_id, ac.cat_name ' .
            ' FROM ' . $GLOBALS['ecs']->table('article') . ' AS a, ' .
                $GLOBALS['ecs']->table('article_cat') . ' AS ac' .
            ' WHERE a.is_open = 1 AND a.cat_id = ac.cat_id AND ac.cat_type = 1' .
            ' ORDER BY a.article_type DESC, a.add_time DESC LIMIT ' . $GLOBALS['_CFG']['article_number'];
    $res = $GLOBALS['db']->getAll($sql);

    $arr = array();
    foreach ($res AS $idx => $row)
    {
        $arr[$idx]['id']          = $row['article_id'];
        $arr[$idx]['title']       = $row['title'];
        $arr[$idx]['short_title'] = $GLOBALS['_CFG']['article_title_length'] > 0 ?
                                        sub_str($row['title'], $GLOBALS['_CFG']['article_title_length']) : $row['title'];
        $arr[$idx]['cat_name']    = $row['cat_name'];
        $arr[$idx]['add_time']    = local_date($GLOBALS['_CFG']['date_format'], $row['add_time']);
        $arr[$idx]['url']         = $row['open_type'] != 1 ?
                                        build_uri('article', array('aid' => $row['article_id']), $row['title']) : trim($row['file_url']);
        $arr[$idx]['cat_url']     = build_uri('article_cat', array('acid' => $row['cat_id']), $row['cat_name']);
    }

    return $arr;
}

/**
 * 获得最新的团购活动
 *
 * @access  private
 * @return  array
 */
function index_get_group_buy()
{
    $time = gmtime();
    $limit = get_library_number('group_buy', 'index');

    $group_buy_list = array();
    if ($limit > 0)
    {
        $sql = 'SELECT gb.*,g.*,gb.act_id AS group_buy_id, gb.goods_id, gb.ext_info, gb.goods_name, g.goods_thumb, g.goods_img ' .
                'FROM ' . $GLOBALS['ecs']->table('goods_activity') . ' AS gb, ' .
                    $GLOBALS['ecs']->table('goods') . ' AS g ' .
                "WHERE gb.act_type = '" . GAT_GROUP_BUY . "' " .
                "AND g.goods_id = gb.goods_id " .
                "AND gb.start_time <= '" . $time . "' " .
                "AND gb.end_time >= '" . $time . "' " .
                "AND g.is_delete = 0 " .
                "ORDER BY gb.act_id DESC " .
                "LIMIT $limit" ;

        $res = $GLOBALS['db']->query($sql);

        while ($row = $GLOBALS['db']->fetchRow($res))
        {
            /* 如果缩略图为空，使用默认图片 */
            $row['goods_img'] = get_image_path($row['goods_id'], $row['goods_img']);
            $row['thumb'] = get_image_path($row['goods_id'], $row['goods_thumb'], true);

            /* 根据价格阶梯，计算最低价 */
            $ext_info = unserialize($row['ext_info']);
            $price_ladder = $ext_info['price_ladder'];
            if (!is_array($price_ladder) || empty($price_ladder))
            {
                $row['last_price'] = price_format(0);
            }
            else
            {
                foreach ($price_ladder AS $amount_price)
                {
                    $price_ladder[$amount_price['amount']] = $amount_price['price'];
                }
            }
            ksort($price_ladder);
            $row['last_price'] = price_format(end($price_ladder));
            $row['url'] = build_uri('group_buy', array('gbid' => $row['group_buy_id']));
            $row['short_name']   = $GLOBALS['_CFG']['goods_name_length'] > 0 ?
                                           sub_str($row['goods_name'], $GLOBALS['_CFG']['goods_name_length']) : $row['goods_name'];
            $row['short_style_name']   = add_style($row['short_name'],'');

			$stat = group_buy_stat($row['act_id'], $row['deposit']);
			$row['valid_goods'] = $stat['valid_goods'];
            $group_buy_list[] = $row;
        }
    }

    return $group_buy_list;
}

/**
 * 取得拍卖活动列表
 * @return  array
 */
function index_get_auction()
{
    $now = gmtime();
    $limit = get_library_number('auction', 'index');
    $sql = "SELECT a.act_id, a.goods_id, a.goods_name, a.ext_info, g.goods_thumb ".
            "FROM " . $GLOBALS['ecs']->table('goods_activity') . " AS a," .
                      $GLOBALS['ecs']->table('goods') . " AS g" .
            " WHERE a.goods_id = g.goods_id" .
            " AND a.act_type = '" . GAT_AUCTION . "'" .
            " AND a.is_finished = 0" .
            " AND a.start_time <= '$now'" .
            " AND a.end_time >= '$now'" .
            " AND g.is_delete = 0" .
            " ORDER BY a.start_time DESC" .
            " LIMIT $limit";
    $res = $GLOBALS['db']->query($sql);

    $list = array();
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        $ext_info = unserialize($row['ext_info']);
        $arr = array_merge($row, $ext_info);
        $arr['formated_start_price'] = price_format($arr['start_price']);
        $arr['formated_end_price'] = price_format($arr['end_price']);
        $arr['thumb'] = get_image_path($row['goods_id'], $row['goods_thumb'], true);
        $arr['url'] = build_uri('auction', array('auid' => $arr['act_id']));
        $arr['short_name']   = $GLOBALS['_CFG']['goods_name_length'] > 0 ?
                                           sub_str($arr['goods_name'], $GLOBALS['_CFG']['goods_name_length']) : $arr['goods_name'];
        $arr['short_style_name']   = add_style($arr['short_name'],'');
        $list[] = $arr;
    }

    return $list;
}

/**
 * 获得所有的友情链接
 *
 * @access  private
 * @return  array
 */
function index_get_links()
{
    $sql = 'SELECT link_logo, link_name, link_url FROM ' . $GLOBALS['ecs']->table('friend_link') . ' ORDER BY show_order';
    $res = $GLOBALS['db']->getAll($sql);

    $links['img'] = $links['txt'] = array();

    foreach ($res AS $row)
    {
        if (!empty($row['link_logo']))
        {
            $links['img'][] = array('name' => $row['link_name'],
                                    'url'  => $row['link_url'],
                                    'logo' => $row['link_logo']);
        }
        else
        {
            $links['txt'][] = array('name' => $row['link_name'],
                                    'url'  => $row['link_url']);
        }
    }

    return $links;
}

function get_flash_xml()
{
    $flashdb = array();
    if (file_exists(ROOT_PATH . DATA_DIR . '/flash_data.xml'))
    {

        // 兼容v2.7.0及以前版本
        if (!preg_match_all('/item_url="([^"]+)"\slink="([^"]+)"\stext="([^"]*)"\ssort="([^"]*)"/', file_get_contents(ROOT_PATH . DATA_DIR . '/flash_data.xml'), $t, PREG_SET_ORDER))
        {
            preg_match_all('/item_url="([^"]+)"\slink="([^"]+)"\stext="([^"]*)"/', file_get_contents(ROOT_PATH . DATA_DIR . '/flash_data.xml'), $t, PREG_SET_ORDER);
        }

        if (!empty($t))
        {
            foreach ($t as $key => $val)
            {
                $val[4] = isset($val[4]) ? $val[4] : 0;
                $flashdb[] = array('src'=>$val[1],'url'=>$val[2],'text'=>$val[3],'sort'=>$val[4]);

				//print_r($flashdb);
            }
        }
    }
    return $flashdb;
}

function get_wap_advlist( $position, $num )
{
		$arr = array( );
		$sql = "select ap.ad_width,ap.ad_height,ad.ad_id,ad.ad_name,ad.ad_code,ad.ad_link,ad.ad_id from ".$GLOBALS['ecs']->table( "touch_ad_position" )." as ap left join ".$GLOBALS['ecs']->table( "touch_ad" )." as ad on ad.position_id = ap.position_id where ap.position_name='".$position.( "' and UNIX_TIMESTAMP()>ad.start_time and UNIX_TIMESTAMP()<ad.end_time and ad.enabled=1 limit ".$num );
		$res = $GLOBALS['db']->getAll( $sql );
		foreach ( $res as $idx => $row )
		{
				$arr[$row['ad_id']]['name'] = $row['ad_name'];
				$arr[$row['ad_id']]['url'] = "affiche.php?ad_id=".$row['ad_id']."&uri=".$row['ad_link'];
				$arr[$row['ad_id']]['image'] = "data/afficheimg/".$row['ad_code'];
				$tmparray = strpos($row['ad_code'], "http://");
				if($tmparray>0){

					$arr[$row['ad_id']]['ad_code'] = $row['ad_code'];
				}else{
					$arr[$row['ad_id']]['ad_code'] = "data/afficheimg/".$row['ad_code'];

				}
				//echo $arr[$row['ad_id']]['ad_code'];
		}
		return $arr;
}

function get_is_computer(){
$is_computer=$_REQUEST['is_computer'];
return $is_computer;
}

/* function get_menu()
{
	$sql = "select * from ".$GLOBALS['ecs']->table('touch_nav');
	$list = $GLOBALS['db']->getAll($sql);
	$arr = array();
	foreach($list as $key => $rows)
	{
		$arr[$key]['id'] = $rows['id'];
		$arr[$key]['menu_name'] = $rows['name'];
		$arr[$key]['menu_img'] = $rows['pic'];
		$arr[$key]['menu_url'] = $rows['url'];
	}
	return $arr;
} */
?>