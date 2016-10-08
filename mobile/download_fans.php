<?php
define('IN_ECTOUCH', true);
set_time_limit ( 0 );
require_once (dirname(__FILE__) . '/include/init.php');
require_once (ROOT_PATH . 'api/weixin_api.php');
echo date('Y-m-d H:i:s');
echo "下载开始<br/>";
$weixin_api = new weixin_api();
$count = $weixin_api->download_fans();
echo date('Y-m-d H:i:s');
echo "<br/>";
echo "下载完成，文件地址：" . dirname(__FILE__) . "/wxch_fans.sql";