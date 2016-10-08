<?php

/**
 * ECSHOP 管理员信息以及权限管理程序
 * ============================================================================
 * 版权所有 2005-2010 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liuhui $
 * $Id: privilege.php 17063 2010-03-25 06:35:46Z liuhui $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . 'supplier/includes/cls_image.php');
$logger = LoggerManager::getLogger('privilege.php');

/* act操作项的初始化 */
if (empty($_REQUEST['act']))
{
    $_REQUEST['act'] = 'login';
}
else
{
    $_REQUEST['act'] = trim($_REQUEST['act']);
}

/* 初始化 $exc 对象 */
$exc = new exchange($ecs->table("admin_user"), $db, 'user_id', 'user_name');

/*------------------------------------------------------ */
//-- 退出登录
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'logout')
{
    /* 清除cookie */
    setcookie('ECSCP[admin_id]',   '', 1);
    setcookie('ECSCP[admin_pass]', '', 1);

    $sess->destroy_session();

    $_REQUEST['act'] = 'login';
}

/*------------------------------------------------------ */
//-- 登陆界面
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'login')
{
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Cache-Control: no-cache, must-revalidate");
    header("Pragma: no-cache");

    if ((intval($_CFG['captcha']) & CAPTCHA_ADMIN) && gd_version() > 0)
    {
        $smarty->assign('gd_version', gd_version());
        $smarty->assign('random',     mt_rand());
    }

    $smarty->display('login.htm');
}

elseif($_REQUEST['act'] == 'upload_head_image'){
	//上传商品详情的图片
	$fname = $_REQUEST['fname'];
	$image = wxch_upload_file($_FILES[$fname], 'suppliers');
	$logger->debug($image);
	if($image){
		//将商品设置为未审核
		make_json_result($image);
	}else{
		make_json_error('上传失败');
	}
}

elseif($_REQUEST['act'] == 'get_regions'){
	//上传商品详情的图片
	$pid = $_REQUEST['pid'];
	$list = get_regions(0, $pid);
	exit(json_encode($list));
}

/*------------------------------------------------------ */
//-- 验证登陆信息
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'signin')
{
    if (!empty($_SESSION['captcha_word']) && (intval($_CFG['captcha']) & CAPTCHA_ADMIN))
    {
        include_once(ROOT_PATH . 'includes/cls_captcha.php');

        /* 检查验证码是否正确 */
        $validator = new captcha();
        if (!empty($_POST['captcha']) && !$validator->check_word($_POST['captcha']))
        {
            sys_msg($_LANG['captcha_error'], 1);
        }
    }

    $_POST['username'] = isset($_POST['username']) ? trim($_POST['username']) : '';
    $_POST['password'] = isset($_POST['password']) ? trim($_POST['password']) : '';
	
	$sql="SELECT `ec_salt` FROM ". $ecs->table('admin_user') ."WHERE user_name = '" . $_POST['username']."'";
    $ec_salt =$db->getOne($sql);
    
    if(!empty($ec_salt))
    {
         /* 检查密码是否正确 */
         $sql = "SELECT user_id, user_name, password, last_login, action_list, last_login,suppliers_id,ec_salt".
            " FROM " . $ecs->table('admin_user') .
            " WHERE user_name = '" . $_POST['username']. "' AND password = '" . md5(md5($_POST['password']).$ec_salt) . "'";
    }
    else
    {
         /* 检查密码是否正确 */
         $sql = "SELECT user_id, user_name, password, last_login, action_list, last_login,suppliers_id,ec_salt".
            " FROM " . $ecs->table('admin_user') .
            " WHERE user_name = '" . $_POST['username']. "' AND password = '" . md5($_POST['password']) . "'";
    }	

    /* 检查密码是否正确 */
//    $sql = "SELECT user_id, user_name, password, last_login, action_list, last_login, suppliers_id,ec_salt".
//            " FROM " . $ecs->table('admin_user') .
//            " WHERE user_name = '" . $_POST['username']. "' AND password = '" . md5($_POST['password']) . "'";
    $row = $db->getRow($sql);
	
    if ($row)
    {
        // 检查是否为供货商的管理员 所属供货商是否有效
        if (!empty($row['suppliers_id']))
        {
            $supplier_is_check = suppliers_list_info(' is_check = 1 AND suppliers_id = ' . $row['suppliers_id']);
            if (empty($supplier_is_check))
            {
                sys_msg($_LANG['login_disable'], 1);
            }
        }elseif(!in_array($row['user_name'], explode(',', SUPPLIER_USERS))){
        	 sys_msg('您没有权限登录系统', 1);
        }

        // 登录成功
        set_admin_session($row['user_id'], $row['user_name'], $row['action_list'], $row['last_login']);
        $_SESSION['suppliers_id'] = $row['suppliers_id'];
        
        if(empty($row['ec_salt']))
	    {
			$ec_salt=rand(1,9999);
			$new_possword=md5(md5($_POST['password']).$ec_salt);
            $db->query("UPDATE " .$ecs->table('admin_user').
                 " SET ec_salt='" . $ec_salt . "', password='" .$new_possword . "'".
                 " WHERE user_id='$_SESSION[admin_id]'");
		}
				

        if($row['action_list'] == 'all' && empty($row['last_login']))
        {
            $_SESSION['shop_guide'] = true;
        }

        // 更新最后登录时间和IP
        $db->query("UPDATE " .$ecs->table('admin_user').
                 " SET last_login='" . gmtime() . "', last_ip='" . real_ip() . "'".
                 " WHERE user_id='$_SESSION[admin_id]'");

        if (isset($_POST['remember']))
        {
            $time = gmtime() + 3600 * 24 * 365;
            setcookie('ECSCP[admin_id]',   $row['user_id'],                            $time);
            setcookie('ECSCP[admin_pass]', md5($row['password'] . $_CFG['hash_code']), $time);
        }

        // 清除购物车中过期的数据
        clear_cart();

        ecs_header("Location: ./index.php\n");

        exit;
    }
    else
    {
        sys_msg($_LANG['login_faild'], 1);
    }
}

/*------------------------------------------------------ */
//-- 管理员列表页面
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'list')
{
    /* 模板赋值 */
    $smarty->assign('ur_here',     $_LANG['admin_list']);
    $smarty->assign('action_link', array('href'=>'privilege.php?act=add', 'text' => $_LANG['admin_add']));
    $smarty->assign('full_page',   1);
    $smarty->assign('admin_list',  get_admin_userlist());

    /* 显示页面 */
    assign_query_info();
    $smarty->display('privilege_list.htm');
}

/*------------------------------------------------------ */
//-- 查询
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    $smarty->assign('admin_list',  get_admin_userlist());

    make_json_result($smarty->fetch('privilege_list.htm'));
}

/*------------------------------------------------------ */
//-- 添加管理员页面
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'add')
{
    /* 检查权限 */
    admin_priv('admin_manage');

     /* 模板赋值 */
    $smarty->assign('ur_here',     $_LANG['admin_add']);
    $smarty->assign('action_link', array('href'=>'privilege.php?act=list', 'text' => $_LANG['admin_list']));
    $smarty->assign('form_act',    'insert');
    $smarty->assign('action',      'add');
    $smarty->assign('select_role',  get_role_list());

    /* 显示页面 */
    assign_query_info();
    $smarty->display('privilege_info.htm');
}

/*------------------------------------------------------ */
//-- 添加管理员的处理
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'insert')
{
    admin_priv('admin_manage');

    /* 判断管理员是否已经存在 */
    if (!empty($_POST['user_name']))
    {
        $is_only = $exc->is_only('user_name', stripslashes($_POST['user_name']));

        if (!$is_only)
        {
            sys_msg(sprintf($_LANG['user_name_exist'], stripslashes($_POST['user_name'])), 1);
        }
    }

    /* Email地址是否有重复 */
    if (!empty($_POST['email']))
    {
        $is_only = $exc->is_only('email', stripslashes($_POST['email']));

        if (!$is_only)
        {
            sys_msg(sprintf($_LANG['email_exist'], stripslashes($_POST['email'])), 1);
        }
    }

    /* 获取添加日期及密码 */
    $add_time = gmtime();
    
    $password  = md5($_POST['password']);
    $role_id = '';
    $action_list = '';
    if (!empty($_POST['select_role']))
    {
        $sql = "SELECT action_list FROM " . $ecs->table('role') . " WHERE role_id = '".$_POST['select_role']."'";
        $row = $db->getRow($sql);
        $action_list = $row['action_list'];
        $role_id = $_POST['select_role'];
    }

        $sql = "SELECT nav_list FROM " . $ecs->table('admin_user') . " WHERE action_list = 'all'";
        $row = $db->getRow($sql);


    $sql = "INSERT INTO ".$ecs->table('admin_user')." (user_name, email, password, add_time, nav_list, action_list, role_id) ".
           "VALUES ('".trim($_POST['user_name'])."', '".trim($_POST['email'])."', '$password', '$add_time', '$row[nav_list]', '$action_list', '$role_id')";

    $db->query($sql);
    /* 转入权限分配列表 */
    $new_id = $db->Insert_ID();

    /*添加链接*/
    $link[0]['text'] = $_LANG['go_allot_priv'];
    $link[0]['href'] = 'privilege.php?act=allot&id='.$new_id.'&user='.$_POST['user_name'].'';

    $link[1]['text'] = $_LANG['continue_add'];
    $link[1]['href'] = 'privilege.php?act=add';

    sys_msg($_LANG['add'] . "&nbsp;" .$_POST['user_name'] . "&nbsp;" . $_LANG['action_succeed'],0, $link);

    /* 记录管理员操作 */
    admin_log($_POST['user_name'], 'add', 'privilege');
 }
 
elseif($_REQUEST['act'] == 'edit_pwd')
{
	$user_id = $_SESSION['admin_id'];
	if(empty($user_id)){
		sys_msg('登录超时，请重新登录!', 1);
	}
	
	$admin_info = admin_info();
    $smarty->assign('admin_info',    $admin_info);
    $smarty->display('change_pwd.htm');
}

elseif($_REQUEST['act'] == 'save_pwd'){
	$user_id = $_SESSION['admin_id'];
	if(empty($user_id)){
		sys_msg('登录超时，请重新登录!', 1);
	}
	$admin_info = admin_info();
    
    /* 变量初始化 */
    $pwd_old = $_REQUEST['pwd_old'];
    $pwd_new = $_REQUEST['pwd_new'];
    $pwd_confirm = $_REQUEST['pwd_confirm'];
    
    if(empty($pwd_old) || empty($pwd_new) || empty($pwd_confirm)){
    	sys_msg("密码不能为空！", 1);
    }
    
    if($pwd_new != $pwd_confirm){
    	sys_msg("两次输入的密码不一致！", 1);
    }
    
    $sql="SELECT `ec_salt` FROM ". $ecs->table('admin_user') ."WHERE user_id = '" . $user_id ."'";
    $ec_salt = $db->getOne($sql);
    if($ec_salt){
    	/* 检查密码是否正确 */
         $sql = "SELECT user_id, user_name, password".
            " FROM " . $ecs->table('admin_user') .
            " WHERE user_id = '$user_id' AND password = '" . md5(md5($pwd_old).$ec_salt) . "'";
    } else {
         /* 检查密码是否正确 */
         $sql = "SELECT user_id, user_name, password".
            " FROM " . $ecs->table('admin_user') .
            " WHERE user_id = '$user_id' AND password = '" . md5($pwd_old) . "'";
    }
    
    
    $row = $db->getRow($sql);
    if(empty($row)){
    	sys_msg("原密码错误！", 1);
    }	

    $ec_salt = mt_rand(1, 1000);
	$db->autoExecute($ecs->table('admin_user'), array(
		'password' => md5(md5($pwd_new).$ec_salt),
		'ec_salt' => $ec_salt
	), 'UPDATE', "user_id = '$user_id'");


   sys_msg("操作成功!", 0);
}
/*------------------------------------------------------ */
//-- 编辑管理员信息
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit')
{
	$user_id = $_SESSION['admin_id'];
	if(empty($user_id)){
		sys_msg('登录超时，请重新登录!', 1);
	}
	
    /* 不能编辑demo这个管理员 */
    if ($_SESSION['admin_name'] == 'demo')
    {
       $link[] = array('text' => $_LANG['back_list'], 'href'=>'privilege.php?act=list');
       sys_msg($_LANG['edit_admininfo_cannot'], 0, $link);
    }

    $province_list = get_regions(1, '86');
    $smarty->assign('province_list', $province_list);

	$suppliers = array();

    /* 取得供货商信息 */
    $sql = "SELECT s.* FROM " . $ecs->table('suppliers') . " s inner join " . $ecs->table('admin_user') . " au on s.suppliers_id = au.suppliers_id and au.user_id = '$user_id'";
    $suppliers = $db->getRow($sql);
    if (count($suppliers) <= 0)
    {
        sys_msg('suppliers does not exist');
    }

    /* 取得所有管理员，*/
    /* 标注哪些是该供货商的('this')，哪些是空闲的('free')，哪些是别的供货商的('other') */
    /* 排除是办事处的管理员 */
    $sql = "SELECT user_id, user_name, CASE
            WHEN suppliers_id = '$id' THEN 'this'
            WHEN suppliers_id = 0 THEN 'free'
            ELSE 'other' END AS type
            FROM " . $ecs->table('admin_user') . "
            WHERE agency_id = 0
            AND action_list <> 'all'";
    $suppliers['admin_list'] = $db->getAll($sql);

    $smarty->assign('ur_here', $_LANG['edit_suppliers']);
    $smarty->assign('action_link', array('href' => 'suppliers.php?act=list', 'text' => $_LANG['suppliers_list']));

    $smarty->assign('form_action', 'update');
    $smarty->assign('suppliers', $suppliers);

    assign_query_info();

    $smarty->display('privilege_info.htm');
}

/*------------------------------------------------------ */
//-- 更新管理员信息
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'update' || $_REQUEST['act'] == 'update_self')
{

    $suppliers = array('id'   => trim($_POST['id']));

        $suppliers['new'] = array('suppliers_name'   => trim($_POST['suppliers_name']),
                           'suppliers_desc'   => trim($_POST['suppliers_desc']),
                           'phone'			  => trim($_POST['phone']),
                           'weixin'			  => trim($_POST['weixin']),
                           'head_img'		  => trim($_POST['head_img']),
                           'province'		  => trim($_POST['province']),
                           'city'			  => trim($_POST['city']),
                           'district'		  => trim($_POST['district'])
                           );

        /* 取得供货商信息 */
        $sql = "SELECT * FROM " . $ecs->table('suppliers') . " WHERE suppliers_id = '" . $suppliers['id'] . "'";
        $suppliers['old'] = $db->getRow($sql);
        if (empty($suppliers['old']['suppliers_id']))
        {
            sys_msg('suppliers does not exist');
        }

        /* 判断名称是否重复 */
        $sql = "SELECT suppliers_id
                FROM " . $ecs->table('suppliers') . "
                WHERE suppliers_name = '" . $suppliers['new']['suppliers_name'] . "'
                AND suppliers_id <> '" . $suppliers['id'] . "'";
        if ($db->getOne($sql))
        {
            sys_msg($_LANG['suppliers_name_exist']);
        }

        /* 保存供货商信息 */
        $db->autoExecute($ecs->table('suppliers'), $suppliers['new'], 'UPDATE', "suppliers_id = '" . $suppliers['id'] . "'");

        /* 记日志 */
        admin_log($suppliers['old']['suppliers_name'], 'edit', 'suppliers');

        /* 清除缓存 */
        clear_cache_files();

        /* 提示信息 */
        sys_msg("操作成功！", 0, null);

}

/*------------------------------------------------------ */
//-- 编辑个人资料
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'modif')
{
    /* 不能编辑demo这个管理员 */
    if ($_SESSION['admin_name'] == 'demo')
    {
       $link[] = array('text' => $_LANG['back_admin_list'], 'href'=>'privilege.php?act=list');
       sys_msg($_LANG['edit_admininfo_cannot'], 0, $link);
    }

    include_once('includes/inc_menu.php');

    /* 包含插件菜单语言项 */
    $sql = "SELECT code FROM ".$ecs->table('plugins');
    $rs = $db->query($sql);
    while ($row = $db->FetchRow($rs))
    {
        /* 取得语言项 */
        if (file_exists(ROOT_PATH.'plugins/'.$row['code'].'/languages/common_'.$_CFG['lang'].'.php'))
        {
            include_once(ROOT_PATH.'plugins/'.$row['code'].'/languages/common_'.$_CFG['lang'].'.php');
        }

        /* 插件的菜单项 */
        if (file_exists(ROOT_PATH.'plugins/'.$row['code'].'/languages/inc_menu.php'))
        {
            include_once(ROOT_PATH.'plugins/'.$row['code'].'/languages/inc_menu.php');
        }
    }

    foreach ($modules AS $key => $value)
    {
        ksort($modules[$key]);
    }
    ksort($modules);

    foreach ($modules AS $key => $val)
    {
        $menus[$key]['label'] = $_LANG[$key];
        if (is_array($val))
        {
            foreach ($val AS $k => $v)
            {
                $menus[$key]['children'][$k]['label']  = $_LANG[$k];
                $menus[$key]['children'][$k]['action'] = $v;
            }
        }
        else
        {
            $menus[$key]['action'] = $val;
        }
    }

    /* 获得当前管理员数据信息 */
    $sql = "SELECT user_id, user_name, email, nav_list ".
           "FROM " .$ecs->table('admin_user'). " WHERE user_id = '".$_SESSION['admin_id']."'";
    $user_info = $db->getRow($sql);

    /* 获取导航条 */
    $nav_arr = (trim($user_info['nav_list']) == '') ? array() : explode(",", $user_info['nav_list']);
    $nav_lst = array();
    foreach ($nav_arr AS $val)
    {
        $arr              = explode('|', $val);
        $nav_lst[$arr[1]] = $arr[0];
    }

    /* 模板赋值 */
    $smarty->assign('lang',        $_LANG);
    $smarty->assign('ur_here',     $_LANG['modif_info']);
    $smarty->assign('action_link', array('text' => $_LANG['admin_list'], 'href'=>'privilege.php?act=list'));
    $smarty->assign('user',        $user_info);
    $smarty->assign('menus',       $modules);
    $smarty->assign('nav_arr',     $nav_lst);

    $smarty->assign('form_act',    'update_self');
    $smarty->assign('action',      'modif');

    /* 显示页面 */
    assign_query_info();
    $smarty->display('privilege_info.htm');
}

/*------------------------------------------------------ */
//-- 为管理员分配权限
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'allot')
{
    include_once(ROOT_PATH . 'languages/' .$_CFG['lang']. '/admin/priv_action.php');

    admin_priv('allot_priv');
    if ($_SESSION['admin_id'] == $_GET['id'])
    {
        admin_priv('all');
    }

    /* 获得该管理员的权限 */
    $priv_str = $db->getOne("SELECT action_list FROM " .$ecs->table('admin_user'). " WHERE user_id = '$_GET[id]'");

    /* 如果被编辑的管理员拥有了all这个权限，将不能编辑 */
    if ($priv_str == 'all')
    {
       $link[] = array('text' => $_LANG['back_admin_list'], 'href'=>'privilege.php?act=list');
       sys_msg($_LANG['edit_admininfo_cannot'], 0, $link);
    }

    /* 获取权限的分组数据 */
    $sql_query = "SELECT action_id, parent_id, action_code,relevance FROM " .$ecs->table('admin_action').
                 " WHERE parent_id = 0";
    $res = $db->query($sql_query);
    while ($rows = $db->FetchRow($res))
    {
        $priv_arr[$rows['action_id']] = $rows;
    }

    /* 按权限组查询底级的权限名称 */
    $sql = "SELECT action_id, parent_id, action_code,relevance FROM " .$ecs->table('admin_action').
           " WHERE parent_id " .db_create_in(array_keys($priv_arr));
    $result = $db->query($sql);
    while ($priv = $db->FetchRow($result))
    {
        $priv_arr[$priv["parent_id"]]["priv"][$priv["action_code"]] = $priv;
    }

    // 将同一组的权限使用 "," 连接起来，供JS全选
    foreach ($priv_arr AS $action_id => $action_group)
    {
        $priv_arr[$action_id]['priv_list'] = join(',', @array_keys($action_group['priv']));

        foreach ($action_group['priv'] AS $key => $val)
        {
            $priv_arr[$action_id]['priv'][$key]['cando'] = (strpos($priv_str, $val['action_code']) !== false || $priv_str == 'all') ? 1 : 0;
        }
    }

    /* 赋值 */
    $smarty->assign('lang',        $_LANG);
    $smarty->assign('ur_here',     $_LANG['allot_priv'] . ' [ '. $_GET['user'] . ' ] ');
    $smarty->assign('action_link', array('href'=>'privilege.php?act=list', 'text' => $_LANG['admin_list']));
    $smarty->assign('priv_arr',    $priv_arr);
    $smarty->assign('form_act',    'update_allot');
    $smarty->assign('user_id',     $_GET['id']);

    /* 显示页面 */
    assign_query_info();
    $smarty->display('privilege_allot.htm');
}

/*------------------------------------------------------ */
//-- 更新管理员的权限
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'update_allot')
{
    admin_priv('admin_manage');

    /* 取得当前管理员用户名 */
    $admin_name = $db->getOne("SELECT user_name FROM " .$ecs->table('admin_user'). " WHERE user_id = '$_POST[id]'");

    /* 更新管理员的权限 */
    $act_list = @join(",", $_POST['action_code']);
    $sql = "UPDATE " .$ecs->table('admin_user'). " SET action_list = '$act_list', role_id = '' ".
           "WHERE user_id = '$_POST[id]'";

    $db->query($sql);
    /* 动态更新管理员的SESSION */
    if ($_SESSION["admin_id"] == $_POST['id'])
    {
        $_SESSION["action_list"] = $act_list;
    }

    /* 记录管理员操作 */
    admin_log(addslashes($admin_name), 'edit', 'privilege');

    /* 提示信息 */
    $link[] = array('text' => $_LANG['back_admin_list'], 'href'=>'privilege.php?act=list');
    sys_msg($_LANG['edit'] . "&nbsp;" . $admin_name . "&nbsp;" . $_LANG['action_succeed'], 0, $link);

}

/*------------------------------------------------------ */
//-- 删除一个管理员
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'remove')
{
    check_authz_json('admin_drop');

    $id = intval($_GET['id']);

    /* 获得管理员用户名 */
    $admin_name = $db->getOne('SELECT user_name FROM '.$ecs->table('admin_user')." WHERE user_id='$id'");

    /* demo这个管理员不允许删除 */
    if ($admin_name == 'demo')
    {
        make_json_error($_LANG['edit_remove_cannot']);
    }

    /* ID为1的不允许删除 */
    if ($id == 1)
    {
        make_json_error($_LANG['remove_cannot']);
    }

    /* 管理员不能删除自己 */
    if ($id == $_SESSION['admin_id'])
    {
        make_json_error($_LANG['remove_self_cannot']);
    }

    if ($exc->drop($id))
    {
        $sess->delete_spec_admin_session($id); // 删除session中该管理员的记录

        admin_log(addslashes($admin_name), 'remove', 'privilege');
        clear_cache_files();
    }

    $url = 'privilege.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);

    ecs_header("Location: $url\n");
    exit;
}

/* 获取管理员列表 */
function get_admin_userlist()
{
    $list = array();
    $sql  = 'SELECT user_id, user_name, email, add_time, last_login '.
            'FROM ' .$GLOBALS['ecs']->table('admin_user').' ORDER BY user_id DESC';
    $list = $GLOBALS['db']->getAll($sql);

    foreach ($list AS $key=>$val)
    {
        $list[$key]['add_time']     = local_date($GLOBALS['_CFG']['time_format'], $val['add_time']);
        $list[$key]['last_login']   = local_date($GLOBALS['_CFG']['time_format'], $val['last_login']);
    }

    return $list;
}

/* 清除购物车中过期的数据 */
function clear_cart()
{
    /* 取得有效的session */
    $sql = "SELECT DISTINCT session_id " .
            "FROM " . $GLOBALS['ecs']->table('cart') . " AS c, " .
                $GLOBALS['ecs']->table('sessions') . " AS s " .
            "WHERE c.session_id = s.sesskey ";
    $valid_sess = $GLOBALS['db']->getCol($sql);

    // 删除cart中无效的数据
    $sql = "DELETE FROM " . $GLOBALS['ecs']->table('cart') .
            " WHERE session_id NOT " . db_create_in($valid_sess);
    $GLOBALS['db']->query($sql);
}

/* 获取角色列表 */
function get_role_list()
{
    $list = array();
    $sql  = 'SELECT role_id, role_name, action_list '.
            'FROM ' .$GLOBALS['ecs']->table('role');
    $list = $GLOBALS['db']->getAll($sql);
    return $list;
}

function wxch_upload_file($upload, $dir){
	$image = new cls_image();
	$res = $image->upload_image($upload, $dir);
	if($res)
	{
		return $res;
	}
	else
	{
		return false;
	}
}
?>