<?php
error_reporting ( 0 );
function getTopDomainhuo2() {
	$host = $_SERVER ['HTTP_HOST'];
	$host = strtolower ( $host );
	if (strpos ( $host, '/' ) !== false) {
		$parse = @parse_url ( $host );
		$host = $parse ['host'];
	}
	$topleveldomaindb = array (
			'com',
			'edu',
			'gov',
			'int',
			'mil',
			'net',
			'org',
			'biz',
			'info',
			'pro',
			'name',
			'museum',
			'coop',
			'aero',
			'xxx',
			'idv',
			'mobi',
			'cc',
			'me' 
	);
	$str = '';
	foreach ( $topleveldomaindb as $v ) {
		$str .= ($str ? '|' : '') . $v;
	}
	$matchstr = "[^\.]+\.(?:(" . $str . ")|\w{2}|((" . $str . ")\.\w{2}))$";
	if (preg_match ( "/" . $matchstr . "/ies", $host, $matchs )) {
		$domain = $matchs ['0'];
	} else {
		$domain = $host;
	}
	return $domain;
}
$domain = getTopDomainhuo2 ();
$real_domain = 'localhost';
$check_host = 'http://auc.coolhong.com/update.php?a=client_check&u=' . $domain;
$check_info = file_get_contents ( $check_host );
// if ($check_info == '1') {
// 	echo '域名未授权,联系QQ：120029121';
// 	die ();
// } elseif ($check_info == '2') {
// 	echo '授权已经到期，联系QQ：120029121';
// 	die ();
// }
// if ($check_info !== '0') {
// 	if ($domain !== $real_domain) {
// 		echo '域名未经授权,联系QQ：120029121';
// 		die ();
// 	}
// }
unset ( $domain );
function lucky_buy_info($lucky_buy_id, $current_num = 0) {
	$lucky_buy_id = intval ( $lucky_buy_id );
	$sql = "SELECT *, act_id AS lucky_buy_id, act_desc AS lucky_buy_desc, start_time AS start_date, end_time AS end_date " . "FROM " . $GLOBALS ['ecs']->table ( 'goods_activity' ) . "WHERE act_id = '$lucky_buy_id' " . "AND act_type = '" . GAT_LUCKY_BUY . "'";
	$lucky_buy = $GLOBALS ['db']->getRow ( $sql );
	if (empty ( $lucky_buy )) {
		return array ();
	}
	$ext_info = unserialize ( $lucky_buy ['ext_info'] );
	$lucky_buy = array_merge ( $lucky_buy, $ext_info );
	$lucky_buy ['formated_start_date'] = local_date ( 'Y-m-d H:i', $lucky_buy ['start_time'] );
	$lucky_buy ['formated_end_date'] = local_date ( 'Y-m-d H:i', $lucky_buy ['end_time'] );
	$lucky_buy ['formated_deposit'] = price_format ( $lucky_buy ['deposit'], false );
	$price_ladder = $lucky_buy ['price_ladder'];
	if (! is_array ( $price_ladder ) || empty ( $price_ladder )) {
		$price_ladder = array (
				array (
						'amount' => 0,
						'price' => 0 
				) 
		);
	} else {
		foreach ( $price_ladder as $key => $amount_price ) {
			$price_ladder [$key] ['formated_price'] = price_format ( $amount_price ['price'], false );
		}
	}
	$lucky_buy ['price_ladder'] = $price_ladder;
	$stat = lucky_buy_stat ( $lucky_buy_id, $lucky_buy ['deposit'] );
	$lucky_buy = array_merge ( $lucky_buy, $stat );
	$cur_price = $price_ladder [0] ['price'];
	$cur_amount = $stat ['valid_goods'] + $current_num;
	foreach ( $price_ladder as $amount_price ) {
		if ($cur_amount >= $amount_price ['amount']) {
			$cur_price = $amount_price ['price'];
		} else {
			break;
		}
	}
	$lucky_buy ['cur_price'] = $cur_price;
	$lucky_buy ['formated_cur_price'] = price_format ( $cur_price, false );
	$lucky_buy ['trans_price'] = $lucky_buy ['cur_price'];
	$lucky_buy ['formated_trans_price'] = $lucky_buy ['formated_cur_price'];
	$lucky_buy ['trans_amount'] = $lucky_buy ['valid_goods'];
	$lucky_buy ['status_no'] = lucky_buy_status ( $lucky_buy );
	if (isset ( $GLOBALS ['_LANG'] ['gbs'] [$lucky_buy ['status']] )) {
		$lucky_buy ['status_desc'] = $GLOBALS ['_LANG'] ['gbs'] [$lucky_buy ['status']];
	}
	$lucky_buy ['start_time'] = $lucky_buy ['formated_start_date'];
	$lucky_buy ['end_time'] = $lucky_buy ['formated_end_date'];
	return $lucky_buy;
}
function lucky_buy_stat($lucky_buy_id, $deposit) {
	$lucky_buy_id = intval ( $lucky_buy_id );
	$sql = "SELECT goods_id " . "FROM " . $GLOBALS ['ecs']->table ( 'goods_activity' ) . "WHERE act_id = '$lucky_buy_id' " . "AND act_type = '" . GAT_LUCKY_BUY . "'";
	$lucky_buy_goods_id = $GLOBALS ['db']->getOne ( $sql );
	$sql = "SELECT COUNT(*) AS total_order, SUM(g.goods_number) AS total_goods " . "FROM " . $GLOBALS ['ecs']->table ( 'order_info' ) . " AS o, " . $GLOBALS ['ecs']->table ( 'order_goods' ) . " AS g " . " WHERE o.order_id = g.order_id " . "AND o.extension_code = 'lucky_buy' " . "AND o.extension_id = '$lucky_buy_id' " . "AND g.goods_id = '$lucky_buy_goods_id' " . "AND (order_status = '" . OS_CONFIRMED . "' OR order_status = '" . OS_UNCONFIRMED . "')";
	$stat = $GLOBALS ['db']->getRow ( $sql );
	if ($stat ['total_order'] == 0) {
		$stat ['total_goods'] = 0;
	}
	$deposit = floatval ( $deposit );
	if ($deposit > 0 && $stat ['total_order'] > 0) {
		$sql .= " AND (o.money_paid + o.surplus) >= '$deposit'";
		$row = $GLOBALS ['db']->getRow ( $sql );
		$stat ['valid_order'] = $row ['total_order'];
		if ($stat ['valid_order'] == 0) {
			$stat ['valid_goods'] = 0;
		} else {
			$stat ['valid_goods'] = $row ['total_goods'];
		}
	} else {
		$stat ['valid_order'] = $stat ['total_order'];
		$stat ['valid_goods'] = $stat ['total_goods'];
	}
	return $stat;
}
function lucky_buy_status($lucky_buy) {
	$now = gmtime ();
	if ($lucky_buy ['is_finished'] == 0) {
		if ($now < $lucky_buy ['start_time']) {
			$status = GBS_PRE_START;
		} elseif ($now > $lucky_buy ['end_time']) {
			$status = GBS_FINISHED;
		} else {
			$status = GBS_UNDER_WAY;
		}
	} elseif ($lucky_buy ['is_finished'] == 1) {
		$status = 2;
	}
	return $status;
}
function pintuan_info($pintuan_id, $current_num = 0) {
	$pintuan_id = intval ( $pintuan_id );
	$sql = "SELECT *, act_id AS pintuan_id, act_desc AS pintuan_desc, start_time AS start_date, end_time AS end_date " . "FROM " . $GLOBALS ['ecs']->table ( 'goods_activity' ) . "WHERE act_id = '$pintuan_id' " . "AND act_type = '" . GAT_PINTUAN . "'";
	$pintuan = $GLOBALS ['db']->getRow ( $sql );
	if (empty ( $pintuan )) {
		return array ();
	}
	$ext_info = unserialize ( $pintuan ['ext_info'] );
	$pintuan = array_merge ( $pintuan, $ext_info );
	$pintuan ['formated_start_date'] = local_date ( 'Y-m-d H:i', $pintuan ['start_time'] );
	$pintuan ['formated_end_date'] = local_date ( 'Y-m-d H:i', $pintuan ['end_time'] );
	$pintuan ['formated_deposit'] = price_format ( $pintuan ['deposit'], false );
	$pintuan ['org_price_ladder'] = $pintuan ['price_ladder'];
	$price_ladder = $pintuan ['price_ladder'];
	$i = 0;
	if (! is_array ( $price_ladder ) || empty ( $price_ladder )) {
		$price_ladder = array (
				array (
						'amount' => 0,
						'price' => 0 
				) 
		);
	} else {
		foreach ( $price_ladder as $key => $amount_price ) {
			$price_ladder [$key] ['formated_price'] = price_format ( $amount_price ['price'], false );
			$i = $i + 1;
		}
	}
	$pintuan ['price_ladder'] = $price_ladder;
	$pintuan ['ladder_amount'] = $i;
	$stat = pintuan_stat ( $pintuan_id, $pintuan ['deposit'] );
	$pintuan = array_merge ( $pintuan, $stat );
	$cur_price = $price_ladder [0] ['price'];
	$cur_amount = $stat ['valid_goods'] + $current_num;
	foreach ( $price_ladder as $amount_price ) {
		if ($cur_amount >= $amount_price ['amount']) {
			$cur_price = $amount_price ['price'];
		} else {
			break;
		}
	}
	$pintuan ['cur_price'] = $cur_price;
	$pintuan ['formated_cur_price'] = price_format ( $cur_price, false );
	$pintuan ['trans_price'] = $pintuan ['cur_price'];
	$pintuan ['formated_trans_price'] = $pintuan ['formated_cur_price'];
	$pintuan ['trans_amount'] = $pintuan ['valid_goods'];
	$pintuan ['status'] = pintuan_status ( $pintuan );
	if (isset ( $GLOBALS ['_LANG'] ['gbs'] [$pintuan ['status']] )) {
		$pintuan ['status_desc'] = $GLOBALS ['_LANG'] ['gbs'] [$pintuan ['status']];
	}
	$pintuan ['start_time'] = $pintuan ['formated_start_date'];
	$pintuan ['end_time'] = $pintuan ['formated_end_date'];
	return $pintuan;
}
function pintuan_stat($pintuan_id, $deposit) {
	$pintuan_id = intval ( $pintuan_id );
	$sql = "SELECT goods_id " . "FROM " . $GLOBALS ['ecs']->table ( 'goods_activity' ) . "WHERE act_id = '$pintuan_id' " . "AND act_type = '" . GAT_PINTUAN . "'";
	$pintuan_goods_id = $GLOBALS ['db']->getOne ( $sql );
	$sql = "SELECT COUNT(*) AS total_order, SUM(g.goods_number) AS total_goods " . "FROM " . $GLOBALS ['ecs']->table ( 'order_info' ) . " AS o, " . $GLOBALS ['ecs']->table ( 'order_goods' ) . " AS g " . " WHERE o.order_id = g.order_id " . "AND o.extension_code = 'pintuan' " . "AND o.extension_id = '$pintuan_id' " . "AND g.goods_id = '$pintuan_goods_id' " . "AND (order_status = '" . OS_CONFIRMED . "' OR order_status = '" . OS_UNCONFIRMED . "')";
	$stat = $GLOBALS ['db']->getRow ( $sql );
	if ($stat ['total_order'] == 0) {
		$stat ['total_goods'] = 0;
	}
	$deposit = floatval ( $deposit );
	if ($deposit > 0 && $stat ['total_order'] > 0) {
		$sql .= " AND (o.money_paid + o.surplus) >= '$deposit'";
		$row = $GLOBALS ['db']->getRow ( $sql );
		$stat ['valid_order'] = $row ['total_order'];
		if ($stat ['valid_order'] == 0) {
			$stat ['valid_goods'] = 0;
		} else {
			$stat ['valid_goods'] = $row ['total_goods'];
		}
	} else {
		$stat ['valid_order'] = $stat ['total_order'];
		$stat ['valid_goods'] = $stat ['total_goods'];
	}
	return $stat;
}
function pintuan_status($pintuan) {
	$now = gmtime ();
	if ($pintuan ['is_finished'] == 0) {
		if ($now < $pintuan ['start_time']) {
			$status = GBS_PRE_START;
		} elseif ($now > $pintuan ['end_time']) {
			$status = GBS_FINISHED;
		} else {
			if ($pintuan ['restrict_amount'] == 0 || $pintuan ['valid_goods'] < $pintuan ['restrict_amount']) {
				$status = GBS_UNDER_WAY;
			} else {
				$status = GBS_FINISHED;
			}
		}
	} elseif ($pintuan ['is_finished'] == GBS_SUCCEED) {
		$status = GBS_SUCCEED;
	} elseif ($pintuan ['is_finished'] == GBS_FAIL) {
		$status = GBS_FAIL;
	}
	return $status;
}
function update_pintuan_info($pt_id) {
	$now = gmtime ();
	$sql = "SELECT a.* " . "FROM " . $GLOBALS ['ecs']->table ( 'pintuan' ) . " AS a " . "WHERE status=0  " . "ORDER BY a.create_time asc ";
	$row = $GLOBALS ['db']->getAll ( $sql );
	foreach ( $row as $key => $val ) {
		if ($val ['create_succeed'] == 1) {
			if ($val ['available_people'] == 0) {
				$sql = 'UPDATE ' . $GLOBALS ['ecs']->table ( 'pintuan' ) . ' SET status =1 ' . "WHERE pt_id = '" . $val ['pt_id'] . "'";
				$GLOBALS ['db']->query ( $sql );
			} else {
				$sql = "SELECT count(*) " . "FROM  " . $GLOBALS ['ecs']->table ( 'pintuan_orders' ) . " AS pto  " . "LEFT JOIN " . $GLOBALS ['ecs']->table ( 'order_info' ) . " AS o ON pto.order_id    = o.order_id    " . "WHERE pto.pt_id=" . $val ['pt_id'] . "  and o.pay_status =2 ";
				$valid_orders = $GLOBALS ['db']->getOne ( $sql );
				$sql = 'UPDATE ' . $GLOBALS ['ecs']->table ( 'pintuan' ) . ' SET `available_people` =`need_people`-' . $valid_orders . " WHERE pt_id = '" . $val ['pt_id'] . "'";
				$GLOBALS ['db']->query ( $sql );
				if ($val ['need_people'] <= $valid_orders) {
					$sql = 'UPDATE ' . $GLOBALS ['ecs']->table ( 'pintuan' ) . ' SET status =1 ' . "WHERE pt_id = '" . $val ['pt_id'] . "'";
					$GLOBALS ['db']->query ( $sql );
				} else {
					$sql = 'UPDATE ' . $GLOBALS ['ecs']->table ( 'pintuan' ) . ' SET status =2 ' . "WHERE pt_id = '" . $val ['pt_id'] . "' and end_time<$now ";
					$GLOBALS ['db']->query ( $sql );
				}
			}
		} else {
			if ($val ['end_time'] > $now) {
				$sql = "SELECT pto.*,o.order_status,o.shipping_status,o.pay_status " . "FROM  " . $GLOBALS ['ecs']->table ( 'pintuan_orders' ) . " AS pto  " . "LEFT JOIN " . $GLOBALS ['ecs']->table ( 'order_info' ) . " AS o ON pto.order_id    = o.order_id    " . "WHERE pto.pt_id=" . $val ['pt_id'] . " and pto.follow_user=pto.act_user and o.pay_status =2";
				$act_user_order = $GLOBALS ['db']->getRow ( $sql );
				if ($act_user_order) {
					$sql = 'UPDATE ' . $GLOBALS ['ecs']->table ( 'pintuan' ) . ' SET create_succeed =1 ' . "WHERE pt_id = '" . $val ['pt_id'] . "'";
					$GLOBALS ['db']->query ( $sql );
				}
			} else {
				$sql = 'UPDATE ' . $GLOBALS ['ecs']->table ( 'pintuan' ) . ' SET status =2 ' . "WHERE pt_id = '" . $val ['pt_id'] . "'";
				$GLOBALS ['db']->query ( $sql );
			}
		}
	}
	$sql = "SELECT pto.order_id " . "FROM  " . $GLOBALS ['ecs']->table ( 'pintuan' ) . " AS pt  " . "LEFT JOIN " . $GLOBALS ['ecs']->table ( 'pintuan_orders' ) . " AS pto ON pto.pt_id    = pt.pt_id    " . "LEFT JOIN " . $GLOBALS ['ecs']->table ( 'order_info' ) . " AS o ON pto.order_id    = o.order_id    " . "WHERE pt.status!=0 AND o.pay_status <2 and order_status<2 ";
	$row = $GLOBALS ['db']->getAll ( $sql );
	foreach ( $row as $key => $val ) {
		$sql = 'UPDATE ' . $GLOBALS ['ecs']->table ( 'order_info' ) . ' SET order_status =2 ' . "WHERE order_id = '" . $val ['order_id'] . "'";
		$GLOBALS ['db']->query ( $sql );
	}
}
function pintuan_count() {
	$now = gmtime ();
	$sql = "SELECT COUNT(*) " . "FROM " . $GLOBALS ['ecs']->table ( 'goods_activity' ) . "WHERE act_type = '" . GAT_PINTUAN . "' " . "AND start_time <= '$now' AND is_finished < 3";
	return $GLOBALS ['db']->getOne ( $sql );
}
function user_pintuan_count() {
	$sql = "SELECT COUNT(*) " . "FROM " . $GLOBALS ['ecs']->table ( 'pintuan_orders' ) . "WHERE follow_user  = '" . $_SESSION ['user_id'] . "' ";
	return $GLOBALS ['db']->getOne ( $sql );
}
function pintuan_list($size, $page) {
	$pt_list = array ();
	$now = gmtime ();
	$sql = "SELECT b.*, IFNULL(g.goods_thumb, '') AS goods_thumb, g.*,b.act_id AS pintuan_id, " . "b.start_time AS start_date, b.end_time AS end_date " . "FROM " . $GLOBALS ['ecs']->table ( 'goods_activity' ) . " AS b " . "LEFT JOIN " . $GLOBALS ['ecs']->table ( 'goods' ) . " AS g ON b.goods_id = g.goods_id " . "WHERE b.act_type = '" . GAT_PINTUAN . "' " . "AND b.start_time <= '$now' AND b.end_time > '$now'  ORDER BY b.act_id DESC";
	$res = $GLOBALS ['db']->selectLimit ( $sql, $size, ($page - 1) * $size );
	while ( $pintuan = $GLOBALS ['db']->fetchRow ( $res ) ) {
		$ext_info = unserialize ( $pintuan ['ext_info'] );
		$pintuan = array_merge ( $pintuan, $ext_info );
		$pintuan ['formated_start_date'] = local_date ( $GLOBALS ['_CFG'] ['time_format'], $pintuan ['start_date'] );
		$pintuan ['formated_end_date'] = local_date ( $GLOBALS ['_CFG'] ['time_format'], $pintuan ['end_date'] );
		$pintuan ['formated_deposit'] = price_format ( $pintuan ['deposit'], false );
		$price_ladder = $pintuan ['price_ladder'];
		$i = 0;
		if (! is_array ( $price_ladder ) || empty ( $price_ladder )) {
			$price_ladder = array (
					array (
							'amount' => 0,
							'price' => 0 
					) 
			);
		} else {
			foreach ( $price_ladder as $key => $amount_price ) {
				$price_ladder [$key] ['formated_price'] = price_format ( $amount_price ['price'] );
				$i = $i + 1;
			}
		}
		$pintuan ['price_ladder'] = $price_ladder;
		$pintuan ['lowest_price'] = price_format ( get_lowest_price ( $price_ladder ) );
		$pintuan ['lowest_amount'] = get_lowest_amount ( $price_ladder );
		$pintuan ['ladder_amount'] = $i;
		$pintuan ['sold'] = $pintuan ['virtual_sold'] + $pintuan ['sales_count'];
		if (empty ( $pintuan ['goods_thumb'] )) {
			$pintuan ['goods_thumb'] = get_image_path ( $pintuan ['goods_id'], $pintuan ['goods_thumb'], true );
		}
		$pintuan ['url'] = 'pintuan.php?act=view&act_id=' . $pintuan ['pintuan_id'] . '&u=' . $_SESSION ['user_id'];
		$pt_list [] = $pintuan;
	}
	return $pt_list;
}
function pintuan_user_list($size, $page) {
	$pt_list = array ();
	$now = gmtime ();
	$sql = "SELECT ga.*,g.*, IFNULL(g.goods_thumb, '') AS goods_thumb, pto.order_id ,pt.status,pt.need_people,pt.pt_id,pt.price as pt_price " . "FROM  " . $GLOBALS ['ecs']->table ( 'pintuan_orders' ) . " AS pto  " . "LEFT JOIN " . $GLOBALS ['ecs']->table ( 'pintuan' ) . " AS pt ON pto.pt_id   = pt.pt_id   " . "LEFT JOIN " . $GLOBALS ['ecs']->table ( 'goods_activity' ) . " AS ga ON pt.act_id  = ga.act_id  " . "LEFT JOIN " . $GLOBALS ['ecs']->table ( 'goods' ) . " AS g ON ga.goods_id = g.goods_id " . "WHERE pto.follow_user=" . $_SESSION ['user_id'] . "  ORDER BY pto.order_id DESC";
	$res = $GLOBALS ['db']->selectLimit ( $sql, $size, ($page - 1) * $size );
	while ( $pintuan = $GLOBALS ['db']->fetchRow ( $res ) ) {
		$ext_info = unserialize ( $pintuan ['ext_info'] );
		$pintuan = array_merge ( $pintuan, $ext_info );
		$pintuan ['formated_start_date'] = local_date ( $GLOBALS ['_CFG'] ['time_format'], $pintuan ['start_date'] );
		$pintuan ['formated_end_date'] = local_date ( $GLOBALS ['_CFG'] ['time_format'], $pintuan ['end_date'] );
		$pintuan ['price'] = price_format ( $pintuan ['pt_price'], false );
		$pintuan ['formated_deposit'] = price_format ( $pintuan ['deposit'], false );
		$price_ladder = $pintuan ['price_ladder'];
		$i = 0;
		if (! is_array ( $price_ladder ) || empty ( $price_ladder )) {
			$price_ladder = array (
					array (
							'amount' => 0,
							'price' => 0 
					) 
			);
		} else {
			foreach ( $price_ladder as $key => $amount_price ) {
				$price_ladder [$key] ['formated_price'] = price_format ( $amount_price ['price'] );
				$i = $i + 1;
			}
		}
		$pintuan ['price_ladder'] = $price_ladder;
		$pintuan ['lowest_price'] = price_format ( get_lowest_price ( $price_ladder ) );
		$pintuan ['lowest_amount'] = get_lowest_amount ( $price_ladder );
		$pintuan ['ladder_amount'] = $i;
		$pintuan ['sold'] = $pintuan ['virtual_sold'] + $pintuan ['sales_count'];
		if (empty ( $pintuan ['goods_thumb'] )) {
			$pintuan ['goods_thumb'] = get_image_path ( $pintuan ['goods_id'], $pintuan ['goods_thumb'], true );
		}
		$pintuan ['url'] = 'pintuan.php?act=view&act_id=' . $pintuan ['pintuan_id'] . '&u=' . $_SESSION ['user_id'];
		$pt_list [] = $pintuan;
	}
	return $pt_list;
}
function pintuan_detail_info($pintuan_id) {
	$sql = "SELECT ga.*,IFNULL(g.goods_thumb, '') AS goods_thumb, pt.*,g.* " . "FROM  " . $GLOBALS ['ecs']->table ( 'pintuan' ) . " AS pt  " . "LEFT JOIN " . $GLOBALS ['ecs']->table ( 'goods_activity' ) . " AS ga ON pt.act_id  = ga.act_id  " . "LEFT JOIN " . $GLOBALS ['ecs']->table ( 'goods' ) . " AS g ON ga.goods_id = g.goods_id " . "WHERE pt.pt_id=" . $pintuan_id . "  ";
	$pintuan = $GLOBALS ['db']->getRow ( $sql );
	$ext_info = unserialize ( $pintuan ['ext_info'] );
	$pintuan = array_merge ( $pintuan, $ext_info );
	$pintuan ['create_time'] = local_date ( $GLOBALS ['_CFG'] ['time_format'], $pintuan ['create_time'] );
	$pintuan ['price'] = price_format ( $pintuan ['price'], false );
	if (empty ( $pintuan ['goods_thumb'] )) {
		$pintuan ['goods_thumb'] = get_image_path ( $pintuan ['goods_id'], $pintuan ['goods_thumb'], true );
	}
	$pintuan ['url'] = 'pintuan.php?act=view&act_id=' . $pintuan ['act_id'] . '&u=' . $_SESSION ['user_id'];
	return $pintuan;
}
function get_lowest_price($price_ladder) {
	if (is_array ( $price_ladder )) {
		$aa = array ();
		foreach ( $price_ladder as $key => $value ) {
			$aa [] = $value ['price'];
		}
		sort ( $aa );
		return $aa [0];
	}
}
function get_lowest_amount($price_ladder) {
	if (is_array ( $price_ladder )) {
		$aa = array ();
		foreach ( $price_ladder as $key => $value ) {
			$aa [] = $value ['amount'];
		}
		sort ( $aa );
		return $aa [0];
	}
}
function get_new_pintuan($act_id) {
	$new_pintuan = array ();
	$sql = "SELECT a.* " . "FROM " . $GLOBALS ['ecs']->table ( 'pintuan' ) . " AS a " . "WHERE act_id = '$act_id' and status=0 and create_succeed=1 " . "ORDER BY a.create_time desc LIMIT 10";
	$res = $GLOBALS ['db']->query ( $sql );
	while ( $row = $GLOBALS ['db']->fetchRow ( $res ) ) {
		$row ['create_time'] = local_date ( $GLOBALS ['_CFG'] ['time_format'], $row ['create_time'] );
		$row ['price'] = price_format ( $row ['price'], false );
		$new_pintuan [] = $row;
	}
	return $new_pintuan;
}
function get_pintuan() {
	$filter ['act_id'] = empty ( $_REQUEST ['act_id'] ) ? 0 : intval ( $_REQUEST ['act_id'] );
	$filter ['sort_by'] = empty ( $_REQUEST ['sort_by'] ) ? 'create_time' : trim ( $_REQUEST ['sort_by'] );
	$filter ['sort_order'] = empty ( $_REQUEST ['sort_order'] ) ? 'DESC' : trim ( $_REQUEST ['sort_order'] );
	$where = empty ( $filter ['act_id'] ) ? '' : " WHERE act_id='$filter[act_id]' ";
	$sql = "SELECT count(*) FROM " . $GLOBALS ['ecs']->table ( 'pintuan' ) . $where;
	$filter ['record_count'] = $GLOBALS ['db']->getOne ( $sql );
	$filter = page_and_size ( $filter );
	$sql = "SELECT * " . " FROM " . $GLOBALS ['ecs']->table ( 'pintuan' ) . $where . " ORDER by " . $filter ['sort_by'] . " " . $filter ['sort_order'] . " LIMIT " . $filter ['start'] . ", " . $filter ['page_size'];
	$row = $GLOBALS ['db']->getAll ( $sql );
	foreach ( $row as $key => $val ) {
		$row [$key] ['create_time'] = local_date ( 'Y-m-d H:i', $val ['create_time'] );
		$row [$key] ['end_time'] = local_date ( 'Y-m-d H:i', $val ['end_time'] );
	}
	$arr = array (
			'pintuan' => $row,
			'filter' => $filter,
			'page_count' => $filter ['page_count'],
			'record_count' => $filter ['record_count'] 
	);
	return $arr;
}
function get_pintuan_detail() {
	$filter ['pt_id'] = empty ( $_REQUEST ['pt_id'] ) ? 0 : intval ( $_REQUEST ['pt_id'] );
	$filter ['sort_by'] = empty ( $_REQUEST ['sort_by'] ) ? 'follow_time' : trim ( $_REQUEST ['sort_by'] );
	$filter ['sort_order'] = empty ( $_REQUEST ['sort_order'] ) ? 'DESC' : trim ( $_REQUEST ['sort_order'] );
	$where = empty ( $filter ['pt_id'] ) ? '' : " WHERE pt_id='$filter[pt_id]' ";
	$sql = "SELECT count(*) FROM " . $GLOBALS ['ecs']->table ( 'pintuan_orders' ) . $where;
	$filter ['record_count'] = $GLOBALS ['db']->getOne ( $sql );
	$filter = page_and_size ( $filter );
	$sql = "SELECT s.* " . " FROM " . $GLOBALS ['ecs']->table ( 'pintuan_orders' ) . " AS s " . $where . " ORDER by " . $filter ['sort_by'] . " " . $filter ['sort_order'] . " LIMIT " . $filter ['start'] . ", " . $filter ['page_size'];
	$row = $GLOBALS ['db']->getAll ( $sql );
	foreach ( $row as $key => $val ) {
		$row [$key] ['follow_time'] = local_date ( 'Y-m-d H:i', $val ['follow_time'] );
	}
	$arr = array (
			'pintuan' => $row,
			'filter' => $filter,
			'page_count' => $filter ['page_count'],
			'record_count' => $filter ['record_count'] 
	);
	return $arr;
}
function get_pintuan_info($id) {
	global $ecs, $db, $_CFG;
	$sql = "SELECT act_id, act_name AS cut_name, goods_id, product_id, goods_name, start_time, end_time, act_desc, ext_info" . " FROM " . $GLOBALS ['ecs']->table ( 'goods_activity' ) . " WHERE act_id='$id' AND act_type = " . GAT_PINTUAN;
	$cut = $db->GetRow ( $sql );
	$cut ['start_time'] = local_date ( 'Y-m-d H:i', $cut ['start_time'] );
	$cut ['end_time'] = local_date ( 'Y-m-d H:i', $cut ['end_time'] );
	$row = unserialize ( $cut ['ext_info'] );
	unset ( $cut ['ext_info'] );
	if ($row) {
		foreach ( $row as $key => $val ) {
			$cut [$key] = $val;
		}
	}
	return $cut;
}
function get_pintuan_by_ptid($pt_id) {
	$sql = "SELECT pt.* " . " FROM  " . $GLOBALS ['ecs']->table ( 'pintuan' ) . " AS pt  " . " WHERE pt.pt_id=" . $pt_id . "  ";
	return $GLOBALS ['db']->getRow ( $sql );
}
function cut_info($act_id, $config = false) {
	$sql = "SELECT * FROM " . $GLOBALS ['ecs']->table ( 'goods_activity' ) . " WHERE act_id = '$act_id'";
	$cut = $GLOBALS ['db']->getRow ( $sql );
	if ($cut ['act_type'] != GAT_CUT) {
		return array ();
	}
	$cut ['status_no'] = cut_status ( $cut );
	if ($config == true) {
		$cut ['start_time'] = local_date ( 'Y-m-d H:i', $cut ['start_time'] );
		$cut ['end_time'] = local_date ( 'Y-m-d H:i', $cut ['end_time'] );
	} else {
		$cut ['start_time'] = local_date ( $GLOBALS ['_CFG'] ['time_format'], $cut ['start_time'] );
		$cut ['end_time'] = local_date ( $GLOBALS ['_CFG'] ['time_format'], $cut ['end_time'] );
	}
	$ext_info = unserialize ( $cut ['ext_info'] );
	$cut = array_merge ( $cut, $ext_info );
	$cut ['formated_start_price'] = price_format ( $cut ['start_price'] );
	$cut ['formated_end_price'] = price_format ( $cut ['end_price'] );
	$cut ['formated_max_price'] = price_format ( $cut ['max_price'] );
	$cut ['formated_deposit'] = price_format ( $cut ['deposit'] );
	$cut ['goods_name'] = $cut ['act_name'] ? $cut ['act_name'] : $cut ['goods_name'];
	return $cut;
}
function cut_log($act_id) {
	$log = array ();
	$sql = "SELECT a.* " . "FROM " . $GLOBALS ['ecs']->table ( 'users_activity' ) . " AS a " . "WHERE act_id = '$act_id' " . "ORDER BY a.new_price ASC LIMIT 10";
	$res = $GLOBALS ['db']->query ( $sql );
	$rownum = 1;
	while ( $row = $GLOBALS ['db']->fetchRow ( $res ) ) {
		$row ['bid_time'] = local_date ( $GLOBALS ['_CFG'] ['time_format'], $row ['bid_time'] );
		$row ['user_nickname'] = $row ['user_nickname'];
		$row ['shop_price'] = price_format ( $row ['shop_price'], false );
		$row ['new_price'] = price_format ( $row ['new_price'], false );
		$row ['rownum'] = $rownum;
		$rownum = $rownum + 1;
		$log [] = $row;
	}
	return $log;
}
function user_cut_log($user_id, $act_id, $page = 1) {
	$count = $GLOBALS ['db']->getOne ( 'SELECT COUNT(*) FROM ' . $GLOBALS ['ecs']->table ( 'cut_log' ) . " WHERE act_user = '$user_id' AND act_id = '$act_id' " );
	$size = 10;
	$page_count = ($count > 0) ? intval ( ceil ( $count / $size ) ) : 1;
	$log = array ();
	$sql = "SELECT c.* " . "FROM " . $GLOBALS ['ecs']->table ( 'cut_log' ) . " AS c  " . "LEFT JOIN " . $GLOBALS ['ecs']->table ( 'users_activity' ) . " AS u ON (u.user_id = c.act_user and u.act_id=c.act_id) " . "WHERE u.user_id = '$user_id' " . "AND u.act_id = '$act_id' " . "ORDER BY c.log_id DESC";
	$res = $GLOBALS ['db']->selectLimit ( $sql, $size, ($page - 1) * $size );
	while ( $row = $GLOBALS ['db']->fetchRow ( $res ) ) {
		$row ['bid_user_nickname'] = $row ['bid_user_nickname'];
		$row ['formated_bid_price'] = price_format ( $row ['bid_price'], false );
		$row ['formated_bid_price'] = price_format ( $row ['bid_price'], false );
		$row ['formated_after_bid_price'] = price_format ( $row ['after_bid_price'], false );
		$log [] = $row;
	}
	$pager ['page'] = $page;
	$pager ['size'] = $size;
	$pager ['record_count'] = $count;
	$pager ['page_count'] = $page_count;
	$pager ['page_first'] = "javascript:gotoPage(1,$id,$type)";
	$pager ['page_prev'] = $page > 1 ? "cut.php?act=logpage&id=$act_id&actuid=$user_id&page=" . ($page - 1) : false;
	$pager ['page_next'] = $page < $page_count ? "cut.php?act=logpage&id=$act_id&actuid=$user_id&page=" . ($page + 1) : false;
	$pager ['page_last'] = $page < $page_count ? 'javascript:gotoPage(' . $page_count . ",$id,$type)" : 'javascript:;';
	$log = array (
			'log' => $log,
			'pager' => $pager 
	);
	return $log;
}
function cut_status($cut) {
	$now = gmtime ();
	if ($cut ['is_finished'] == 0) {
		if ($now < $cut ['start_time']) {
			return PRE_START;
		} elseif ($now > $cut ['end_time']) {
			return FINISHED;
		} else {
			return UNDER_WAY;
		}
	} elseif ($cut ['is_finished'] == 1) {
		return FINISHED;
	} else {
		return SETTLED;
	}
}
function cut_count() {
	$now = gmtime ();
	$sql = "SELECT COUNT(*) " . "FROM " . $GLOBALS ['ecs']->table ( 'goods_activity' ) . "WHERE act_type = '" . GAT_CUT . "' " . "AND start_time <= '$now' AND end_time >= '$now' AND is_finished < 2";
	return $GLOBALS ['db']->getOne ( $sql );
}
function cut_list($size, $page) {
	$cut_list = array ();
	$cut_list ['finished'] = $cut_list ['finished'] = array ();
	$now = gmtime ();
	$sql = "SELECT a.*,g.*, IFNULL(g.goods_thumb, '') AS goods_thumb " . "FROM " . $GLOBALS ['ecs']->table ( 'goods_activity' ) . " AS a " . "LEFT JOIN " . $GLOBALS ['ecs']->table ( 'goods' ) . " AS g ON a.goods_id = g.goods_id " . "WHERE a.act_type = '" . GAT_CUT . "' " . "AND a.start_time <= '$now' AND a.end_time >= '$now' AND a.is_finished < 2 ORDER BY a.act_id DESC";
	$res = $GLOBALS ['db']->selectLimit ( $sql, $size, ($page - 1) * $size );
	while ( $row = $GLOBALS ['db']->fetchRow ( $res ) ) {
		$ext_info = unserialize ( $row ['ext_info'] );
		$cut = array_merge ( $row, $ext_info );
		$cut ['status_no'] = cut_status ( $cut );
		$cut ['start_time'] = local_date ( $GLOBALS ['_CFG'] ['time_format'], $cut ['start_time'] );
		$cut ['end_time'] = local_date ( $GLOBALS ['_CFG'] ['time_format'], $cut ['end_time'] );
		$cut ['formated_start_price'] = price_format ( $cut ['start_price'] );
		$cut ['formated_end_price'] = price_format ( $cut ['end_price'] );
		$cut ['formated_deposit'] = price_format ( $cut ['deposit'] );
		$cut ['goods_thumb'] = get_image_path ( $row ['goods_id'], $row ['goods_thumb'], true );
		$cut ['url'] = build_uri ( 'cut', array (
				'auid' => $cut ['act_id'] 
		) );
		$cut ['shop_price'] = price_format ( $row ['shop_price'] );
		$cut ['goods_name'] = $row ['act_name'] ? $row ['act_name'] : $row ['goods_name'];
		if ($cut ['status_no'] < 2) {
			$cut_list ['under_way'] [] = $cut;
		} else {
			$cut_list ['finished'] [] = $cut;
		}
	}
	$cut_list = @array_merge ( $cut_list ['under_way'], $cut_list ['finished'] );
	return $cut_list;
}
function cut_user_list($size, $page, $act_user) {
	$cut_list = array ();
	$cut_list ['finished'] = $cut_list ['finished'] = array ();
	$now = gmtime ();
	$sql = "SELECT a.*,g.*, IFNULL(g.goods_thumb, '') AS goods_thumb " . "FROM " . $GLOBALS ['ecs']->table ( 'users_activity' ) . " AS u " . "LEFT JOIN " . $GLOBALS ['ecs']->table ( 'goods_activity' ) . " AS a ON u.act_id  = a.act_id  " . "LEFT JOIN " . $GLOBALS ['ecs']->table ( 'goods' ) . " AS g ON a.goods_id = g.goods_id " . "WHERE a.act_type = '" . GAT_CUT . "' " . "AND u.user_id='$act_user' and a.start_time <= '$now' AND a.end_time >= '$now' AND a.is_finished < 2 ORDER BY a.act_id DESC";
	$res = $GLOBALS ['db']->selectLimit ( $sql, $size, ($page - 1) * $size );
	while ( $row = $GLOBALS ['db']->fetchRow ( $res ) ) {
		$ext_info = unserialize ( $row ['ext_info'] );
		$cut = array_merge ( $row, $ext_info );
		$cut ['status_no'] = cut_status ( $cut );
		$cut ['start_time'] = local_date ( $GLOBALS ['_CFG'] ['time_format'], $cut ['start_time'] );
		$cut ['end_time'] = local_date ( $GLOBALS ['_CFG'] ['time_format'], $cut ['end_time'] );
		$cut ['formated_start_price'] = price_format ( $cut ['start_price'] );
		$cut ['formated_end_price'] = price_format ( $cut ['end_price'] );
		$cut ['formated_deposit'] = price_format ( $cut ['deposit'] );
		$cut ['goods_thumb'] = get_image_path ( $row ['goods_id'], $row ['goods_thumb'], true );
		$cut ['url'] = build_uri ( 'cut', array (
				'auid' => $cut ['act_id'] 
		) );
		$cut ['shop_price'] = price_format ( $row ['shop_price'] );
		$cut ['goods_name'] = $row ['act_name'] ? $row ['act_name'] : $row ['goods_name'];
		if ($cut ['status_no'] < 2) {
			$cut_list ['under_way'] [] = $cut;
		} else {
			$cut_list ['finished'] [] = $cut;
		}
	}
	$cut_list = @array_merge ( $cut_list ['under_way'], $cut_list ['finished'] );
	return $cut_list;
}
function get_cutlist() {
	$result = get_filter ();
	if ($result === false) {
		$filter ['keywords'] = empty ( $_REQUEST ['keywords'] ) ? '' : trim ( $_REQUEST ['keywords'] );
		if (isset ( $_REQUEST ['is_ajax'] ) && $_REQUEST ['is_ajax'] == 1) {
			$filter ['keywords'] = json_str_iconv ( $filter ['keywords'] );
		}
		$filter ['sort_by'] = empty ( $_REQUEST ['sort_by'] ) ? 'act_id' : trim ( $_REQUEST ['sort_by'] );
		$filter ['sort_order'] = empty ( $_REQUEST ['sort_order'] ) ? 'DESC' : trim ( $_REQUEST ['sort_order'] );
		$where = (! empty ( $filter ['keywords'] )) ? " AND act_name like '%" . mysql_like_quote ( $filter ['keywords'] ) . "%'" : '';
		$sql = "SELECT COUNT(*) FROM " . $GLOBALS ['ecs']->table ( 'goods_activity' ) . " WHERE act_type =" . GAT_CUT . $where;
		$filter ['record_count'] = $GLOBALS ['db']->getOne ( $sql );
		$filter = page_and_size ( $filter );
		$sql = "SELECT act_id, act_name AS cut_name, goods_name, start_time, end_time, is_finished, ext_info, product_id " . " FROM " . $GLOBALS ['ecs']->table ( 'goods_activity' ) . " WHERE act_type = " . GAT_CUT . $where . " ORDER by $filter[sort_by] $filter[sort_order] LIMIT " . $filter ['start'] . ", " . $filter ['page_size'];
		$filter ['keywords'] = stripslashes ( $filter ['keywords'] );
		set_filter ( $filter, $sql );
	} else {
		$sql = $result ['sql'];
		$filter = $result ['filter'];
	}
	$row = $GLOBALS ['db']->getAll ( $sql );
	foreach ( $row as $key => $val ) {
		$row [$key] ['start_time'] = local_date ( $GLOBALS ['_CFG'] ['time_format'], $val ['start_time'] );
		$row [$key] ['end_time'] = local_date ( $GLOBALS ['_CFG'] ['time_format'], $val ['end_time'] );
		$info = unserialize ( $row [$key] ['ext_info'] );
		unset ( $row [$key] ['ext_info'] );
		if ($info) {
			foreach ( $info as $info_key => $info_val ) {
				$row [$key] [$info_key] = $info_val;
			}
		}
	}
	$arr = array (
			'cuts' => $row,
			'filter' => $filter,
			'page_count' => $filter ['page_count'],
			'record_count' => $filter ['record_count'] 
	);
	return $arr;
}
function get_cut_info($id) {
	global $ecs, $db, $_CFG;
	$sql = "SELECT act_id, act_name AS cut_name, goods_id, product_id, goods_name, start_time, end_time, act_desc, ext_info" . " FROM " . $GLOBALS ['ecs']->table ( 'goods_activity' ) . " WHERE act_id='$id' AND act_type = " . GAT_CUT;
	$cut = $db->GetRow ( $sql );
	$cut ['start_time'] = local_date ( 'Y-m-d H:i', $cut ['start_time'] );
	$cut ['end_time'] = local_date ( 'Y-m-d H:i', $cut ['end_time'] );
	$row = unserialize ( $cut ['ext_info'] );
	unset ( $cut ['ext_info'] );
	if ($row) {
		foreach ( $row as $key => $val ) {
			$cut [$key] = $val;
		}
	}
	return $cut;
}
function get_user_cut_detail() {
	$filter ['act_id'] = empty ( $_REQUEST ['act_id'] ) ? 0 : intval ( $_REQUEST ['act_id'] );
	$filter ['uid'] = empty ( $_REQUEST ['uid'] ) ? 0 : intval ( $_REQUEST ['uid'] );
	$filter ['sort_by'] = empty ( $_REQUEST ['sort_by'] ) ? 'bid_time' : trim ( $_REQUEST ['sort_by'] );
	$filter ['sort_order'] = empty ( $_REQUEST ['sort_order'] ) ? 'DESC' : trim ( $_REQUEST ['sort_order'] );
	$where = (empty ( $filter ['act_id'] ) || empty ( $filter ['uid'] )) ? '' : " WHERE act_id='$filter[act_id]' and act_user='$filter[uid]'";
	$sql = "SELECT count(*) FROM " . $GLOBALS ['ecs']->table ( 'cut_log' ) . $where;
	$filter ['record_count'] = $GLOBALS ['db']->getOne ( $sql );
	$filter = page_and_size ( $filter );
	$sql = "SELECT s.* " . " FROM " . $GLOBALS ['ecs']->table ( 'cut_log' ) . " AS s " . $where . " ORDER by " . $filter ['sort_by'] . " " . $filter ['sort_order'] . " LIMIT " . $filter ['start'] . ", " . $filter ['page_size'];
	$row = $GLOBALS ['db']->getAll ( $sql );
	foreach ( $row as $key => $val ) {
		$row [$key] ['bid_time'] = local_date ( 'Y-m-d H:i', $val ['bid_time'] );
	}
	$arr = array (
			'bid' => $row,
			'filter' => $filter,
			'page_count' => $filter ['page_count'],
			'record_count' => $filter ['record_count'] 
	);
	return $arr;
}
function get_cut_detail() {
	$filter ['act_id'] = empty ( $_REQUEST ['act_id'] ) ? 0 : intval ( $_REQUEST ['act_id'] );
	$filter ['sort_by'] = empty ( $_REQUEST ['sort_by'] ) ? 'activity_time' : trim ( $_REQUEST ['sort_by'] );
	$filter ['sort_order'] = empty ( $_REQUEST ['sort_order'] ) ? 'DESC' : trim ( $_REQUEST ['sort_order'] );
	$where = empty ( $filter ['act_id'] ) ? '' : " WHERE act_id='$filter[act_id]' ";
	$sql = "SELECT count(*) FROM " . $GLOBALS ['ecs']->table ( 'users_activity' ) . $where;
	$filter ['record_count'] = $GLOBALS ['db']->getOne ( $sql );
	$filter = page_and_size ( $filter );
	$sql = "SELECT * " . " FROM " . $GLOBALS ['ecs']->table ( 'users_activity' ) . $where . " ORDER by " . $filter ['sort_by'] . " " . $filter ['sort_order'] . " LIMIT " . $filter ['start'] . ", " . $filter ['page_size'];
	$row = $GLOBALS ['db']->getAll ( $sql );
	foreach ( $row as $key => $val ) {
		$row [$key] ['activity_time'] = local_date ( 'Y-m-d H:i', $val ['activity_time'] );
	}
	$arr = array (
			'bid' => $row,
			'filter' => $filter,
			'page_count' => $filter ['page_count'],
			'record_count' => $filter ['record_count'] 
	);
	return $arr;
}
?>