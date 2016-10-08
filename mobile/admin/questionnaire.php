<?php
define('IN_ECTOUCH', true);
//var_dump($_REQUEST['act']);exit;
require(dirname(__FILE__) . '/includes/init.php');
//$logger = LoggerManager::getLogger('user.php');
/* act操作项的初始化 */
if (empty($_REQUEST['act']))
{
    $_REQUEST['act'] = 'list';
}
else
{
    $_REQUEST['act'] = trim($_REQUEST['act']);
}

/* 初始化$exc对象 */
$exc = new exchange($ecs->table('questionnaire'), $db, 'id', 'title');

/*------------------------------------------------------ */
//-- 红包类型列表页面
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    check_authz_json('questionnaire');
    
    $smarty->assign('ur_here',     $_LANG['04_bonustype_list']);
    $smarty->assign('action_link', array('text' => $_LANG['questionnaire_add'], 'href' => 'questionnaire.php?act=add'));
    $smarty->assign('full_page',   1);

    $list = get_type_list();
    $tmp=array();
    foreach($list['item'] as $k=>$v){
        $tmp[$k]['id']=$v['id'];
        $tmp[$k]['title']=$v['title'];
        $tmp[$k]['mTime']=local_date('Y-m-d',$v['mTime']);
//         $tmp[$k]['start_time']=local_date('Y-m-d',$v['start_time']);
//         $tmp[$k]['end_time']=local_date('Y-m-d',$v['end_time']);
//         $tmp[$k]['']=$v[''];
//         $tmp[$k]['']=$v[''];
//         $tmp[$k]['']=$v[''];
//         $tmp[$k]['']=$v[''];
        $tmp[$k]['song']=$v['song'];
        $tmp[$k]['jine']=$v['jine'];
        $tmp[$k]['text']=$v['text'];
    }
    $smarty->assign('type_list',    $tmp);
    $smarty->assign('filter',       $list['filter']);
    $smarty->assign('record_count', $list['record_count']);
    $smarty->assign('page_count',   $list['page_count']);

    $sort_flag  = sort_flag($list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    assign_query_info();
    $smarty->display('questionnaire_type.htm');
}


/*------------------------------------------------------ */
//-- 问卷添加页面
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'add')
{
    admin_priv('questionnaire');

    $smarty->assign('lang',         $_LANG);
    $smarty->assign('ur_here',      $_LANG['bonustype_add']);
    $smarty->assign('action_link',  array('href' => 'questionnaire.php?act=list', 'text' => $_LANG['04_bonustype_list']));
    $smarty->assign('action',       'add');

    $smarty->assign('form_act',     'insert');
    $smarty->assign('cfg_lang',     $_CFG['lang']);

    $next_month = local_strtotime('+1 months');
    $bonus_arr['send_start_date']   = local_date('Y-m-d');
    $bonus_arr['use_start_date']    = local_date('Y-m-d');
    $bonus_arr['send_end_date']     = local_date('Y-m-d', $next_month);
    $bonus_arr['use_end_date']      = local_date('Y-m-d', $next_month);

    $smarty->assign('bonus_arr',    $bonus_arr);

    assign_query_info();
    $smarty->display('questionnaire_add.htm');
}
/*------------------------------------------------------ */
//-- 问卷添加的处理
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'insert')
{
    /* 去掉问卷标题前后的空格 */
    $type_name   = !empty($_POST['type_name']) ? trim($_POST['type_name']) : '';
    
    /* 获得日期信息 */
    $send_startdate = local_strtotime($_POST['send_start_date']);
    $send_enddate   = local_strtotime($_POST['send_end_date']);
    $send_startdate = time();
    $send_enddate   = time();
    $song=(int)$_POST['song'];
    $jine=$_POST['jine'];
    $text=$_POST['url'];
    $intro=$_POST['intro'];
    /* 插入数据库。 */
    $sql = "INSERT INTO ".$ecs->table('questionnaire')." (intro,title, mTime,cTime,start_time,end_time,song,jine,text)
    VALUES ('$intro',
    '$type_name',
    ".time().",
    ".time().",
    '$send_startdate',
    '$send_enddate',
    '$song',
    '$jine',
    '$text')";
    $aa=$db->query($sql);
    $sql="SELECT id FROM ".$ecs->table('questionnaire')." ORDER BY cTime desc";
    $id=mysql_fetch_assoc($db->query($sql));
    $q_id=$id['id'];
    //循环问题
    $num=(int)$_POST['num'];
    if($num){
        for($i=0;$i<$num;$i++){
            
                $name=$_POST['name_'.$i];
                $type = $_POST['type_'.$i];
                $extra=trim($_POST['daan_'.$i]);
                $sort=$_POST['paixu_'.$i];
                $is_must=(int)$_POST['is_must_'.$i];
            //添加问题表
            $sql="INSERT INTO ".$ecs->table('questionnaire_class')." (is_must,intro, extra,type,q_id,sort)
            VALUES ('$is_must',
            '$name',
            '$extra',
            '$type',
            '$q_id',
            '$sort')";
            $db->query($sql);
        }
    
    }
    
    $text=urldecode(json_encode($tmp));
    
    
    /* 记录管理员操作 */
    //admin_log($_POST['type_name'], 'add', 'questionnaire');

    /* 清除缓存 */
    clear_cache_files();

    /* 提示信息 */
    $link[0]['text'] = $_LANG['continus_add'];
    $link[0]['href'] = 'questionnaire.php?act=add';

    $link[1]['text'] = $_LANG['back_list'];
    $link[1]['href'] = 'questionnaire.php?act=list';

    sys_msg($_LANG['add'] . "&nbsp;" .$_POST['type_name'] . "&nbsp;" . $_LANG['attradd_succed'],0, $link);

}

/*------------------------------------------------------ */
//-- 问卷编辑页面
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'edit')
{
    admin_priv('questionnaire');

    /* 获取问卷类型数据 */
    $type_id = !empty($_GET['type_id']) ? intval($_GET['type_id']) : 0;
    $bonus_arr = $db->getRow("SELECT * FROM " .$ecs->table('questionnaire'). " WHERE id = '$type_id'");

//     $bonus_arr['send_start_date']   = local_date('Y-m-d', $bonus_arr['start_time']);
//     $bonus_arr['send_end_date']     = local_date('Y-m-d', $bonus_arr['end_time']);
    $sql="SELECT * FROM ".$ecs->table('questionnaire_class')." WHERE q_id=".$type_id;
    $text=$db->getAll($sql);
//     $smarty->assign('lang',        $_LANG);
//     $smarty->assign('ur_here',     $_LANG['bonustype_edit']);
//     $smarty->assign('action_link', array('href' => 'questionnaire.php?act=list&' . list_link_postfix(), 'text' => $_LANG['04_bonustype_list']));
    //判断是否为红包类型
    if($bonus_arr['song']==2){
        $sql="SELECT type_id,type_name,type_money FROM ".$ecs->table('bonus_type'). " WHERE type_id=".(int)$bonus_arr['jine'];
        $jine=mysql_fetch_assoc($db->query($sql));
        $bonus_arr['jine']=$jine['type_money'];
        $bonus_arr['type_id']=$jine['type_id'];
        $bonus_arr['type_name']=$jine['type_name'];
    }
    $smarty->assign('text',$text);
    $smarty->assign('form_act',    'update');
    $smarty->assign('bonus_arr',   $bonus_arr);
    $smarty->assign('nums',count($text));

    assign_query_info();
    $smarty->display('questionnaire_add.htm');
}
/*------------------------------------------------------ */
//-- 问卷编辑的处理
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'update')
{
    $type_id     = !empty($_POST['type_id'])    ? intval($_POST['type_id'])    : 0;
    /* 去掉问卷标题前后的空格 */
    $type_name   = !empty($_POST['type_name']) ? trim($_POST['type_name']) : '';
    //循环问题
    $num=(int)$_POST['num'];
    if($num){
        for($i=0;$i<$num;$i++){
            $name=$_POST['name_'.$i];
            $type = $_POST['type_'.$i];
            $extra=trim($_POST['daan_'.$i]);
            $sort=$_POST['paixu_'.$i];
            $is_must=(int)$_POST['is_must_'.$i];
            if(empty($_POST['daan_id_'.$i])){
                //添加问题表
                $sql="INSERT INTO ".$ecs->table('questionnaire_class')." (is_must,intro, extra,type,q_id,sort)
                VALUES ('$is_must',
                '$name',
                '$extra',
                '$type',
                '$type_id',
                '$sort')";
                $db->query($sql);
            }else{
                $sql = "UPDATE " .$ecs->table('questionnaire_class'). " SET ".
                "is_must       = '$is_must', ".
                "intro = '$name', ".
                "extra   = '$extra', ".
                "type       = $type," .
                "sort      = '$sort' " .
                "WHERE id   = ".(int)$_POST['daan_id_'.$i];
                mysql_query($sql);
            }
        }
    
    }
    /* 获得日期信息 */
//     $send_startdate = local_strtotime($_POST['send_start_date']);
//     $send_enddate   = local_strtotime($_POST['send_end_date']);

    $sql = "UPDATE " .$ecs->table('questionnaire'). " SET ".
        "intro       = '".$_POST['intro']."', ".
        "title       = '$type_name', ".
//         "start_time = '$send_startdate', ".
//         "end_time   = '$send_enddate', ".
        "mTime       = ".time().", ".
        "text      = '".$_POST['url']."',  " .
        "song      = '".$_POST['song']."' , " .
        "jine      = '".$_POST['jine']."' " .
        "WHERE id   = '$type_id'";
    mysql_query($sql);
    /* 记录管理员操作 */
    //admin_log($_POST['type_name'], 'edit', 'bonustype');

    /* 清除缓存 */
    clear_cache_files();

    /* 提示信息 */
    $link[] = array('text' => $_LANG['back_list'], 'href' => 'bonus.php?act=list&' . list_link_postfix());
    sys_msg($_LANG['edit'] .' '.$_POST['type_name'].' '. $_LANG['attradd_succed'], 0, $link);

}
/*------------------------------------------------------ */
//-- 删除问卷
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'remove')
{
    $id = intval($_REQUEST['id']);

    /* 检查权限 */
    check_authz_json('questionnaire');
    $sql="DELETE FROM ".$ecs->table('questionnaire'). " WHERE id=".$id;
    if ($db->query($sql))
    {
        clear_cache_files();
        //$goods_name = $exc->get_name(id);

        //admin_log(addslashes($goods_name), 'delete', 'questionnaire'); // 记录日志

        $url = 'questionnaire.php?act=list';
        
        //ecs_header("Loaction: $url\n");
        echo "<script>alert('删除成功');location.href='".$_SERVER["HTTP_REFERER"]."';</script>";
        
        exit;
    }
}

/*------------------------------------------------------ */
//-- 问卷详情
/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'q_list')
{
    $id = intval($_REQUEST['id']);
    $sql="SELECT id,title,song,jine,text FROM ".$ecs->table('questionnaire'). " WHERE id=".$id;
    $bonus_arr=mysql_fetch_assoc($db->query($sql));
    
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
    $smarty->assign('num',count($tmp));
    
    //调查结果
    $naire=array();
    $sql="SELECT count(*) num FROM ".$ecs->table('questionnaire_info')." WHERE question_id=$id";
    $nums=mysql_fetch_assoc($db->query($sql));
    
    $naire['zong']=$nums['num']/count($tmp);
    $daan=array();
    $i=0;
    foreach($tmp as $k=>$v){
        if($v['type']==1){
            $tmp=explode('+', trim($v['extra']));
            foreach($tmp as $k1=>$v1){
                $sql="SELECT count(*) num FROM ".$ecs->table('questionnaire_info')." WHERE question_id=$id AND survey_id=".$v['id']." AND answer LIKE '%|".$k1."|%'";
                
                $num=mysql_fetch_assoc($db->query($sql));
                $naire[$v['id']][$k1]=$num['num'];
            }
        }elseif ($v['type']==0){
            $tmp=explode('+', trim($v['extra']));
            foreach($tmp as $k1=>$v1){
                $sql="SELECT count(*) num FROM ".$ecs->table('questionnaire_info')." WHERE question_id=$id AND survey_id=".$v['id']." AND answer=".$k1;
                
                $num=mysql_fetch_assoc($db->query($sql));
                $naire[$v['id']][$k1]=$num['num'];
            }
        }elseif ($v['type']==2){
            $sql="SELECT uid,survey_id,answer FROM ".$ecs->table('questionnaire_info')." WHERE question_id=$id AND survey_id=".$v['id'];
            $tmp=$db->getAll($sql);
            foreach($tmp as $v){
                $daan[$i]['answer']=$v['answer'];
                $sql="SELECT intro FROM ".$ecs->table('questionnaire_class')." WHERE id=".$v['survey_id'];
                $wenti=mysql_fetch_assoc($db->query($sql));
                $daan[$i]['wenti']=$wenti['intro'];
                $sql="SELECT user_name FROM ".$ecs->table('users')." WHERE user_id=".$v['uid'];
                $name=mysql_fetch_assoc($db->query($sql));
                $daan[$i]['username']=$name['user_name'];
                $i++;
            }
        }
    }
    $smarty->assign('daan',$daan);
    $smarty->assign('naire',$naire);
    $smarty->display('questionnaire_info.htm');
}
/*------------------------------------------------------ */
//-- 问卷编辑的处理
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'ajax'){
    $name=$_REQUEST['name'];
    $sql="SELECT type_id,type_name FROM ".$ecs->table('bonus_type')." WHERE type_name like '%$name%' ORDER BY type_id DESC limit 0,6";
    $names=$db->getAll($sql);
    $json=array();
    foreach($names as $k=>$v){
        $json[$k]['type_id']=$v['type_id'];
        $json[$k]['type_name']=urlencode($v['type_name']);
    }
    echo urldecode(json_encode($json));
}


/**
 * 获取问卷列表
 * @access  public
 * @return void
 */
function get_type_list()
{
    
    /* 获得所有红包类型的发放数量 */
    $sql = "SELECT id, COUNT(*) AS used_count".
        " FROM " .$GLOBALS['ecs']->table('questionnaire') ;
    $res = $GLOBALS['db']->query($sql);
    $used_arr = array();
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        $used_arr[$row['id']] = $row['used_count'];
    }

    $result = get_filter();
    if ($result === false)
    {
        /* 查询条件 */
//         $filter['sort_by']    = empty($_REQUEST['sort_by']) ? 'type_id' : trim($_REQUEST['sort_by']);
//         $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

        $sql = "SELECT COUNT(*) FROM ".$GLOBALS['ecs']->table('questionnaire');
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        /* 分页大小 */
        $filter = page_and_size($filter);

        $sql = "SELECT * FROM " .$GLOBALS['ecs']->table('questionnaire'). " ORDER BY mTime desc";

        set_filter($filter, $sql);
    }
    else
    {
        $sql    = $result['sql'];
        $filter = $result['filter'];
    }
    $arr = array();
    $res = $GLOBALS['db']->selectLimit($sql, $filter['page_size'], $filter['start']);

    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        $row['send_by'] = $GLOBALS['_LANG']['send_by'][$row['send_type']];
        $row['send_count'] = isset($sent_arr[$row['type_id']]) ? $sent_arr[$row['type_id']] : 0;
        $row['use_count'] = isset($used_arr[$row['type_id']]) ? $used_arr[$row['type_id']] : 0;

        $arr[] = $row;
    }

    $arr = array('item' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

    return $arr;
}

