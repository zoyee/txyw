<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wxch_oauth`;");
E_C("CREATE TABLE `wxch_oauth` (
  `oid` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `contents` text NOT NULL,
  `count` int(10) unsigned NOT NULL,
  `status` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`oid`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8");
E_D("replace into `wxch_oauth` values('1','手机版网站首页','http://wxmiqi.wushuai.net/mobile','13556','1');");
E_D("replace into `wxch_oauth` values('12','推广教程','http://wxmiqi.wushuai.net/mobile/article.php?id=37','5254','1');");
E_D("replace into `wxch_oauth` values('14','分销中心','http://wxmiqi.wushuai.net/mobile/distribute.php','12820','1');");
E_D("replace into `wxch_oauth` values('16','新单品演示','http://wxmiqi.wushuai.net/mobile/tuiguang.php?id=45','6799','1');");

require("../../inc/footer.php");
?>