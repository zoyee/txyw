<?php
/**
 * 关键字处理程序
 * @author liuzhy
 *
 */

class keyword_processor{
	protected static $logger;
	protected static $db;
	protected static $wxch_cfg;
	protected static $keyword_lang;
	protected static $m_url;
	protected static $base_url;
	protected static $article_url;
	protected static $oauth_location;
	protected static $oauth_state;
	protected $weixin_api;
	protected $keyword_name;

	function __construct($db){
		self::$db = $db;
		if(!$this->logger){
			$this->logger = LoggerManager::getLogger(basename(__FILE__));
		}
		
		if(! self::$wxch_cfg){
			self::$wxch_cfg = array();
			$cfgs = $db->getAll("SELECT `cfg_name`,`cfg_value` FROM `wxch_cfg`");
			foreach ($cfgs as $row){
				self::$wxch_cfg[$row['cfg_name']] = $row['cfg_value'];
			}
			self::$m_url = self::$wxch_cfg['baseurl'] . self::$wxch_cfg['murl'];
			self::$base_url = self::$wxch_cfg['baseurl'];
			self::$article_url = self::$wxch_cfg['baseurl'] . self::$wxch_cfg['murl'] . self::$wxch_cfg['article'];
			self::$oauth_location = self::$wxch_cfg['baseurl'] . 'wechat/oauth/wxch_oauths.php?uri=';
			self::$oauth_state =  self::$wxch_cfg['oauth'];
		}
		if(! $this->keyword_lang){
			self::$keyword_lang = array();
			$cfgs = $db->getAll("SELECT `lang_name`,`lang_value` FROM `wxch_lang`");
			foreach ($cfgs as $row){
				self::$keyword_lang[$row['lang_name']] = $row['lang_value'];
			}
		}
	}

	/**
	 * 关键字处理进程
	 * 处理成功返回0，未找到匹配的处理方式返回消息记录ID:$belong
	 */
	public function process($keyword, $fromUsername = null, $toUsername = null, $belong = null){
		$this->logger->debug('$keyword = ' . $keyword);
		//纯数字的关键词，加“_”前缀对应
		$preg = '/^[0-9]*$/';
		if (preg_match($preg, $keyword) && file_exists(dirname(__FILE__). '/key_' . $keyword . '.php')){
			$keyword = 'key_' . $keyword;
		}
		if(file_exists(dirname(__FILE__). '/' . $keyword . '.php')){
			require_once (dirname(__FILE__). '/' . $keyword . '.php');
			$time = time();
			$keyword = strtolower($keyword);
			$child_processor = new $keyword;
			$child_processor->process($child_processor, $fromUsername, $toUsername, $belong);
			$child_processor->plusPoint($child_processor, $keyword, $fromUsername);
			$this->logger->debug("处理完成");
			return 0;
		} else {
			//自定义关键字回复
			require_once (dirname(__FILE__). '/keywords_auto.php');
			$child_processor = new keywords_auto();
			if($child_processor->process($keyword, $fromUsername, $toUsername, $belong)){
				$child_processor->plusPoint($child_processor, $keyword, $fromUsername);
				$this->logger->debug("处理完成");
				return 0;
			}else{
				//关键字回复未定义
				$this->logger->debug("关键字回复未定义，查询商品");
				require_once (dirname(__FILE__). '/query_goods.php');
				$child_processor = new query_goods();
				if($child_processor->process($keyword, $fromUsername, $toUsername, $belong)){
					$child_processor->plusPoint($child_processor, $keyword, $fromUsername);
					$this->logger->debug("处理完成");
					return 0;
				}else{
					return $belong;
				}
				
			}
		}

	}
	
	/**
	 * 插入微信交互日志(消息返回)
	 * @param DB $db 数据库连接
	 * @param string $fromUsername openid
	 * @param string $w_message 日志内容
	 * @param int $time 时间戳
	 * @param int $belong 消息返回对应的消息ID
	 */
	protected function insert_wmessage($fromUsername, $w_message, $belong) {
		$time = time();
		$w_message = mysql_real_escape_string($w_message);
		$sql = "INSERT INTO `wxch_message` (`wxid`, `w_message`, `belong`, `dateline`) VALUES
			('$fromUsername', '$w_message', '$belong', '$time');";
		self::$db -> query($sql);
	}

	/**
	 * 积分加减
	 * @param processor $child_processor 关键字处理器
	 * @param DB $db 数据库连接
	 * @param string $fromUsername openid
	 */
	protected function plusPoint($child_processor, $fromUsername){
		$child_processor->plusPoint($child_processor, $fromUsername);
	}

	/**
	 * 通用积分加减处理方法
	 * @param processor $child_processor 关键字处理器
	 * @param DB $db 数据库连接
	 * @param string $fromUsername openid
	 * @return int -1：没有配置积分  -2：积分功能关闭  0：次数超限  1：成功但无积分变更  2：成功变更积分
	 */
	protected function common_plusPoint($fromUsername){
		$keyword = get_class($this);

		$row = self::$db -> getRow("SELECT `point_name`, `point_value`, `autoload`, `point_num` FROM `wxch_point` WHERE `point_name` = '$keyword'");
		if (empty($row)) {
			//没有配置积分
			return -1;
		}
		if($row['autoload'] != 'yes'){
			//积分功能关闭
			return -2;
		}

		$potin_name = $row['point_name'];
		$points = intval($row['point_value']);
		$num = $row['point_num'];

		$row = self::$db -> getRow("SELECT `user_id` FROM `ecs_users` WHERE `wxid` ='$fromUsername'");
		$user_id = $row['user_id'];

		$sql = "SELECT * FROM `wxch_point_record` WHERE `point_name` = '$keyword' AND `wxid` = '$fromUsername'";
		$record = self::$db -> getRow($sql);
		$lasttime=strtotime(date('Y-m-d',time())) + 24*60*60;
		$point_change = 0;
		$record_flag = 0;
		$time = time();

		if (empty($record)) {
			//首次访问功能
			$insert_sql = "INSERT INTO `wxch_point_record` (`wxid`, `point_name`, `num`, `lasttime`, `datelinie`) VALUES
					('$fromUsername', '$keyword' , 1, $lasttime, $time);";
			self::$db -> query($insert_sql);
			if($points > 0){
				log_account_change($user_id, 0, 0, 0, $points, $this->keyword_name, ACT_OTHER);
				return 2;
			}
			return 1;
		} else {
			$lasttime_sql = "SELECT `lasttime`, `num` FROM `wxch_point_record` WHERE `point_name` = '$keyword' AND `wxid` = '$fromUsername'";
			$row = self::$db -> getRow($lasttime_sql);
			$db_lasttime = $row['lasttime'];
			$record_num = $row['num'];

			if ($time > $db_lasttime) {
				//隔天访问功能
				$update_sql = "UPDATE `wxch_point_record` SET `num` = 0,`lasttime` = '$lasttime' WHERE `wxid` ='$fromUsername' and `point_name` = '$keyword';";
				self::$db -> query($update_sql);
				if($points > 0){
					log_account_change($user_id, 0, 0, 0, $points, $this->keyword_name, ACT_OTHER);
					return 2;
				}
				return 1;
			}
			if ($num == 0 || $record_num < $num) {
				$update_sql = "UPDATE `wxch_point_record` SET `num` = `num`+1,`lasttime` = '$lasttime' WHERE `point_name` = '$keyword' AND `wxid` ='$fromUsername';";
				self::$db -> query($update_sql);
				if($points > 0){
					log_account_change($user_id, 0, 0, 0, $points, $this->keyword_name, ACT_OTHER);
					return 2;
				}
				return 1;
			}else{
				return 0;
			}
		}

	}

	protected function https_request($url, $data = null) {
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
		if (!empty($data)){
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		}
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($curl);
		curl_close($curl);
		return $output;
	}

	/**
	 * 获取推荐商品列表
	 * @param string $type 推荐类型
	 * @param string $fromUsername openid
	 */
	protected function get_recommend_goods($type, $fromUsername){
		$recommon_cond = "";
		if($type == "new"){
			$recommend_cond = "`is_new` = 1";
		} else if($type == "best"){
			$recommend_cond = "`is_best` = 1";
		} else if($type == "hot"){
			$recommend_cond = "`is_hot` = 1";
		} else if($type == "promote"){
			$recommend_cond = "`is_promote` = 1";
		}
		$thistable = $GLOBALS['ecs']->table('goods');
		$base_img_path = self::$base_url;
		$goods_is = '';
		if (self::$wxch_cfg['goods'] == 'false') {
			$goods_is = ' AND is_delete = 0 AND is_on_sale = 1';
		}

		$affiliate_id = self::$db -> getOne("SELECT `affiliate` FROM `wxch_user` WHERE `wxid` = '$fromUsername'");
		if ($affiliate_id >= 1) {
			$affiliate = '&u=' . $affiliate_id;
		}

		$query_sql = "SELECT * FROM $thistable WHERE $recommend_cond $goods_is ORDER BY sort_order, last_update DESC  LIMIT 0 , 6 ";
		$ret = self::$db->getAll($query_sql);
		$ArticleCount = count($ret);
		$items = '';
		if($ArticleCount >= 1){
			foreach($ret as $v){
				if(self::$wxch_cfg['imgpath'] == 'local'){
					$v['thumbnail_pic'] = $base_img_path . $v['goods_img'];
				}elseif(self::$wxch_cfg['imgpath'] == 'server'){
					$v['thumbnail_pic'] = $v['goods_img'];
				}
				if(self::$oauth_state == 'true'){
					$goods_url = self::$oauth_location . self::$m_url . 'goods.php?id=' . $v['goods_id'] . $affiliate;
				}elseif(self::$oauth_state == 'false'){
					$goods_url = self::$m_url . 'goods.php?id=' . $v['goods_id'] . '&wxid=' . $fromUsername . $affiliate;
				}
				$items .= "<item>
                 <Title><![CDATA[" . $v['goods_name'] . "]]></Title>
                 <PicUrl><![CDATA[" . $v['thumbnail_pic'] . "]]></PicUrl>
                 <Url><![CDATA[" . $goods_url . "]]></Url>
                 </item>";
			}
		}
		$data = array();
		$data['ArticleCount'] = $ArticleCount;
		$data['items'] = $items;
		return $data;
	}
	
	/**
	 * 
	 */
	protected function get_keywords_articles($kws_id) {
		$sql = "SELECT `article_id` FROM `wxch_keywords_article` WHERE `kws_id` = '$kws_id'";
		$ret = self::$db -> getAll($sql);
		$articles = '';
		foreach($ret as $v) {
			$articles .= $v['article_id'] . ',';
		}
		$length = strlen($articles)-1;
		$articles = substr($articles, 0, $length);
		if (!empty($articles)) {
			$sql2 = "SELECT `article_id`,`title`,`file_url`,`description` FROM " . $GLOBALS['ecs'] -> table('article') . " WHERE `article_id` IN ($articles) ORDER BY `add_time` DESC ";
			$res = self::$db -> getAll($sql2);
		}
		return $res;
	}
}

class Template{
	public static $textTpl = "<xml>
		<ToUserName><![CDATA[%s]]></ToUserName>
		<FromUserName><![CDATA[%s]]></FromUserName>
		<CreateTime>%s</CreateTime>
		<MsgType><![CDATA[text]]></MsgType>
		<Content><![CDATA[%s]]></Content>
		<FuncFlag>0</FuncFlag>
		</xml>";

	public static $newsTpl = "<xml>
		<ToUserName><![CDATA[%s]]></ToUserName>
		<FromUserName><![CDATA[%s]]></FromUserName>
		<CreateTime>%s</CreateTime>
		<MsgType><![CDATA[news]]></MsgType>
		<ArticleCount>%s</ArticleCount>
		<Articles>
		%s
		</Articles>
		<FuncFlag>0</FuncFlag>
		</xml>";

	public static $serviceTpl = "<xml>
		<ToUserName><![CDATA[%s]]></ToUserName>
		<FromUserName><![CDATA[%s]]></FromUserName>
		<CreateTime>%s</CreateTime>
		<MsgType><![CDATA[transfer_customer_service]]></MsgType>
		</xml>";

	public static $imageTpl = "<xml>
		<ToUserName><![CDATA[%s]]></ToUserName>
		<FromUserName><![CDATA[%s]]></FromUserName>
		<CreateTime>%s</CreateTime>
		<MsgType><![CDATA[image]]></MsgType>
		<Image>
		<MediaId><![CDATA[%s]]></MediaId>
		</Image>
		</xml>";

	public static $voiceTpl = "<xml>
		<ToUserName><![CDATA[toUser]]></ToUserName>
		<FromUserName><![CDATA[fromUser]]></FromUserName>
		<CreateTime>12345678</CreateTime>
		<MsgType><![CDATA[voice]]></MsgType>
		<Voice>
		<MediaId><![CDATA[media_id]]></MediaId>
		</Voice>
		</xml>";
}



