<?php
/**
 * 关键词自动回复
 * @author liuzhy
 *
 */
class keywords_auto extends keyword_processor {

	function __construct(){
		$this->logger = LoggerManager::getLogger(basename(__FILE__));
		$this->keyword_name = "关键词自动回复";
	}

	public function process($keyword = null, $fromUsername = null, $toUsername = null, $belong = null){
		$time = time();
		//查询关键词自动回复定义
		$sql = "select * from wxch_keywords where status = 1";
		$all = parent::$db->getAll($sql);
		$match = 0;
		foreach($all as $k => $v) {
			$res_ks = explode(' ', $v['keyword']);
			if ($v['type'] == 1) {
				foreach($res_ks as $kk => $vv) {
					if ($vv == $keyword) {
						$contentStr = $v['contents'];
						$resultStr = sprintf(Template::$textTpl, $fromUsername, $toUsername, $time, $contentStr);
						echo $resultStr;
						$this->logger->debug($resultStr);
						parent::$db -> query("UPDATE `wxch_keywords` SET `count` = `count`+1 WHERE `id` =$v[id]");
						$this->insert_wmessage($fromUsername, $resultStr, $belong);
						$match = 1;
					}
				}
			} elseif ($v['type'] == 2) {
				foreach($res_ks as $kk => $vv) {
					if ($vv == $keyword) {
						$res = $this -> get_keywords_articles($v['id'], parent::$db);
						foreach($res as $vvv) {
							if (!empty($vvv['file_url'])) {
								$picurl = parent::$base_url . $vvv['file_url'];
							} else {
								$picurl = parent::$base_url . 'themes/default/images/logo.gif';
								if (!is_null($GLOBALS['_CFG']['template'])) {
									$picurl = $base_url . 'themes/' . $GLOBALS['_CFG']['template'] . '/images/logo.gif';
								}
							}
							$gourl = parent::$article_url . $vvv['article_id'];
							$ArticleCount = count($res);
							$items .= "<item>
	                             <Title><![CDATA[" . $vvv['title'] . "]]></Title>
	                             <Description><![CDATA[" . $vvv['description'] . "]]></Description>
	                             <PicUrl><![CDATA[" . $picurl . "]]></PicUrl>
	                             <Url><![CDATA[" . $gourl . "]]></Url>
	                             </item>";
						}
						$resultStr = sprintf(Template::$newsTpl, $fromUsername, $toUsername, $time, $ArticleCount, $items);
						echo $resultStr;
						$this->logger->debug($resultStr);
						parent::$db -> query("UPDATE `wxch_keywords` SET `count` = `count`+1 WHERE `id` =$v[id];");
						$this->insert_wmessage($fromUsername, $resultStr, $belong);
						$match = 1;
					}
				}
			}
			
		}
		
		return $match;
	}

	protected function plusPoint($child_processor, $fromUsername){
		$this->common_plusPoint($fromUsername);
	}
}