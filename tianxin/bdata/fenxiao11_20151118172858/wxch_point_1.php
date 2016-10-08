<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wxch_point`;");
E_C("CREATE TABLE `wxch_point` (
  `point_id` int(3) unsigned NOT NULL AUTO_INCREMENT,
  `point_name` varchar(64) NOT NULL DEFAULT '',
  `point_value` int(3) unsigned NOT NULL,
  `point_num` int(3) NOT NULL,
  `autoload` varchar(20) NOT NULL DEFAULT 'yes',
  PRIMARY KEY (`point_id`),
  UNIQUE KEY `option_name` (`point_name`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8");
E_D("replace into `wxch_point` values('7','zjd','5','0','yes');");
E_D("replace into `wxch_point` values('8','qiandao','10','1','yes');");
E_D("replace into `wxch_point` values('9','dzp','10','1','yes');");
E_D("replace into `wxch_point` values('11','share_fpoint','10','1','yes');");
E_D("replace into `wxch_point` values('12','share_dfpoint','10','1','yes');");
E_D("replace into `wxch_point` values('13','g_point','10','5','yes');");
E_D("replace into `wxch_point` values('14','share_arpoint','1','1','yes');");

require("../../inc/footer.php");
?>