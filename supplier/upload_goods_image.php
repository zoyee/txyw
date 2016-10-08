<?php
define('IN_ECTOUCH', true);
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . 'supplier/includes/cls_image.php');
$cls_image = new cls_image($_CFG['bgcolor']);
$logger = LoggerManager::getLogger('upload_goods_image.php');
$month = date('Ym');

if($_REQUEST['act'] == 'upload_goods_image'){
	//上传首图
	$fname = $_REQUEST['fname'];
	
	/* 如果是安全模式，检查目录是否存在 */
    if (ini_get('safe_mode') == 1 && (!file_exists('../' . IMAGE_DIR . '/'.$month) || !is_dir('../' . IMAGE_DIR . '/'.$month))) {
        if (@!mkdir('../' . IMAGE_DIR . '/'.$month, 0777)) {
            $warning = sprintf($_LANG['safe_mode_warning'], '../' . IMAGE_DIR . '/'.$month);
            make_json_error($warning);
        }
    }

    /* 如果目录存在但不可写，提示用户 */
    elseif (file_exists('../' . IMAGE_DIR . '/'.$month) && file_mode_info('../' . IMAGE_DIR . '/'.$month) < 2) {
        $warning = sprintf($_LANG['not_writable_warning'], '../' . IMAGE_DIR . '/'.$month);
        make_json_error($warning);
    }
    
	$image = wxch_upload_file($_FILES[$fname], $month.'/source_img');
	$logger->debug($image);
	if($image){
		make_json_result($image);
	}else{
		make_json_error('上传失败');
	}
}elseif($_REQUEST['act'] == 'upload_gallery_image'){
	//上传相册图片
	$fname = $_REQUEST['fname'];
	$goods_id = $_REQUEST['goods_id'];
	if(empty($goods_id)){
		make_json_error('缺少商品ID');
	}
	
	/* 如果是安全模式，检查目录是否存在 */
    if (ini_get('safe_mode') == 1 && (!file_exists('../' . IMAGE_DIR . '/'.$month) || !is_dir('../' . IMAGE_DIR . '/'.$month))) {
        if (@!mkdir('../' . IMAGE_DIR . '/'.$month, 0777)) {
            $warning = sprintf($_LANG['safe_mode_warning'], '../' . IMAGE_DIR . '/'.$month);
            make_json_error($warning);
        }
    }

    /* 如果目录存在但不可写，提示用户 */
    elseif (file_exists('../' . IMAGE_DIR . '/'.$month) && file_mode_info('../' . IMAGE_DIR . '/'.$month) < 2) {
        $warning = sprintf($_LANG['not_writable_warning'], '../' . IMAGE_DIR . '/'.$month);
        make_json_error($warning);
    }
    
	$image = wxch_upload_file($_FILES[$fname], $month.'/source_img');
	$logger->debug($image);
	//生成压缩图
	// 如果设置大小不为0，缩放图片
    if ($_CFG['image_width'] != 0 || $_CFG['image_height'] != 0) {
        $goods_img = $cls_image->make_thumb('../'. $image , $GLOBALS['_CFG']['image_width'],  $GLOBALS['_CFG']['image_height'], ROOT_PATH. IMAGE_DIR . '/'. $month. '/goods_img/');
        if ($goods_img === false) {
            make_json_error($goods_img->error_msg());
        }
    }
    if ($_CFG['thumb_width'] != 0 || $_CFG['thumb_height'] != 0) {
	    $gallery_thumb = $cls_image->make_thumb('../' . $image, $GLOBALS['_CFG']['thumb_width'],  $GLOBALS['_CFG']['thumb_height'], ROOT_PATH. IMAGE_DIR . '/'. $month. '/thumb_img/');
	    if ($gallery_thumb === false) {
	        make_json_error($cls_image->error_msg());
	    }
	}
    
	
	//添加相册记录
	$db->autoExecute($ecs->table('goods_gallery'), array(
		'goods_id' => $goods_id,
		'img_url' => $goods_img,
		'thumb_url' => $gallery_thumb,
		'img_original' => $image,
		'img_desc' => ''
	), 'INSERT');
	$img_id = $db->insert_id();
	
	//将商品设置为未审核
	$db->autoExecute($ecs->table('goods'), array(
		'is_pass' => GOODS_VERIFY_WAITING
	), 'UPDATE', "goods_id='$goods_id'");
	
	if($goods_img){
		//make_json_result($goods_img);
		exit(json_encode(array(
			error => 0,
			message => '',
			content => $goods_img,
			img_id => $img_id
		)));
	}else if($image){
		make_json_result($image);
	}else{
		make_json_error('上传失败');
	}
}elseif($_REQUEST['act'] == 'upload_desc_image'){
	//上传商品详情的图片
	$fname = $_REQUEST['fname'];
	$goods_id = $_REQUEST['goods_id'];
	if(empty($goods_id)){
		make_json_error('缺少商品ID');
	}
	$image = wxch_upload_file($_FILES[$fname], $month.'/source_img');
	$logger->debug($image);
	if($image){
		//将商品设置为未审核
		/*$db->autoExecute($ecs->table('goods'), array(
			'is_pass' => GOODS_VERIFY_WAITING
		), 'UPDATE', "goods_id='$goods_id'");*/
		make_json_result($image);
	}else{
		make_json_error('上传失败');
	}
}


function wxch_upload_file($upload, $dir){
	$image = new cls_image();
	$res = $image->upload_image($upload, $dir);
	if($res)
	{
		return $res;
	}
	else
	{
		return false;
	}
}