<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wxch_config`;");
E_C("CREATE TABLE `wxch_config` (
  `id` int(1) NOT NULL,
  `token` varchar(100) NOT NULL,
  `appid` char(18) NOT NULL,
  `appsecret` char(32) NOT NULL,
  `access_token` text NOT NULL,
  `dateline` int(10) unsigned NOT NULL,
  `is_auto_distribute` int(1) NOT NULL DEFAULT '0' COMMENT '是否自动分销',
  `two_level_deduct` int(3) NOT NULL DEFAULT '0',
  `three_level_deduct` int(3) NOT NULL DEFAULT '0',
  `is_userdefine_subscribe` int(1) NOT NULL DEFAULT '0' COMMENT '是否自定义关注回复',
  `subscribe_guide_page` varchar(255) NOT NULL COMMENT '引导关注页网址',
  `app_name` varchar(50) NOT NULL COMMENT '公众号名称',
  `init_subscribe_user_counts` int(8) NOT NULL DEFAULT '0' COMMENT '初始化关注人数',
  `is_auto_deposit` int(1) NOT NULL DEFAULT '0' COMMENT '充值知否自动入账',
  `deposit_lower_limit` decimal(6,2) NOT NULL DEFAULT '1.00' COMMENT '充值最低限制',
  `is_distributor_limit` int(1) NOT NULL DEFAULT '1' COMMENT '自己关注能否成为分销商',
  `is_my_distribute_view` int(1) NOT NULL DEFAULT '1',
  `is_my_subscribe_view` int(1) NOT NULL DEFAULT '0',
  `home_page_style` int(1) NOT NULL DEFAULT '1',
  `withdraw_alert_msg` varchar(255) NOT NULL,
  `distribute_keywords` varchar(20) NOT NULL DEFAULT '分销',
  `withdraw_lower_limit` decimal(6,2) NOT NULL DEFAULT '100.00' COMMENT '提现底限',
  `distributor_name` varchar(20) NOT NULL DEFAULT '分销商',
  `one_level_member_name` varchar(20) NOT NULL DEFAULT '一级会员',
  `two_level_member_name` varchar(20) NOT NULL DEFAULT '二级会员',
  `three_level_member_name` varchar(20) NOT NULL DEFAULT '三级会员',
  `be_distributor` decimal(6,2) NOT NULL DEFAULT '100.00' COMMENT '成为分销商',
  `one_level_deduct` int(3) NOT NULL DEFAULT '0',
  `limit_account_replay` varchar(40) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");
E_D("replace into `wxch_config` values('1','weixin','1','3','3uNvtWWYh7L4Pi_cbA7jc_bosPTiiHjgoXwiYAzZhjut4raG7_ZFTTmahROvjcVs_NlesDyEg3ZZnt_XwwUG1vI1EazrkukPeNzWUkIZEt4','1433199247','0','0','0','0','','','0','0','1.00','1','1','0','1','','分销','100.00','分销商','一级会员','二级会员','三级会员','100.00','0','');");

require("../../inc/footer.php");
?>