<?php

require_once('../mobile/include/emoji.php');
class wechatCallbackapi {
	public function valid($db) {
		$logger = LoggerManager::getLogger('callback-ent.php');
		$logger -> debug('验证');
		$echoStr = $_GET["echostr"];
		if ($this -> checkSignature($db)) {
			echo $echoStr;
		}
	}
	public function msgError($error) {
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
		if (isset($postStr)) {
			$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
			$fromUsername = $postObj -> FromUserName;
			$msgType = $postObj -> MsgType;
			$toUsername = $postObj -> ToUserName;
			if($msgType=="voice"){
				$keyword = trim($postObj -> Recognition);
			}else{
				$keyword = trim($postObj -> Content);
			}
			$time = time();
			$textTpl = "<xml>
                            <ToUserName><![CDATA[%s]]></ToUserName>
                            <FromUserName><![CDATA[%s]]></FromUserName>
                            <CreateTime>%s</CreateTime>
                            <MsgType><![CDATA[%s]]></MsgType>
                            <Content><![CDATA[%s]]></Content>
                            <FuncFlag>0</FuncFlag>
                            </xml>";
			$contentStr = $error;
			$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
			echo $resultStr;
			exit;
		}
	}
	public function responseMsg($db, $user, $base_url) {
		$logger = LoggerManager::getLogger('callback-ent.php');
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
		$myfile = fopen("d:/bb.txt", "wr");
		$txt = 'ECShop 验证消息 文件执行'."\n";
		fwrite($myfile, $txt);
		fclose($myfile);
		$logger->debug("ECShop 消息验证 xml数据：".$postStr);
		
		$clean_text = emoji_google_to_unified($postStr);
		$logger->debug('验证消息  方法 = ' . $clean_text);
		$logger->debug('$clean_text = ' . $clean_text);
		$html = emoji_unified_to_html($clean_text);
		$logger->debug('$html = ' . $html);
		$logger->debug('$postStr = ' . $postStr);
		
		if (empty($postStr)) {
			echo "";
			exit;
		}
		
		$time = time();
		
		$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
		$logger->debug('$postObj = ' . json_encode($postObj));
		$fromUsername = $postObj -> FromUserName;
		$toUsername = $postObj -> ToUserName;
		$text = $postObj -> Content;
		$logger->debug('$Content1 = ' . emoji_google_to_unified($text));
		$logger->debug('$Content2 = ' . emoji_unified_to_html(emoji_google_to_unified($text)));
		
		//记录收到的消息
		$db -> query("INSERT INTO `wxch_message` (`wxid`, `message`, `dateline`) VALUES ('$fromUsername', '$postStr', $time);");
		$belong = $db -> insert_id();
		$logger->debug('$belong = ' . $belong);
		
		require_once ('../mobile/api/weixin_api.php');
		require_once ('../mobile/api/haibao_api.php');
		require_once ('custom_keyword/keyword_process.php');
		$keyword_processor = new keyword_processor($db);
		
		$sql = "select u.user_id from " . $GLOBALS['ecs']->table('users') . " u, wxch_user w where w.wxid=u.wxid and w.wxid='$fromUsername'";
		$logger->debug('$sql = ' . $sql);
		$check_new_user = $db->getOne($sql);
		$logger->debug('$check_new_user:-' . $check_new_user . '-');
		if($check_new_user){
			$is_new_user = 0;
		}else{
			$is_new_user = 1;
		}
		$logger->debug('$is_new_user = ' . $is_new_user);
		if($is_new_user && $postObj -> Event != 'unsubscribe' && $postObj -> Event != 'user_scan_product'
				&& $postObj -> Event != 'user_scan_product_enter_session' && $postObj -> Event != 'user_scan_product_async'){
			$keyword_processor -> process('g_point', $fromUsername, $toUsername, $belong);
			$keyword_processor -> process('auto_reg', $fromUsername, $toUsername, $belong);
		}elseif($is_new_user == 0 && $postObj -> Event == 'subscribe'){
			$keyword_processor -> process('g_point', $fromUsername, $toUsername, $belong);
		}
		
		
        if ($postObj -> MsgType == 'event'){
        	if($postObj -> Event == 'CLICK'){
        		$keyword = $postObj -> EventKey;
        		$keyword_processor -> process($keyword, $fromUsername, $toUsername, $belong);
        	}elseif($postObj -> Event == 'VIEW'){
        		
        	}elseif($postObj -> Event == 'subscribe' || $postObj -> Event == 'SCAN'){
        		$logger->debug('event = ' . $postObj -> Event);
        		$qrscene = $postObj -> EventKey;
        		$scene_id = (string)$qrscene;
				if(stripos($scene_id, "_") > 0){
					$sa = explode('_', $scene_id);
					$scene_id = $sa[1];
				}
				
				if(!empty($scene_id)){
					$logger->debug("二维码场景触发,场景ID: " . $scene_id);
					$db->query("update wxch_message set scene = '$scene_id' where id='$belong'");
					require_once ('scene/scene_process.php');
					$scene_processor = new scene_processor($db);
					$scene_processor->process($scene_id, $fromUsername, $toUsername, $belong, $is_new_user);
				}else{
					//查看ticket是否有值
					$ticket = $postObj -> Ticket;
					$ticket = (string)$ticket;
					if(!empty($ticket)){
						$scene_id = $db->getOne("select scene_id from wxch_scene where ticket = '$ticket'");
						if($scene_id){
							$logger->debug("二维码场景触发,场景ID: " . $scene_id);
							require_once ('scene/scene_process.php');
							$scene_processor = new scene_processor($db);
							$scene_processor->process($scene_id, $fromUsername, $toUsername, $belong, $is_new_user);
						}
					}
				}
        	}elseif($postObj -> Event == 'unsubscribe'){
        		$db -> query("UPDATE  `wxch_user` SET  `subscribe` =  '0' WHERE  `wxch_user`.`wxid` = '$fromUsername'");
        	}elseif($postObj -> Event == 'user_scan_product'){
        		//扫一扫事件
        	}elseif($postObj -> Event == 'user_scan_product_enter_session'){
        		//扫一扫事件
        	}elseif($postObj -> Event == 'user_scan_product_async'){
        		//扫一扫事件
        	}elseif($postObj -> Event == 'kf_close_session'){
        		//客服链接中断
        	}
        }elseif($postObj -> MsgType == 'text'){
        	$keyword = trim($postObj -> Content);
        	if($keyword_processor -> process($keyword, $fromUsername, $toUsername, $belong) > 0){
        		//未找到匹配的关键字
        		$logger->debug("关键字回复未定义，查询商品");
        		$this->autoResponseUnknowKeyword($db, $fromUsername, $toUsername, $keyword, $base_url, $time, $belong);
        	}
        	
        }elseif($postObj -> MsgType == 'image'){
        	$this->autoResponseUnknowKeyword($db, $fromUsername, $toUsername, $keyword, $base_url, $time, $belong);
        }elseif($postObj -> MsgType == 'voice'){
        	$recognition = $postObj -> Recognition;
        	$recognition = (string)$recognition;
        	$recognition = str_replace(array("！"),"", $recognition);
        	if(!empty($recognition)){
	        	$keyword = trim($recognition);
	        	if($keyword_processor -> process($keyword, $fromUsername, $toUsername, $belong) > 0){
	        		//未找到匹配的关键字
	        		$logger->debug("关键字回复未定义，查询商品");
	        		$this->autoResponseUnknowKeyword($db, $fromUsername, $toUsername, $keyword, $base_url, $time, $belong);
	        	}
        	}else{
        		$this->autoResponseUnknowKeyword($db, $fromUsername, $toUsername, $keyword, $base_url, $time, $belong);	
        	}
        }elseif($postObj -> MsgType == 'video'){
        	$this->autoResponseUnknowKeyword($db, $fromUsername, $toUsername, $keyword, $base_url, $time, $belong);
        }elseif($postObj -> MsgType == 'shortvideo'){
        	$this->autoResponseUnknowKeyword($db, $fromUsername, $toUsername, $keyword, $base_url, $time, $belong);
        }elseif($postObj -> MsgType == 'location'){
        	$this->autoResponseUnknowKeyword($db, $fromUsername, $toUsername, $keyword, $base_url, $time, $belong);
        }elseif($postObj -> MsgType == 'link'){
        	$this->autoResponseUnknowKeyword($db, $fromUsername, $toUsername, $keyword, $base_url, $time, $belong);
        }


		/*Add by ZhangNu */
// 			$keyword=str_replace(array("！"),"",$keyword);
// 			if(mb_strlen($keyword,'UTF8') >= 5)
// 			{
// 				exit;
// 			}
		/*修改语音搜索的bug */

		
	}

	/**
	 * 自动回复未匹配的关键字
	 * 可以转发多客服，或者发起机器人回话
	 * by liuzhy 20160216
	 */
	protected function autoResponseUnknowKeyword($db, $fromUsername, $toUsername, $keyword, $base_url, $time, $belong){
		$serviceTpl = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[%s]]></MsgType>
                        </xml>";
		//$access_token = $db->getOne("SELECT `access_token` FROM `wxch_config` ");
		//echo $this->kefureturn($access_token, $fromUsername);
		$msgType = "transfer_customer_service";
		$contentStr = '客服转接';
		$resultStr = sprintf($serviceTpl, $fromUsername, $toUsername, $time, $msgType);
		echo $resultStr;
	}

	public function guolv($str) {
		$str = str_replace("\r", "", $str);
		$str = str_replace("\n", "", $str);
		$str = str_replace("\t", "", $str);
		$str = str_replace("\r\n", "", $str);
		$str = trim($str);
		return $str;
	}
	private function checkSignature($db) {
		$signature = $_GET["signature"];
		$timestamp = $_GET["timestamp"];
		$nonce = $_GET["nonce"];
		$ret = $db -> getRow("SELECT * FROM `wxch_config` WHERE `id` = 1");
		$token = $ret['token'];
		$tmpArr = array($token, $timestamp, $nonce);
		sort($tmpArr, SORT_STRING);
		$tmpStr = implode($tmpArr);
		$tmpStr = sha1($tmpStr);
		if ($tmpStr == $signature) {
			return true;
		} else {
			return false;
		}
	}
	

//新增
function htmltowei($contents)
{
	$contents = strip_tags($contents,'<br>');
	$contents = str_replace('<br />',"\r\n",$contents);
	$contents = str_replace('&quot;','"',$contents);
	$contents = str_replace('&nbsp;','',$contents);
	return $contents;
}


}
