<?php
/**
 * 二维码场景处理程序
 * @author liuzhy
 *
 */
class scene_processor{
	protected static $logger;
	protected static $db;
	protected static $wxch_cfg;
	protected static $keyword_lang;
	protected static $m_url;
	protected static $base_url;
	protected static $oauth_location;
	protected static $oauth_state;
	protected $keyword_name;
	protected $weixin_api;

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
			self::$oauth_location = self::$wxch_cfg['baseurl'] . 'wechat/oauth/wxch_oauths.php?uri=';
			self::$oauth_state =  self::$wxch_cfg['oauth'];
		}
		if(! self::$keyword_lang){
			self::$keyword_lang = array();
			$cfgs = $db->getAll("SELECT `lang_name`,`lang_value` FROM `wxch_lang`");
			foreach ($cfgs as $row){
				self::$keyword_lang[$row['lang_name']] = $row['lang_value'];
			}
		}
	}

	public function process($scene_id, $fromUsername = null, $toUsername = null, $belong = null, $is_new_user = false){
		$this->logger->debug('$scene_id = ' . $scene_id);
		$scene = self::$db->getRow("select * from wxch_scene where scene_id='$scene_id' order by id desc LIMIT 0 , 1");
		if($scene){
			$param = json_decode($scene['param']);
			if(property_exists($param, 'time_limit')){
				if(file_exists(dirname(__FILE__). '/time_limit.php')){
					require_once (dirname(__FILE__). '/time_limit.php');
					$child_processor = new time_limit();
					if($child_processor->process($scene_id, $fromUsername, $toUsername, $belong, $is_new_user)){
						return;
					}
				}
			}
			if(property_exists($param, 'new_user_limit')){
				if(file_exists(dirname(__FILE__). '/new_user_limit.php')){
					require_once (dirname(__FILE__). '/new_user_limit.php');
					$child_processor = new new_user_limit();
					if($child_processor->process($scene_id, $fromUsername, $toUsername, $belong, $is_new_user)){
						return;
					}
				}
			}
			if(property_exists($param, 'scan_limit')){
				if(file_exists(dirname(__FILE__). '/scan_limit.php')){
					require_once (dirname(__FILE__). '/scan_limit.php');
					$child_processor = new scan_limit();
					if($child_processor->process($scene_id, $fromUsername, $toUsername, $belong, $is_new_user)){
						return;
					}
				}
			}
			if(property_exists($param, 'user_scan_limit')){
				if(file_exists(dirname(__FILE__). '/user_scan_limit.php')){
					require_once (dirname(__FILE__). '/user_scan_limit.php');
					$child_processor = new user_scan_limit();
					if($child_processor->process($scene_id, $fromUsername, $toUsername, $belong, $is_new_user)){
						return;
					}
				}
			}
			foreach($param as $key => $val){
				$key = strtolower($key);
				if($key == 'scan_limit' || $key == 'user_scan_limit' || $key == 'new_user_limit' || $key == 'time_limit') continue;
				if(file_exists(dirname(__FILE__). '/' . $key . '.php')){
					require_once (dirname(__FILE__). '/' . $key . '.php');
					$child_processor = new $key;
					$child_processor->process($val, $fromUsername, $toUsername, $belong, $is_new_user);
					$child_processor->plusPoint($val, $fromUsername);
				} else {
					//关键字回复未定义
					$this->logger->debug("场景属性未定义：" . $key);
				}
			}
			//更新扫码次数
			$this->logger->debug('更新扫码次数');
			self::$db->query("update wxch_scene set scan = scan + 1 where id='" . $scene['id'] . "'");
		}else{
			$this->logger->debug("没找到对应的场景映射!!");
		}
		$this->logger->debug("二维码场景处理完成");
	}

	/**
	 * 插入微信交互日志
	 * @param DB $db 数据库连接
	 * @param string $fromUsername openid
	 * @param string $w_message 日志内容
	 * @param int $time 时间戳
	 * @param unknown $belong
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
				$update_sql = "UPDATE `wxch_point_record` SET `num` = 0,`lasttime` = '$lasttime' WHERE `wxid` ='$fromUsername';";
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
}




