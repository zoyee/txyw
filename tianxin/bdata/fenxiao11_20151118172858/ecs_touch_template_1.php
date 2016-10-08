<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `ecs_touch_template`;");
E_C("CREATE TABLE `ecs_touch_template` (
  `filename` varchar(30) NOT NULL DEFAULT '',
  `region` varchar(40) NOT NULL DEFAULT '',
  `library` varchar(40) NOT NULL DEFAULT '',
  `sort_order` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `number` tinyint(1) unsigned NOT NULL DEFAULT '5',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `theme` varchar(60) NOT NULL DEFAULT '',
  `remarks` varchar(30) NOT NULL DEFAULT '',
  KEY `filename` (`filename`,`region`),
  KEY `theme` (`theme`),
  KEY `remarks` (`remarks`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");
E_D("replace into `ecs_touch_template` values('index','','/library/group_buy.lbi','0','0','3','0','miqinew3','');");
E_D("replace into `ecs_touch_template` values('index','','/library/recommend_promotion.lbi','0','0','4','0','miqinew3','');");
E_D("replace into `ecs_touch_template` values('index','中部主区域','/library/recommend_hot.lbi','3','0','3','0','miqinew3','');");
E_D("replace into `ecs_touch_template` values('index','中部主区域','/library/recommend_new.lbi','2','0','3','0','miqinew3','');");
E_D("replace into `ecs_touch_template` values('index','中部主区域','/library/recommend_best.lbi','4','0','3','0','miqinew3','');");
E_D("replace into `ecs_touch_template` values('index','touch首页广告区域','/library/ad_position.lbi','0','1','3','4','miqinew3','');");
E_D("replace into `ecs_touch_template` values('index','首页推荐模块','/library/recommend_best.lbi','0','0','3','0','tianxin100','');");
E_D("replace into `ecs_touch_template` values('index','首页推荐模块','/library/recommend_new.lbi','1','0','3','0','tianxin100','');");
E_D("replace into `ecs_touch_template` values('index','首页推荐模块','/library/recommend_hot.lbi','2','0','3','0','tianxin100','');");
E_D("replace into `ecs_touch_template` values('index','首页促销模块','/library/recommend_promotion.lbi','0','0','4','0','tianxin100','');");
E_D("replace into `ecs_touch_template` values('index','','/library/group_buy.lbi','0','0','3','0','tianxin100','');");
E_D("replace into `ecs_touch_template` values('index','手机端首页广告1-1','/library/ad_position.lbi','0','2','0','4','tianxin100','');");
E_D("replace into `ecs_touch_template` values('index','手机端首页广告1-2','/library/ad_position.lbi','0','1','0','4','tianxin100','');");
E_D("replace into `ecs_touch_template` values('index','手机端首页广告1-3','/library/ad_position.lbi','0','6','0','4','tianxin100','');");
E_D("replace into `ecs_touch_template` values('index','手机端首页广告2-1','/library/ad_position.lbi','0','7','0','4','tianxin100','');");
E_D("replace into `ecs_touch_template` values('index','手机端首页广告2-2','/library/ad_position.lbi','0','8','0','4','tianxin100','');");
E_D("replace into `ecs_touch_template` values('index','手机端首页广告3-1','/library/ad_position.lbi','0','11','0','4','tianxin100','');");
E_D("replace into `ecs_touch_template` values('index','手机端首页广告3-2','/library/ad_position.lbi','0','9','0','4','tianxin100','');");
E_D("replace into `ecs_touch_template` values('index','手机端首页广告3-3','/library/ad_position.lbi','0','17','0','4','tianxin100','');");
E_D("replace into `ecs_touch_template` values('index','手机端首页广告4','/library/ad_position.lbi','0','16','0','4','tianxin100','');");

require("../../inc/footer.php");
?>