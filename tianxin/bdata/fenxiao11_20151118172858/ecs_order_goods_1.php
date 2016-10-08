<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `ecs_order_goods`;");
E_C("CREATE TABLE `ecs_order_goods` (
  `rec_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `goods_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `goods_name` varchar(120) NOT NULL DEFAULT '',
  `goods_sn` varchar(60) NOT NULL DEFAULT '',
  `product_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `goods_number` smallint(5) unsigned NOT NULL DEFAULT '1',
  `market_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `goods_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `goods_attr` text NOT NULL,
  `send_number` smallint(5) unsigned NOT NULL DEFAULT '0',
  `is_real` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `extension_code` varchar(30) NOT NULL DEFAULT '',
  `parent_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `is_gift` smallint(5) unsigned NOT NULL DEFAULT '0',
  `goods_attr_id` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`rec_id`),
  KEY `order_id` (`order_id`),
  KEY `goods_id` (`goods_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1049 DEFAULT CHARSET=utf8");
E_D("replace into `ecs_order_goods` values('1043','998','164','111111','ECS000000','0','1','25.00','21.00','颜色:红色[1] \n','0','1','','0','0','351');");
E_D("replace into `ecs_order_goods` values('1044','999','164','111111','ECS000000','0','1','25.00','21.00','颜色:红色[1] \n','1','1','','0','0','351');");
E_D("replace into `ecs_order_goods` values('1045','1000','164','111111','ECS000000','0','1','25.00','21.00','颜色:红色[1] \n','0','1','','0','0','351');");
E_D("replace into `ecs_order_goods` values('1046','1001','164','111111','ECS000000','0','1','25.00','21.00','颜色:红色[1] \n','0','1','','0','0','351');");
E_D("replace into `ecs_order_goods` values('1047','1002','164','111111','ECS000000','0','1','25.00','21.00','颜色:红色[1] \n','0','1','','0','0','351');");
E_D("replace into `ecs_order_goods` values('1048','1003','164','111111','ECS000000','0','1','25.00','21.00','颜色:红色[1] \n','0','1','','0','0','351');");

require("../../inc/footer.php");
?>