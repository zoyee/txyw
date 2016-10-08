<?
define('IN_ECTOUCH', true);

require(dirname(__FILE__) . '/include/init.php');

if ((DEBUG_MODE & 2) != 2)
{
    $smarty->caching = false;
}


        $smarty->assign('cfg', $_CFG);
        assign_template();
        $position = assign_ur_here();

		
    //更新商品点击次数
    $sql = 'select * from  ' . $ecs->table('brand') ;
    $brandlist= $db->GetAll($sql);

 $smarty->assign('brandlist', $brandlist);
  $smarty->display('pinpai.dwt');

?>