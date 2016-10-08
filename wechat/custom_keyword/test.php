<?php
define('IN_ECS', true);
error_reporting(0);
require(dirname(__FILE__) . '/../../includes/init.php');

require_once 'keyword_process.php';
require_once ('../../mobile/api/weixin_api.php');
$p = new keyword_processor($db);
$weixin_api = new weixin_api();
set_time_limit(0);
//$keyword = $_REQUEST['keyword'];
$sql = "select wxid, affiliate from wxch_user where uname='' and wxid!='' and subscribe=1 order by uid desc";
$arr = $db->getAll($sql);
$wxid = null;
foreach($arr as $row){
	$wxid = $row['wxid'];
	$weixin_api->refreshWxInfo($wxid);
	$p->process("auto_reg", $wxid, "gh_ebb5f1b98707", "");
//	if($row['affiliate'] > 0){
		$db->query("update ecs_users set parent_id='".$row['affiliate']."' where wxid='$wxid'");
//	}
//	sleep(1);
}
