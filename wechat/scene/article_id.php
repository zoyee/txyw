<?php
/**
 * 推荐文章
 * @author liuzhy
 *
 */
class article_id extends scene_processor{

	function __construct(){
		$this->logger = LoggerManager::getLogger(basename(__FILE__));
		$this->weixin_api = new weixin_api();
		$this->keyword_name = "推荐文章";
	}

	public function process($val = null, $fromUsername = null, $toUsername = null, $belong = null, $is_new_user = false){
		$this->logger->debug('article_id = ' . $val);
//		$article = $this->get_article_info($val);
//		$param = array();
//		
//		if(parent::$wxch_cfg['imgpath'] == 'local'){
//			$article['thumbnail_pic'] = parent::$base_url . $article['file_url'];
//		}elseif(parent::$wxch_cfg['imgpath'] == 'server'){
//			$article['thumbnail_pic'] = $article['file_url'];
//		}
//		if(parent::$oauth_state == 'true'){
//			$article_url = parent::$oauth_location . parent::$m_url . 'article.php?id=' . $val;
//		}elseif(parent::$oauth_state == 'false'){
//			$article_url = parent::$m_url . 'article.php?id=' . $val;
//		}
//		array_push($param, array(
//			'title' => $article['title'],
//			'description' => $article['description'],
//			'url' => $article_url,
//			'picurl' => $article['thumbnail_pic']
//		));

		$param = $this->get_articles($val, $fromUsername);
		$this->logger->debug('$param = ' . json_encode($param));
		if($param){
			$this->weixin_api->send_custom_multi_news($fromUsername, $param);
			return 1;
		}else{
			$this->weixin_api->send_custom_message($fromUsername, "没有推文", 0);
			return 0;
		}
		
		$this->weixin_api->send_custom_multi_news($fromUsername, $param);
		$this->logger->debug("推荐文章逻辑处理完毕");
	}

	protected function plusPoint($val, $fromUsername){
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

	function get_article_info($article_id){
	    /* 获得文章的信息 */
	    $sql = "SELECT a.*, IFNULL(AVG(r.comment_rank), 0) AS comment_rank ".
	            "FROM " .$GLOBALS['ecs']->table('article'). " AS a ".
	            "LEFT JOIN " .$GLOBALS['ecs']->table('comment'). " AS r ON r.id_value = a.article_id AND comment_type = 1 ".
	            "WHERE a.is_open = 1 AND a.article_id = '$article_id' GROUP BY a.article_id";
	    $row = parent::$db->getRow($sql);
	
	    if ($row !== false){
	        $row['comment_rank'] = ceil($row['comment_rank']);                              // 用户评论级别取整
	        $row['add_time']     = local_date($GLOBALS['_CFG']['date_format'], $row['add_time']); // 修正添加时间显示
	
	        /* 作者信息如果为空，则用网站名称替换 */
	        if (empty($row['author']) || $row['author'] == '_SHOPHELP'){
	            $row['author'] = $GLOBALS['_CFG']['shop_name'];
	        }
	    }
	
	    return $row;
	}
}