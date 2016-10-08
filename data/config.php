<?php
// database host
$db_host   = "127.0.0.1:3305";

// database name
$db_name   = "zlb";

// database username
$db_user   = "root";

// database password
$db_pass   = "root";

// table prefix
$prefix    = "ecs_";

$timezone    = "PRC";

$cookie_path    = "/";

$cookie_domain    = "";

$session = "1440";

define('EC_CHARSET','utf-8');

define('ADMIN_PATH','admin');

define('AUTH_KEY', 'this is a key');

define('OLD_AUTH_KEY', '');

define('API_TIME', '');

$config['site_url']="http://".$_SERVER['HTTP_HOST']."/"; //电脑版地址

//define('SUPPLIER_ACTION_LIST', 'goods_manage,suppliers_manage,order_os_edit,order_ps_edit,order_ss_edit,order_view,order_view_finished,delivery_view,back_view');
define('SUPPLIER_ACTION_LIST', 'goods_manage,order_ss_edit,order_view');

define('SUPPLIER_USERS', 'admin');//可以登录的高级用户，不要有空格
?>