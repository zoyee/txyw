<?php
/**
 * ECSHOP 销售统计
 * ============================================================================
 * 统计销售情况
 * ============================================================================
 * $Author: zmh $
 * $Id: sale_statis.php $
 */

define('IN_ECTOUCH', true);
require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . 'include/lib_order.php');
require_once(ROOT_PATH . 'lang/' .$_CFG['lang']. '/admin/statistic.php');
$smarty->assign('lang', $_LANG);
$_REQUEST['type'] = empty($_REQUEST['type']) ? 'finished' : $_REQUEST['type'];
if (isset($_REQUEST['act']) && ($_REQUEST['act'] == 'query' ||  $_REQUEST['act'] == 'download'))
{
	/* 检查权限 */
	check_authz_json('sale_order_stats');
	if (strstr($_REQUEST['start_date'], '-') === false)
	{
		$_REQUEST['start_date'] = local_date('Y-m-d', $_REQUEST['start_date']);
		$_REQUEST['end_date'] = local_date('Y-m-d', $_REQUEST['end_date']);
	}
	/*------------------------------------------------------ */
	//--Excel文件下载
	/*------------------------------------------------------ */
	if ($_REQUEST['act'] == 'download')
	{
		$file_name = $_REQUEST['start_date'].'_'.$_REQUEST['end_date'] . '_sale';
		$goods_sales_list = get_sale_list(false);
		header("Content-type: application/vnd.ms-excel; charset=utf-8");
		header("Content-Disposition: attachment; filename=$file_name.csv");

		/* 文件标题 */
		echo ecs_iconv(EC_CHARSET, 'gb2312', $_REQUEST['start_date']. $_LANG['to'] .$_REQUEST['end_date']. $_LANG['sales_list']) . "\t\n";

		/* 商品名称,订单号,商品数量,销售价格,销售日期 */
// 		echo ecs_iconv(EC_CHARSET, 'gb2312', '订单id') . ",";
		echo ecs_iconv(EC_CHARSET, 'gb2312', $_LANG['order_sn']) . ",";
		echo ecs_iconv(EC_CHARSET, 'gb2312', '销售价') . ",";
		echo ecs_iconv(EC_CHARSET, 'gb2312', '折扣') . ",";
		echo ecs_iconv(EC_CHARSET, 'gb2312', '配送费'). ",";
		echo ecs_iconv(EC_CHARSET, 'gb2312', '实付价') . ",";
		//echo ecs_iconv(EC_CHARSET, 'gb2312', '分成') . ",";
		echo ecs_iconv(EC_CHARSET, 'gb2312', '收货人') . ",";
		echo ecs_iconv(EC_CHARSET, 'gb2312', '状态') . ",";
		echo ecs_iconv(EC_CHARSET, 'gb2312', '下单时间') . ",";
		echo ecs_iconv(EC_CHARSET, 'gb2312', '付款时间') . ",";
		echo "\n";
		foreach ($goods_sales_list['sale_list_data'] AS $key => $value)
		{
// 			echo ecs_iconv(EC_CHARSET, 'gb2312', $value['order_id']) . ",";
			echo ecs_iconv(EC_CHARSET, 'gb2312', '`' . $value['tid']) . ",";

			echo ecs_iconv(EC_CHARSET, 'gb2312', $value['total_fee']) . ",";
			echo ecs_iconv(EC_CHARSET, 'gb2312', $value['discount_fee']) . ",";
			echo ecs_iconv(EC_CHARSET, 'gb2312', $value['post_fee']) . ",";
			echo ecs_iconv(EC_CHARSET, 'gb2312', $value['payment']) . ",";
			echo ecs_iconv(EC_CHARSET, 'gb2312', $value['buyer_nick']) . ",";
			echo ecs_iconv(EC_CHARSET, 'gb2312', $value['order_status']) . ",";
			echo ecs_iconv(EC_CHARSET, 'gb2312', $value['add_time']) . ",";
			echo ecs_iconv(EC_CHARSET, 'gb2312', $value['pay_time']) . ",";;
			echo "\n";
		}

		/***
		 foreach ($goods_sales_list AS $key => $value)
		 {
		 echo ecs_iconv(EC_CHARSET, 'utf-8', $value['goods_name']) . "\t";
		 echo ecs_iconv(EC_CHARSET, 'utf-8', '[ ' . $value['order_sn'] . ' ]') . "\t";
		 echo ecs_iconv(EC_CHARSET, 'utf-8', $value['goods_num']) . "\t";
		 echo ecs_iconv(EC_CHARSET, 'utf-8', $value['sales_price']) . "\t";
		 echo ecs_iconv(EC_CHARSET, 'utf-8', $value['cost_price']) . "\t";
		 echo ecs_iconv(EC_CHARSET, 'utf-8', $value['gross_profit']) . "\t";
		 echo ecs_iconv(EC_CHARSET, 'utf-8', $value['sales_time']) . "\t";
		 echo "\n";
		 }
		 ***/
		if($goods_sales_list['total_fee'])//$total_isdisplay
		{
			echo ecs_iconv(EC_CHARSET, 'gb2312', '') . ",";
			echo ecs_iconv(EC_CHARSET, 'gb2312', '') . ",";
			echo ecs_iconv(EC_CHARSET, 'gb2312', $goods_sales_list['total_fee']) . ",";
			echo ecs_iconv(EC_CHARSET, 'gb2312', $goods_sales_list['discount_fee']) . ",";
			echo ecs_iconv(EC_CHARSET, 'gb2312', $goods_sales_list['post_fee']) . ",";
			echo ecs_iconv(EC_CHARSET, 'gb2312', $goods_sales_list['payment']) . ",";
			echo ecs_iconv(EC_CHARSET, 'gb2312', '') . ",";
			echo ecs_iconv(EC_CHARSET, 'gb2312', '') . ",";
			echo ecs_iconv(EC_CHARSET, 'gb2312', '') . ",";
		}
		exit;
	}
	$sale_list_data = get_sale_list();
	$smarty->assign('goods_sales_list', $sale_list_data['sale_list_data']);
	$smarty->assign('filter',       $sale_list_data['filter']);
	$smarty->assign('record_count', $sale_list_data['record_count']);
	$smarty->assign('page_count',   $sale_list_data['page_count']);


	make_json_result($smarty->fetch('new_sale_statis.htm'), '', array('filter' => $sale_list_data['filter'], 'page_count' => $sale_list_data['page_count']));
}

else if ($_REQUEST['act'] == 'wps')
{
	check_authz_json('sale_order_stats');
	if (strstr($_REQUEST['start_date'], '-') === false)
	{
		$_REQUEST['start_date'] = local_date('Y-m-d', $_REQUEST['start_date']);
		$_REQUEST['end_date'] = local_date('Y-m-d', $_REQUEST['end_date']);
	}
	if ($_REQUEST['act'] == 'wps')
	{
		$file_name = $_REQUEST['start_date'].'_'.$_REQUEST['end_date'] . '_sale';
		$goods_sales_list = get_sale_list(false);
		header("Content-type: application/vnd.ms-excel; charset=utf-8");
		header("Content-Disposition: attachment; filename=$file_name.xls");

		/* 文件标题 */
		echo ecs_iconv(EC_CHARSET, 'utf-8', $_REQUEST['start_date']. $_LANG['to'] .$_REQUEST['end_date']. $_LANG['sales_list']) . "\t\n";

		/* 商品名称,订单号,商品数量,销售价格,销售日期 */
		echo ecs_iconv(EC_CHARSET, 'utf-8', '订单id') . "\t";
		echo ecs_iconv(EC_CHARSET, 'utf-8', $_LANG['order_sn']) . "\t";
		echo ecs_iconv(EC_CHARSET, 'utf-8', '销售价') . "\t";
		echo ecs_iconv(EC_CHARSET, 'utf-8', '折扣') . "\t";
		echo ecs_iconv(EC_CHARSET, 'utf-8', '配送费'). "\t";
		echo ecs_iconv(EC_CHARSET, 'utf-8', '实付价') . "\t";
		//echo ecs_iconv(EC_CHARSET, 'gb2312', '分成') . ",";
		echo ecs_iconv(EC_CHARSET, 'utf-8', '收货人') . "\t";
		echo ecs_iconv(EC_CHARSET, 'utf-8', '状态') . "\t";
		echo ecs_iconv(EC_CHARSET, 'utf-8', '省') . "\t";
		echo ecs_iconv(EC_CHARSET, 'utf-8', '市') . "\t";
		echo ecs_iconv(EC_CHARSET, 'utf-8', '县') . "\t";
		echo ecs_iconv(EC_CHARSET, 'utf-8', '下单时间') . "\t";
		echo ecs_iconv(EC_CHARSET, 'utf-8', '付款时间') . "\t";
		echo "\n";
		foreach ($goods_sales_list['sale_list_data'] AS $key => $value)
		{
			echo ecs_iconv(EC_CHARSET, 'utf-8', $value['order_id']) . "\t";
			echo ecs_iconv(EC_CHARSET, 'utf-8', '`' . $value['tid']) . "\t";

			echo ecs_iconv(EC_CHARSET, 'utf-8', $value['total_fee']) . "\t";
			echo ecs_iconv(EC_CHARSET, 'utf-8', $value['discount_fee']) . "\t";
			echo ecs_iconv(EC_CHARSET, 'utf-8', $value['post_fee']) . "\t";
			echo ecs_iconv(EC_CHARSET, 'utf-8', $value['payment']) . "\t";
			echo ecs_iconv(EC_CHARSET, 'utf-8', $value['buyer_nick']) . "\t";
			echo ecs_iconv(EC_CHARSET, 'utf-8', $value['order_status']) . "\t";
			echo ecs_iconv(EC_CHARSET, 'utf-8', $value['province_name']) . "\t";
			echo ecs_iconv(EC_CHARSET, 'utf-8', $value['city_name']) . "\t";
			echo ecs_iconv(EC_CHARSET, 'utf-8', $value['district_name']) . "\t";
			echo ecs_iconv(EC_CHARSET, 'utf-8', $value['add_time']) . "\t";
			echo ecs_iconv(EC_CHARSET, 'utf-8', $value['pay_time']) . "\t";
			echo "\n";
		}

		/***
		 foreach ($goods_sales_list AS $key => $value)
		 {
		 echo ecs_iconv(EC_CHARSET, 'utf-8', $value['goods_name']) . "\t";
		 echo ecs_iconv(EC_CHARSET, 'utf-8', '[ ' . $value['order_sn'] . ' ]') . "\t";
		 echo ecs_iconv(EC_CHARSET, 'utf-8', $value['goods_num']) . "\t";
		 echo ecs_iconv(EC_CHARSET, 'utf-8', $value['sales_price']) . "\t";
		 echo ecs_iconv(EC_CHARSET, 'utf-8', $value['cost_price']) . "\t";
		 echo ecs_iconv(EC_CHARSET, 'utf-8', $value['gross_profit']) . "\t";
		 echo ecs_iconv(EC_CHARSET, 'utf-8', $value['sales_time']) . "\t";
		 echo "\n";
		 }
		 ***/
		if($goods_sales_list['total_fee'])//$total_isdisplay
		{
			echo ecs_iconv(EC_CHARSET, 'utf-8', '') . "\t";
			echo ecs_iconv(EC_CHARSET, 'utf-8', '') . "\t";
			echo ecs_iconv(EC_CHARSET, 'utf-8', $goods_sales_list['total_fee']) . "\t";
			echo ecs_iconv(EC_CHARSET, 'utf-8', $goods_sales_list['discount_fee']) . "\t";
			echo ecs_iconv(EC_CHARSET, 'utf-8', $goods_sales_list['post_fee']) . "\t";
			echo ecs_iconv(EC_CHARSET, 'utf-8', $goods_sales_list['payment']) . "\t";
			echo ecs_iconv(EC_CHARSET, 'utf-8', '') . "\t";
			echo ecs_iconv(EC_CHARSET, 'utf-8', '') . "\t";
			echo ecs_iconv(EC_CHARSET, 'utf-8', '') . "\t";
			echo ecs_iconv(EC_CHARSET, 'utf-8', '') . "\t";
			echo ecs_iconv(EC_CHARSET, 'utf-8', '') . "\t";
			echo ecs_iconv(EC_CHARSET, 'utf-8', '') . "\t";
		}
		exit;
	}
	$sale_list_data = get_sale_list();
	echo  'a';
	$smarty->assign('goods_sales_list', $sale_list_data['sale_list_data']);
	$smarty->assign('filter',       $sale_list_data['filter']);
	$smarty->assign('record_count', $sale_list_data['record_count']);
	$smarty->assign('page_count',   $sale_list_data['page_count']);


	make_json_result($smarty->fetch('new_sale_statis.htm'), '', array('filter' => $sale_list_data['filter'], 'page_count' => $sale_list_data['page_count']));
}
/*------------------------------------------------------ */
//--商品明细列表
/*------------------------------------------------------ */
else
{
	/* 权限判断 */
	admin_priv('sale_order_stats');
	/* 时间参数 */
	if (!isset($_REQUEST['start_date']))
	{
		$start_date = local_strtotime('-1 days');
	}
	if (!isset($_REQUEST['end_date']))
	{
		$end_date = local_strtotime('-1 today');
	}

	$sale_list_data = get_sale_list();
	//var_dump( $sale_list_data["abc"]);
	/* 赋值到模板 */
	$smarty->assign('filter',       $sale_list_data['filter']);
	$smarty->assign('record_count', $sale_list_data['record_count']);
	$smarty->assign('page_count',   $sale_list_data['page_count']);
	$smarty->assign('goods_sales_list', $sale_list_data['sale_list_data']);
	$smarty->assign('full_page',        1);
	$smarty->assign('start_date',       local_date('Y-m-d', $start_date));
	$smarty->assign('end_date',         local_date('Y-m-d', $start_date));
	$smarty->assign('cfg_lang',     $_CFG['lang']);
	$smarty->assign('ur_here',          $_LANG['sale_statis']);
	//$smarty->assign('sale_total',       $sale_list_data['sale_total']);
	//$smarty->assign('cost_total',     $sale_list_data['cost_total']);
	//$smarty->assign('gross_profit_total',$sale_list_data['gross_profit_total']);
	//$smarty->assign('gross_profit_rate',$sale_list_data['gross_profit_rate']);

	$smarty->assign('action_link',  array('text' => $_LANG['down_sales'],'href'=>'#download'));
	$smarty->assign('type',  $_REQUEST['type'] );
	/* 显示页面 */
	assign_query_info();
	$smarty->display('new_sale_statis.htm');
}

/*------------------------------------------------------ */
//--获取销售明细需要的函数
/*------------------------------------------------------ */
/**
 * 取得销售明细数据信息
 * @param   bool  $is_pagination  是否分页
 * @return  array   销售明细数据
 */

function get_sale_list($is_pagination = true){
	date_default_timezone_set('PRC'); //
	//$time = time();

	/* 时间参数 */
	$filter['start_date'] = empty($_REQUEST['start_date']) ? local_strtotime(date("Y-m-d",strtotime("-1 day"))) : local_strtotime($_REQUEST['start_date']);
	$filter['end_date'] = empty($_REQUEST['end_date']) ? local_strtotime(date("Y-m-d",strtotime("-1 day"))) : local_strtotime($_REQUEST['end_date']);
	$filter['type'] = $_REQUEST['type'];
	/* 查询数据的条件 */
	$where = " WHERE (t.pay_status = 2  or order_status = 4) ".   //.order_query_sql($filter['type'], 't.') .
	" AND t.pay_time >= '".($filter['start_date'])."' AND t.pay_time < '" . ($filter['end_date'] + 86400) . "'";

	$sql = "SELECT sum(1) FROM " .
			$GLOBALS['ecs']->table('order_info') . ' AS t '.
			$where;
	$filter['record_count'] = $GLOBALS['db']->getOne($sql);
	// echo $sql;




	/* 分页大小 */
	$filter = page_and_size($filter);

	/*** $sql = 'SELECT og.goods_id, og.goods_sn, og.goods_name, og.goods_number AS goods_num, og.goods_price '.
	 'AS sales_price, oi.add_time AS sales_time, oi.order_id, oi.order_sn '.
	 "FROM " . $GLOBALS['ecs']->table('order_goods')." AS og, ".$GLOBALS['ecs']->table('order_info')." AS oi ".
	 $where. " ORDER BY sales_time DESC, goods_num DESC";
	***/

	$sql = 'SELECT '.
			't.order_id, '.
			't.order_sn tid, '.
			't.province, '.
			't.city, '.
			't.district, '.
			't.`goods_amount` total_fee, '.
			't.`discount` + t.`bonus`+t.`surplus`+t.`integral_money` discount_fee,'.
			't.`shipping_fee` post_fee, '.
			'CASE t.`pay_status` WHEN 2 THEN t.`money_paid` ELSE t.`order_amount` END payment,'.
			//'t.`money_paid` payment, '.
				't.`surplus`, '.'t.`integral_money`, '.
			'u.`user_name` buyer_nick, '.
			't.`consignee` receiver_name, '.
			'CASE t.`order_status` WHEN 4 THEN \'退货\' ELSE \'已付款\' END order_status, '.
			't.add_time , '.
			't.pay_time '.
			'FROM ' .$GLOBALS['ecs']->table('order_info')." AS t ".
			'LEFT JOIN ' .$GLOBALS['ecs']->table('users'). ' as u ON t.`user_id`=u.`user_id`'.
			$where. "  ORDER BY t.add_time DESC";

	//echo $sql;sum(value_name)  sum(oi.shipping_fee) as sumfee,
	if ($is_pagination)
	{
		$sql .= " LIMIT " . $filter['start'] . ', ' . $filter['page_size'];
	}
	$sale_list_data = $GLOBALS['db']->query($sql);
	//var_dump($sql);
	// $sale_list_data = $GLOBALS['db']->getAll($sql);

	// $sale_total = $cost_total = $gross_profit_total =$gross_profit_rate = 0;  // 添加的
	/**
	 foreach ($sale_list_data as $key => $item)
	 {

	 $sale_list_data[$key]['sales_price'] = price_format($sale_list_data[$key]['sales_price']);
	 $sale_list_data[$key]['sales_time']  = local_date($GLOBALS['_CFG']['time_format'], $sale_list_data[$key]['sales_time']);

	 }
	 **/

	$total_fee = $discount_fee = $post_fee =$payment = 0;

	while ($items = $GLOBALS['db']->fetchRow($sale_list_data))
	{
		$total_fee += $items['total_fee'];
		$discount_fee += $items['discount_fee'];
		$post_fee += $items['post_fee'];
		$payment += $items['payment'];
		$items['total_fee']   = price_format($items['total_fee']);
		$items['discount_fee']   = price_format($items['discount_fee']);
		$items['post_fee']   = price_format($items['post_fee']);
		$items['payment']   = price_format($items['payment']);
		$items['add_time']    = local_date($GLOBALS['_CFG']['time_format'], $items['add_time']);
		$items['pay_time']    = local_date($GLOBALS['_CFG']['time_format'], $items['pay_time']);
		$cityName = "";
		if($items['city'])
		{
			$citySQL = 'select region_name from ' .$GLOBALS['ecs']->table('region').' where region_id ='.$items['city'];
			$city = $GLOBALS['db']->getOne($citySQL);
			if($city){
				$cityName = $city;
			}
		}
		$provinceName = "";
		if($items['province']){
			$provinceSQL = 'select region_name from ' .$GLOBALS['ecs']->table('region').' where region_id ='.$items['province'];
			$province = $GLOBALS['db']->getOne($provinceSQL);
			if($province){
				$provinceName = $province;
			}
		}
		$districtName = "";
		if($items['district']){
			$districSQL = 'select region_name from ' .$GLOBALS['ecs']->table('region').' where region_id ='.$items['district'];
			$district = $GLOBALS['db']->getOne($districSQL);
			if($district){
				$districtName = $district;
			}
		}
		
		$items['city_name']   =$cityName;
		$items['province_name']   =$provinceName;
		$items['district_name']   =$districtName;
		
		
		
		$goods_sales_list[]     = $items;
	}

	$total_isdisplay = false;
	if($total_fee>0)
	{
		$total_isdisplay = true;
		$total_fee   = price_format($total_fee);
		$discount_fee   = price_format($discount_fee);
		$post_fee   = price_format($post_fee);
		$payment   = price_format($payment);

	}
	//var_dump($cost_total);
	$arr = array('sale_list_data' => $goods_sales_list, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count'],'total_fee'=>$total_fee,'discount_fee'=>$discount_fee,'post_fee'=>$post_fee,'payment'=>$payment);
	//var_dump($arr);
	return $arr;

}






?>