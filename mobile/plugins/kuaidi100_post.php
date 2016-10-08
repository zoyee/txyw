<?php
$getcom = trim($_GET["com"]);
$getNu = trim($_GET["nu"]);
$callbackurl = trim($_GET["callbackurl"]);

define('IN_ECS', true);
define('ECS_ADMIN', true);
include_once("kuaidi100_config.php");
// header('Cache-control: private');
// header('Content-type: text/html; charset=utf-8');

if(isset($postcom)&&isset($getNu)){

// 	$url = 'http://www.kuaidi100.com/applyurl?key='.$kuaidi100key.'&com='.$postcom.'&nu='.$getNu;
	// echo $url;
	//请勿删除变量$powered 的信息，否者本站将不再为你提供快递接口服务。
	$powered = '查询服务由：<a href="http://www.kuaidi100.com" target="_blank" style="color:blue">快递100</a> 网站提供';
	$url = "http://m.kuaidi100.com/index_all.html?type=$postcom&postid=$getNu&callbackurl=$callbackurl";

	//优先使用curl模式发送数据
// 	if (function_exists('curl_init') == 1){
// 	  $curl = curl_init();
// 	  curl_setopt ($curl, CURLOPT_URL, $url);
// 	  curl_setopt ($curl, CURLOPT_HEADER,0);
// 	  curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1);
// 	  curl_setopt ($curl, CURLOPT_USERAGENT,$_SERVER['HTTP_USER_AGENT']);
// 	  curl_setopt ($curl, CURLOPT_TIMEOUT,5);
// 	  $get_content = curl_exec($curl);
// 	  curl_close ($curl);
// 	}else{
// 	  include("snoopy.php");
// 	  $snoopy = new snoopy();
// 	  $snoopy->fetch($url);
// 	  $get_content = $snoopy->results;
// 	}
	//$get_content=iconv('UTF-8', 'GB2312//IGNORE', $get_content);
	//if(strpos($get_content,'地点和跟踪进度')== false){
	//  echo '查询失败，请重试';
	//}
    echo '<iframe id="kd_fm" src="'.$url.'" width="100%" height="340" frameborder="no" border="0" marginwidth="0" marginheight="0" scrolling="auto" allowtransparency="yes"><br/>';

}else{
	echo '查询失败，请重试';
}
exit();
?>
