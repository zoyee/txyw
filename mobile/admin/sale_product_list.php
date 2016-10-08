<?php
/**
 * ECSHOP 销售产品统计表
 * ============================================================================
 * 版权所有 2005-2009 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: zhangnu $
 * $Id: sale_list.php 16881 2009-12-14 09:19:16Z liubo $
*/

define('IN_ECTOUCH', true);

require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . 'include/lib_order.php');
require_once(ROOT_PATH . 'lang/' .$_CFG['lang']. '/admin/statistic.php');
$smarty->assign('lang', $_LANG);

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
		echo ecs_iconv(EC_CHARSET, 'gb2312', '产品名称') . ",";
		echo ecs_iconv(EC_CHARSET, 'gb2312', '销售总价') . ",";
		echo ecs_iconv(EC_CHARSET, 'gb2312', '销售数量'). ",\n";
		
        foreach ($goods_sales_list['sale_list_data'] AS $key => $value)
        {
           
		    echo ecs_iconv(EC_CHARSET, 'gb2312', $value['title']) . ",";
			echo ecs_iconv(EC_CHARSET, 'gb2312', $value['price']) . ",";
            echo ecs_iconv(EC_CHARSET, 'gb2312', $value['num']) . ",";            
			
			echo "\n";
        }
		
        exit;
    }
    $sale_list_data = get_sale_list();
    $smarty->assign('goods_sales_list', $sale_list_data['sale_list_data']);
    $smarty->assign('filter',       $sale_list_data['filter']);	
    $smarty->assign('record_count', $sale_list_data['record_count']);
    $smarty->assign('page_count',   $sale_list_data['page_count']);
	
	
    make_json_result($smarty->fetch('sale_product_list.htm'), '', array('filter' => $sale_list_data['filter'], 'page_count' => $sale_list_data['page_count']));
}

else if(isset($_REQUEST['act']) && $_REQUEST['act'] == 'wps'){
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
	if ($_REQUEST['act'] == 'wps')
	{
		$file_name = $_REQUEST['start_date'].'_'.$_REQUEST['end_date'] . '_sale';
		$goods_sales_list = get_sale_list(false);
		header("Content-type: application/vnd.ms-excel; charset=utf-8");
		header("Content-Disposition: attachment; filename=$file_name.xls");
	
		/* 文件标题 */
		echo ecs_iconv(EC_CHARSET, 'utf-8', $_REQUEST['start_date']. $_LANG['to'] .$_REQUEST['end_date']. $_LANG['sales_list']) . "\t\n";
	
		/* 商品名称,订单号,商品数量,销售价格,销售日期 */
		echo ecs_iconv(EC_CHARSET, 'utf-8', '产品名称') . "\t";
		echo ecs_iconv(EC_CHARSET, 'utf-8', '销售总价') . "\t";
		echo ecs_iconv(EC_CHARSET, 'utf-8', '销售数量'). "\t\n";
	
		foreach ($goods_sales_list['sale_list_data'] AS $key => $value)
		{
			 
			echo ecs_iconv(EC_CHARSET, 'utf-8', $value['title']) . "\t";
			echo ecs_iconv(EC_CHARSET, 'utf-8', $value['price']) . "\t";
			echo ecs_iconv(EC_CHARSET, 'utf-8', $value['num']) . "\t";
				
			echo "\n";
		}
	
		exit;
	}
	$sale_list_data = get_sale_list();
	$smarty->assign('goods_sales_list', $sale_list_data['sale_list_data']);
	$smarty->assign('filter',       $sale_list_data['filter']);
	$smarty->assign('record_count', $sale_list_data['record_count']);
	$smarty->assign('page_count',   $sale_list_data['page_count']);
	
	
	make_json_result($smarty->fetch('sale_product_list.htm'), '', array('filter' => $sale_list_data['filter'], 'page_count' => $sale_list_data['page_count']));
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
        $start_date = local_strtotime(date("Y-m-d",strtotime("-1 day")));
    }
    if (!isset($_REQUEST['end_date']))
    {
        $end_date = local_strtotime(date("Y-m-d",strtotime("-1 day")));
    }
    
    $sale_list_data = get_sale_list();
	//var_dump( $sale_list_data["abc"]);
    /* 赋值到模板 */
	$smarty->assign('abc',      $sale_list_data["abc"][0]);
    $smarty->assign('filter',       $sale_list_data['filter']);
    $smarty->assign('record_count', $sale_list_data['record_count']);
    $smarty->assign('page_count',   $sale_list_data['page_count']);
    $smarty->assign('goods_sales_list', $sale_list_data['sale_list_data']);
    $smarty->assign('ur_here',          $_LANG['sell_stats']);
    $smarty->assign('full_page',        1);
    $smarty->assign('start_date',       local_date('Y-m-d', $start_date));
    $smarty->assign('end_date',         local_date('Y-m-d', $end_date));
    $smarty->assign('ur_here',      $_LANG['sale_list']);
    $smarty->assign('cfg_lang',     $_CFG['lang']);

    $smarty->assign('action_link',  array('text' => $_LANG['down_sales'],'href'=>'#download'));

    /* 显示页面 */
    assign_query_info();
    $smarty->display('sale_product_list.htm');
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
	date_default_timezone_set('PRC');
    /* 时间参数 */
    $filter['start_date'] = empty($_REQUEST['start_date']) ? local_strtotime(date("Y-m-d",strtotime("-1 day"))) : local_strtotime($_REQUEST['start_date']);
    $filter['end_date'] = empty($_REQUEST['end_date']) ? local_strtotime(date("Y-m-d",strtotime("-1 day"))) : local_strtotime($_REQUEST['end_date']);
  


    /* 查询数据的条件 */
    $where = " WHERE t.pay_time >= '".($filter['start_date'])."' AND t.pay_time < '" . ($filter['end_date'] + 86400 ) . "' and t.pay_status = 2";

    $sql = 'select count(1)  from ( SELECT '.
			'count(1) '.
			'FROM ' .$GLOBALS['ecs']->table('order_goods')." AS g ".
			'LEFT JOIN ' .$GLOBALS['ecs']->table('order_info'). ' AS t ON g.`order_id`=t.`order_id`'.
			$where. " GROUP BY g.goods_id ) as cnt";

            
	$filter['record_count'] = $GLOBALS['db']->getOne($sql);
	// echo $sql;
    
    $filter = page_and_size($filter);

    	$sql = 'SELECT '.
			'g.goods_id id, '.
			'g.goods_name title, '.
			'sum( g.goods_price  * g.goods_number) price, '.
			'sum(g.goods_number) num '.
			'FROM ' .$GLOBALS['ecs']->table('order_goods')." AS g ".
			'LEFT JOIN ' .$GLOBALS['ecs']->table('order_info'). ' AS t ON g.`order_id`=t.`order_id`'.
			$where. "  GROUP BY g.goods_id ORDER BY price DESC";
    
    if ($is_pagination)
	{
		$sql .= " LIMIT " . $filter['start'] . ', ' . $filter['page_size'];
	}
	//var_dump($sql);
	$sale_list_data = $GLOBALS['db']->query($sql);

	

	while ($items = $GLOBALS['db']->fetchRow($sale_list_data))
	{
        //格式化
		$items['price']   = price_format($items['price']);
        /*
		$items['discount_fee']   = price_format($items['discount_fee']);
		$items['post_fee']   = price_format($items['post_fee']);
		$items['payment']   = price_format($items['payment']);
		$items['add_time']    = local_date($GLOBALS['_CFG']['time_format'], $items['add_time']);
		$items['pay_time']    = local_date($GLOBALS['_CFG']['time_format'], $items['pay_time']);
        */
		$goods_sales_list[]     = $items;
	}

	//var_dump($cost_total);
	$arr = array('sale_list_data' => $goods_sales_list, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
	//var_dump($arr);
	return $arr;

}



?>