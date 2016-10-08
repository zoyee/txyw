<?php
/**
 * 推荐
 * @author liuzhy
 *
 */
class tuijian extends keyword_processor {

	function __construct(){
		$this->logger = LoggerManager::getLogger(basename(__FILE__));
		$this->keyword_name = "推荐";
	}

	public function process($processor = null, $fromUsername = null, $toUsername = null, $belong = null){
		$time = time();
		$data = $this->get_goods($fromUsername);
		$resultStr = sprintf(Template::$newsTpl, $fromUsername, $toUsername, $time, $data['ArticleCount'], $data['items']);
		$this->insert_wmessage($fromUsername, $resultStr, $belong);
		echo $resultStr;
		$this->logger->debug($resultStr);
	}

	protected function plusPoint($child_processor, $fromUsername){
		$this->common_plusPoint($fromUsername);
	}

	private function get_goods($fromUsername){
		$query_sql = "SELECT * FROM " . $GLOBALS['ecs']->table('article') . " WHERE `is_tuijian` = 1  ORDER BY add_time DESC  LIMIT 0 , 6 ";
		$ret = parent::$db -> getAll($query_sql);
		$ArticleCount = count($ret);
		if ($ArticleCount >= 1) {
			foreach($ret as $v) {

				if (parent::$oauth_state == 'true') {
					$article_url = parent::$oauth_location . parent::$m_url . 'article.php?id=' . $v['article_id'];
				} elseif ($oauth_state == 'false') {
					$article_url = parent::$m_url . 'article.php?id=' . $v['article_id'];
				}
				$items .= "<item>
			         <Title><![CDATA[" . $v['title'] ."]]></Title>
			         <PicUrl><![CDATA[" .$m_url.'../'. $v['file_url'] . "]]></PicUrl>
			         <Url><![CDATA[" . $article_url . "]]></Url>
			         </item>";
			}
		}
		$data = array();
		$data['ArticleCount'] = $ArticleCount;
		$data['items'] = $items;
		return $data;
	}
}