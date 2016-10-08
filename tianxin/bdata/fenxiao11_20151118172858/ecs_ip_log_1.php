<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `ecs_ip_log`;");
E_C("CREATE TABLE `ecs_ip_log` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `ip` varchar(20) DEFAULT NULL,
  `u_id` int(20) DEFAULT NULL,
  `state` int(2) DEFAULT '0',
  `phone_state` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1330 DEFAULT CHARSET=utf8");
E_D("replace into `ecs_ip_log` values('1329','127.0.0.1','4177','1','Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/40.0.2214.91 Safari/537.36');");

require("../../inc/footer.php");
?>