<?php
/**
 * 添加后台管理功能模块
 */
define('IN_ECTOUCH', true);
require(dirname(__FILE__) . '/includes/init.php');
$logger = LoggerManager::getLogger('add_module.php');

$admin_path = ROOT_PATH . "admin";
$action_table = $ecs->table('touch_action');
$action_lang_file = ROOT_PATH . "lang/zh_cn/admin/priv_action.php";
$module_lang_file = ROOT_PATH . "lang/zh_cn/admin/common.php";
$inc_menu_file = $admin_path . "/includes/inc_menu.php";
$inc_priv_file = $admin_path . "/includes/inc_priv.php";
$template_dir = "hplus_templates";

if($_REQUEST['act'] == 'do_add'){
	$parent_module = $_REQUEST['parent_module'];
	$parent_menu = $_REQUEST['parent_menu'];
	$module_en_name = $_REQUEST['module_en_name'];
	$module_zh_cn_name = $_REQUEST['module_zh_cn_name'];
	$php_file = $_REQUEST['php_file'];
	$menu_url = $_REQUEST['menu_url'];
	$htm_file = $_REQUEST['htm_file'];
	
	//验证唯一性
	$sql = "select action_id from $action_table where action_code='$module_en_name'";
	$check = $db->getOne($sql);
	if($check){
		sys_msg("模块英文名称重复!", 1);
	}
	
	include_once($inc_menu_file);
	if(isset($modules[$parent_menu][$module_en_name])){
		sys_msg("模块英文名称重复!", 1);
	}
	
	$file = file($inc_priv_file);
	$file_arr = array();
	$end_flag = false;
	foreach($file as &$line) {
		if(trim($line) == '?>'){
			$file_arr[] = '//' . $module_zh_cn_name . "\r\n";
			$file_arr[] = '$purview["' . $module_en_name . '"] = "' . $module_en_name . '";' . "\r\n";
			$end_flag = true;
		}
		$file_arr[] = $line;
	}
	if(!$end_flag){
		$file_arr[] = '//' . $module_zh_cn_name . "\r\n";
		$file_arr[] = '$purview["' . $module_en_name . '"] = "' . $module_en_name . '";' . "\r\n";
	}
	file_put_contents($inc_priv_file, $file_arr);
	
	$file = file($inc_menu_file);
	$file_arr = array();
	$end_flag = false;
	foreach($file as &$line) {
		if(trim($line) == '?>'){
			$file_arr[] = '//' . $module_zh_cn_name . "\r\n";
			$file_arr[] = '$modules' . "['$parent_menu']['$module_en_name'] = '$menu_url';" . "\r\n";
			$end_flag = true;
		}
		$file_arr[] = $line;
	}
	if(!$end_flag){
		$file_arr[] = '//' . $module_zh_cn_name . "\r\n";
		$file_arr[] = '$modules' . "['$parent_menu']['$module_en_name'] = '$menu_url';" . "\r\n";
	}
	file_put_contents($inc_menu_file, $file_arr);
	
	$file = file($action_lang_file);
	$file_arr = array();
	$end_flag = false;
	foreach($file as &$line) {
		if(trim($line) == '?>'){
			$file_arr[] = '$_LANG' . "['$module_en_name']  = '$module_zh_cn_name';" . "\r\n";
			$end_flag = true;
		}
		$file_arr[] = $line;
	}
	if(!$end_flag){
		$file_arr[] = '$_LANG' . "['$module_en_name']  = '$module_zh_cn_name';" . "\r\n";
	}
	file_put_contents($action_lang_file, $file_arr);
	
	$file = file($module_lang_file);
	$file_arr = array();
	$end_flag = false;
	foreach($file as &$line) {
		if(trim($line) == '?>'){
			$file_arr[] = '$_LANG' . "['$module_en_name']  = '$module_zh_cn_name';" . "\r\n";
			$end_flag = true;
		}
		$file_arr[] = $line;
	}
	if(!$end_flag){
		$file_arr[] = '$_LANG' . "['$module_en_name']  = '$module_zh_cn_name';" . "\r\n";
	}
	file_put_contents($module_lang_file, $file_arr);
	
	//生成php文件
	$p_file = fopen($admin_path . "/" . $php_file, "w") or die("创建文件失败!");
	fwrite($p_file, "<?php
/**
 * $module_zh_cn_name
 */
define('IN_ECTOUCH', true);
require(dirname(__FILE__) . '/includes/init.php');
" . '$' . "logger = LoggerManager::getLogger('$php_file');


" . '$' . "smarty->display('$htm_file');
?>");
	fclose($p_file);
	
	//生成htm文件
	$tmpl_file = fopen($admin_path . "/" . $template_dir . "/" . $htm_file, "w") or die("创建文件失败!");
	$txt = "<!DOCTYPE html>
<html>

{include file='head.htm'}
<script type=\"text/javascript\">
" . '$' . "(function(){
});

</script>
<body class=\"gray-bg\">
<h3>模块创建成功</h3>
</body>
</html>";
	fwrite($tmpl_file, $txt);
	fclose($tmpl_file);
	
	//添加权限控制
	$sql = "insert into $action_table (`parent_id`, `action_code`) values ($parent_module, '$module_en_name');";
	$db->query($sql);
	
	sys_msg("操作成功，请刷新菜单!", 0);
}else{
	$sql = "select * from $action_table where parent_id=0";
	$parent_modules = $db->getAll($sql);
	include_once($action_lang_file);
	foreach($parent_modules as $i => $row){
		$parent_modules[$i]['action_name'] = $_LANG[$row['action_code']];
	}
	
	include_once($inc_menu_file);
	include_once($module_lang_file);
	$menu_keys = array_keys($modules);
	foreach($menu_keys as $k => $key){
		$parent_menu[] = array('key' => $key, 'key_name' => $_LANG[$key]);
	}
	
	
	$smarty->assign('parent_modules', $parent_modules);
	$smarty->assign('parent_menu', $parent_menu);
	$smarty->display('add_module.htm');
}

