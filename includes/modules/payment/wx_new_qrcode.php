<?php
/**
 * ECSHOP微信新版JSAPI支付插件  甜心  10 0 开发  修复
 */
if(!defined('IN_ECS')){
	die('Hacking attempt');
}
$payment_lang = ROOT_PATH . 'languages/' . $GLOBALS['_CFG']['lang'] . '/payment/wx_new_qrcode.php';
if(file_exists($payment_lang)){
	global $_LANG;

	include_once ($payment_lang);
}

/* 模块的基本信息 */
if(isset($set_modules) && $set_modules == TRUE){
	$i = isset($modules) ? count($modules) : 0;

	/* 代码 */
	$modules[$i]['code'] = basename(__FILE__, '.php');

	/* 描述对应的语言项 */
	$modules[$i]['desc'] = 'wx_new_qrcode_desc';

	/* 是否支持货到付款 */
	$modules[$i]['is_cod'] = '0';

	/* 是否支持在线支付 */
	$modules[$i]['is_online'] = '1';

	/* 作者 */
	$modules[$i]['author'] = 'QQ:3487658996';

	/* 网址 */
	$modules[$i]['website'] = 'http://wx.qq.com';

	/* 版本号 */
	$modules[$i]['version'] = '1.0.0';

	/* 配置信息 */
	$modules[$i]['config'] = array(
			array(
					'name' => 'appid',
					'type' => 'text',
					'value' => ''
			),
			array(
					'name' => 'mchid',
					'type' => 'text',
					'value' => ''
			),
			array(
					'name' => 'key',
					'type' => 'text',
					'value' => ''
			),
			array(
					'name' => 'appsecret',
					'type' => 'text',
					'value' => ''
			),
			array(
					'name' => 'logs',
					'type' => 'text',
					'value' => ''
			)
	);

	return;
}
class wx_new_qrcode {

	function __construct(){
		$payment = get_payment('wx_new_qrcode');

		if(!defined('WXAPPID')){
			$root_url = str_replace('mobile/', '', $GLOBALS['ecs']->url());
			define("WXAPPID", $payment['appid']);
			define("WXMCHID", $payment['mchid']);
			define("WXKEY", $payment['key']);
			define("WXAPPSECRET", $payment['appsecret']);
			define("WXCURL_TIMEOUT", 30);
			define('WXNOTIFY_URL', $root_url . 'wx_native_callback.php');
			define('WXSSLCERT_PATH', dirname(__FILE__) . '/WxPayPubHelper/cacert/apiclient_cert.pem');
			define('WXSSLKEY_PATH', dirname(__FILE__) . '/WxPayPubHelper/cacert/apiclient_key.pem');

			define('WXJS_API_CALL_URL', $root_url . 'wx_refresh.php');
		}
		require_once (dirname(__FILE__) . "/WxPayPubHelper/WxPayPubHelper.php");
	}

	function get_code($order, $payment){
		$unifiedOrder = new UnifiedOrder_pub();

		$unifiedOrder->setParameter("body", $order['order_sn']); // 商品描述
		$out_trade_no = $order['order_sn'];
		$unifiedOrder->setParameter("out_trade_no", "$out_trade_no"); // 商户订单号
		$unifiedOrder->setParameter("attach", strval($order['log_id'])); // 商户支付日志
		$unifiedOrder->setParameter("total_fee", strval(intval($order['order_amount'] * 100))); // 总金额
		$unifiedOrder->setParameter("notify_url", WXNOTIFY_URL); // 通知地址
		$unifiedOrder->setParameter("trade_type", "NATIVE"); // 交易类型

		$unifiedOrderResult = $unifiedOrder->getResult();

		$html = '<button type="button" onclick="javascript:alert(\'出错了\')">微信支付</button>';

		if($unifiedOrderResult["code_url"] != NULL){
			$code_url = $unifiedOrderResult["code_url"];
			$html = '<div class="wx_qrcode" style="text-align:center">';
			$html .= $this->getcode($code_url);
			$html .= "</div>";

			$html .= "<div style=\"text-align:center\">支付后点击<a href=\"user.php?act=order_list\">此处</a>查看我的订单</div>";
		}

		return $html;
	}

	function respond(){
		$payment = get_payment('wx_new_qrcode');

		$notify = new Notify_pub();
		$xml = $GLOBALS['HTTP_RAW_POST_DATA'];

		if($payment['logs']){
			$this->log(ROOT_PATH . '/data/wx_new_log.txt', "传递过来的XML\r\n" . var_export($xml, true));
		}
		$notify->saveData($xml);
		if($notify->checkSign() == TRUE){
			if($notify->data["return_code"] == "FAIL"){
				// 此处应该更新一下订单状态，商户自行增删操作
				if($payment['logs']){
					$this->log(ROOT_PATH . '/data/wx_new_log.txt', "return_code失败\r\n");
				}
			}elseif($notify->data["result_code"] == "FAIL"){
				// 此处应该更新一下订单状态，商户自行增删操作
				if($payment['logs']){
					$this->log(ROOT_PATH . '/data/wx_new_log.txt', "result_code失败\r\n");
				}
			}else{
				$log_id = $notify->data["attach"];
				$check_receive = $GLOBALS['db']->getOne("select log_id from " . $GLOBALS['ecs']->table('pay_log') .
						" where log_id='$log_id' and receive_callback = 0");
				if($check_receive){
					$GLOBALS['db']->query("update " . $GLOBALS['ecs']->table('pay_log') .
							" set receive_callback=1 where log_id='$log_id'");
				}else{
					if($payment['logs']){
						$this->log(ROOT_PATH . '/data/wx_new_log.txt', "支付回调已处理过\r\n");
					}
					echo "
						<xml>
						  <return_code><![CDATA[SUCCESS]]></return_code>
						  <return_msg><![CDATA[OK]]></return_msg>
						</xml>";
					return;
				}

				// 此处应该更新一下订单状态，商户自行增删操作
				if($payment['logs']){
					$this->log(ROOT_PATH . '/data/wx_new_log.txt', "支付成功\r\n");
				}
				$total_fee = $notify->data["total_fee"];

				$sql = 'SELECT order_amount FROM ' . $GLOBALS['ecs']->table('pay_log') . " WHERE log_id = '$log_id'";
				$amount = $GLOBALS['db']->getOne($sql);

				if($payment['logs']){
					$this->log(ROOT_PATH . '/data/wx_new_log.txt', '订单金额' . $amount . "\r\n");
				}

				if(intval($amount * 100) != $total_fee){

					if($payment['logs']){
						$this->log(ROOT_PATH . '/data/wx_new_log.txt', '订单金额不符' . "\r\n");
					}

					echo "<xml>
						  <return_code><![CDATA[FAIL]]></return_code>
						  <return_msg><![CDATA[订单金额不符]]></return_msg>
						</xml>";
					return;
				}

				order_paid($log_id, 2);
				$sql = "SELECT * FROM " . $GLOBALS['ecs']->table('pay_log') . " WHERE log_id = '$log_id'";
				$pay_log = $GLOBALS['db']->getRow($sql);
				$sql = "select autoload from `wxch_order` where order_name='pay'";
				$autoload = $GLOBALS['db']->getOne($sql);
				$this->log(ROOT_PATH . '/data/wx_new_log.txt', 'pay_log:' . $pay_log['is_paid'] . "\r\n");
				/* 店 铺 地 址： http:// we10.taobao. com */

				if($pay_log['is_paid'] == 1){
					if($autoload == "yes"){
						/* 取得订单信息 */
						$sql = 'SELECT * ' . 'FROM ' . $GLOBALS['ecs']->table('order_info') . " WHERE order_id = '$pay_log[order_id]'";
						$order = $GLOBALS['db']->getRow($sql);
						$wxch_user_id = $order['user_id'];
						$query_sql = "SELECT wxid FROM " . $GLOBALS['ecs']->table('users') . " WHERE user_id = '$wxch_user_id'";
						$ret_w = $GLOBALS['db']->getOne($query_sql);

						if(!empty($ret_w)){
							// include(ROOT_PATH . 'wxch_order.php');
							$this->log(ROOT_PATH . '/data/wx_new_log.txt', '发送微信信息1' . "\r\n");
							include (ROOT_PATH . 'wxch_order.php');
							$this->log(ROOT_PATH . '/data/wx_new_log.txt', '发送微信信息2' . "\r\n");
						}
					}
				}
				/* 甜 心 1 00 修复付 款 提 醒 */
				echo "<xml>
					  <return_code><![CDATA[SUCCESS]]></return_code>
					  <return_msg><![CDATA[OK]]></return_msg>
					</xml>";
				return;
			}
		}else{
			$this->log(ROOT_PATH . '/data/wx_new_log.txt', "签名失败\r\n");
		}
		echo "<xml>
			  <return_code><![CDATA[FAIL]]></return_code>
			  <return_msg><![CDATA[签名失败]]></return_msg>
			</xml>";
		return ;
	}

	function getcode($url){
		if(file_exists(ROOT_PATH . 'includes/phpqrcode.php')){
			include (ROOT_PATH . 'includes/phpqrcode.php');
		}
		// 纠错级别：L、M、Q、H
		$errorCorrectionLevel = 'Q';
		// 点的大小：1到10
		$matrixPointSize = 5;
		// 生成的文件名
		$tmp = ROOT_PATH . 'images/qrcode/';
		if(!is_dir($tmp)){
			@mkdir($tmp);
		}
		$filename = $tmp . $errorCorrectionLevel . $matrixPointSize . '.png';
		QRcode::png($url, $filename, $errorCorrectionLevel, $matrixPointSize, 2);
		return '<img src="' . $GLOBALS['ecs']->url() . 'images/qrcode/' . basename($filename) . '" />';
	}

	function log($file, $txt){
		$fp = fopen($file, 'ab+');
		fwrite($fp, '-----------' . local_date('Y-m-d H:i:s') . '-----------------');
		fwrite($fp, $txt);
		fwrite($fp, "\r\n\r\n\r\n");
		fclose($fp);
	}
}
