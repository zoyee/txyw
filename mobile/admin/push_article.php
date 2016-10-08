<?php

define('IN_ECTOUCH', true);

require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . 'include/fckeditor/fckeditor.php');
require_once(ROOT_PATH . 'include/cls_image_tianxin.php');
$logger = LoggerManager::getLogger('push_article.php');


/*------------------------------------------------------ */
//-- 文章作为推文推送到公众号
/*------------------------------------------------------ */
if($_REQUEST['act'] == 'push'){
//	$logger->debug($_REQUEST['act']);
	require_once (ROOT_PATH . 'api/weixin_api.php');
	check_authz_json('article_manage');
	$country = $_REQUEST['country'];
	$province = $_REQUEST['province'];
	$city = $_REQUEST['city'];
	$sex = $_REQUEST['sex'];
	$lonely = $_REQUEST['lonely'];
	
	$art_id = $_REQUEST['id'];
	$sql = "SELECT * FROM " .$ecs->table('article'). " WHERE article_id='$art_id'";
    $article = $db->getRow($sql);
    $weixin_api = new weixin_api();
    $image_local = ROOT_PATH . "../" . $article['file_url'];
    $thumb_media_id = $weixin_api->upload_image($image_local);
	
	//$content = htmlspecialchars($article['content']);
	$content = str_replace('"', '\"', $article['content']);
//	$logger->debug('$content = ' . $content);
	$post_msg = '{"articles": [{
            "thumb_media_id":"' . $thumb_media_id . '",
            "author":"' . $article["author"] . '",
			"title":"' . $article["title"] . '",
			"content_source_url":"' . $article["link"] . '",
			"content":"' . $content . '",
			"digest":"' . $article["description"] . '",
            "show_cover_pic":"0"
	}]}';
	$logger->debug(json_encode($post_msg));
	$media_id = $weixin_api->upload_news($post_msg);
	$logger->debug('$media_id = ' . $media_id);
	set_time_limit(0);
	if($media_id){
		$db->autoExecute($ecs->table('article'), array(
				"media_id" => $media_id
		), 'UPDATE', " article_id='$art_id'");
		
		$num = 0;
		$cl = 0;
		while($cl == 0 || $num == 10000){
			//查询粉丝
			$sql = "select distinct u.user_id, u.user_name, w1.wxid from wxch_user w1
						left join ecs_users u on w1.uname=u.user_name";
			$sql .= get_where_clause();
			$sql .= " limit " . ($cl * 10000) . ", 10000";
			$cl++;
			
			$logger->debug('$sql = ' . $sql);
			$all_fanc = $db->getAll($sql);
			
			$touser = array();
			$num = count($all_fanc);
			$logger->debug('$num = ' . $num);
			for($i = 0; $i<$num; $i++){
				if($i>0 && count($touser) % 5000 == 0){
					$send_param = array(
							"touser" => $touser,
							"msgtype" => "mpnews",
							"mpnews" => array("media_id" => $media_id)
					);
					
					$logger->debug(json_encode($send_param));
					$weixin_api->send_wxids($send_param);
					
					$touser = array();
					array_push($touser, $all_fanc[$i]['wxid']);
				}else{
					array_push($touser, $all_fanc[$i]['wxid']);
				}
			}
			
	//		$touser = array("oceJrwL_SukEsw5k6CEGbayYy8SA","oceJrwNEVAy8E_-EF-MJaqDDkJDc");
			$send_param = array(
					"touser" => $touser,
					"msgtype" => "mpnews",
					"mpnews" => array("media_id" => $media_id)
			);
			
			$logger->debug(json_encode($send_param));
			$weixin_api->send_wxids($send_param);
		}
		
		sys_msg('操作完成！', 0);  
	}else{
		sys_msg('操作失败，无法生成推文！', 1);  
	}
}
elseif($_REQUEST['act'] == 'pre_push'){
	$art_id = $_REQUEST['id'];
	$smarty->assign('art_id', $art_id);
	$country = $db->getAll("select DISTINCT country as name from wxch_user where country <>''");
	$smarty->assign('country_list', $country);
	$smarty->display('push_article.htm');
	
}
else if($_REQUEST['act'] == 'get_area'){
	$parent = $_REQUEST['parent'];
	$parent_name = $_REQUEST['parent_name'];
	$child_name = $_REQUEST['child_name'];
	$sql = "select DISTINCT $child_name as name from wxch_user where $parent_name ='$parent'  and $child_name <> ''";
//	$logger->debug($sql);
	$child_list = $db->getAll($sql);
//	$logger->debug(json_encode($child_list));
	echo json_encode($child_list);
}
else if($_REQUEST['act'] == 'get_fanc_num'){
	$sql = "select  count(distinct w1.wxid) as name from wxch_user w1
					left join ecs_users u on w1.uname=u.user_name";
	$sql .= get_where_clause();
	$logger->debug($sql);
	$num = $db->getOne($sql);
	$logger->debug($num);
	echo json_encode($num);
}

function get_where_clause(){
	$country = $_REQUEST['country'];
	$province = $_REQUEST['province'];
	$city = $_REQUEST['city'];
	$area_invert = $_REQUEST['area_invert'];
	$sex = $_REQUEST['sex'];
	$sex_invert = $_REQUEST['sex_invert'];
	$lonely = $_REQUEST['lonely'];
	$lonely_invert = $_REQUEST['lonely_invert'];
	$has_order = $_REQUEST['has_order'];
	$has_order_invert = $_REQUEST['has_order_invert'];
	$is_user = $_REQUEST['is_user'];
	
	$cond = '';
	$sql = " where w1.subscribe=1";
	if($is_user == 1){
		$sql .= " and w1.uname <> ''";
	}elseif($is_user == 2){
		$sql .= " and w1.uname = ''";
	}
	if($city){
		if(!is_array($city) || count($city) == 1){
			$cond = empty($area_invert) ? "=" : "<>";
			$city = is_array($city) ? $city[0] : $city;
			$sql .= " and w1.city $cond '$city'";
		}else{
			$cond = empty($area_invert) ? "in" : "not in";
			$city = "'" . join("','", $city) . "'";
			$sql .= " and w1.city $cond ($city)";
		}
	}elseif($province){
		if(!is_array($province) || count($province) == 1){
			$cond = empty($area_invert) ? "=" : "<>";
			$province = is_array($province) ? $province[0] : $province;
			$sql .= " and w1.province $cond '$province'";
		}else{
			$cond = empty($area_invert) ? "in" : "not in";
			$province = "'" . join("','", $province) . "'";
			$sql .= " and w1.province $cond ($province)";
		}
	}elseif($country){
		if(!is_array($country) || count($country) == 1){
			$cond = empty($area_invert) ? "=" : "<>";
			$country = is_array($country) ? $country[0] : $country;
			$sql .= " and w1.country $cond '$country'";
		}else{
			$cond = empty($area_invert) ? "in" : "not in";
			$country = "'" . join("','", $country) . "'";
			$sql .= " and w1.country $cond ($country)";
		}
	}
	if($sex){
		$cond = empty($sex_invert) ? "=" : "<>";
		$sql .= " and w1.sex $cond '$sex'";
	}
	if($lonely == 1){
		$cond = empty($lonely_invert) ? "not in" : "in";
		$sql .= " and u.user_id $cond(select distinct parent_id from ecs_users )";
	}elseif($lonely == 2){
		$cond = empty($lonely_invert) ? "in" : "not in";
		$sql .= " and u.user_id $cond(select distinct parent_id from ecs_users )";
	}
	if($has_order == 1){
		$cond = empty($has_order_invert) ? "in" : "not in";
		$sql .= " and u.user_id $cond(select distinct user_id from ecs_order_info )";
	}elseif($has_order == 2){
		$cond = empty($has_order_invert) ? "not in" : "in";
		$sql .= " and u.user_id $cond(select distinct user_id from ecs_order_info )";
	}
	
	return $sql;
}