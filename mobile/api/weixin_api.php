<?php
/**
 * 微信开放接口平台API
 */
//require_once(dirname(__FILE__) . '/../include/emoji.php');
class weixin_api // extends Thread
{
	public $appid;
	public $appsecret;
	public $access_token;
	public $dateline;
	public $logger;
	public $api_url;
	public $post_msg;
	const TEMP_TOPCOLOR = "#FF0000";
	const TEMP_KEYWORD_COLOR = "#173177";
	const TEMP_MSG_URL = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=";
	const CUSTOM_MSG_URL = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=";
	const UPLOAD_IMG_URL = "http://file.api.weixin.qq.com/cgi-bin/media/upload?type=image&access_token=";
	const CUSTOM_SVC_URL_C = "https://api.weixin.qq.com/customservice/kfsession/create?access_token=";
	const CUSTOM_SVC_URL_Q = "https://api.weixin.qq.com/cgi-bin/customservice/getonlinekflist?access_token=";
	const GET_WX_INFO = "https://api.weixin.qq.com/cgi-bin/user/info?";
	const CR_QRCODE = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=";
	const SHOW_QRCODE = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=";
	const UPLOAD_NEWS = "https://api.weixin.qq.com/cgi-bin/media/uploadnews?access_token=";
	const SEND_WXIDS = "https://api.weixin.qq.com/cgi-bin/message/mass/send?access_token=";
	const GET_FANS_LIST = "https://api.weixin.qq.com/cgi-bin/user/get?access_token=";
	const BATCH_GET_USER_INFO = "https://api.weixin.qq.com/cgi-bin/user/info/batchget?access_token=";
	const GET_MEDIA = "http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=";

	/*
	 * public function run(){
	 * sleep(1);
	 * $this->send($this->api_url, $this->post_msg);
	 * }
	 */
	public function __construct(){
		$this->logger = LoggerManager::getLogger('weixin_api.php');
		$this->get_appid_appsecret();
		$this->refresh_access_token();
		$this->get_access_token();
	}
	
	/**
	 * 根据媒体ID下载多媒体文件
	 */
	public function download_media($media_id, $sub_folder = ""){
		$url = weixin_api::GET_MEDIA . $this->get_access_token() . "&media_id=" . $media_id;

		$media=$this -> downloadimageformweixin($url);

		if(empty($media)){
//			$this->refresh_access_token(true);
//			$url = weixin_api::GET_MEDIA . $this->get_access_token() . "&media_id=" . $media_id;
			$media=$this -> downloadimageformweixin($url);
			if(empty($media)){
				$ret = array(
						"errcode" => 1,
						"errmsg" => "文件下载失败，请检查服务器环境后重试",
				);
				$ret = json_decode(json_encode($ret));
				return $ret;
			}
		}

		$time = date('YmdHis_' . mt_rand(1,1000));	//时间和随机数作为文件名，避免重复覆盖
		$relative_path = 'media/'.$time.'.jpg';
		if(!empty($sub_folder)){
			$relative_path = 'media/'.$sub_folder.'/'.$time.'.jpg';
		}
		$path = dirname(__FILE__) . "/../" . $relative_path;
		$local_file=fopen($path,'a');
		if(false !==fwrite($local_file,$media)){
			fclose($local_file);
			$ret = array(
					"errcode" => 0,
					"errmsg" => "",
					"data" => $relative_path,
			);
			$ret = json_decode(json_encode($ret));
			return $ret;
		}else{
			$ret = array(
					"errcode" => 1,
					"errmsg" => "保存文件失败,检查fwrite函数是否生效请重试",
			);
			$ret = json_decode(json_encode($ret));
			return $ret;
		}
	}
	
	/**
	 * 下载公众号粉丝
	 */
	public function download_fans(){
		if(file_exists("wxch_fans.sql")){
			@unlink("wxch_fans.sql");
		}
		
		$sqlfile = fopen("wxch_fans.sql", "w");
		$next_openid = "";
		$count = 0;
		//下载粉丝列表
		do{
			$user_list = array();	//查询用户详细信息的openid数组
			$w_user_list = array();	//完整用户信息的数组
			$size = 0;
			$url = weixin_api::GET_FANS_LIST . $this->access_token;
			$this->logger->debug('$url = ' . $url);
			if(!empty($next_openid)) $url .= "&next_openid=" . $next_openid;
			$ret = $this->curl_get_contents($url);
			$this->logger->debug('$ret_json = ' . $ret);
			$ret = json_decode($ret);
			
			if($ret->errmsg && $ret->errmsg != 'ok'){
				$ret = $this->curl_get_contents($url);
				$this->logger->debug('$ret_json = ' . $ret);
				$ret = json_decode($ret);
			}
			
			$data = $ret->data;
//			$this->logger->debug(json_encode($data));
			$data = $data->openid;
//			$this->logger->debug(count($data));
			
			foreach ( $data as $k => $openid ) {
				$w_user = array(
					'wxid' => $openid
				);
				$w_user_list[$openid] = $w_user;
				
	       		//下载粉丝详细信息
	       		array_push($user_list, array(
	       			'openid' => $openid
	       		));
	       		$size++;
	       		if($size>0 && $size % 100 == 0){
	       			$param = array(
	       				'user_list' => $user_list
	       			);
	       			$post_msg = json_encode($param);
	       			$user_info_resut = $this->send(weixin_api::BATCH_GET_USER_INFO, $post_msg);
	       			$user_info_list = $user_info_resut->user_info_list;
	       			foreach ( $user_info_list as $i => $user_info ) {
	       				$nick_name=str_replace("'","\'", $user_info->nickname);
	       				$w_user_list[$user_info->openid]['nickname'] = $nick_name;
						$w_user_list[$user_info->openid]['subscribe'] = $user_info->subscribe;
						$w_user_list[$user_info->openid]['sex'] = $user_info->sex;
						$w_user_list[$user_info->openid]['city'] = $user_info->city;
						$w_user_list[$user_info->openid]['province'] = $user_info->province;
						$w_user_list[$user_info->openid]['country'] = $user_info->country;
						$w_user_list[$user_info->openid]['language'] = $user_info->language;
						$w_user_list[$user_info->openid]['headimgurl'] = $user_info->headimgurl;
						$w_user_list[$user_info->openid]['dateline'] = time();
						$w_user_list[$user_info->openid]['subscribe_time'] = $user_info->subscribe_time;
						
//	       				$this->logger->debug($w_user_list[$user_info->openid]);
	       			}
	       			$user_list = array();
	       			$size = 0;
	       		}
			}
			
			if(count($user_list) > 0){
				$param = array(
       				'user_list' => $user_list
       			);
       			$post_msg = json_encode($param);
       			$user_info_resut = $this->send(weixin_api::BATCH_GET_USER_INFO, $post_msg);
       			$user_info_list = $user_info_resut->user_info_list;
       			foreach ( $user_info_list as $i => $user_info ) {
       				$nick_name=str_replace("'","\'", $user_info->nickname);
       				$w_user_list[$user_info->openid]['nickname'] = $nick_name;
					$w_user_list[$user_info->openid]['sex'] = $user_info->sex;
					$w_user_list[$user_info->openid]['city'] = $user_info->city;
					$w_user_list[$user_info->openid]['province'] = $user_info->province;
					$w_user_list[$user_info->openid]['country'] = $user_info->country;
					$w_user_list[$user_info->openid]['language'] = $user_info->language;
					$w_user_list[$user_info->openid]['headimgurl'] = $user_info->headimgurl;
					$w_user_list[$user_info->openid]['dateline'] = time();
					$w_user_list[$user_info->openid]['subscribe_time'] = $user_info->subscribe_time;
					
//       				$this->logger->debug($w_user_list[$user_info->openid]);
       			}
       			$user_list = array();
       			$size = 0;
			}
			
			foreach($w_user_list as $opid => $user){
				$sql = "insert into wxch_fans(subscribe, wxid, nickname, sex, city, province, country, language, headimgurl, dateline, subscribe_time) select 1, '$user[wxid]', '$user[nickname]', '$user[sex]', '$user[city]', '$user[province]', '$user[country]', '$user[language]', '$user[headimgurl]', '$user[dateline]', '$user[subscribe_time]' from dual where not exists(select wxid from wxch_fans where wxid='$user[wxid]');\n";
//						"values (1, '$user[wxid]', '$user[nickname]', '$user[sex]', '$user[city]', '$user[province]', '$user[country]', '$user[language]', '$user[headimgurl]', '$user[dateline]', '$user[subscribe_time]');\n";
//	       		$this->logger->debug($sql);
	       		fwrite($sqlfile, $sql);
			}
			
			$next_openid = $ret->next_openid;
		}while(!empty($next_openid));
		
		
		
		
		fclose($sqlfile);
		return $count;
	}
	
	/**
	 * 上传图文消息素材
	 * 
	 * @param array $param
	 * @param string media_id 媒体文件/图文消息上传公众号后获取的唯一标识
	 */
	public function upload_news($param){
		$this->api_url = weixin_api::UPLOAD_NEWS;
		$this->post_msg = $param;
		$this->logger->debug('$post_msg = ' . $this->post_msg);
		$ret_json = $this->curl_grab_page($this->api_url . $this->access_token, $this->post_msg);
		$this->logger->debug('$ret_json = ' . $ret_json);
		$ret = json_decode($ret_json);
		if($ret->errcode && $ret->errcode > 0){
			$this->refresh_access_token(true);
			$this->logger->debug('$post_msg = ' . $this->post_msg);
			$ret = $this->curl_grab_page($this->api_url . $this->access_token, $this->post_msg);
			$this->logger->debug('$ret_json = ' . $ret);
			$ret = json_decode($ret_json);
		}
		return $ret->media_id;
	}
	
	/**
	 * 根据OpenID列表群发
	 */
	public function send_wxids($param){
		$this->api_url = weixin_api::SEND_WXIDS;
		$this->post_msg = json_encode($param);
		$ret_json = $this->curl_grab_page($this->api_url . $this->access_token, $this->post_msg);
		$this->logger->debug('$ret_json = ' . $ret_json);
		$ret = json_decode($ret_json);
		if($ret->errcode && $ret->errcode > 0){
			$this->refresh_access_token(true);
			$this->logger->debug('$post_msg = ' . $this->post_msg);
			$ret = $this->curl_grab_page($this->api_url . $this->access_token, $this->post_msg);
			$this->logger->debug('$ret_json = ' . $ret);
			$ret = json_decode($ret_json);
		}
		return $ret;
	}

	/**
	 * 生成二维码
	 * @param array $param
	 * @param string $sub_folder 二维码本地保存相对路径，相对于mobile
	 */
	public function cr_qrcode($param, $sub_folder = ""){
		$this->api_url = weixin_api::CR_QRCODE;
		$this->post_msg = json_encode($param);
		$ret = $this->send($this->api_url, $this->post_msg);

		$ticket = $ret->ticket;
		if($ticket) {
			$ticket_url = urlencode($ticket);
			$ticket_url = weixin_api::SHOW_QRCODE . $ticket_url;
			$imageinfo=$this -> downloadimageformweixin($ticket_url);

			if(empty($imageinfo)){
				$imageinfo=$this -> downloadimageformweixin($ticket_url);
				if(empty($imageinfo)){
					$ret = array(
							"errcode" => 1,
							"errmsg" => "下载二维码失败，请检查服务器环境后重试",
					);
					$ret = json_decode(json_encode($ret));
					return $ret;
				}
			}

			$time = date('YmdHis_' . mt_rand(1,1000));	//时间和随机数作为文件名，避免重复覆盖
			$relative_path = 'images/qrcode/'.$time.'.jpg';
			if(!empty($sub_folder)){
				$relative_path = 'images/qrcode/'.$sub_folder.'/'.$time.'.jpg';
			}
			$path = dirname(__FILE__) . "/../" . $relative_path;
			$local_file=fopen($path,'a');
			if(false !==fwrite($local_file,$imageinfo)){
				fclose($local_file);
				$ret = array(
						"errcode" => 0,
						"errmsg" => "",
						"data" => $relative_path,
						"ticket" => $ticket
				);
				$ret = json_decode(json_encode($ret));
				return $ret;
			}else{
				$ret = array(
						"errcode" => 1,
						"errmsg" => "保存二维码图片失败,检查fwrite函数是否生效请重试",
				);
				$ret = json_decode(json_encode($ret));
				return $ret;
			}
		}
		return $ret;
	}

	/**
	 * 转发多客服系统
	 *
	 * @param string $openid
	 *        	客户openid
	 * @param string $message
	 *        	消息
	 */
	public function transfer_custom_service($openid, $message){
		$kf_online_list = $this->query_custom_svc_status();
		if($kf_online_list == null) return;
		$best_kf = null;
		foreach ($kf_online_list as $kf_online){
			if($kf_online['accepted_case'] == 0){
				$best_kf = $kf_online;
				break;
			}else if($best_kf == null || $kf_online['accepted_case'] < $best_kf['accepted_case']){
				//取会话数最小的客服接入
				$best_kf = $kf_online;
			}
		}

		if($best_kf){
			$post_msg = array(
					'kf_account' => $kf_online['kf_account'],
					'openid' => $openid,
					'text' => urlencode($message),
			);
	
	
			$this->post_msg = urldecode(json_encode($post_msg));
			$this->api_url = weixin_api::CUSTOM_SVC_URL_C;
			$this->logger->debug('客服' . $kf_online['kf_account'] . '[' . $kf_online['kf_id'] . ']接入' . $openid);
			$ret = $this->send($this->api_url, $this->post_msg);
		}else{
			$ret = array(
					"errcode" => 1,
					"errmsg" => "多客服不在线",
			);
			$ret = json_decode(json_encode($ret));
			return $ret;
		}
		
	}

	/**
	 * 查询多客服系统客服状态
	 */
	public function query_custom_svc_status(){
		$ret = $this->get(weixin_api::CUSTOM_SVC_URL_Q);
		return $ret['kf_online_list'];
	}

	/**
	 * 向客户推送模板数据
	 * @param string $template_type 模板key
	 * @param object $data 数据(touser、url及其他属性)
	 */
	public function send_template_msg($template_type, $data){
		$this->api_url = weixin_api::TEMP_MSG_URL;
		$template = $GLOBALS['db'] -> getRow("select * from wxch_order where autoload='yes' and order_name='$template_type'");
		//$this->logger->debug(json_encode($GLOBALS['_CFG']));
		if(empty($template)){
			$ret = array(
					"errcode" => 1,
					"errmsg" => "系统设置不发送该消息",
			);
			$ret = json_decode(json_encode($ret));
			return $ret;
		}

		if($template['use_template'] > 0 && !empty($template['template_id'] )){
			//推送微信模板消息
			$post_msg = array(
					'topcolor' => weixin_api::TEMP_TOPCOLOR,
					'touser' => $data['touser'],
					'template_id' => $template['template_id'],
			);
			if($data['url']){
				$post_msg['url'] = urlencode($data['url']);
			}

			$d = array();
			foreach ($data as $key => $val){
				if($key != 'touser' && $key != 'url'){
					$d[$key] = array(
							'value' => urlencode($val),
							'color' => weixin_api::TEMP_KEYWORD_COLOR
					);
				}
			}
			$post_msg['data'] = $d;
			$this->post_msg = urldecode(json_encode($post_msg));

			$ret = $this->send($this->api_url, $this->post_msg);
		}else{
			$post_msg = '';
			if(!empty($template['content']) || !empty($template['title'])){
				$title = $template['title'];
				$post_msg = $template['content'];
				$arr_preg = array();
				$arr_place = array();
				foreach ($data as $key => $val){
					array_push($arr_preg, '/{{' . $key . '.DATA}}/');
					array_push($arr_place, $val);
				}
				$title = preg_replace($arr_preg, $arr_place, $title);
				$post_msg = preg_replace($arr_preg, $arr_place, $template['content']);
				$this->post_msg = $post_msg;
				if(empty($template['image'])){
					//推送纯文本消息
					$this->post_msg = $title . "\r\n\r\n" . $this->post_msg;
					$ret = $this->send_custom_message(
							$data['touser'],
							$this->post_msg);
				}else{
					//推送图文消息
					$base_url = $GLOBALS['db']->getOne("SELECT cfg_value FROM  `wxch_cfg` where cfg_name='baseurl'");
					$mobile_url = $GLOBALS['db']->getOne("SELECT cfg_value FROM  `wxch_cfg` where cfg_name='murl'");
					$ret = $this->send_custom_single_news(
							$data['touser'],
							$title,
							$this->post_msg,
							$base_url . $mobile_url . $template['image'],
							$data['url']);
				}
			}else if(!empty($template['image'])){
				//推送单图消息
				$ret = $this->send_custom_image(
						$data['touser'],
						dirname(__FILE__) . "/../" . $template['image']);
			}else{
				$ret = array(
						"errcode" => 1,
						"errmsg" => "系统设置的消息无任何内容",
				);
				$ret = json_decode(json_encode($ret));
				return $ret;
			}


		}
		return $ret;
	}

	/**
	 * 获取模板拼出来的消息主体内容信息
	 * @param string $template_type 模板key
	 * @param object $data 数据(touser、url及其他属性)
	 * @return string 消息主体内容信息
	 */
	public function get_template_content($template_type, $data){
		$template = $GLOBALS['db'] -> getRow("select * from wxch_order where autoload='yes' and order_name='$template_type'");
		$post_msg = $template['content'];
		$arr_preg = array();
		$arr_place = array();
		foreach ($data as $key => $val){
			array_push($arr_preg, '/{{' . $key . '.DATA}}/');
			array_push($arr_place, $val);
		}
		$post_msg = preg_replace($arr_preg, $arr_place, $template['content']);
		return $post_msg;
	}

	/**
	 * 向客户推送单一图文消息
	 *
	 * @param string $wxid
	 *        	客户openid
	 * @param string $title
	 *        	标题
	 * @param string $description
	 *        	文本内容
	 * @param string $picurl
	 *        	图片地址
	 * @param string $url
	 *        	链接地址
	 * @return object json对象
	 */
	public function send_custom_single_news($wxid, $title = '', $description = '', $picurl = '', $url = ''){
		$this->api_url = weixin_api::CUSTOM_MSG_URL;
		$this->post_msg = '{
	       "touser":"' . $wxid . '",
	       "msgtype":"news",
	       "news":{
	           "articles": [
	            {
	                "title":"' . $title . '",
	                "description":"' . $description . '",
	                "url":"' . $url . '",
	                "picurl":"' . $picurl . '"
	            }
	            ]
	       }
	   	}';
		$ret = $this->send($this->api_url, $this->post_msg);
		return $ret;
	}
	
	/**
	 * 向客户推送多图文消息
	 */
	public function send_custom_multi_news($wxid, $param){
		$this->api_url = weixin_api::CUSTOM_MSG_URL;
		$this->post_msg = '{
	       "touser":"' . $wxid . '",
	       "msgtype":"news",
	       "news":{
	           "articles": [';
	    foreach($param as $art){
	    	$this->post_msg .= '{
	                "title":"' . $art['title'] . '",
	                "description":"' . $art['description'] . '",
	                "url":"' . $art['url'] . '",
	                "picurl":"' . $art['picurl'] . '"
	            },';
	    }    
	    $this->post_msg .=']}}';
		$ret = $this->send($this->api_url, $this->post_msg);
		return $ret;
	}

	/**
	 * zmh  发送消息
	 * @param unknown $mes
	 * @return Ambigous <object, mixed>
	 */
	public function zmh_send_custom_mes($mes){
		$this->api_url = weixin_api::CUSTOM_MSG_URL;
		$this->post_msg = $mes;
		$ret = $this->send($this->api_url, $this->post_msg);
		return $ret;
	}

	/**
	 * 向客户推送单一图片消息
	 *
	 * @param string $wxid
	 *        	客户openid
	 * @param string $image_dir
	 *        	图片本地地址（绝对地址）
	 * @return object json对象
	 */
	public function send_custom_image($wxid, $image_dir, $thread_able = 0){
		$media_id = $this->upload_image($image_dir);

		$this->api_url = weixin_api::CUSTOM_MSG_URL;
		$template = '{
		    "touser":"%s",
		    "msgtype":"image",
		    "image": {
		         "media_id":"%s"
		    }
		}';
		$this->post_msg = sprintf($template, $wxid, $media_id);
//		$post_msg = array(
//				"touser" => $wxid,
//				"msgtype" => "image",
//				"image" => array(
//						"media_id" => $media_id
//				)
//		);
//		$this->post_msg = json_encode($post_msg);

		if($thread_able > 0){
			$this->start();
		}else{
			$ret = $this->send($this->api_url, $this->post_msg);
			return $ret;
		}
	}

	/**
	 * 上传图片到微信服务器
	 *
	 * @param string $image_dir
	 *        	图片绝对路径
	 * @return string media_id
	 */
	public function upload_image($image_dir){
		$url = weixin_api::UPLOAD_IMG_URL . $this->access_token;
		$this->logger->debug('$url = ' . $url);
		$filedata = array(
				"media" => "@" . $image_dir
		);
		$this->logger->debug('$filedata = ' . json_encode($filedata));
		$res_json = $this->https_request($url, $filedata);
		$this->logger->debug("res_json = " . $res_json);
		$json = json_decode($res_json);
		if($json->errcode && $json->errcode > 0){
			$this->refresh_access_token(true);
			$this->logger->debug('$filedata = ' . json_encode($filedata));
			$res_json = $this->https_request($url, $filedata);
			$this->logger->debug("res_json = " . $res_json);
			$ret = json_decode($res_json);
		}
		return $json->media_id;
	}

	/**
	 * 向客户推送文本消息
	 *
	 * @param string $wxid
	 *        	客户openid
	 * @param string $content
	 *        	文本内容，链接请使用单引号
	 * @param int $thread_able
	 *        	是否启用线程异步发送：1是0否
	 * @return object json对象
	 */
	public function send_custom_message($wxid, $content = '', $thread_able = 0){
		$this->api_url = weixin_api::CUSTOM_MSG_URL;
		$template = '{
		    "touser":"%s",
		    "msgtype":"text",
		    "text": {
		         "content":"%s"
		    }
		}';
		$this->post_msg = sprintf($template, $wxid, $content);

		if($thread_able > 0){
			$this->start();
		}else{
			$ret = $this->send($this->api_url, $this->post_msg);
			return $ret;
		}
	}
	
	/**
	 * 载入用户微信信息
	 * string $wxid openid
	 */
	function loadWxInfo($wxid){
		if (! empty ( $wxid )) {
			$exist = $GLOBALS['db']->getOne("select wxid from wxch_user where wxid='$wxid'");
			if($exist){
				return $this->refreshWxInfo($wxid);
			}
			
			$access_token = $this->get_access_token();
			$url = weixin_api::GET_WX_INFO . "openid=$wxid&access_token=$access_token";

			$this->logger->debug('$url = ' . $url);
			$ret = $this->curl_get_contents($url);
			$this->logger->debug('$ret_json = ' . $ret);
			$ret = json_decode($ret);
			if($ret->errcode == '40001'){
				$this->refresh_access_token(true);
				$this->logger->debug('$url = ' . $url);
				$ret = $this->curl_get_contents($url);
				$this->logger->debug('$ret_json = ' . $ret);
				$ret = json_decode($ret);
			}

			$arr['wxid'] = $wxid;
			$arr['nickname'] = $ret->nickname;
			$arr['sex'] = $ret->sex;
			$arr['city'] = $ret->city;
			$arr['province'] = $ret->province;
			$arr['country'] = $ret->country;
			$arr['language'] = $ret->language;
			$arr['headimgurl'] = $ret->headimgurl;
			$arr['dateline'] = time();
			$arr['from_type'] = 1;
			$this->logger->debug('$arr = ' . json_encode($arr));
			$GLOBALS['db']->autoExecute("wxch_user", $arr, 'INSERT');
			return $ret;
		}
	}

	/**
	 *
	 * @param string $wxid openid
	 * @return string
	 */
	function refreshWxInfo($wxid) {
		if (! empty ( $wxid )) {
			$access_token = $this->get_access_token();
			$url = weixin_api::GET_WX_INFO . "openid=$wxid&access_token=$access_token";

			$this->logger->debug('$url = ' . $url);
			$ret = $this->curl_get_contents($url);
//			$emoji = emoji_google_to_unified($ret);
//			$this->logger->debug('$emoji_ret_json = ' . $emoji);
			$this->logger->debug('$ret_json = ' . $ret);
			$ret = json_decode($ret);
			if($ret->errcode == '40001'){
				$this->refresh_access_token(true);
				$this->logger->debug('$url = ' . $url);
				$ret = $this->curl_get_contents($url);
				$this->logger->debug('$ret_json = ' . $ret);
				$ret = json_decode($ret);
			}
			if($ret->subscribe > 0){
				$nick_name=str_replace("'","\'", $ret->nickname);
				$arr['nickname'] = $nick_name;
				$arr['sex'] = $ret->sex;
				$arr['city'] = $ret->city;
				$arr['province'] = $ret->province;
				$arr['country'] = $ret->country;
				$arr['language'] = $ret->language;
				$arr['headimgurl'] = $ret->headimgurl;
				//$arr['subscribe_time'] = $ret->subscribe_time;
				$this->logger->debug('$arr = ' . json_encode($arr));
				$GLOBALS['db']->autoExecute("wxch_user", $arr, 'UPDATE', " wxid='$wxid'");
			}
			$wuser = $GLOBALS['db']->getRow("select * from wxch_user where wxid='$wxid'");
			$this->logger->debug('$wuser = ' . json_encode($wuser));
			return $wuser;
		}
	}

	/**
	 * 客服消息推送
	 *
	 * @param string $api_url
	 * @param string $post_msg
	 * @return object json对象
	 */
	public function send($api_url, $post_msg){
		$this->logger->debug('$url = ' . $api_url . $this->access_token);
		$this->logger->debug('$post_msg = ' . $post_msg);

		$ret_json = $this->curl_grab_page($api_url . $this->access_token, $post_msg);
		$this->logger->debug('$ret_json = ' . $ret_json);
		$ret = json_decode($ret_json);
		if($ret->errmsg && $ret->errmsg != 'ok'){
			$this->refresh_access_token(true);
			$this->logger->debug('$post_msg = ' . $post_msg);
			$ret = $this->curl_grab_page($api_url . $this->access_token, $post_msg);
			$this->logger->debug('$ret_json = ' . $ret);
			$ret = json_decode($ret_json);
		}
		return $ret;
	}

	/**
	 * get请求微信api
	 * @param unknown $api_url
	 */
	public function get($api_url){
		$this->logger->debug('$url = ' . $api_url . $this->access_token);
		$ret = $this->curl_get_contents($api_url . $this->access_token);
		$this->logger->debug('$ret_json = ' . $ret);
		$ret = json_decode($ret, true);
		if($ret->errmsg && $ret->errmsg != 'ok'){
			$this->refresh_access_token(true);
			$this->logger->debug('$url = ' . $api_url . $this->access_token);
			$ret = $this->curl_get_contents($api_url . $this->access_token);
			$this->logger->debug('$ret_json = ' . $ret);
			$ret = json_decode($ret, true);
		}
		return $ret;
	}

	/**
	 * 刷新access_token
	 */
	public function refresh_access_token($force = false){
		$time = time();
		if(($time - $this->dateline) > 3600 || $force){
			$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $this->appid . "&secret=" . $this->appsecret;
			$this->logger->debug($url);
			$ret_json = $this->curl_get_contents($url);
			$this->logger->debug($ret_json);
			$ret = json_decode($ret_json);
			if($ret->access_token){
				$GLOBALS['db']->query("UPDATE `wxch_config` SET `access_token` = '$ret->access_token',`dateline` = '$time' WHERE `wxch_config`.`id` =1;");
				$this->access_token = $ret->access_token;
				$this->logger->debug('refresh access_token = ' . $this->access_token);
				$this->dateline = $time;
			}
		}
	}
//  备份
 	public function get_access_token(){
 	    $ret = $GLOBALS['db']->getRow("SELECT `access_token` FROM `wxch_config`");
 	    $this->access_token = $ret['access_token'];
 	    return $this->access_token;
 	}
	
	public function get_access_token1(){
	    $url="http://shopdev.byhill.com/dou/index.php?s=/Home/Public/acction_lw.html";
	    $access_token=$this->curl_get_contents($url);
	    var_dump($access_token);exit;
// 		$ret = $GLOBALS['db']->getRow("SELECT `access_token` FROM `wxch_config`");
// 		$this->access_token = $ret['access_token'];
// 		return $this->access_token;
	}

	public function get_appid_appsecret(){
		$ret = $GLOBALS['db']->getRow("SELECT * FROM `wxch_config` WHERE `id` = 1");
		$this->appid = $ret['appid'];
		$this->appsecret = $ret['appsecret'];
		$this->dateline = $ret['dateline'];
	}

	public function curl_get_contents($url){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		curl_setopt($ch, CURLOPT_USERAGENT, _USERAGENT_);
		curl_setopt($ch, CURLOPT_REFERER, _REFERER_);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		if(defined('CURLOPT_IPRESOLVE') && defined('CURL_IPRESOLVE_V4')){
			curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
		}
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		$r = curl_exec($ch);
		curl_close($ch);
		return $r;
	}

	public function curl_grab_page($url, $data, $proxy = '', $proxystatus = '', $ref_url = ''){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		if($proxystatus == 'true'){
			curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, true);
			curl_setopt($ch, CURLOPT_PROXY, $proxy);
		}
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_URL, $url);
		if(!empty($ref_url)){
			curl_setopt($ch, CURLOPT_HEADER, true);
			curl_setopt($ch, CURLOPT_REFERER, $ref_url);
		}
		if(defined('CURLOPT_IPRESOLVE') && defined('CURL_IPRESOLVE_V4')){
			curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
		}
		curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		ob_start();
		return curl_exec($ch);
		ob_end_clean();
		curl_close($ch);
		unset($ch);
	}

	public function https_request($url, $data = null){
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_TIMEOUT, 60);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
		if(!empty($data)){
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		}
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($curl);
		curl_close($curl);
		return $output;
	}

	/**
	 * 从微信上下载图片
	 * @param string $url 图片地址
	 * @return unknown
	 */
	function downloadimageformweixin($url){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		ob_start();
		curl_exec($ch);
		$return_content = ob_get_contents();
		ob_end_clean();

		$return_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		return $return_content;
	}
	
	
	/**
	 * 生成永久二维码的场景ID（MD5的32位字符串）
	 */
	function cr_limit_scene_id(){
		return md5(time() . mt_rand(0,1000));
//		return (string)(time() . mt_rand(0,1000));
	}
}