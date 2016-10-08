<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `ecs_error_log`;");
E_C("CREATE TABLE `ecs_error_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `info` varchar(255) NOT NULL,
  `file` varchar(100) NOT NULL,
  `time` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `time` (`time`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=utf8");
E_D("replace into `ecs_error_log` values('1','includes/modules/cron/order_confirm.php not found!','C:yanshiwwwrootwxmiqiapicron.php','1432126888');");
E_D("replace into `ecs_error_log` values('2','includes/modules/cron/order_confirm.php not found!','C:yanshiwwwrootwxmiqiapicron.php','1432149837');");
E_D("replace into `ecs_error_log` values('3','includes/modules/cron/order_confirm.php not found!','C:yanshiwwwrootwxmiqiapicron.php','1432223625');");
E_D("replace into `ecs_error_log` values('4','includes/modules/cron/order_confirm.php not found!','C:yanshiwwwrootwxmiqiapicron.php','1432316060');");
E_D("replace into `ecs_error_log` values('5','includes/modules/cron/order_confirm.php not found!','C:yanshiwwwrootwxmiqiapicron.php','1432376558');");
E_D("replace into `ecs_error_log` values('6','includes/modules/cron/order_confirm.php not found!','C:yanshiwwwrootwxmiqiapicron.php','1432376707');");
E_D("replace into `ecs_error_log` values('7','includes/modules/cron/order_confirm.php not found!','C:yanshiwwwrootwxmiqimobileapicron.php','1432380753');");
E_D("replace into `ecs_error_log` values('8','includes/modules/cron/order_confirm.php not found!','C:yanshiwwwrootwxmiqimobileapicron.php','1432380942');");
E_D("replace into `ecs_error_log` values('9','includes/modules/cron/order_confirm.php not found!','C:yanshiwwwrootwxmiqimobileapicron.php','1432381149');");
E_D("replace into `ecs_error_log` values('10','includes/modules/cron/order_confirm.php not found!','C:yanshiwwwrootwxmiqimobileapicron.php','1432381313');");
E_D("replace into `ecs_error_log` values('11','includes/modules/cron/order_confirm.php not found!','C:yanshiwwwrootwxmiqimobileapicron.php','1432381531');");
E_D("replace into `ecs_error_log` values('12','includes/modules/cron/order_confirm.php not found!','C:yanshiwwwrootwxmiqimobileapicron.php','1432381576');");
E_D("replace into `ecs_error_log` values('13','includes/modules/cron/order_confirm.php not found!','F:wwwwAPMServ5.2.6wwwhtdocsapicron.php','1437298131');");
E_D("replace into `ecs_error_log` values('14','includes/modules/cron/order_confirm.php not found!','F:wwwwAPMServ5.2.6wwwhtdocsapicron.php','1437546395');");
E_D("replace into `ecs_error_log` values('15','includes/modules/cron/order_confirm.php not found!','F:wwwwAPMServ5.2.6wwwhtdocsapicron.php','1439923538');");
E_D("replace into `ecs_error_log` values('16','includes/modules/cron/order_confirm.php not found!','F:wwwwAPMServ5.2.6wwwhtdocsapicron.php','1440670900');");
E_D("replace into `ecs_error_log` values('17','includes/modules/cron/order_confirm.php not found!','F:wwwwAPMServ5.2.6wwwhtdocsapicron.php','1440807872');");
E_D("replace into `ecs_error_log` values('18','includes/modules/cron/order_confirm.php not found!','F:wwwwAPMServ5.2.6wwwhtdocsapicron.php','1440907985');");
E_D("replace into `ecs_error_log` values('19','includes/modules/cron/order_confirm.php not found!','F:wwwwAPMServ5.2.6wwwhtdocsapicron.php','1441103077');");
E_D("replace into `ecs_error_log` values('20','includes/modules/cron/order_confirm.php not found!','F:wwwwAPMServ5.2.6wwwhtdocsapicron.php','1443946525');");
E_D("replace into `ecs_error_log` values('21','includes/modules/cron/order_confirm.php not found!','F:wwwwAPMServ5.2.6wwwhtdocsapicron.php','1447215204');");
E_D("replace into `ecs_error_log` values('22','includes/modules/cron/order_confirm.php not found!','F:wwwwAPMServ5.2.6wwwhtdocsapicron.php','1447370408');");
E_D("replace into `ecs_error_log` values('23','includes/modules/cron/order_confirm.php not found!','F:wwwwAPMServ5.2.6wwwhtdocsapicron.php','1447411958');");
E_D("replace into `ecs_error_log` values('24','includes/modules/cron/order_confirm.php not found!','F:wwwwAPMServ5.2.6wwwhtdocsapicron.php','1447570581');");
E_D("replace into `ecs_error_log` values('25','includes/modules/cron/affiliate_confirm.php not found!','F:wwwwAPMServ5.2.6wwwhtdocsapicron.php','1447570581');");
E_D("replace into `ecs_error_log` values('26','includes/modules/cron/order_confirm.php not found!','F:wwwwAPMServ5.2.6wwwhtdocsapicron.php','1447661980');");
E_D("replace into `ecs_error_log` values('27','includes/modules/cron/affiliate_confirm.php not found!','F:wwwwAPMServ5.2.6wwwhtdocsapicron.php','1447661980');");
E_D("replace into `ecs_error_log` values('28','includes/modules/cron/order_confirm.php not found!','F:wwwwAPMServ5.2.6wwwhtdocsapicron.php','1447801662');");
E_D("replace into `ecs_error_log` values('29','includes/modules/cron/affiliate_confirm.php not found!','F:wwwwAPMServ5.2.6wwwhtdocsapicron.php','1447801662');");

require("../../inc/footer.php");
?>