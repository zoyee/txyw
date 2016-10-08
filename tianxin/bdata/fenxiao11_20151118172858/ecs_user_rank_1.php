<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `ecs_user_rank`;");
E_C("CREATE TABLE `ecs_user_rank` (
  `rank_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `rank_name` varchar(30) NOT NULL DEFAULT '',
  `min_points` int(10) unsigned NOT NULL DEFAULT '0',
  `max_points` int(10) unsigned NOT NULL DEFAULT '0',
  `discount` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `show_price` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `special_rank` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `award_scale` int(10) NOT NULL DEFAULT '0' COMMENT '会员等级所对应的奖金比例',
  `award_on` int(1) NOT NULL DEFAULT '0' COMMENT '会员等级奖金开关',
  PRIMARY KEY (`rank_id`)
) ENGINE=MyISAM AUTO_INCREMENT=101 DEFAULT CHARSET=utf8");
E_D("replace into `ecs_user_rank` values('1','注册用户','0','10000','100','1','0','10','0');");
E_D("replace into `ecs_user_rank` values('2','vip','10000','10000000','95','1','0','10','1');");
E_D("replace into `ecs_user_rank` values('99','微信用户','1','2','100','1','0','10','1');");
E_D("replace into `ecs_user_rank` values('100','分红100','270','300','100','1','0','10','1');");

require("../../inc/footer.php");
?>