<?php
define('IN_ECTOUCH', true);
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
$logger = LoggerManager::getLogger('haibao.php');

$act = isset($_REQUEST['act']) ? $_REQUEST['act'] : 'list';
if($act == 'list'){
	//海报模板列表查询
	$all_row = $db->getAll("select * from wxch_haibao order by startdate desc");
	$now_date = date('Y-m-d', time());
	$current_id = $db->getOne("select id from wxch_haibao where keyword='user_haibao' and startdate <='$now_date' and '$now_date'<=enddate order by startdate desc limit 1");
	//$logger->debug($now_date);
	$prod_current_id = $db->getOne("select id from wxch_haibao where keyword='product_haibao' and startdate <='$now_date' and '$now_date'<=enddate order by startdate desc limit 1");
	$smarty->assign('current_id', $current_id);
	$smarty->assign('prod_current_id', $prod_current_id);
	$smarty->assign('listData', $all_row);
	$smarty->display('haibao_list.htm');

}
elseif($_REQUEST['act'] == 'edit' || $_REQUEST['act'] == 'add'){
	$id = $_REQUEST['id'];
	if(!empty($id)){
		$row = $db->getRow("select * from wxch_haibao where id='$id'");
		$smarty->assign('data', $row);
	}else{
		$smarty->assign('data', array(
			'image' => $_REQUEST['image'],
			'startdate' => local_date('Y-m-d', time()),
			'enddate' => local_date('Y-m-d', time()),
			'subject' => '',
			'head_size' => '90',
			'head_x' => 133,
			'head_y' => 267,
			'qr_x' => 133,
			'qr_y' => 267,
			'qr_size' => 140,
			'product_size' => 480,
			'product_x' => 23,
			'product_y' => 86
		));
	}

	$smarty->assign('action', $_REQUEST['act']);
	$smarty->display('haibao_info.htm');
}
elseif($_REQUEST['act'] == 'do_save'){
	$id = $_REQUEST['id'];
	$param = array(
			'keyword' => $_REQUEST['keyword'],
			'image' => $_REQUEST['image'],
			'startdate' => $_REQUEST['startdate'],
			'enddate' => $_REQUEST['enddate'],
			'subject' => $_REQUEST['subject'],
			'head_size' => $_REQUEST['head_size'],
			'head_x' => $_REQUEST['head_x'],
			'head_y' => $_REQUEST['head_y'],
			'qr_x' => $_REQUEST['qr_x'],
			'qr_y' => $_REQUEST['qr_y'],
			'qr_size' => $_REQUEST['qr_size'],
			'product_size' => isset($_REQUEST['product_size']) ? $_REQUEST['product_size'] : '',
			'product_x' => isset($_REQUEST['product_x']) ? $_REQUEST['product_x'] : '',
			'product_y' => isset($_REQUEST['product_y']) ? $_REQUEST['product_y'] : '',
	);

	if(empty($id)){
		$property = "";
		$values = "";
		foreach ($param as $key => $val){
			$property .= "`" . $key . "`,";
			$values .= "'" . $val . "',";
		}
		$property = substr($property, 0, strlen($property) -1);
		$values = substr($values, 0, strlen($values) -1);
		$sql = "insert into `wxch_haibao` ($property) values ($values)";
	}else{
		$property = "";
		foreach ($param as $key => $val){
			$property .= "`" . $key . "` = '" . $val . "',";
		}
		$property = substr($property, 0, strlen($property) -1);
		$sql = "update `wxch_haibao` set $property where id = '$id'";
	}
	$logger->debug($sql);
	$db->query($sql);

	$link[] = array('href' =>'haibao.php?act=list', 'text' => '海报模板设置');
	sys_msg('设置成功',0,$link);
}