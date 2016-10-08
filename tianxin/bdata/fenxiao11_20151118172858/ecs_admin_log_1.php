<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `ecs_admin_log`;");
E_C("CREATE TABLE `ecs_admin_log` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `log_time` int(10) unsigned NOT NULL DEFAULT '0',
  `user_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `log_info` varchar(255) NOT NULL DEFAULT '',
  `ip_address` varchar(15) NOT NULL DEFAULT '',
  PRIMARY KEY (`log_id`),
  KEY `log_time` (`log_time`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=155 DEFAULT CHARSET=utf8");
E_D("replace into `ecs_admin_log` values('130','1434188949','1','编辑权限管理: xiaojun','127.0.0.1');");
E_D("replace into `ecs_admin_log` values('131','1434190810','1','编辑权限管理: xiaojun','127.0.0.1');");
E_D("replace into `ecs_admin_log` values('132','1434326444','1','编辑权限管理: xiaojun','127.0.0.1');");
E_D("replace into `ecs_admin_log` values('133','1434326578','1','编辑权限管理: xiaojun','127.0.0.1');");
E_D("replace into `ecs_admin_log` values('134','1434327021','1','编辑权限管理: xiaojun','127.0.0.1');");
E_D("replace into `ecs_admin_log` values('135','1434327143','1','编辑权限管理: xiaojun','127.0.0.1');");
E_D("replace into `ecs_admin_log` values('136','1435827986','1','编辑商店设置: ','127.0.0.1');");
E_D("replace into `ecs_admin_log` values('137','1435829365','1','编辑商店设置: ','127.0.0.1');");
E_D("replace into `ecs_admin_log` values('138','1439793064','1','添加商品: 111111','127.0.0.1');");
E_D("replace into `ecs_admin_log` values('139','1440670941','1','添加属性: 颜色','127.0.0.1');");
E_D("replace into `ecs_admin_log` values('140','1440670956','1','编辑商品: 111111','127.0.0.1');");
E_D("replace into `ecs_admin_log` values('141','1440673798','1','编辑会员等级: 注册用户','127.0.0.1');");
E_D("replace into `ecs_admin_log` values('142','1443948282','1','编辑权限管理: xiaojun','127.0.0.1');");
E_D("replace into `ecs_admin_log` values('143','1443948440','1','编辑权限管理: xiaojun','127.0.0.1');");
E_D("replace into `ecs_admin_log` values('144','1444031951','1','编辑权限管理: xiaojun','127.0.0.1');");
E_D("replace into `ecs_admin_log` values('145','1444036026','1','编辑权限管理: xiaojun','127.0.0.1');");
E_D("replace into `ecs_admin_log` values('146','1444687052','1','编辑会员等级: vip','0.0.0.0');");
E_D("replace into `ecs_admin_log` values('147','1444688165','1','添加拍卖活动: 测试拍卖','0.0.0.0');");
E_D("replace into `ecs_admin_log` values('148','1444688338','1','编辑拍卖活动: 测试拍卖','0.0.0.0');");
E_D("replace into `ecs_admin_log` values('149','1444689716','1','编辑商品: 111111','0.0.0.0');");
E_D("replace into `ecs_admin_log` values('150','1447234054','1','编辑会员账号: 11111111','127.0.0.1');");
E_D("replace into `ecs_admin_log` values('151','1447236017','1','编辑会员账号: 111111','127.0.0.1');");
E_D("replace into `ecs_admin_log` values('152','1447417961','1','编辑广告: 3','127.0.0.1');");
E_D("replace into `ecs_admin_log` values('153','1447417975','1','编辑广告: 2','127.0.0.1');");
E_D("replace into `ecs_admin_log` values('154','1447417986','1','编辑广告: 1','127.0.0.1');");

require("../../inc/footer.php");
?>