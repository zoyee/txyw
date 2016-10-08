<?php
/**
 * 查询文章列表
 * @author liuzhy
 *
 */
class articles extends keyword_processor {

	function __construct(){
		$this->logger = LoggerManager::getLogger(basename(__FILE__));
		$this->weixin_api = new weixin_api();
		$this->keyword_name = "查询文章";
	}

	public function process($keyword = null, $fromUsername = null, $toUsername = null, $belong = null){
		$time = time();
		$param = $this->get_articles($keyword, $fromUsername);
		$this->logger->debug('$param = ' . json_encode($param));
		if($param){
			$this->weixin_api->send_custom_multi_news($fromUsername, $param);
			return 1;
		}else{
			$this->weixin_api->send_custom_message($fromUsername, "没有推文", 0);
			return 0;
		}
	}

	protected function plusPoint($child_processor, $fromUsername){
		return 0;
	}

	private function get_articles($keyword, $fromUsername){
		$affiliate_id = parent::$db -> getOne("SELECT `user_id` FROM " . $GLOBALS['ecs']->table('users') . " WHERE `wxid` = '$fromUsername'");
		if ($affiliate_id >= 1) {
			$affiliate = '&u=' . $affiliate_id;
		}
		
		$search_sql = "SELECT * FROM  " . $GLOBALS['ecs']->table('article') . " WHERE  is_open = 1 AND cat_id=15 ORDER BY article_type desc, add_time DESC LIMIT 0,8";
		$this->logger->debug('$search_sql = ' . $search_sql);
		$ret = parent::$db -> getAll($search_sql);
		$ArticleCount = count($ret);
		if ($ArticleCount >= 1) {
			$param = array();
			foreach($ret as $v) {
				if(parent::$wxch_cfg['imgpath'] == 'local'){
					$v['thumbnail_pic'] = parent::$base_url . $v['file_url'];
				}elseif(parent::$wxch_cfg['imgpath'] == 'server'){
					$v['thumbnail_pic'] = $v['file_url'];
				}
				if(parent::$oauth_state == 'true'){
					$article_url = parent::$oauth_location . parent::$m_url . 'article.php?id=' . $v['article_id'] . $affiliate;
				}elseif(parent::$oauth_state == 'false'){
					$article_url = parent::$m_url . 'article.php?id=' . $v['article_id'] . $affiliate;
				}
				
            	array_push($param, array(
					'title' => $v['title'],
					'description' => $v['description'],
					'url' => $article_url,
					'picurl' => $v['thumbnail_pic']
				));
			}
			return $param;
		} else {
			return null;
		}
	}
}