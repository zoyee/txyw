<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `ecs_users`;");
E_C("CREATE TABLE `ecs_users` (
  `user_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `aite_id` text NOT NULL,
  `email` varchar(60) NOT NULL DEFAULT '',
  `user_name` varchar(60) NOT NULL DEFAULT '',
  `password` varchar(32) NOT NULL DEFAULT '',
  `question` varchar(255) NOT NULL DEFAULT '',
  `answer` varchar(255) NOT NULL DEFAULT '',
  `sex` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `birthday` date NOT NULL DEFAULT '0000-00-00',
  `user_money` decimal(10,2) NOT NULL DEFAULT '0.00',
  `frozen_money` decimal(10,2) NOT NULL DEFAULT '0.00',
  `pay_points` int(10) unsigned NOT NULL DEFAULT '0',
  `rank_points` int(10) unsigned NOT NULL DEFAULT '0',
  `address_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `reg_time` int(10) unsigned NOT NULL DEFAULT '0',
  `last_login` int(11) unsigned NOT NULL DEFAULT '0',
  `last_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_ip` varchar(15) NOT NULL DEFAULT '',
  `visit_count` smallint(5) unsigned NOT NULL DEFAULT '0',
  `user_rank` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `is_special` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `ec_salt` varchar(10) DEFAULT NULL,
  `salt` varchar(10) NOT NULL DEFAULT '0',
  `parent_id` mediumint(9) NOT NULL DEFAULT '0',
  `flag` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `alias` varchar(60) NOT NULL,
  `msn` varchar(60) NOT NULL,
  `qq` varchar(20) NOT NULL,
  `office_phone` varchar(20) NOT NULL,
  `home_phone` varchar(20) NOT NULL,
  `mobile_phone` varchar(20) NOT NULL,
  `is_validated` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `credit_line` decimal(10,2) unsigned NOT NULL,
  `passwd_question` varchar(50) DEFAULT NULL,
  `passwd_answer` varchar(255) DEFAULT NULL,
  `wxid` char(28) NOT NULL,
  `wxch_bd` char(2) NOT NULL,
  `nicheng` varchar(255) DEFAULT NULL,
  `password_tianxin` varchar(40) NOT NULL,
  `password_account_replay` varchar(255) NOT NULL COMMENT '提现密码',
  `limit_account_replay` int(11) NOT NULL DEFAULT '0' COMMENT '提现额度',
  `nick_name` varchar(255) NOT NULL,
  `headpic_thumb` varchar(255) NOT NULL,
  `subscribe_time` int(10) NOT NULL,
  `is_subscribe` int(1) NOT NULL DEFAULT '0' COMMENT '是否关注',
  `affiliate_id` int(10) NOT NULL DEFAULT '0',
  `second_affiliate_id` int(10) NOT NULL DEFAULT '0',
  `third_affiliate_id` int(10) NOT NULL DEFAULT '0',
  `brokerage_all` decimal(8,2) NOT NULL DEFAULT '0.00',
  `brokerage_first` decimal(8,2) NOT NULL DEFAULT '0.00',
  `brokerage_second` decimal(8,2) NOT NULL DEFAULT '0.00',
  `brokerage_third` decimal(8,2) NOT NULL DEFAULT '0.00',
  `sales_all` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '分销销售额',
  `sales_first` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '一级销售额',
  `sales_second` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '二级销售额',
  `sales_third` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '三级销售额',
  `is_distributor` int(1) NOT NULL DEFAULT '0' COMMENT '是否为分销商',
  `be_distributor_time` int(10) NOT NULL DEFAULT '0',
  `real_name` varchar(50) NOT NULL COMMENT '真实姓名',
  `bank_name` varchar(50) NOT NULL COMMENT '开户行',
  `bank_account` varchar(100) NOT NULL COMMENT '银行账号',
  `sales1_id` decimal(10,0) NOT NULL DEFAULT '0' COMMENT '一级上级',
  `sales2_id` decimal(10,0) NOT NULL DEFAULT '0' COMMENT '二级上级',
  `sales3_id` decimal(10,0) NOT NULL DEFAULT '0' COMMENT '三级上级',
  `sales4_id` decimal(10,0) NOT NULL DEFAULT '0' COMMENT '四级上级',
  `sales5_id` decimal(10,0) NOT NULL DEFAULT '0' COMMENT '五级上级',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_name` (`user_name`),
  KEY `email` (`email`),
  KEY `parent_id` (`parent_id`),
  KEY `flag` (`flag`),
  KEY `user_id` (`user_id`),
  KEY `pay_points` (`pay_points`),
  KEY `wxid` (`wxid`)
) ENGINE=InnoDB AUTO_INCREMENT=4179 DEFAULT CHARSET=utf8");
E_D("replace into `ecs_users` values('4176','','','111111','7ccd52a2b44e7f99983ebf94c1c34b22','','','0','1955-01-01','0.00','0.00','21','21','1308','0','1447803165','0000-00-00 00:00:00','127.0.0.1','8','0','0','8496','0','4177','0','','','','','','','0','0.00',NULL,NULL,'','',NULL,'','','0','','','0','0','0','0','0','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0','0','','','','4177','0','0','0','0');");
E_D("replace into `ecs_users` values('4177','','','11111111','d3cbc9d5a87896c8bbe823a72e109675','','','0','1955-01-01','25.20','0.00','0','0','1307','0','1447234081','0000-00-00 00:00:00','127.0.0.1','2','0','0','4285','0','0','0','','','','','','','0','0.00',NULL,NULL,'','',NULL,'111111','','0','','','0','0','0','0','0','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0','0','','','','0','0','0','0','0');");
E_D("replace into `ecs_users` values('4178','','','ceshi123','059d38a8c888d5109fa33a9815866013','','','0','0000-00-00','0.00','0.00','0','0','0','0','1444714104','0000-00-00 00:00:00','0.0.0.0','1','0','0',NULL,'0','0','0','','','','','','','0','0.00',NULL,NULL,'','',NULL,'','','0','','','0','0','0','0','0','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0','0','','','','0','0','0','0','0');");

require("../../inc/footer.php");
?>