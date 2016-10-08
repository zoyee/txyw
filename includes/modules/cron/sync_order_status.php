<?php
/**
 * 同步订单状态
 * $Author：zoyee
 * $Id: sync_order_status.php
 *
 */
if (! defined ( 'IN_ECS' )) {
	die ( 'Hacking attempt' );
}

error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE );
define("LOG4PHP_DIR", ROOT_PATH . "includes/log4php");
require_once (LOG4PHP_DIR . '/LoggerManager.php');
$logger = LoggerManager::getLogger('sync_order_status.php');
$cron_lang = ROOT_PATH . 'languages/' . $GLOBALS ['_CFG'] ['lang'] . '/cron/sync_order_status.php';

if (file_exists ( $cron_lang )) {
	global $_LANG;
	include_once ($cron_lang);
}

/* 模块的基本信息 */
if (isset ( $set_modules ) && $set_modules == TRUE) {
	$i = isset ( $modules ) ? count ( $modules ) : 0;

	/* 代码 */
	$modules [$i] ['code'] = basename ( __FILE__, '.php' );
	/* 描述对应的语言项 */
	$modules [$i] ['desc'] = 'sync_order_status_desc';
	/* 作者 */
	$modules [$i] ['author'] = 'zoyee';
	/* 网址 */
	$modules [$i] ['website'] = 'http://www.ecshop.com';
	/* 版本号 */
	$modules [$i] ['version'] = '1.0.0';
	/* 配置信息 */
	$modules [$i] ['config'] = array (
			array (
					'name' => 'server_ip',
					'type' => 'text',
					'value' => '183.61.117.101'
			),
			array (
					'name' => 'sqlsrv_database',
					'type' => 'text',
					'value' => 'AIS20150715152331'
			),
			array (
					'name' => 'sqlsrv_uid',
					'type' => 'text',
					'value' => 'sa'
			),
			array (
					'name' => 'sqlsrv_pwd',
					'type' => 'text',
					'value' => 'longxi*2017Qqww#ggry'
			),
			array (
					'name' => 'sqlsrv_CharacterSet',
					'type' => 'text',
					'value' => 'UTF-8'
			),
			array (
					'name' => 'dayOfOrderFrom',
					'type' => 'text',
					'value' => '17'
			),
			array (
					'name' => 'dayOfOrderTo',
					'type' => 'text',
					'value' => '7'
			)
	);

	return;
}

set_time_limit ( 0 );
$cur_date = date ( 'Y-m-d', time () );
// $logger->info("开始同步订单状态定时任务!");

$connectionInfo = array (
		"UID" => $cron ['sqlsrv_uid'],
		"PWD" => $cron ['sqlsrv_pwd'],
		"Database" => $cron ['sqlsrv_database'],
		"CharacterSet" => $cron ['sqlsrv_CharacterSet']
);
$conn = sqlsrv_connect ( $cron ['server_ip'], $connectionInfo );
if ($conn == false) {
	$logger->error("sqlserver数据库连接失败！IP=" . $cron ['server_ip']);
	$logger->error(sqlsrv_errors ());
} else {
// 	$logger->info("sqlserver连接成功！IP=" . $cron ['server_ip']);
}

$cron['dayOfOrderTo'] = abs(1 - intval($cron['dayOfOrderTo']));

/* 查询订单信息 */
$page_size = 500;
$page_num = 0;
$count = 0;
$total_count = 0;
//金蝶快递公司ID对应商城快递id
$shop_mapping = array(
		'47' => '5',
		'17' => '9',
		'15' => '2',
		'36' => '10',
		'44' => '14',
		'18' => '15'
);

$order_status_mapping = array(
		'1' => '',//待审核
		'2' => '',//已审核
		'6' => '',//已配货
		'14' => '',//已发货
);

do {
	$count = 0;
	$from_index = $page_size * $page_num;
	$cond = empty ( $cron ['dayOfOrderFrom'] ) ? "" : " and o.FOrderDate >= dateadd(day,-".$cron ['dayOfOrderFrom'].",CONVERT(char(19), getdate(), 112))";
	$sql = "select top " . $page_size . " o.FMobile as mobile, o.FSiteOrderID+'' as order_id, o.FID,
			o.FOrderStatus as order_status, o.FCheckStatus as check_status,
			CONVERT(varchar(30),o.FOrderDate,112) as trade_date, CONVERT(char(19), o.FOrderDate, 120) as create_time, o.FWebshopID as shop_id,
			o.FLogisticsID as logistics_id, o.FLogisticsComp as logistics_comp, FLogisticsOrder as logistics_order
			from IC_Web2ERPOrders o
			where o.FWebshopID='OTHER_13' and o.FOrderStatus = 14".
				$cond.
				" and o.FOrderDate < dateadd(day,-0,CONVERT(char(19), getdate(), 112))
				and o.FID not in (select top " . $from_index . " FID
					from IC_Web2ERPOrders o
					where o.FWebshopID='OTHER_13' and o.FOrderStatus = 14".
						$cond.
						" and o.FOrderDate < dateadd(day,-".$cron ['dayOfOrderTo'].",CONVERT(char(19), getdate(), 112))
					order by o.FOrderDate desc)
			order by o.FOrderDate desc;";
	$logger->debug($sql);

	$query = sqlsrv_query ( $conn, $sql );
	$save_arr = array ();
	$cust_order = null;
	while ( $row = sqlsrv_fetch_array ( $query ) ) {
		//同步积分商城的订单状态
// 		if($row['shop_id'] == 'OTHER_13'){
		if(empty($row['logistics_id']) || empty($row['logistics_order'])){
			$sql = "update ecs_order_info o
					set o.shipping_status=1 where o.order_id='".$row['order_id']."'";
		} else {
			$sql = "update ecs_order_info o, ecs_touch_shipping s
					set o.shipping_status=1, o.shipping_id = '".$shop_mapping[$row['logistics_id'].""]. "'
					,o.shipping_name=s.shipping_name ,o.invoice_no ='" . $row['logistics_order'] . "'  where
					s.shipping_id = '".$shop_mapping[$row['logistics_id'].""]."' and o.order_id='".$row['order_id']."'";
		}
// 		$logger->debug("更新自有商城订单状态: " . $sql);
		$GLOBALS['db']->query($sql);
// 		}

		$count += 1;
	}
	// if($cust_info != null) $cust_info->saveAll($save_arr);

	$total_count += $count;
	$page_num += 1;
} while ( $count == $page_size );

// $logger->info("同步订单状态" . $total_count . "条，定时任务完成!");