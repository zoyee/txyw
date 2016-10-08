<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wxch_msg`;");
E_C("CREATE TABLE `wxch_msg` (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `function` varchar(30) NOT NULL,
  `command` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8");
E_D("replace into `wxch_msg` values('1','新品','news','xk 新款 News');");
E_D("replace into `wxch_msg` values('2','精品','best','Best 精品');");
E_D("replace into `wxch_msg` values('3','热销','hot','Hot 热销');");
E_D("replace into `wxch_msg` values('4','绑定会员','bd','BD Bd 绑定会员');");
E_D("replace into `wxch_msg` values('5','重新生成二维码','cxsc','cxsc');");
E_D("replace into `wxch_msg` values('6','订单列表','ddlb','订单列表 Ddlb');");
E_D("replace into `wxch_msg` values('7','订单查询','ddcx','订单查询 Ddcx');");
E_D("replace into `wxch_msg` values('8','订单快递','kdcx','订单快递 Kdcx');");
E_D("replace into `wxch_msg` values('9','帮助说明','help','帮助说明 Help 帮助');");
E_D("replace into `wxch_msg` values('10','砸金蛋','zjd','砸金蛋 Zjd');");
E_D("replace into `wxch_msg` values('11','签到','qiandao','qiandao 签到');");
E_D("replace into `wxch_msg` values('12','大转盘','dzp','大转盘 Dzp');");
E_D("replace into `wxch_msg` values('13','图文二维码','qrcode','qrcode');");
E_D("replace into `wxch_msg` values('14','推广二维码（增值）','tianxin100','tianxin100');");

require("../../inc/footer.php");
?>