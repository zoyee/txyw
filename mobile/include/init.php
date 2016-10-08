<?php

/**
 * ECSHOP 前台公用文件
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: init.php 17217 2011-01-19 06:29:08Z liubo $
 */

if (!defined('IN_ECTOUCH'))
{
    die('Hacking attempt');
}

error_reporting(E_ALL);

if (__FILE__ == '') {
    die('Fatal error code: 0');
}
ob_start();
require(dirname(__FILE__) . '/../data/config.php');

if (!file_exists(ROOT_PATH . 'data/install.lock') && !file_exists(ROOT_PATH . 'include/install.lock') && !defined('NO_CHECK_INSTALL')) {
    header("Location: ./install/index.php\n");
    exit;
}

/* 初始化设置 */
@ini_set('memory_limit', '640M');
@ini_set('session.cache_expire', 180);
@ini_set('session.use_trans_sid', 0);
@ini_set('session.use_cookies', 1);
@ini_set('session.auto_start', 0);
@ini_set('display_errors', 1);

if (DIRECTORY_SEPARATOR == '\\') {
    @ini_set('include_path', '.;' . ROOT_PATH);
} else {
    @ini_set('include_path', '.:' . ROOT_PATH);
}

if (defined('DEBUG_MODE') == false) {
    define('DEBUG_MODE', 2);
}

if (PHP_VERSION >= '5.1' && !empty($timezone)) {
    date_default_timezone_set($timezone);
}

$php_self = isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
if ('/' == substr($php_self, -1)) {
    $php_self .= 'index.php';
}
define('PHP_SELF', $php_self);

require(ROOT_PATH . 'include/inc_constant.php');
require(ROOT_PATH . 'include/cls_ecshop.php');
require(ROOT_PATH . 'include/cls_error.php');
require(ROOT_PATH . 'include/lib_time.php');
require(ROOT_PATH . 'include/lib_base.php');
require(ROOT_PATH . 'include/lib_common.php');
require(ROOT_PATH . 'include/lib_main.php');
require(ROOT_PATH . 'include/lib_insert.php');
require(ROOT_PATH . 'include/lib_goods.php');
require(ROOT_PATH . 'include/lib_article.php');
require(ROOT_PATH . 'include/cls_wechat.php');


error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE );
define("LOG4PHP_DIR", ROOT_PATH . "../includes/log4php");
require_once (LOG4PHP_DIR . '/LoggerManager.php');
$logger = LoggerManager::getLogger('init.php');
$logger->debug('$_SERVER["REQUEST_URI"] = ' . $_SERVER["REQUEST_URI"]);
//$logger->debug('SESSION ID = '. $_SESSION->get_session_id());
// $logger->debug('$_REQUEST = '. json_encode($_REQUEST));
/* 对用户传入的变量进行转义操作。 */
if (!get_magic_quotes_gpc()) {
    if (!empty($_GET)) {
        $_GET = addslashes_deep($_GET);
    }
    if (!empty($_POST)) {
        $_POST = addslashes_deep($_POST);
    }

    $_COOKIE = addslashes_deep($_COOKIE);
    $_REQUEST = addslashes_deep($_REQUEST);
}
// $logger->debug('$_REQUEST = '. json_encode($_REQUEST));

/* 创建 ECSHOP 对象 */
$ecs = new ECS($db_name, $prefix);
define('DATA_DIR', $ecs->data_dir());
define('IMAGE_DIR', $ecs->image_dir());

/* 初始化数据库类 */
require(ROOT_PATH . 'include/cls_mysql.php');
$db = new cls_mysql($db_host, $db_user, $db_pass, $db_name);
$db->set_disable_cache_tables(array($ecs->table('sessions'), $ecs->table('sessions_data'), $ecs->table('cart')));
$db_host = $db_user = $db_pass = $db_name = NULL;

/* 创建错误处理对象 */
$err = new ecs_error('message.dwt');

/* 载入系统参数 */
$_CFG = load_config();
$_CFG['URL_HTTP_HOST'] = $config['site_url'];

/* 载入图标配置参数 */
if(!isset($_CFG['valid_special_icon_key'])){
	$special_key = array("best", "hot", "new", "promote");
	$special_cat_conf = $GLOBALS['db']->getOne("select value from ecs_touch_shop_config where code='special_cat_conf'");
	 if($special_cat_conf){
		$special_cat_conf = unserialize($special_cat_conf);
		$c = count($special_key);
		//特殊标签排序
		do{
			$done = 1;
			for ($i = 0; $i < $c; $i++){
				$key = $special_key[$i];
				if($special_cat_conf[$key]['show']==1 && $special_cat_conf[$key]['ico'] != ''){
					if($i == $c - 1) break;
					$key_next = $special_key[$i+1];
					if($special_cat_conf[$key]['order'] > $special_cat_conf[$key_next]['order']){
						$temp = $special_key[$i];
						$special_key[$i] = $special_key[$i+1];
						$special_key[$i+1] = $temp;
						$done = 0;
					}
				}else{
					array_splice($special_key, $i, 1);
					$c = count($special_key);
					$i--;
				}
			}
		}while(!$done);
	}
	$_CFG['valid_special_icon_key'] = $special_key;
	$special_cat_conf['left'] = round(intval($special_cat_conf['x'])/3.5,2) . '%';
	$special_cat_conf['top'] = round(intval($special_cat_conf['y'])/3.5,2) . '%';
	$special_cat_conf['width'] = round(intval($special_cat_conf['size'])/3.5,2) . '%';
	$_CFG['special_cat_conf'] = $special_cat_conf;
// 	$logger->debug(json_encode($special_cat_conf));
// 	$logger->debug(json_encode($special_key));
}


/* 载入语言文件 */
require(ROOT_PATH . 'lang/' . $_CFG['lang'] . '/common.php');

if ($_CFG['shop_closed'] == 1) {
    /* 商店关闭了，输出关闭的消息 */
    header('Content-type: text/html; charset=' . EC_CHARSET);

    die('<div style="margin: 150px; text-align: center; font-size: 14px"><p>' . $_LANG['shop_closed'] . '</p><p>' . $_CFG['close_comment'] . '</p></div>');
}

if (is_spider()) {
	$logger->debug('is_spider');
    /* 如果是蜘蛛的访问，那么默认为访客方式，并且不记录到日志中 */
    if (!defined('INIT_NO_USERS')) {
        define('INIT_NO_USERS', true);
        /* 整合UC后，如果是蜘蛛访问，初始化UC需要的常量 */
        if ($_CFG['integrate_code'] == 'ucenter') {
            $user = & init_users();
        }
    }
    $_SESSION = array();
    $_SESSION['user_id'] = 0;
    $_SESSION['user_name'] = '';
    $_SESSION['email'] = '';
    $_SESSION['user_rank'] = 0;
    $_SESSION['discount'] = 1.00;
}

if (!defined('INIT_NO_USERS')) {
 	$logger->debug('!defined(INIT_NO_USERS)');
    /* 初始化session */
    include(ROOT_PATH . 'include/cls_session.php');

    $sess = new cls_session($db, $ecs->table('sessions'), $ecs->table('sessions_data'));

    define('SESS_ID', $sess->get_session_id());
}
// $logger->debug('$_SESSION[user_id] = ' . $_SESSION['user_id']);
if (isset($_SERVER['PHP_SELF'])) {
    $_SERVER['PHP_SELF'] = htmlspecialchars($_SERVER['PHP_SELF']);
}
if (!defined('INIT_NO_SMARTY')) {
// 	$logger->debug('!defined(INIT_NO_SMARTY)');
    header('Cache-control: private');
    header('Content-type: text/html; charset=' . EC_CHARSET);

    /* 创建 Smarty 对象。 */
    require(ROOT_PATH . 'include/cls_template.php');
    $smarty = new cls_template;

    $smarty->cache_lifetime = $_CFG['cache_time'];
    $smarty->template_dir = ROOT_PATH . 'themes/' . $_CFG['template'];
    $smarty->cache_dir = ROOT_PATH . 'data/caches';
    $smarty->compile_dir = ROOT_PATH . 'data/compiled';

    if ((DEBUG_MODE & 2) == 2) {
        $smarty->direct_output = true;
        $smarty->force_compile = true;
    } else {
        $smarty->direct_output = false;
        $smarty->force_compile = false;
    }
    $smarty->direct_output = false;
    $smarty->force_compile = false;

    $smarty->assign('lang', $_LANG);
    $smarty->assign('ecs_charset', EC_CHARSET);
    if (!empty($_CFG['stylename'])) {
        $smarty->assign('ectouch_css', 'themes/' . $_CFG['template'] . '/style_' . $_CFG['stylename'] . '.css');
    } else {
        $smarty->assign('ectouch_css', 'themes/' . $_CFG['template'] . '/style.css');
    }
    $smarty->assign('ectouch_themes', 'themes/' . $_CFG['template']);
    $smarty->assign('site_url', $config['site_url']); //不带/结尾
    $smarty->assign("_CFG", $_CFG);
}

if (!defined('INIT_NO_USERS')) {
// 	$logger->debug('!defined(INIT_NO_USERS)');
    /* 会员信息 */
    $user = & init_users();

    if (!isset($_SESSION['user_id'])) {
        /* 获取投放站点的名称 */
        $site_name = isset($_GET['from']) ? htmlspecialchars($_GET['from']) : addslashes($_LANG['self_site']);
        $from_ad = !empty($_GET['ad_id']) ? intval($_GET['ad_id']) : 0;

        $_SESSION['from_ad'] = $from_ad; // 用户点击的广告ID
        $_SESSION['referer'] = stripslashes($site_name); // 用户来源

        unset($site_name);

        if (!defined('INGORE_VISIT_STATS')) {
            visit_stats();
        }
    }

    if (empty($_SESSION['user_id'])) {
// 	$logger->debug('empty($_SESSION[user_id])');
        if ($user->get_cookie()) {
            /* 如果会员已经登录并且还没有获得会员的帐户余额、积分以及优惠券 */
            if ($_SESSION['user_id'] > 0) {
                update_user_info();
            }
        } else {
            $_SESSION['user_id'] = 0;
            $_SESSION['user_name'] = '';
            $_SESSION['email'] = '';
            $_SESSION['user_rank'] = 0;
            $_SESSION['discount'] = 1.00;
            if (!isset($_SESSION['login_fail'])) {
                $_SESSION['login_fail'] = 0;
            }
        }
    }


    /* 设置推荐会员 */
    if (isset($_GET['u'])) {

        set_affiliate();
    }
    /* 设置推荐会员 */
    if (isset($_GET['wxid'])) {

        set_affiliate();
    }

//     $logger->debug('session 不存在，检查cookie');
//     $logger->debug('$_COOKIE[ECS][user_id] = ' . $_COOKIE['ECS']['user_id']);
    /* session 不存在，检查cookie */
    if (!empty($_COOKIE['ECS']['user_id']) && !empty($_COOKIE['ECS']['password'])) {
        // 找到了cookie, 验证cookie信息
        $sql = 'SELECT user_id, user_name, password ' .
                ' FROM ' . $ecs->table('users') .
                " WHERE user_id = '" . intval($_COOKIE['ECS']['user_id']) . "' AND password = '" . $_COOKIE['ECS']['password'] . "'";

        $row = $db->GetRow($sql);

        if (!$row) {
            // 没有找到这个记录
            $time = time() - 3600;
            setcookie("ECS[user_id]", '', $time, '/');
            setcookie("ECS[password]", '', $time, '/');
        } else {
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['user_name'] = $row['user_name'];
            update_user_info();
        }
    }

//     $logger->debug('$_SESSION = ' . json_encode($_SESSION));
    if (isset($smarty)) {
        $smarty->assign('ecs_session', $_SESSION);
    }
}

if ((DEBUG_MODE & 1) == 1) {
    error_reporting(E_ALL);
} else {
    error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
}
if ((DEBUG_MODE & 4) == 4) {
    include(ROOT_PATH . 'include/lib.debug.php');
}

/* 判断是否支持 Gzip 模式 */
if (!defined('INIT_NO_SMARTY') && gzip_enabled()) {
    ob_start('ob_gzhandler');
} else {
    ob_start();
}



//*20141208青山老农独家开发新增*/
	if (isset($_GET['u']))
    {
		$u=$_GET['u'];

    }else{
    	
    	
    	if(get_affiliate())
    	{
    		$u = get_affiliate();
    		
    	}
    	else{
    		$u="";
    	}
	}

	$share_info=array();
	//登陆的情况
	if(!empty($_SESSION['user_id'])){
		$user_id=$_SESSION['user_id'];
		$sql = "SELECT parent_id FROM ". $ecs->table('users') .  "where user_id ='$user_id'";
		$parent_id=$GLOBALS['db']->getOne($sql);
//		$logger->debug('$u1 = ' . $u);
		//登陆会员没上级
		if(empty($parent_id)){
			if($u){
				if($u== $user_id){
					$share_info=array();
				}else{
					$sql = "SELECT * FROM ".$GLOBALS['ecs']->table('users')." where user_id ='$u'";
					$user_info=$GLOBALS['db']->getRow($sql);
					$share_userid=$user_info['wxid'];
					$sql = "SELECT * FROM wxch_user where wxid ='$share_userid'";
					$share_info=$GLOBALS['db']->getRow($sql);
				}
			}
		}else{
			//登陆会员有上级
			$sql = "SELECT wxid FROM ". $ecs->table('users') .  "where user_id ='$parent_id'";
			$share_userid=$GLOBALS['db']->getOne($sql);
			$sql = "SELECT * FROM wxch_user where wxid ='$share_userid'";
			$share_info=$GLOBALS['db']->getRow($sql);
		}
		update_user_info();
	}else{
		//没登陆的情况
// 		$logger->debug('$_GET[u] = ' . $_GET['u']);
//		$logger->debug('$u2 = ' . $u);
		if($u){
				$sql = "SELECT * FROM ".$GLOBALS['ecs']->table('users')." where user_id ='$u'";
				$user_info=$GLOBALS['db']->getRow($sql);
				$share_userid=$user_info['wxid'];
				$sql = "SELECT * FROM wxch_user where wxid ='$share_userid'";
				$share_info=$GLOBALS['db']->getRow($sql);
		}
	}
// 	$logger->debug("用户等级user_rank=" . $_SESSION['user_rank']);


if($_SESSION['user_id'] && !defined('INIT_NO_SMARTY')){
	$login_user = $db->getRow("select u.user_id, u.user_name, wu.nickname, wu.headimgurl, wu.country, " .
			"wu.province, wu.city, wu.wxid, wu.subscribe from " . $ecs->table('users') . 
			" u inner join wxch_user wu on u.user_name=wu.uname where u.user_id='" . $_SESSION['user_id'] . "'");
	$logger->debug('$login_user = ' . json_encode($login_user));
	$smarty->assign('login_user', $login_user);
}
 	
$logger->debug('SESS_ID = ' . SESS_ID);
$wechat = new Wechat();

/*20141208青山老农独家开发新增*/
/* 检查是否是微信浏览器访问 */
function is_wechat_browser(){
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    if (strpos($user_agent, 'MicroMessenger') === false){
      //echo '非微信浏览器禁止浏览';
      return false;
    } else {
      //echo '微信浏览器，允许访问';
      //preg_match('/.*?(MicroMessenger\/([0-9.]+))\s*/', $user_agent, $matches);
      //echo '<br>你的微信版本号为:'.$matches[2];
      return true;
    }
}
?>