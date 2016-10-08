<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `ecs_user_account`;");
E_C("CREATE TABLE `ecs_user_account` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `admin_user` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `add_time` int(10) NOT NULL DEFAULT '0',
  `paid_time` int(10) NOT NULL DEFAULT '0',
  `admin_note` varchar(255) NOT NULL,
  `user_note` varchar(255) NOT NULL,
  `process_type` tinyint(1) NOT NULL DEFAULT '0',
  `payment` varchar(90) NOT NULL,
  `is_paid` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `is_paid` (`is_paid`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8");
E_D("replace into `ecs_user_account` values('1','381','','111.00','1428803144','0','','11','0','微信支付','0');");
E_D("replace into `ecs_user_account` values('2','381','','111.00','1428803169','0','','11','0','支付宝','0');");
E_D("replace into `ecs_user_account` values('4','3623','','0.01','1432345864','0','','吕河','0','微信支付','0');");
E_D("replace into `ecs_user_account` values('5','164','','-1.00','1432468131','0','','真实姓名:【】开户行:【】银行账户:【】手机:【】留言:【1】','1','','0');");
E_D("replace into `ecs_user_account` values('6','164','','-20.00','1432843366','0','','真实姓名:【】开户行:【】银行账户:【】手机:【】留言:【111】','1','','0');");

require("../../inc/footer.php");
?>