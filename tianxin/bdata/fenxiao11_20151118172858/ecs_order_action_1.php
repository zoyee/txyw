<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `ecs_order_action`;");
E_C("CREATE TABLE `ecs_order_action` (
  `action_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `action_user` varchar(30) NOT NULL DEFAULT '',
  `order_status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `shipping_status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `pay_status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `action_place` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `action_note` varchar(255) NOT NULL DEFAULT '',
  `log_time` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`action_id`),
  KEY `order_id` (`order_id`)
) ENGINE=MyISAM AUTO_INCREMENT=135 DEFAULT CHARSET=utf8");
E_D("replace into `ecs_order_action` values('125','998','admin','1','0','0','0','11111','1447234163');");
E_D("replace into `ecs_order_action` values('126','999','admin','1','0','0','0','1111','1447234436');");
E_D("replace into `ecs_order_action` values('127','999','admin','1','0','2','0','222','1447234443');");
E_D("replace into `ecs_order_action` values('128','999','admin','5','5','2','0','','1447234455');");
E_D("replace into `ecs_order_action` values('129','999','admin','1','1','2','1','','1447234455');");
E_D("replace into `ecs_order_action` values('130','1000','admin','1','0','0','0','121212','1447234597');");
E_D("replace into `ecs_order_action` values('131','1000','admin','1','0','2','0','1212','1447234605');");
E_D("replace into `ecs_order_action` values('132','1001','admin','1','0','2','0','2323','1447235771');");
E_D("replace into `ecs_order_action` values('133','1002','admin','1','0','2','0','222','1447235924');");
E_D("replace into `ecs_order_action` values('134','1003','admin','1','0','2','0','1212','1447236086');");

require("../../inc/footer.php");
?>