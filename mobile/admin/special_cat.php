<?php
define('IN_ECTOUCH', true);

require(dirname(__FILE__) . '/includes/init.php');
include_once(ROOT_PATH . 'include/cls_image.php');
$logger = LoggerManager::getLogger('user.php');
$image = new cls_image($_CFG['bgcolor']);

if ($_REQUEST['act'] == 'list'){
	$special_cat_conf = $db->getOne("select value from ecs_touch_shop_config where `code`='special_cat_conf'");
	if($special_cat_conf){
		$special_cat_conf = unserialize($special_cat_conf);
	}else{
		$special_cat_conf = array(
				'size' => 70,
				'x' => 12,
				'y' => 0,
				'mult_switch' => 1,
				'hot' => array(
						'show' => 1,
						'order' => 1,
						'ico' => ''
				),
				'best' => array(
						'show' => 1,
						'order' => 2,
						'ico' => ''
				),
				'new' => array(
						'show' => 1,
						'order' => 3,
						'ico' => ''
				),
				'promote' => array(
						'show' => 1,
						'order' => 4,
						'ico' => ''
				)
		);
	}
	$smarty->assign('data',   $special_cat_conf);
	$smarty->display('special_cat_conf.htm');
} elseif ($_REQUEST['act'] == 'save'){
	$special_cat_conf = array();
	$special_cat_conf['size'] = $_REQUEST['size'];
	$special_cat_conf['x'] = $_REQUEST['x'];
	$special_cat_conf['y'] = $_REQUEST['y'];
	$special_cat_conf['mult_switch'] = isset($_REQUEST['mult_switch']) ? $_REQUEST['mult_switch'] : 0;
	$special_key = array('hot', 'best', 'new', 'promote');
	foreach ($special_key as $key){
		$temp = array();
		$temp['show'] = isset($_REQUEST['show_'.$key]) ? $_REQUEST['show_'.$key] : 0;
		$temp['order'] = empty($_REQUEST['order_'.$key]) ? 0 : $_REQUEST['order_'.$key];
		$temp['ico'] = empty($_REQUEST[$key.'_ico']) ? '' : $_REQUEST[$key.'_ico'];
		$special_cat_conf[$key] = $temp;
	}
	$logger->debug(json_encode($special_cat_conf));
	$special_cat_conf = serialize($special_cat_conf);
	$db->query("update ecs_touch_shop_config set value='$special_cat_conf' where `code`='special_cat_conf'");
	$link[] = array('href' =>'special_cat.php?act=list', 'text' => '特殊分类标签配置');
	sys_msg('设置成功',0,$link);
}

