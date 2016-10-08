<?php
define('IN_ECTOUCH', true);

require(dirname(__FILE__) . '/include/init.php');
require(ROOT_PATH . 'include/lib_weixintong.php');
$logger = LoggerManager::getLogger('index.php');
$user_id = $wechat->get_userid();
// $user_id=4301;
//判断用户是否登录
if(!$_SESSION['user_id']){
    tiaoUrl('请先用微信关注……','/mobile/subscribe.php');
	exit;
}
$id = intval($_REQUEST['id']);
 //$_SESSION['user_id'];

if(!$id){
    tiaoUrl('参数错误……');
    exit;
}

//判断当前用户是否已经回答问卷
$sql="SELECT count(id) id FROM ".$ecs->table('questionnaire_info'). " WHERE question_id = ".$id." AND (uid = $user_id)";
$num=mysql_fetch_assoc($db->query($sql));

//判断当前id问卷是否存在
$sql="SELECT id FROM ".$ecs->table('questionnaire'). " WHERE id = ".$id;
$is_p=mysql_fetch_assoc($db->query($sql));
if(!$is_p){
    tiaoUrl('该问卷不存在');
    exit;
}
//
if($_REQUEST['act']==''&&$id>0){
    $sql="SELECT id,title,intro,song,jine,text FROM ".$ecs->table('questionnaire'). " WHERE id=".$id;
    $bonus_arr=mysql_fetch_assoc($db->query($sql));
    //判断是否为红包类型
    if($bonus_arr['song']==2){
        $sql="SELECT type_money FROM ".$ecs->table('bonus_type'). " WHERE type_id=".(int)$bonus_arr['jine'];
        $jine=mysql_fetch_assoc($db->query($sql));
        $bonus_arr['jine']=$jine['type_money'];
    }
    //循环遍历出答案
    $sql="SELECT * FROM".$ecs->table('questionnaire_class'). " WHERE q_id=".$id." ORDER BY sort";
    
    $tmp=$db->getAll($sql);
    $text=array();
    $i=0;
    foreach($tmp as $k=>$v){
        $text[$k]['xuhao']=++$i;
        $text[$k]['id']=$v['id'];
        $text[$k]['intro']=$v['intro'];
        $text[$k]['is_must']=$v['is_must'];
        $text[$k]['type']=$v['type'];
        $text[$k]['extra']=explode('+', trim($v['extra']));
    }
    $smarty->assign('text',$text);
    $smarty->assign('bonus_arr',$bonus_arr);
    $smarty->assign('num',count($text));
    $smarty->assign('id_act',(int)$num['id']);
    
    
		/*青山老农修改*/
		$userid=$_SESSION['user_id'];
		if(!empty($userid)){
			$affiliate = unserialize($GLOBALS['_CFG']['affiliate']);
			$level_register_up = (float)$affiliate['config']['level_register_up'];
			$rank_points =  $GLOBALS['db']->getOne("SELECT rank_points FROM " . $GLOBALS['ecs']->table('users')."where user_id=".$_SESSION["user_id"]);
			if($rank_points>$level_register_up||$rank_points==$level_register_up){
			$url=$config['site_url']."mobile/naire.php?u=".$userid."&id=".$id;
			//20141204新增分享返积分
			//$dourl=$config['site_url']."mobile/re_url.php?user_id=".$userid;
			}else{
					$url="";
					//20141204新增分享返积分
					$dourl="";

			}
		}else{
			$url=$config['site_url']."mobile/";
			//20141204新增分享返积分
			//$dourl="";
		}
		require_once "wxjs/jssdk.php";
		$ret = $db->getRow("SELECT  *  FROM `wxch_config`");
		$jssdk = new JSSDK($appid=$ret['appid'], $ret['appsecret']);
		$signPackage = $jssdk->GetSignPackage();
		$smarty->assign('signPackage',  $signPackage);
		$smarty->assign('userid',  $userid);
		$smarty->assign('share_info',  $share_info);
		$smarty->assign('dourl',  $dourl);
		$smarty->assign('url',  $url);
		$smarty->assign('title',$bonus_arr['title']);
		$smarty->assign('imgUrl','http://shop.byhill.com/images/logo.jpg');
		$smarty->assign('centen',$bonus_arr['intro']);
		/*青山老农修改*/
    
    
    
    
	$smarty->display('naire.dwt');
}
/*------------------------------------------------------ */
//-- 调查问卷添加
/*------------------------------------------------------ */
elseif($_REQUEST['act']=='add'){
	$id=intval($_POST['id']);
	
	//判断当前用户是否已经回答问卷
	
	if((int)$num['id']){
	    tiaoUrl('每个用户只能回答一次');
	    exit;
	}
	
	foreach($_POST as $k=>$v){
		if($k>0){
			if(is_array($v)){
				$tmp='|';
				foreach($v as $v1){
					$tmp.=$v1."|";
				}
				$v=$tmp;
			}
			$sql="INSERT INTO ".$ecs->table('questionnaire_info')." (openid,uid,question_id,Time,survey_id,answer) 
			VALUES ('$openid',
			'$user_id',
			'$id',
		    ".time().",
		    '$k',
		    '$v')";
			//var_dump($v);
			$db->query($sql);
		}

	}
	//问卷调查结束添加积分或青豆
	$sql="SELECT song,jine FROM ".$ecs->table('questionnaire')." WHERE id=".$id;
	$arr=mysql_fetch_assoc($db->query($sql));
	if($arr['song']==1){
	    //添加青豆
	    $qd_jf=$arr['jine'];
	    $info="回答调查问卷送青豆";
	    log_account_change($user_id, 0, 0, 0, $qd_jf, $info);
	}else{
	    $qd_jf=(int)$arr['jine'];
	    $sql = "INSERT INTO " . $ecs->table('user_bonus') .
	    "(bonus_type_id, bonus_sn, user_id, used_time, order_id, emailed) " .
	    "VALUES ('$qd_jf', 0, '$user_id', 0, 0, " .BONUS_MAIL_SUCCEED. ")";
	    
		//var_dump($v);
		$db->query($sql);
	}
	//跳转到
	if(empty($_POST['url'])){
	    $url='/mobile';
	}else{
	    $url=$_POST['url'];
	}
	
	tiaoUrl('回答完成，感谢您的参与！',$url);
}

/*
 * 跳转设置
 * @param $str  string  提示内容
 * @param $url  string  跳转url
 * @param $time int     等待时间
 */
function tiaoUrl($str='系统错误',$url="/mobile",$time=3){
    echo "<div style='whdth:100%;margin-top:30px;text-align:center;'><span style='height:60px;text-align:center;font-size:30px;'>{$str}</span></div>";
    echo "<meta http-equiv='refresh' content='{$time}; URL={$url}'>";
}