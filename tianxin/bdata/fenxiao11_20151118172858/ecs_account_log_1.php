<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `ecs_account_log`;");
E_C("CREATE TABLE `ecs_account_log` (
  `log_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(8) unsigned NOT NULL,
  `user_money` decimal(10,2) NOT NULL,
  `frozen_money` decimal(10,2) NOT NULL,
  `rank_points` mediumint(9) NOT NULL,
  `pay_points` mediumint(9) NOT NULL,
  `change_time` int(10) unsigned NOT NULL,
  `change_desc` varchar(255) NOT NULL,
  `change_type` tinyint(3) unsigned NOT NULL,
  `ip` varchar(40) NOT NULL,
  PRIMARY KEY (`log_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=602 DEFAULT CHARSET=utf8");
E_D("replace into `ecs_account_log` values('599','4176','0.00','0.00','21','21','1447234455','订单 2015111281999 赠送的积分','99','');");
E_D("replace into `ecs_account_log` values('600','4177','12.60','0.00','0','0','1447236099','订单号 2015111222001, 分成:金钱 12.60 积分 0','99','');");
E_D("replace into `ecs_account_log` values('601','4177','12.60','0.00','0','0','1447236117','订单号 2015111222001, 分成:金钱 12.60 积分 0','99','');");

require("../../inc/footer.php");
?>