<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `ecs_stats`;");
E_C("CREATE TABLE `ecs_stats` (
  `access_time` int(10) unsigned NOT NULL DEFAULT '0',
  `ip_address` varchar(15) NOT NULL DEFAULT '',
  `visit_times` smallint(5) unsigned NOT NULL DEFAULT '1',
  `browser` varchar(60) NOT NULL DEFAULT '',
  `system` varchar(20) NOT NULL DEFAULT '',
  `language` varchar(20) NOT NULL DEFAULT '',
  `area` varchar(30) NOT NULL DEFAULT '',
  `referer_domain` varchar(100) NOT NULL DEFAULT '',
  `referer_path` varchar(200) NOT NULL DEFAULT '',
  `access_url` varchar(255) NOT NULL DEFAULT '',
  KEY `access_time` (`access_time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");
E_D("replace into `ecs_stats` values('1437298128','127.0.0.1','58','Safari 537.36','Windows NT','zh-CN,zh','LAN','','','/index.php');");
E_D("replace into `ecs_stats` values('1437546387','127.0.0.1','58','Safari 537.36','Windows NT','zh-CN,zh','LAN','','','/index.php');");
E_D("replace into `ecs_stats` values('1439793073','127.0.0.1','161','Safari 537.36','Windows NT','zh-CN,zh','LAN','http://127.0.0.1','/mobile/goods.php?id=164','/index.php');");
E_D("replace into `ecs_stats` values('1439794057','127.0.0.1','162','Safari 537.36','Windows NT','zh-CN,zh','LAN','http://127.0.0.1','/mobile/goods.php?id=164&u=4177','/index.php');");
E_D("replace into `ecs_stats` values('1439923537','127.0.0.1','163','Safari 537.36','Windows NT','zh-CN,zh','LAN','','','/index.php');");
E_D("replace into `ecs_stats` values('1440670895','127.0.0.1','213','Safari 537.36','Windows NT','zh-CN,zh','LAN','','','/index.php');");
E_D("replace into `ecs_stats` values('1440670897','127.0.0.1','213','Safari 537.36','Windows NT','zh-CN,zh','LAN','','','/index.php');");
E_D("replace into `ecs_stats` values('1440670898','127.0.0.1','213','Safari 537.36','Windows NT','zh-CN,zh','LAN','','','/index.php');");
E_D("replace into `ecs_stats` values('1440670959','127.0.0.1','214','Safari 537.36','Windows NT','zh-CN,zh','LAN','http://127.0.0.1','/mobile/goods.php?id=164','/index.php');");
E_D("replace into `ecs_stats` values('1440671164','127.0.0.1','215','Safari 537.36','Windows NT','zh-CN,zh','LAN','http://127.0.0.1','/mobile/goods.php?id=164','/index.php');");
E_D("replace into `ecs_stats` values('1440671431','127.0.0.1','216','Safari 537.36','Windows NT','zh-CN,zh','LAN','http://127.0.0.1','/mobile/goods.php?id=164','/index.php');");
E_D("replace into `ecs_stats` values('1440671461','127.0.0.1','217','Safari 537.36','Windows NT','zh-CN,zh','LAN','http://127.0.0.1','/mobile/goods.php?id=164','/index.php');");
E_D("replace into `ecs_stats` values('1440671530','127.0.0.1','218','Safari 537.36','Windows NT','zh-CN,zh','LAN','http://127.0.0.1','/mobile/goods.php?id=164','/index.php');");
E_D("replace into `ecs_stats` values('1440671628','127.0.0.1','219','Safari 537.36','Windows NT','zh-CN,zh','LAN','http://127.0.0.1','/mobile/goods.php?id=164','/index.php');");
E_D("replace into `ecs_stats` values('1440671644','127.0.0.1','220','Safari 537.36','Windows NT','zh-CN,zh','LAN','http://127.0.0.1','/mobile/goods.php?id=164','/index.php');");
E_D("replace into `ecs_stats` values('1440671685','127.0.0.1','221','Safari 537.36','Windows NT','zh-CN,zh','LAN','http://127.0.0.1','/mobile/goods.php?id=164','/index.php');");
E_D("replace into `ecs_stats` values('1440672038','127.0.0.1','222','Safari 537.36','Windows NT','zh-CN,zh','LAN','http://127.0.0.1','/mobile/goods.php?id=164','/index.php');");
E_D("replace into `ecs_stats` values('1440672049','127.0.0.1','223','Safari 537.36','Windows NT','zh-CN,zh','LAN','http://127.0.0.1','/mobile/goods.php?id=164','/index.php');");
E_D("replace into `ecs_stats` values('1440672069','127.0.0.1','224','Safari 537.36','Windows NT','zh-CN,zh','LAN','http://127.0.0.1','/mobile/goods.php?id=164','/index.php');");
E_D("replace into `ecs_stats` values('1440672077','127.0.0.1','225','Safari 537.36','Windows NT','zh-CN,zh','LAN','http://127.0.0.1','/mobile/goods.php?id=164','/index.php');");
E_D("replace into `ecs_stats` values('1440672167','127.0.0.1','226','Safari 537.36','Windows NT','zh-CN,zh','LAN','http://127.0.0.1','/mobile/goods.php?id=164','/index.php');");
E_D("replace into `ecs_stats` values('1440672175','127.0.0.1','227','Safari 537.36','Windows NT','zh-CN,zh','LAN','http://127.0.0.1','/mobile/goods.php?id=164','/index.php');");
E_D("replace into `ecs_stats` values('1440672242','127.0.0.1','228','Safari 537.36','Windows NT','zh-CN,zh','LAN','http://127.0.0.1','/mobile/goods.php?id=164','/index.php');");
E_D("replace into `ecs_stats` values('1440672353','127.0.0.1','229','Safari 537.36','Windows NT','zh-CN,zh','LAN','http://127.0.0.1','/mobile/goods.php?id=164','/index.php');");
E_D("replace into `ecs_stats` values('1440672425','127.0.0.1','230','Safari 537.36','Windows NT','zh-CN,zh','LAN','http://127.0.0.1','/mobile/goods.php?id=164','/index.php');");
E_D("replace into `ecs_stats` values('1440672463','127.0.0.1','231','Safari 537.36','Windows NT','zh-CN,zh','LAN','http://127.0.0.1','/mobile/goods.php?id=164','/index.php');");
E_D("replace into `ecs_stats` values('1440672483','127.0.0.1','232','Safari 537.36','Windows NT','zh-CN,zh','LAN','http://127.0.0.1','/mobile/goods.php?id=164','/index.php');");
E_D("replace into `ecs_stats` values('1440672619','127.0.0.1','233','Safari 537.36','Windows NT','zh-CN,zh','LAN','http://127.0.0.1','/mobile/goods.php?id=164','/index.php');");
E_D("replace into `ecs_stats` values('1440672720','127.0.0.1','234','Safari 537.36','Windows NT','zh-CN,zh','LAN','http://127.0.0.1','/mobile/goods.php?id=164','/index.php');");
E_D("replace into `ecs_stats` values('1440672728','127.0.0.1','235','Safari 537.36','Windows NT','zh-CN,zh','LAN','http://127.0.0.1','/mobile/goods.php?id=164','/index.php');");
E_D("replace into `ecs_stats` values('1440674411','127.0.0.1','236','Safari 537.36','Windows NT','zh-CN,zh','LAN','http://127.0.0.1','/mobile/goods.php?id=164','/index.php');");
E_D("replace into `ecs_stats` values('1440710920','127.0.0.1','237','Safari 537.36','Windows NT','zh-CN,zh','LAN','','','/index.php');");
E_D("replace into `ecs_stats` values('1440716081','127.0.0.1','237','Safari 537.36','Windows NT','zh-CN,zh','LAN','','','/index.php');");
E_D("replace into `ecs_stats` values('1440719556','127.0.0.1','238','Safari 537.36','Windows NT','zh-CN,zh','LAN','','','/index.php');");
E_D("replace into `ecs_stats` values('1440807870','127.0.0.1','238','Safari 537.36','Windows NT','zh-CN,zh','LAN','','','/index.php');");
E_D("replace into `ecs_stats` values('1440807870','127.0.0.1','238','Safari 537.36','Windows NT','zh-CN,zh','LAN','','','/index.php');");
E_D("replace into `ecs_stats` values('1440907982','127.0.0.1','239','Safari 537.36','Windows NT','zh-CN,zh','LAN','','','/index.php');");
E_D("replace into `ecs_stats` values('1440907982','127.0.0.1','239','Safari 537.36','Windows NT','zh-CN,zh','LAN','','','/index.php');");
E_D("replace into `ecs_stats` values('1440914219','127.0.0.1','240','Safari 537.36','Windows NT','zh-CN,zh','LAN','','','/index.php');");
E_D("replace into `ecs_stats` values('1440914237','127.0.0.1','241','Safari 537.36','Windows NT','zh-CN,zh','LAN','http://127.0.0.1','/mobile/goods.php?id=164','/index.php');");
E_D("replace into `ecs_stats` values('1440914454','127.0.0.1','242','Safari 537.36','Windows NT','zh-CN,zh','LAN','http://127.0.0.1','/mobile/goods.php?id=164','/index.php');");
E_D("replace into `ecs_stats` values('1440914560','127.0.0.1','243','Safari 537.36','Windows NT','zh-CN,zh','LAN','http://127.0.0.1','/mobile/goods.php?id=164','/index.php');");
E_D("replace into `ecs_stats` values('1440914618','127.0.0.1','244','Safari 537.36','Windows NT','zh-CN,zh','LAN','http://127.0.0.1','/mobile/goods.php?id=164','/index.php');");
E_D("replace into `ecs_stats` values('1440914686','127.0.0.1','245','Safari 537.36','Windows NT','zh-CN,zh','LAN','http://127.0.0.1','/mobile/goods.php?id=164','/index.php');");
E_D("replace into `ecs_stats` values('1441103073','127.0.0.1','246','Safari 537.36','Windows NT','zh-CN,zh','LAN','','','/index.php');");
E_D("replace into `ecs_stats` values('1441105800','127.0.0.1','246','Safari 537.36','Windows NT','zh-CN,zh','LAN','http://127.0.0.1','/','/user.php');");
E_D("replace into `ecs_stats` values('1441110139','127.0.0.1','247','Safari 537.36','Windows NT','zh-CN,zh','LAN','','','/index.php');");
E_D("replace into `ecs_stats` values('1441181456','127.0.0.1','247','Safari 537.36','Windows NT','zh-CN,zh','LAN','','','/index.php');");
E_D("replace into `ecs_stats` values('1441181456','127.0.0.1','247','Safari 537.36','Windows NT','zh-CN,zh','LAN','','','/index.php');");
E_D("replace into `ecs_stats` values('1443946525','127.0.0.1','2','Safari 537.36','Windows NT','zh-CN,zh','LAN','','','/index.php');");
E_D("replace into `ecs_stats` values('1443950216','127.0.0.1','3','Safari 537.36','Windows NT','zh-CN,zh','LAN','','','/index.php');");

require("../../inc/footer.php");
?>