<?php
/**
 * 砸金蛋
 * @author liuzhy
 *
 */
class zjd extends keyword_processor{

	function __construct(){
		$this->logger = LoggerManager::getLogger(basename(__FILE__));
		$this->keyword_name = "砸金蛋";
	}

	public function process($processor = null, $fromUsername = null, $toUsername = null, $belong = null){
		$time = time();
		$data = $this -> egg($fromUsername);
		$resultStr = sprintf(Template::$newsTpl, $fromUsername, $toUsername, $time, $data['ArticleCount'], $data['items']);
		$this->insert_wmessage($fromUsername, $resultStr, $belong);
		echo $resultStr;
		$this->logger->debug($resultStr);
	}

	protected function plusPoint($child_processor, $fromUsername){
		$this->common_plusPoint($fromUsername);
	}

	private function egg($fromUsername) {
		$ret = parent::$db -> getAll("SELECT * FROM `wxch_prize` WHERE `fun` = 'egg' AND `status` = 1 ORDER BY `dateline` DESC ");
		$temp_count = count($ret);
		$time = time();
		if ($temp_count > 1) {
			foreach($ret as $k => $v) {
				if ($time <= $v['starttime']) {
					unset($ret[$k]);
				} elseif ($time >= $v['endtime']) {
					unset($ret[$k]);
				}
			}
		}
		$ArticleCount = 1;
		$prize_count = count($ret);
		$prize = $ret[array_rand($ret)];
		$wxch_lang = parent::$keyword_lang['prize_egg'];
		if ($prize_count <= 0) {
			$items = '<item>
             <Title><![CDATA[砸金蛋暂时未开放]]></Title>
             <PicUrl><![CDATA[]]></PicUrl>
             <Url><![CDATA[]]></Url>
             </item>';
		} else {
			$gourl = parent::$base_url . 'wechat/egg/index.php?pid=' . $prize['pid'] . '&wxid=' . $fromUsername;
			$PicUrl = parent::$base_url . 'wechat/egg/images/wx_bd.jpg';
			$items = "<item>
             <Title><![CDATA[砸金蛋]]></Title>
             <Description><![CDATA[" . $wxch_lang . "]]></Description>
             <PicUrl><![CDATA[" . $PicUrl . "]]></PicUrl>
             <Url><![CDATA[" . $gourl . "]]></Url>
             </item>";
		}
		$data = array();
		$data['ArticleCount'] = $ArticleCount;
		$data['items'] = $items;
		return $data;
	}
}