<?php
/**
 * 推荐人
 * @author liuzhy
 *
 */
class user_id extends scene_processor{

	function __construct(){
		$this->logger = LoggerManager::getLogger(basename(__FILE__));
		$this->weixin_api = new weixin_api();
		$this->keyword_name = "推荐人";
	}

	public function process($val = null, $fromUsername = null, $toUsername = null, $belong = null, $is_new_user = false){
		$this->logger->debug('user_id = ' . $val);
		$aff_db = parent::$db -> getRow("SELECT * FROM " . $GLOBALS['ecs']->table('users') . " WHERE `user_id` = '$val'");
		$flag = !empty($aff_db);
		if($aff_db['wxid'] == $fromUsername){
			//不能自己推荐自己
			$flag = false;
		}else{
			$flag = true;
		}
		
		$join_time = date("Y-m-d H:i:s");
		$parent_id = parent::$db -> getOne("SELECT parent_id FROM " . $GLOBALS['ecs']->table('users') . " WHERE `wxid` = '$fromUsername'");
		$this->logger->debug('$parent_id = ' . $parent_id);
		$this->logger->debug('$flag = ' . $flag);
		if($is_new_user && $flag){
			$parent_id = $val;
			//绑定会员账号
			$ecs_usertable = $db -> prefix . 'users';
			$aff_update = "UPDATE " . $GLOBALS['ecs']->table('users') . " SET `parent_id` = '$parent_id' WHERE `wxid` = '$fromUsername';";
			parent::$db -> query($aff_update);
	
			$sql = "SELECT * FROM " . $GLOBALS['ecs']->table('users') . " WHERE `user_id` = '$parent_id'";
			$parent = parent::$db -> getRow($sql);
			$arr['sales1_id'] = $parent_id;
			$arr['sales2_id'] = $parent['sales1_id'];
			$arr['sales3_id'] = $parent['sales2_id'];
			$arr['sales4_id'] = $parent['sales3_id'];
			$arr['sales5_id'] = $parent['sales4_id'];
			$arr['sales6_id'] = $parent['sales5_id'];
			$arr['sales7_id'] = $parent['sales6_id'];
			$arr['sales8_id'] = $parent['sales7_id'];
			$arr['sales9_id'] = $parent['sales8_id'];
			parent::$db->autoExecute($GLOBALS['ecs']->table('users'), $arr, 'UPDATE', " `wxid` = '$fromUsername' ");
	
			//绑定微信账号
			parent::$db -> query("UPDATE `wxch_user` SET `affiliate` = '$parent_id' WHERE `wxid` = '$fromUsername' ");
	
			//发送新增窝友提醒
			$wuser = parent::$db -> getRow("select uname, nickname from wxch_user where wxid='$fromUsername'");
			$p_wxid = $GLOBALS['db']->GetOne("SELECT wxid FROM " . $GLOBALS['ecs']->table('users') . " WHERE user_id = '$parent_id'");
			$w_url = parent::$m_url . "distribute.php?act=fenxiao&rank=1&wxid=".$p_wxid;
			$param = array(
					'touser' => $p_wxid,
					'url' => $w_url,
					'first' => '打开您的会员中心看看，有金牌会员加入啦',
					'keyword1' => $wuser['nickname'],
					'keyword2' => $wuser['uname'],
					'keyword3' => $join_time,
					'remark' => '相似的灵魂早晚会相遇，加油哦',
			);
			$this->weixin_api->send_template_msg('tuijian_reply', $param);
			$this->logger->debug("推荐人逻辑处理完毕");		
		}
	}

	protected function plusPoint($val, $fromUsername){
		return 0;
	}


    /**
     * 检索上级发展了多少代言人，并发放红包
     * @param unknown $parent_id
     */
	function check_and_bonus_parent($db, $parent_id){
		$logger = LoggerManager::getLogger('callback-ent.php');
		$logger->debug('检索上级发展了多少下线，并发放红包 parent_id=' . $parent_id);
		$sql = "select count(1) from " . $db -> prefix . 'users' . " where parent_id='$parent_id'";
		$logger->debug($sql);
		$count = $db->getOne($sql);
		$sql = "select * from wxch_fenxiao_bonus where flag=1 order by child_num";
		$logger->debug($sql);
		$rows = $db->getAll($sql);
		foreach ($rows as $bonus_setting){
			if($count >= $bonus_setting['child_num']){
				$logger->debug($parent_id . "成功发展" . $bonus_setting['child_num'] . "个窝友");
				$sql = "select count(1) from " . $db -> prefix . 'user_bonus' . " where user_id='$parent_id' and bonus_type_id=" . $bonus_setting['bonus_type'];
				$logger->debug($sql);
				$bonus_status = $GLOBALS['db']->getOne($sql);
				if($bonus_status > 0){
					$logger->debug($parent_id . "成功发展" . $bonus_setting['child_num'] . "个窝友的红包已发放");
				}else{
					$sql = "select bonus_id from " . $db -> prefix . 'user_bonus' . " where bonus_type_id=" . $bonus_setting['bonus_type'] . " and user_id=0";
					$logger->debug($sql);
					$bonus_id = $db->getOne($sql);
					if($bonus_id){
						$db->query("update " . $db -> prefix . 'user_bonus' . " set user_id = " . $parent_id . " where bonus_id = " . $bonus_id);
						$logger->debug('给' . $parent_id . "发放红包");
						$logger->debug($sql);
                        $this -> check_and_bonus_parent_alert($db, $parent_id);
					}else{
						$logger->debug('没有足够的红包发放');
					}
				}
			}
		}
	}


 	/**
     * 发红包提醒
     * @param unknown $parent_id
     * Add by ZhangNu
     */
	function check_and_bonus_parent_alert($db, $parent_id){
        $logger = LoggerManager::getLogger('callback-ent.php');
        require_once(ROOT_PATH . 'mobile/include/lib_weixintong.php');

        $sql = "SELECT * FROM wxch_order WHERE id = 8 and autoload = 'yes'";
        $cfg_order = $db->getRow($sql);
        $cfg_baseurl = $db->getOne("SELECT cfg_value FROM wxch_cfg WHERE cfg_name = 'baseurl'");
        $http_ret1 = stristr($cfg_order['image'],'http://');
        $http_ret2 = stristr($cfg_order['image'], 'http:\\');
        $w_picurl = $cfg_baseurl."mobile/".$cfg_order['image'];
        if($http_ret1 or $http_ret2) {
        	$w_picurl = $cfg_order['image'];
        } else {
        	$w_picurl = $cfg_baseurl."mobile/".$cfg_order['image'];
        }

        $time = time();
	    $access_token = access_token($db);
	    $url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$access_token;
	    $sql_two="SELECT wxid FROM " . $GLOBALS['ecs']->table('users') . " WHERE user_id = '$parent_id'";
	    $wxid=$GLOBALS['db']->GetOne($sql_two);
// 	    $w_title="发展窝友满足条件送您红包啦，赶紧看看吧";
// 	    $w_description="这些窝友的消费您都将有提成哦";
	    $w_url= $cfg_baseurl . "mobile/user.php?act=bonus";
	    $post_msg = '{
           "touser":"'.$wxid.'",
           "msgtype":"news",
           "news":{
               "articles": [
                {
                    "title":"'.$cfg_order['title'].'",
                    "description":"'.$cfg_order['content'].'",
                    "url":"'.$w_url.'",
                    "picurl":"'.$w_picurl.'"
                }
                ]
           }
       }';
	    $logger->debug('$post_msg' . $post_msg);
	    $ret_json = curl_grab_page($url, $post_msg);
	    $ret = json_decode($ret_json);
	    if($ret->errmsg != 'ok')
	    {
		    $access_token = new_access_token($db);
		    $url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$access_token;
		    $ret_json = curl_grab_page($url, $post_msg);
		    $ret = json_decode($ret_json);
	    }
    }
}