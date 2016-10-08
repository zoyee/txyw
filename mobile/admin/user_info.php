<?php

/**
 * 获取用户详细资料
 * $Author: Zhangnu $
*/

define('IN_ECTOUCH', true);

require(dirname(__FILE__) . '/includes/init.php');
require(ROOT_PATH . 'include/lib_weixintong.php');

/*------------------------------------------------------ */
//-- 用户帐号信息
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'getUserInfo')
{
	
	$userid = $_REQUEST['userid'];
	
	
     /* 检查权限 */
    //admin_priv('users_manage');

    $userinfo = get_user_detail( $userid );

    /*
    assign_query_info();
    $smarty->assign('ur_here',          $_LANG['users_edit']);
    $smarty->assign('action_link',      array('text' => $_LANG['03_users_list'], 'href'=>'users.php?act=list&' . list_link_postfix()));
    $smarty->assign('user',             $user);
    $smarty->assign('form_action',      'update');
    $smarty->assign('special_ranks',    get_rank_list(true));
    
    

    $smarty->assign('user_list',    $kk);
    $smarty->assign('filter',       $user_list['filter']);
    $smarty->assign('record_count', $user_list['record_count']);
    $smarty->assign('page_count',   $user_list['page_count']);

    $sort_flag  = sort_flag($user_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);
    */

    make_json_result($userinfo);
}

/**
 *  返回用户列表数据
 *
 * @access  public
 * @param
 *
 * @return void
 */
function get_user_detail( $userid )
{
	

	/*
    $sql = "SELECT u.user_id, u.sex, u.birthday, u.pay_points, u.rank_points, u.user_rank , u.user_money, u.frozen_money, u.credit_line, u.parent_id, u2.user_name as parent_username, u.qq, u.msn,
        u.office_phone, u.home_phone, u.mobile_phone,u.password_account_replay,u.limit_account_replay, wxinfo.nickname, wxinfo.sex as wxsex, wxinfo.city,wxinfo.country,wxinfo.province,wxinfo.headimgurl".
            " FROM " .$ecs->table('users'). " u LEFT JOIN " . $ecs->table('users') . " u2 ON u.parent_id = u2.user_id "." LEFT JOIN wxch_user wxinfo on u.user_name = wxinfo.uname "."WHERE u.user_id='$userid'";

*/
	$sql = "SELECT u.user_id, u.sex, u.birthday, u.pay_points, u.rank_points, u.user_rank , u.user_money, u.frozen_money, u.credit_line, u.parent_id, u2.user_name as parent_username, u.qq, u.msn"
            .",u.office_phone, u.home_phone, u.mobile_phone,u.password_account_replay,u.limit_account_replay,u.reg_time"
            .", wxinfo.nickname, case when wxinfo.sex = '1' then '男' WHEN wxinfo.sex='2' then '女' ELSE '未知' END  as wxsex, wxinfo.city,wxinfo.country,wxinfo.province,wxinfo.headimgurl"
	        ." FROM " .$GLOBALS['ecs']->table('users'). " u LEFT JOIN " .$GLOBALS['ecs']->table('users') . " u2 ON u.parent_id = u2.user_id "." LEFT JOIN wxch_user wxinfo on u.user_name = wxinfo.uname "."WHERE u.user_id='$userid'";
	
    $row = $GLOBALS['db']->GetRow($sql);

    if ($row)
    {
        $user['user_id']        = $row['user_id'];
        $user['sex']            = $row['sex'];
        $user['birthday']       = date($row['birthday']);
        $user['pay_points']     = $row['pay_points'];
        $user['rank_points']    = $row['rank_points'];
        $user['user_rank']      = $row['user_rank'];
        $user['user_money']     = $row['user_money'];
        $user['frozen_money']   = $row['frozen_money'];
        $user['credit_line']    = $row['credit_line'];
        $user['formated_user_money'] = price_format($row['user_money']);
        $user['formated_frozen_money'] = price_format($row['frozen_money']);
        $user['parent_id']      = $row['parent_id'];
        $user['parent_username']= $row['parent_username'];
        $user['qq']             = $row['qq'];
        $user['msn']            = $row['msn'];
        $user['office_phone']   = $row['office_phone'];
        $user['home_phone']     = $row['home_phone'];
        $user['mobile_phone']   = $row['mobile_phone'];
		$user['password_account_replay']   = $row['password_account_replay'];
		
		$user['reg_time']   = local_date($GLOBALS['_CFG']['time_format'], $row['reg_time']);
		$user['nickname']   = $row['nickname'];
		$user['wxsex']   = $row['wxsex'];
		$user['city']   = $row['city'];
		$user['country']   = $row['country'];
		$user['province']   = $row['province'];
		$user['headimgurl']   = $row['headimgurl'];
		
		
		//$user['limit_account_replay']   = $row['limit_account_replay'];
    }
    else
    {
          $link[] = array('text' => $_LANG['go_back'], 'href'=>'users.php?act=list');
          sys_msg($_LANG['username_invalid'], 0, $links);
     }

    /* 取出注册扩展字段 */
    $sql = 'SELECT * FROM ' .$GLOBALS['ecs']->table('reg_fields') . ' WHERE type < 2 AND display = 1 AND id != 6 ORDER BY dis_order, id';
    $extend_info_list =  $GLOBALS['db']->getAll($sql);

    $sql = 'SELECT reg_field_id, content ' .
           'FROM ' . $GLOBALS['ecs']->table('reg_extend_info') .
           " WHERE user_id = $user[user_id]";
    $extend_info_arr = $GLOBALS['db']->getAll($sql);

    $temp_arr = array();
    foreach ($extend_info_arr AS $val)
    {
        $temp_arr[$val['reg_field_id']] = $val['content'];
    }

    foreach ($extend_info_list AS $key => $val)
    {
        switch ($val['id'])
        {
            case 1:     $extend_info_list[$key]['content'] = $user['msn']; break;
            case 2:     $extend_info_list[$key]['content'] = $user['qq']; break;
            case 3:     $extend_info_list[$key]['content'] = $user['office_phone']; break;
            case 4:     $extend_info_list[$key]['content'] = $user['home_phone']; break;
            case 5:     $extend_info_list[$key]['content'] = $user['mobile_phone']; break;
            default:    $extend_info_list[$key]['content'] = empty($temp_arr[$val['id']]) ? '' : $temp_arr[$val['id']] ;
        }
    }

    $user['extend_info_list'] = $extend_info_list;
    return $user;
}
?>