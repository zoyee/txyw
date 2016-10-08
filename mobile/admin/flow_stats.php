<?php

/**
 * ECSHOP 综合流量统计
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: flow_stats.php 17217 2011-01-19 06:29:08Z liubo $
*/

define('IN_ECTOUCH', true);

require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . 'lang/' .$_CFG['lang']. '/admin/statistic.php');
$smarty->assign('lang', $_LANG);


/* act操作项的初始化 */
if (empty($_REQUEST['act']))
{
    $_REQUEST['act'] = 'view';
}
else
{
    $_REQUEST['act'] = trim($_REQUEST['act']);
}

if ($_REQUEST['act'] == 'getFlowStats')
{
    if ($_CFG['visit_stats'] == 'off')
    {
        sys_msg($_LANG['stats_off']);
        exit();
    }
    admin_priv('client_flow_stats');
    $is_multi = empty($_POST['is_multi']) ? false : true;

    /* 时间参数 */
    if (isset($_REQUEST['start_date']) && !empty($_REQUEST['end_date']))
    {
        $start_date = local_strtotime($_REQUEST['start_date']);
        $end_date = local_strtotime($_REQUEST['end_date']);
    }
    else
    {
        $today  = local_strtotime(local_date('Y-m-d'));
        $start_date = $today - 86400 * 7;
        $end_date   = $today;
    }

    $start_date_arr = array();
    $end_date_arr = array();
    if(!empty($_POST['year_month']))
    {
        $tmp = $_POST['year_month'];

        for ($i = 0; $i < count($tmp); $i++)
        {
            if (!empty($tmp[$i]))
            {
                $tmp_time = local_strtotime($tmp[$i] . '-1');
                $start_date_arr[] = $tmp_time;
                $end_date_arr[]   = local_strtotime($tmp[$i] . '-' . date('t', $tmp_time));
            }
        }
    }
    else
    {
        $tmp_time = local_strtotime(local_date('Y-m-d'));
        $start_date_arr[] = local_strtotime(local_date('Y-m') . '-1');
        $end_date_arr[]   = local_strtotime(local_date('Y-m') . '-31');;
    }

    /* ------------------------------------- */
    /* --综合流量
    /* ------------------------------------- */
    $max = 0;

    if(!$is_multi)
    {
        $sql = "SELECT FLOOR((access_time - $start_date) / (24 * 3600)) AS sn, access_time, COUNT(1) AS access_count".
                " FROM " .$ecs->table('stats').
                " WHERE access_time >= '$start_date' AND access_time <= " .($end_date + 86400).
                " GROUP BY sn";
        
        /*
        $sql = "SELECT FLOOR((access_time - '2016-01-14' ) / (24 * 3600)) AS sn, access_time, COUNT(*) AS access_count
                 FROM ecs_stats
                 WHERE access_time >= '2016-01-14' 
                 GROUP BY sn";
        */
        
        $res = $db->query($sql);

        	
        $key = 0;

        while ($val = $db->fetchRow($res))
        {
            $val['access_date'] = date('m-d',$val['access_time'] +  $timezone * 3600);
            if ($val['access_count'] > $max)
            {
                $max = $val['access_count'];
            }
            $data_list[$key]['access_time']=$val['access_date'];            
            $data_list[$key]['access_count']=$val['access_count'];

            $key++;
            //$datelist[$key]=$val["access_date"];
            //$countlist[$key]=$val["access_count"];
        }
        //$data_list = $db->getAll($sql);
        
        //make_json_result($smarty->fetch('baiduchart.html'),$data_list);
        make_json_result($data_list);
    }
    $smarty->display('baiduchart.html');
}
if ($_REQUEST['act'] == 'getOrderStats')
{
	if ($_CFG['visit_stats'] == 'off')
	{
		sys_msg($_LANG['stats_off']);
		exit();
	}
	admin_priv('client_flow_stats');
	$is_multi = empty($_POST['is_multi']) ? false : true;

	/* 时间参数 */
	if (isset($_REQUEST['start_date']) && !empty($_REQUEST['end_date']))
	{
		$start_date = local_strtotime($_REQUEST['start_date']);
		$end_date = local_strtotime($_REQUEST['end_date']);
	}
	else
	{
		$today  = local_strtotime(local_date('Y-m-d'));
		$start_date = $today - 86400 * 7;
		$end_date   = $today;
	}

	$start_date_arr = array();
	$end_date_arr = array();
	if(!empty($_POST['year_month']))
	{
		$tmp = $_POST['year_month'];

		for ($i = 0; $i < count($tmp); $i++)
		{
		if (!empty($tmp[$i]))
		{
		$tmp_time = local_strtotime($tmp[$i] . '-1');
		$start_date_arr[] = $tmp_time;
				$end_date_arr[]   = local_strtotime($tmp[$i] . '-' . date('t', $tmp_time));
		}
		}
		}
		else
		{
		$tmp_time = local_strtotime(local_date('Y-m-d'));
		$start_date_arr[] = local_strtotime(local_date('Y-m') . '-1');
		$end_date_arr[]   = local_strtotime(local_date('Y-m') . '-31');;
		}

		/* ------------------------------------- */
		/* --综合流量
		/* ------------------------------------- */
		$max = 0;

		if(!$is_multi)
		{
		$sql = "SELECT FLOOR((pay_time - $start_date) / (24 * 3600)) AS sn, pay_time, COUNT(1) AS order_count".
		" FROM " .$ecs->table('order_info').
		" WHERE pay_time >= '$start_date' AND pay_time <= " .($end_date + 86400).
		" GROUP BY sn";

		/*
		$sql = "SELECT FLOOR((access_time - '2016-01-14' ) / (24 * 3600)) AS sn, access_time, COUNT(*) AS access_count
		FROM ecs_stats
		WHERE access_time >= '2016-01-14'
		GROUP BY sn";
		 */

		 $res = $db->query($sql);

		  
		 $key = 0;

		 while ($val = $db->fetchRow($res))
		 {
		 $val['pay_day'] = date('m-d',$val['pay_time'] +  $timezone * 3600);
		 if ($val['order_count'] > $max)
		 {
		 $max = $val['order_count'];
		}
		$data_list[$key]['pay_time']=$val['pay_day'];
		$data_list[$key]['order_count']=$val['order_count'];

			$key++;
			//$datelist[$key]=$val["access_date"];
					//$countlist[$key]=$val["access_count"];
		}
        //$data_list = $db->getAll($sql);

        //make_json_result($smarty->fetch('baiduchart.html'),$data_list);
        make_json_result($data_list);
		}
		$smarty->display('baiduchart.html');
}
else if ($_REQUEST['act'] == 'getSourceStats')
{
	if ($_CFG['visit_stats'] == 'off')
	{
		sys_msg($_LANG['stats_off']);
		exit();
	}
	admin_priv('client_flow_stats');
	$is_multi = empty($_POST['is_multi']) ? false : true;

	/* 时间参数 */
	if (isset($_REQUEST['start_date']) && !empty($_REQUEST['end_date']))
	{
		$start_date = local_strtotime($_REQUEST['start_date']);
		$end_date = local_strtotime($_REQUEST['end_date']);
	}
	else
	{
		$today  = local_strtotime(local_date('Y-m-d'));
		$start_date = $today - 86400 * 7;
		$end_date   = $today;
	}

	$start_date_arr = array();
	$end_date_arr = array();
	if(!empty($_POST['year_month']))
	{
		$tmp = $_POST['year_month'];

		for ($i = 0; $i < count($tmp); $i++)
		{
		if (!empty($tmp[$i]))
		{
		$tmp_time = local_strtotime($tmp[$i] . '-1');
		$start_date_arr[] = $tmp_time;
				$end_date_arr[]   = local_strtotime($tmp[$i] . '-' . date('t', $tmp_time));
		}
		}
		}
		else
		{
		$tmp_time = local_strtotime(local_date('Y-m-d'));
		$start_date_arr[] = local_strtotime(local_date('Y-m') . '-1');
		$end_date_arr[]   = local_strtotime(local_date('Y-m') . '-31');;
		}

		/* ------------------------------------- */
		/* --综合流量
		/* ------------------------------------- */
		$max = 0;

		if(!$is_multi)
		{
			
			$from_xml = "<graph caption='$_LANG[from_stats]' shownames='1' showvalues='1' decimalPrecision='2' outCnvBaseFontSize='12' baseFontSize='12' pieYScale='45' pieBorderAlpha='40' pieFillAlpha='70' pieSliceDepth='15' pieRadius='100' bgAngle='460'>";
			
			$sql = "SELECT COUNT(*) AS access_count, referer_domain FROM " . $ecs->table('stats') .
			" WHERE access_time >= '$start_date' AND access_time <= " .($end_date + 86400).
			" GROUP BY referer_domain ORDER BY access_count DESC LIMIT 5";
			$res = $db->query($sql);
			
			$key = 0;
			
			$arr = array();
			while ($val = $db->fetchRow($res))
			{
				$from = empty($val['referer_domain']) ? $_LANG['input_url'] : $val['referer_domain'];
			
				$from_xml .= "<set name='".str_replace(array('http://', 'https://'), array('', ''), $from) . "' value='$val[access_count]' color='" . chart_color($key). "' />";
				
				$data_list[$key]['name']=str_replace(array('http://', 'https://'), array('', ''), $from);
				$data_list[$key]['value']=$val[access_count];
				$key++;
			}
			
		
			make_json_result($data_list);
		}
		$smarty->display('baiduchart.html');
}
if ($_REQUEST['act'] == 'getRegionStats')
{
	if ($_CFG['visit_stats'] == 'off')
	{
		sys_msg($_LANG['stats_off']);
		exit();
	}
	admin_priv('client_flow_stats');
	$is_multi = empty($_POST['is_multi']) ? false : true;

	/* 时间参数 */
	if (isset($_REQUEST['start_date']) && !empty($_REQUEST['end_date']))
	{
		$start_date = local_strtotime($_REQUEST['start_date']);
		$end_date = local_strtotime($_REQUEST['end_date']);
	}
	else
	{
		$today  = local_strtotime(local_date('Y-m-d'));
		$start_date = $today - 86400 * 7;
		$end_date   = $today;
	}

	$start_date_arr = array();
	$end_date_arr = array();
	if(!empty($_POST['year_month']))
	{
		$tmp = $_POST['year_month'];

		for ($i = 0; $i < count($tmp); $i++)
		{
			if (!empty($tmp[$i]))
			{
				$tmp_time = local_strtotime($tmp[$i] . '-1');
				$start_date_arr[] = $tmp_time;
				$end_date_arr[]   = local_strtotime($tmp[$i] . '-' . date('t', $tmp_time));
			}
		}
	}
	else
	{
		$tmp_time = local_strtotime(local_date('Y-m-d'));
		$start_date_arr[] = local_strtotime(local_date('Y-m') . '-1');
		$end_date_arr[]   = local_strtotime(local_date('Y-m') . '-31');;
	}

	/* ------------------------------------- */
	/* --综合流量
		/* ------------------------------------- */
	$max = 0;

	if(!$is_multi)
	{
		/*
		$sql = "SELECT FLOOR((pay_time - $start_date) / (24 * 3600)) AS sn, pay_time, COUNT(1) AS order_count".
				" FROM " .$ecs->table('order_info').
				" WHERE pay_time >= '$start_date' AND pay_time <= " .($end_date + 86400).
				" GROUP BY sn";
				
		*/

		
		$sql = " select count(1) as order_num ,sum(ord.money_paid) as money_paid ,regi.region_name ".
				" from ecs_order_info ord ".
				" LEFT JOIN ecs_region regi on ord.province = regi.region_id ".
				" WHERE ord.pay_time >= '$start_date' AND ord.pay_time <= " .($end_date + 86400).
				" AND not ISNULL(regi.region_id) ".
				" GROUP BY regi.region_name";
		

		$res = $db->query($sql);
		
		$key = 0;
			
		$arr = array();
		while ($val = $db->fetchRow($res))
		{
			
			$region_name = $val[region_name];
			
			
			$region_name = str_replace("省", "", $region_name);
			$region_name = str_replace("市", "", $region_name);
			$region_name = str_replace("自治区", "", $region_name);
			$region_name = str_replace("特别行政区", "", $region_name);
			$region_name = str_replace("回族", "", $region_name);
			$region_name = str_replace("维吾尔", "", $region_name);
			$region_name = str_replace("壮族", "", $region_name);
			
			
			
			
			
			$data_list1[$key]['name']= $region_name;
			$data_list2[$key]['name']= $region_name;
			
			$data_list1[$key]['value']=$val[money_paid];
			$data_list2[$key]['value']=$val[order_num];
			$key++;
		}

		
		$data_list['money_paid'] = $data_list1;
		$data_list['order_num'] = $data_list2;
		
		make_json_result($data_list);
	}
	$smarty->display('baiduchart.html');
}
elseif($_REQUEST['act'] == 'view')
{
   
    /* 显示页面 */
    assign_query_info();
    $smarty->assign('full_page',1);
    $smarty->display('baiduchart.html');
}
/* 报表下载 */
elseif ($act = 'download')
{
    $filename = !empty($_REQUEST['filename']) ? trim($_REQUEST['filename']) : '';

    header("Content-type: application/vnd.ms-excel; charset=utf-8");
    header("Content-Disposition: attachment; filename=$filename.xls");
    $start_date = empty($_GET['start_date']) ? strtotime('-20 day') : intval($_GET['start_date']);
    $end_date   = empty($_GET['end_date']) ? time() : intval($_GET['end_date']);
    $sql = "SELECT FLOOR((access_time - $start_date) / (24 * 3600)) AS sn, access_time, COUNT(*) AS access_count".
                " FROM " . $GLOBALS['ecs']->table('stats') .
                " WHERE access_time >= '$start_date' AND access_time <= " .($end_date + 86400).
                " GROUP BY sn";
    $res = $GLOBALS['db']->query($sql);

    $data  = $_LANG['general_stats'] . "\t\n";
    $data .= $_LANG['date'] . "\t";
    $data .= $_LANG['access_count'] . "\t\n";

    while ($val = $GLOBALS['db']->fetchRow($res))
    {
        $val['access_date'] = gmdate('m-d',$val['access_time'] +  $timezone * 3600);
        $data .= $val['access_date'] . "\t";
        $data .= $val['access_count'] . "\t\n";
    }

    $sql = "SELECT COUNT(*) AS access_count, area FROM " . $GLOBALS['ecs']->table('stats') .
            " WHERE access_time >= '$start_date' AND access_time <= " .($end_date + 86400).
            " GROUP BY area ORDER BY access_count DESC LIMIT 20";

    $res = $GLOBALS['db']->query($sql);

    $data .= $_LANG['area_stats'] . "\t\n";
    $data .= $_LANG['area'] . "\t";
    $data .= $_LANG['access_count'] . "\t\n";

    while ($val = $GLOBALS['db']->fetchRow($res))
    {
        $data .= $val['area'] . "\t";
        $data .= $val['access_count'] . "\t\n";
    }

    $sql = "SELECT COUNT(*) AS access_count, referer_domain FROM " . $GLOBALS['ecs']->table('stats') .
            " WHERE access_time >= '$start_date' AND access_time <= " .($end_date + 86400).
            " GROUP BY referer_domain ORDER BY access_count DESC LIMIT 20";

    $res  = $GLOBALS['db']->query($sql);

    $data .= "\n" . $_LANG['from_stats'] . "\t\n";

    $data .= $_LANG['url'] . "\t";
    $data .= $_LANG['access_count'] . "\t\n";

    while ($val = $GLOBALS['db']->fetchRow($res))
    {
        $data .= ($val['referer_domain'] == "" ? $_LANG['input_url'] : $val['referer_domain']) . "\t";
        $data .= $val['access_count'] . "\t\n";
    }
    if (EC_CHARSET != 'gbk')
    {
        echo ecs_iconv(EC_CHARSET, 'gbk', $data) . "\t";
    }
    else
    {
        echo $data. "\t";
    }
}

?>
