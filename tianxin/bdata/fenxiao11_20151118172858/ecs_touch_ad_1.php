<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `ecs_touch_ad`;");
E_C("CREATE TABLE `ecs_touch_ad` (
  `ad_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `position_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `media_type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `ad_name` varchar(60) NOT NULL DEFAULT '',
  `ad_link` varchar(255) NOT NULL DEFAULT '',
  `ad_code` text NOT NULL,
  `start_time` int(11) NOT NULL DEFAULT '0',
  `end_time` int(11) NOT NULL DEFAULT '0',
  `link_man` varchar(60) NOT NULL DEFAULT '',
  `link_email` varchar(60) NOT NULL DEFAULT '',
  `link_phone` varchar(60) NOT NULL DEFAULT '',
  `click_count` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `enabled` tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`ad_id`),
  KEY `position_id` (`position_id`),
  KEY `enabled` (`enabled`)
) ENGINE=MyISAM AUTO_INCREMENT=57 DEFAULT CHARSET=utf8");
E_D("replace into `ecs_touch_ad` values('41','17','0','手机端首页广告3-3','','1442452904186438097.jpg','1439712000','1476604800','','','','9','1');");
E_D("replace into `ecs_touch_ad` values('3','3','0','wap首页主广告1','http://127.0.0.1/ec273/vsc/vsc_xjdv4/topic.php?topic_id=6','1440434742398149569.jpg','1394870400','1492675200','','','','29','1');");
E_D("replace into `ecs_touch_ad` values('4','3','0','wap首页主广告2','http://127.0.0.1/ec273/vsc/vsc_xjdv4/topic.php?topic_id=6','1440434755442738492.jpg','1394870400','1492416000','','','','11','1');");
E_D("replace into `ecs_touch_ad` values('5','3','0','wap首页主广告3','192.168.191.1/ec273/vsc/vsc_xjdv4/mobile/admin/index.php','1440434770177026373.jpg','1394870400','1491984000','','','','10','1');");
E_D("replace into `ecs_touch_ad` values('30','11','0','手机端首页广告3-1','','1442452886830104584.jpg','1440403200','1474617600','','','','11','1');");
E_D("replace into `ecs_touch_ad` values('40','9','0','手机端首页广告3-2','','1442452895055966978.jpg','1442390400','1476604800','','','','7','1');");
E_D("replace into `ecs_touch_ad` values('24','2','0','手机端首页广告1-1','','1442452784680942491.jpg','1440403200','1506153600','','','','17','1');");
E_D("replace into `ecs_touch_ad` values('25','1','0','手机端首页广告1-2','','1442452795423958325.jpg','1440403200','1474617600','','','','12','1');");
E_D("replace into `ecs_touch_ad` values('26','6','0','手机端首页广告1-3','','1442452805449188441.jpg','1440403200','1474617600','','','','5','1');");
E_D("replace into `ecs_touch_ad` values('27','7','0','手机端首页广告2-1','','1442452825847922639.jpg','1440403200','1474617600','','','','9','1');");
E_D("replace into `ecs_touch_ad` values('28','8','0','手机端首页广告2-2','','1442452879563390604.jpg','1440403200','1474617600','','','','4','1');");
E_D("replace into `ecs_touch_ad` values('34','15','0','分类-1-促销广告','','1440444848938395010.jpg','1440403200','1474617600','','','','5','1');");
E_D("replace into `ecs_touch_ad` values('35','4','0','衣云化妆品特卖','','1442790281070548015.jpg','1437724800','1324627200','满68增16GU盘','滋润每一天','','0','1');");
E_D("replace into `ecs_touch_ad` values('36','4','0','cosme卸妆部NO.1','','1442790324337115649.jpg','1437724800','1474617600','6.5折','Fancl无添加纳米卸妆油','','1','1');");
E_D("replace into `ecs_touch_ad` values('37','4','0','菲丽菲拉（PERIPERA）炫彩染色唇彩','','1442790291652215474.jpg','1437724800','1474617600','4.9折','刷出“唇”在感！','','1','1');");
E_D("replace into `ecs_touch_ad` values('38','4','0','【海外直邮】OH MY GOD','','1442790304060132581.jpg','1437724800','1474617600','3.1折','不打美白针,也能白成仙！','','2','1');");
E_D("replace into `ecs_touch_ad` values('39','16','0','手机端首页广告4','','1442451951800521976.jpg','1439712000','1476604800','','','','12','1');");
E_D("replace into `ecs_touch_ad` values('44','21','0','微分销店铺楼层广告1','','1442969320247372537.jpg','1440230400','1508659200','','','','0','1');");
E_D("replace into `ecs_touch_ad` values('45','22','0','微分销店铺楼层广告2','','1442969346373073867.png','1440230400','1508659200','','','','0','1');");
E_D("replace into `ecs_touch_ad` values('46','23','0','微分销店铺楼层广告3','','1442969365540941385.jpg','1440230400','1508659200','','','','0','1');");
E_D("replace into `ecs_touch_ad` values('47','24','0','微分销店铺楼层广告4','','1442969383623524632.jpg','1440230400','1508659200','','','','0','1');");
E_D("replace into `ecs_touch_ad` values('48','25','0','微分销店铺楼层广告5','','1442969401180412911.jpg','1440230400','1508659200','','','','0','1');");
E_D("replace into `ecs_touch_ad` values('49','26','0','微分销店铺楼层广告6','','1442969429354327735.jpg','1440230400','1508659200','','','','0','1');");
E_D("replace into `ecs_touch_ad` values('50','27','0','微分销店铺楼层广告7','','1442969447469704318.jpg','1440230400','1508659200','','','','0','1');");
E_D("replace into `ecs_touch_ad` values('51','28','0','微分销店铺楼层广告8','','1442969510987335006.jpg','1440230400','1508659200','','','','0','1');");
E_D("replace into `ecs_touch_ad` values('52','29','0','手机端首页精品推荐广告1','','1443048728820510470.jpg','1440316800','1508745600','','','','5','1');");
E_D("replace into `ecs_touch_ad` values('53','29','0','手机端首页精品推荐广告2','','1443048778197917510.jpg','1442995200','1445587200','','','','1','1');");
E_D("replace into `ecs_touch_ad` values('54','30','0','1','','1447417986908707107.jpg','1396339200','1525161600','','','','557','1');");
E_D("replace into `ecs_touch_ad` values('55','30','0','2','','1447417975865179981.jpg','1396339200','1525161600','','','','442','1');");
E_D("replace into `ecs_touch_ad` values('56','30','0','3','','1447417961037146493.jpg','1396339200','1525161600','','','','393','1');");

require("../../inc/footer.php");
?>