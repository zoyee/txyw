<?php
define('IN_ECTOUCH', true);

require (dirname(__FILE__) . '/include/init.php');
// echo phpinfo();

// echo scene_encode('12345');
// echo '<br>';
// echo scene_encode(array('12345','67890'));
// echo '<br>';
// echo scene_encode(array('12345','67890', '1234'));
// echo '<br>';
// echo scene_decode("12345");
// echo '<br>';
// echo json_encode(scene_decode("10000123450000067890"));
// echo '<br>';
// echo json_encode(scene_decode("100001234500000678900000001234"));
// echo local_date( 'Y-m-d H:i:s', time());
// echo date("Y-m-d H:i:s", time());

require_once (ROOT_PATH . 'api/weixin_api.php');
$weixin_api = new weixin_api();
require_once (ROOT_PATH . 'api/haibao_api.php');
$haibao_api = new haibao_api();
require_once '../wechat/custom_keyword/keyword_process.php';
$p = new keyword_processor($db);
// $ret = $weixin_api->send_custom_message('oQ5ZLs0Mm0K9M1_lMQXlXqIGoeTw', "错误提示：推荐关系不合法。可能情况：1.不能成为自己的窝友；2.已经有窝");
// $weixin_api->send_custom_image('oQ5ZLs0Mm0K9M1_lMQXlXqIGoeTw', 'C:/shop/byhillshop/images/wap_logo.png');
// echo json_encode($ret);

// $post_msg = array(
// 		"touser" => "OPENID",
// 		"msgtype" => "news",
// 		"news" => array(
// 				"articles" => array(
// 						array(
// 								"title" => "Happy Day",
// 								"description" => "Is Really A Happy Day",
// 								"url" => "URL",
// 								"picurl" => "PIC_URL"
// 						)
// 				)
// 		)
// );
// echo json_encode($post_msg);

// $ret = $weixin_api->send_custom_single_news('oQ5ZLs0Mm0K9M1_lMQXlXqIGoeTw', '点哥威武', '点哥，你有妹妹么？', 'http://shop.byhill.com/mobile/data/afficheimg/1452920619221412320.jpg', 'http://shop.byhill.com/mobile/goods.php?id=184&u=11390');
// echo json_encode($ret);
// echo '<br>';
// echo urlencode('测试图文');
// echo '<br>';
// echo date("Y-m-d H:i:s", time());
// $ret = $weixin_api->send_template_msg('test', array(
// 	'touser' => 'oQ5ZLs0Mm0K9M1_lMQXlXqIGoeTw',
// 	'url' => 'http://shop.byhill.com/mobile/',
// 	'first' => '您的窝友xxx已经下单了\r\n您将获得奖励10元。',
// 	'dp' => '199',
// 	'bv' => '100',
// 	'pv' => '99',
// 	'remark' => '如有疑问请联系客服青小素(qingsu)'
// ));
// echo json_encode($ret);

// $ret = $weixin_api->send_template_msg('order_commission', array(
// 		'touser' => 'oQ5ZLs0Mm0K9M1_lMQXlXqIGoeTw',
// 		'url' => 'http://shop.byhill.com/mobile/',
// 		'nick_name' => 'zoyee',
// 		'order_id' => '2016021501244',
// 		'tatol_amount' => '100',
// 		'commission' => '10',
// 		'remark' => '如有疑问请联系客服青小素(qingsu)'
// ));
// echo json_encode($ret);


// $ret = $weixin_api->query_custom_svc_status();
// echo json_encode($ret);
// $weixin_api->transfer_custom_service('oQ5ZLswfE6ST1zc_UDCAo652zlmw', '你好');
// $ret = array(
// 					"errcode" => 1,
// 					"errmsg" => "系统设置不发送该消息",
// 			);
// echo json_encode($ret);
// $ret = $weixin_api->get_template_content('smgz', array());
// echo $ret;
// $ret = $weixin_api->get_template_content('order_commission', array(
// 		'touser' => 'oQ5ZLs0Mm0K9M1_lMQXlXqIGoeTw',
// 		'url' => 'http://shop.byhill.com/mobile/',
// 		'nick_name' => 'zoyee',
// 		'order_id' => '2016021501244',
// 		'tatol_amount' => '100',
// 		'commission' => '10',
// 		'remark' => '如有疑问请联系客服青小素(qingsu)'
// ));
// echo $ret;

echo date('Y-m-d H:i:s ' . mt_rand(1,1000));
echo "<br/>";
echo date('YmdHis_' . mt_rand(1,1000));
echo "<br/>";
echo local_date('Y-m-d H:i:s ' . mt_rand(1,1000));
//echo phpinfo();
echo "<br/>";
echo strtotime('2016-05-04 12:12:12');
echo "<br/>";
echo time();
echo "<br/>";

$arr=array("aaa","bbb", "ccc");
$arr[0]=$arr[0] ^ $arr[2];
$arr[2]=$arr[0] ^ $arr[2];
$arr[0]=$arr[0] ^ $arr[2];
// echo json_encode($arr);

//echo json_encode(get_goods_special_icons(391));
//$json_arr = array(
//						'action_name' => "QR_SCENE",
//						'expire_seconds' => 2592000,
//						'action_info' => array(
//								'scene' => array(
//										'scene_id' => $scene_id
//								)
//						)
//				);
echo "<br/>";
//echo json_encode($haibao_api->cr_user_haibao("58452"));
echo "<br/>";
//echo json_encode($haibao_api->cr_user_product_haibao("58452", 564));
echo "<br/>";
$path = "/usr/www/html/index.php";
echo basename($path)."<br>";
echo "<br/>";
//如果选择suffix则忽略扩展名
echo basename($path,".php");
echo "<br/>";
$json = '<xml><ToUserName><![CDATA[gh_15e2c5f6f1b6]]></ToUserName>
<FromUserName><![CDATA[oceJrwK76ZGJbNqGWUqct6exGY3w]]></FromUserName>
<CreateTime>1461809295</CreateTime>
<MsgType><![CDATA[event]]></MsgType>
<Event><![CDATA[SCAN]]></Event>
<EventKey><![CDATA[]]></EventKey>
<Ticket><![CDATA[gQFg7zoAAAAAAAAAASxodHRwOi8vd2VpeGluLnFxLmNvbS9xL29qc3p3R3ZsNmV5UTZZMWtfaFcxAAIEekQfVwMEAI0nAA==]]></Ticket>
</xml>';
$a = simplexml_load_string($json, 'SimpleXMLElement', LIBXML_NOCDATA);  
echo (is_object($a->CreateTime));
echo "<br/>";
$EventKey = $a->EventKey;
$EventKey = (string)$EventKey;
echo (is_string($EventKey));
echo "<br/>";
echo ($EventKey);
echo "<br/>";
echo 'end';
echo "<br/>";

//$ret = $weixin_api->refreshWxInfo("oQ5ZLsyLReJNbPhzhJ24DBPBP7E0");
//$ret = $weixin_api->loadWxInfo("oQ5ZLs6dCdcGGHJqMFDNDAA-v2O0");
//$p->process("g_point", "oQ5ZLs6dCdcGGHJqMFDNDAA-v2O0", "gh_ebb5f1b98707", "");
//$p->process("auto_reg", "oQ5ZLs6dCdcGGHJqMFDNDAA-v2O0", "gh_ebb5f1b98707", "");
echo json_encode($ret);
//echo "<br/>";

$lasttime = gmtime();
$lastdate = date("Y-m-d H:i:s",$lasttime);
echo $lastdate;
echo "<br/>";

$result = str_replace("u", "\\u", "u5145u503c10u5143");
echo unicode2utf8($result);
function unicode2utf8($str) {
	if (!$str) return $str;
	$decode = json_decode($str);
	if ($decode) return $decode;
	$str = '["' . $str . '"]';
	print_r($str);
	echo "</br>";
	$decode = json_decode($str);
	print_r($decode);
	if (count($decode) == 1) {
		return $decode[0];
	}
	return $str;
}


$text = "你好  hello 123456000"; //可以为收到的微信消息，可能包含二进制emoji表情字符串 
$tmpStr = json_encode($text); //暴露出unicode 
$tmpStr = preg_replace("#(\\\ue[0-9a-f]{3})#ie","addslashes('\\1')",$tmpStr); //将emoji的unicode留下，其他不动 
$text = json_decode($tmpStr); 
  
echo "<br/>";
echo $text;//你好 \ue415 hello 123
echo "<br/>";

