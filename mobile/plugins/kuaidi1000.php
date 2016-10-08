<?php
$getcom = trim($_GET["com"]);
$getNu = trim($_GET["nu"]);

define('IN_ECS', true);
define('ECS_ADMIN', true);
include_once("kuaidi100_config.php");
header('Cache-control: private');
header('Content-type: text/html; charset=utf-8');

/* 创建 Smarty 对象。*/
require(dirname(__FILE__) . '/../../includes/init.php');
$logger = LoggerManager::getLogger('kuaidi1000.php');
set_time_limit ( 0 );

if(isset($postcom)&&isset($getNu)){
// 	$url = 'http://www.kuaidi100.com/applyurl?key='.$kuaidi100key.'&com='.$postcom.'&nu='.$getNu;
	//优先使用curl模式发送数据
	/* if (function_exists('curl_init') == 1){
	  $curl = curl_init();
	  curl_setopt ($curl, CURLOPT_URL, $url);
	  curl_setopt ($curl, CURLOPT_HEADER,0);
	  curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1);
	  curl_setopt ($curl, CURLOPT_USERAGENT,$_SERVER['HTTP_USER_AGENT']);
	  curl_setopt ($curl, CURLOPT_TIMEOUT,5);
	  $get_content = curl_exec($curl);
	  curl_close ($curl);
	}else{
	  include("snoopy.php");
	  $snoopy = new snoopy();
	  $snoopy->fetch($url);
	  $get_content = $snoopy->results;
	}
	$logger->debug('快递查询$get_content=' . $get_content);
	$option = array(
			'http' => array(
					'header' => "Referer:" . $get_content)
	); */


// 	$QUERY_URL = 'http://www.kuaidi100.com/query?id=1&type='.$postcom.'&postid='.$getNu;
	$QUERY_URL = "http://api.kuaidi.com/openapi.html?id=$kuaidi_key&com=$postcom&nu=$getNu&show=0&muti=0&order=desc&temp=" . time();
	$logger->debug('快递查询：' . $QUERY_URL);
	for ($x=0; $x<=3; $x++) {
// 		$temp = file_get_contents($get_content);
// 		$logger->debug('$temp：' . $temp);
		$result = file_get_contents($QUERY_URL);
		$json = json_decode($result, true);
		if($json['success']) break;
	}
	echo $result;
	//header("Location:".'result.dwt');
// 	$smarty->display('result.dwt');
} else {
	echo '查询失败，请重试';
	exit();
}

