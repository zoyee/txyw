<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `ecs_order_info`;");
E_C("CREATE TABLE `ecs_order_info` (
  `order_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `order_sn` varchar(20) NOT NULL DEFAULT '',
  `user_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `order_status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `shipping_status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `pay_status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `consignee` varchar(60) NOT NULL DEFAULT '',
  `country` smallint(5) unsigned NOT NULL DEFAULT '0',
  `province` smallint(5) unsigned NOT NULL DEFAULT '0',
  `city` smallint(5) unsigned NOT NULL DEFAULT '0',
  `district` smallint(5) unsigned NOT NULL DEFAULT '0',
  `address` varchar(255) NOT NULL DEFAULT '',
  `zipcode` varchar(60) NOT NULL DEFAULT '',
  `tel` varchar(60) NOT NULL DEFAULT '',
  `mobile` varchar(60) NOT NULL DEFAULT '',
  `email` varchar(60) NOT NULL DEFAULT '',
  `best_time` varchar(120) NOT NULL DEFAULT '',
  `sign_building` varchar(120) NOT NULL DEFAULT '',
  `postscript` varchar(255) NOT NULL DEFAULT '',
  `shipping_id` tinyint(3) NOT NULL DEFAULT '0',
  `shipping_name` varchar(120) NOT NULL DEFAULT '',
  `pay_id` tinyint(3) NOT NULL DEFAULT '0',
  `pay_name` varchar(120) NOT NULL DEFAULT '',
  `how_oos` varchar(120) NOT NULL DEFAULT '',
  `how_surplus` varchar(120) NOT NULL DEFAULT '',
  `pack_name` varchar(120) NOT NULL DEFAULT '',
  `card_name` varchar(120) NOT NULL DEFAULT '',
  `card_message` varchar(255) NOT NULL DEFAULT '',
  `inv_payee` varchar(120) NOT NULL DEFAULT '',
  `inv_content` varchar(120) NOT NULL DEFAULT '',
  `goods_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `shipping_fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `insure_fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `pay_fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `pack_fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `card_fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `money_paid` decimal(10,2) NOT NULL DEFAULT '0.00',
  `surplus` decimal(10,2) NOT NULL DEFAULT '0.00',
  `integral` int(10) unsigned NOT NULL DEFAULT '0',
  `integral_money` decimal(10,2) NOT NULL DEFAULT '0.00',
  `bonus` decimal(10,2) NOT NULL DEFAULT '0.00',
  `order_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `from_ad` smallint(5) NOT NULL DEFAULT '0',
  `referer` varchar(255) NOT NULL DEFAULT '',
  `add_time` int(10) unsigned NOT NULL DEFAULT '0',
  `confirm_time` int(10) unsigned NOT NULL DEFAULT '0',
  `pay_time` int(10) unsigned NOT NULL DEFAULT '0',
  `shipping_time` int(10) unsigned NOT NULL DEFAULT '0',
  `pack_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `card_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `bonus_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `invoice_no` varchar(255) NOT NULL DEFAULT '',
  `extension_code` varchar(30) NOT NULL DEFAULT '',
  `extension_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `to_buyer` varchar(255) NOT NULL DEFAULT '',
  `pay_note` varchar(255) NOT NULL DEFAULT '',
  `agency_id` smallint(5) unsigned NOT NULL,
  `inv_type` varchar(60) NOT NULL,
  `tax` decimal(10,2) NOT NULL,
  `is_separate` tinyint(1) NOT NULL DEFAULT '0',
  `parent_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `discount` decimal(10,2) NOT NULL,
  `fencheng` varchar(255) DEFAULT NULL,
  `distribute_status` int(1) NOT NULL DEFAULT '0',
  `back_reason` varchar(255) DEFAULT NULL,
  `sales1_money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '一级分佣',
  `sales2_money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '二级分佣',
  `sales3_money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '三级分佣',
  `sales4_money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '四级分佣',
  `sales5_money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '五级分佣',
  `shouhuo_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订单收货时间',
  PRIMARY KEY (`order_id`),
  UNIQUE KEY `order_sn` (`order_sn`),
  KEY `user_id` (`user_id`),
  KEY `order_status` (`order_status`),
  KEY `shipping_status` (`shipping_status`),
  KEY `pay_status` (`pay_status`),
  KEY `shipping_id` (`shipping_id`),
  KEY `pay_id` (`pay_id`),
  KEY `extension_code` (`extension_code`,`extension_id`),
  KEY `agency_id` (`agency_id`),
  KEY `order_id` (`order_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1004 DEFAULT CHARSET=utf8");
E_D("replace into `ecs_order_info` values('998','2015111290525','4177','1','0','0','1111','1','4','55','540','1111','','18638360405','','','','','','12','天天快递','4','支付宝','等待所有商品备齐后再发','','','','','','','21.00','15.00','0.00','0.00','0.00','0.00','0.00','0.00','0','0.00','0.00','36.00','0','本站','1447234100','1447234163','0','0','0','0','0','','','0','','','0','','0.00','0','0','0.00','21','0',NULL,'12.60','4.20','1.47','0.63','0.21','0');");
E_D("replace into `ecs_order_info` values('999','2015111281999','4176','5','1','2','1111','1','3','37','409','11111','','18638360405','','','','','','5','申通快递','4','支付宝','等待所有商品备齐后再发','','','','','','','21.00','15.00','0.00','0.00','0.00','0.00','36.00','0.00','0','0.00','0.00','0.00','0','本站','1447234305','1447234436','1447234443','1447234455','0','0','0','2015111281999','','0','','','0','','0.00','1','0','0.00','21','0',NULL,'12.60','4.20','1.47','0.63','0.21','0');");
E_D("replace into `ecs_order_info` values('1000','2015111268955','4176','1','0','2','1111','1','3','37','409','11111','','18638360405','','','','','','5','申通快递','4','支付宝','等待所有商品备齐后再发','','','','','','','21.00','15.00','0.00','0.00','0.00','0.00','36.00','0.00','0','0.00','0.00','0.00','0','本站','1447234578','1447234597','1447234605','0','0','0','0','','','0','','','0','','0.00','1','0','0.00','21','0',NULL,'12.60','4.20','1.47','0.63','0.21','0');");
E_D("replace into `ecs_order_info` values('1001','2015111261584','4176','1','0','2','1111','1','3','37','409','11111','','18638360405','','','','','','5','申通快递','4','支付宝','等待所有商品备齐后再发','','','','','','','21.00','15.00','0.00','0.00','0.00','0.00','36.00','0.00','0','0.00','0.00','0.00','0','本站','1447235714','1447235771','1447235771','0','0','0','0','','','0','','','0','','0.00','1','0','0.00','21','0',NULL,'12.60','4.20','1.47','0.00','0.00','0');");
E_D("replace into `ecs_order_info` values('1002','2015111250492','4176','1','0','2','1111','1','3','37','409','11111','','18638360405','','','','','','5','申通快递','4','支付宝','等待所有商品备齐后再发','','','','','','','21.00','15.00','0.00','0.00','0.00','0.00','36.00','0.00','0','0.00','0.00','0.00','0','本站','1447235907','1447235924','1447235924','0','0','0','0','','','0','','','0','','0.00','1','0','0.00','21','0',NULL,'12.60','4.20','1.47','0.00','0.00','0');");
E_D("replace into `ecs_order_info` values('1003','2015111222001','4176','1','0','2','1111','1','3','37','409','11111','','18638360405','','','','','','5','申通快递','4','支付宝','等待所有商品备齐后再发','','','','','','','21.00','15.00','0.00','0.00','0.00','0.00','36.00','0.00','0','0.00','0.00','0.00','0','本站','1447236072','1447236086','1447236086','0','0','0','0','','','0','','','0','','0.00','1','0','0.00','21','0',NULL,'12.60','4.20','1.47','0.00','0.00','0');");

require("../../inc/footer.php");
?>