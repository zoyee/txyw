<?php
define('IN_ECTOUCH', true);
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . 'include/cls_image.php');
$logger = LoggerManager::getLogger('upload_image.php');

if($_REQUEST['act'] == 'ajax_upload'){
	$fname = $_REQUEST['fname'];
	$image = wxch_upload_file($_FILES[$fname]);
	$logger->debug($image);
	if($image){
		make_json_result($image);
	}else{
		make_json_error('上传失败');
	}
}


function wxch_upload_file($upload){
	$image = new cls_image();
	$res = $image->upload_image($upload);
	if($res)
	{
		return $res;
	}
	else
	{
		return false;
	}
}