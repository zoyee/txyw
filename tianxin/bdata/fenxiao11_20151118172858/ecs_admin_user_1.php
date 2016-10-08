<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `ecs_admin_user`;");
E_C("CREATE TABLE `ecs_admin_user` (
  `user_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `user_name` varchar(60) NOT NULL DEFAULT '',
  `email` varchar(60) NOT NULL DEFAULT '',
  `password` varchar(32) NOT NULL DEFAULT '',
  `ec_salt` varchar(10) DEFAULT NULL,
  `add_time` int(11) NOT NULL DEFAULT '0',
  `last_login` int(11) NOT NULL DEFAULT '0',
  `last_ip` varchar(15) NOT NULL DEFAULT '',
  `action_list` text NOT NULL,
  `nav_list` text NOT NULL,
  `lang_type` varchar(50) NOT NULL DEFAULT '',
  `agency_id` smallint(5) unsigned NOT NULL,
  `suppliers_id` smallint(5) unsigned DEFAULT '0',
  `todolist` longtext,
  `role_id` smallint(5) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  KEY `user_name` (`user_name`),
  KEY `agency_id` (`agency_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8");
E_D("replace into `ecs_admin_user` values('1','admin','584027996@qq.com','15adfd165589131b6970ff89f4ea558c','2148','1403613471','1447838457','127.0.0.1','all','商品列表|goods.php?act=list,订单列表|order.php?act=list,用户评论|comment_manage.php?act=list,会员列表|users.php?act=list,商店设置|shop_config.php?act=list_edit','','0','0','','0');");
E_D("replace into `ecs_admin_user` values('2','tianxin100','admin@admin.com','6c70606973404c2f57bdaede4d5b3b9b','6750','0','1432509498','61.145.16.196','all','','','0','0','','0');");
E_D("replace into `ecs_admin_user` values('3','xiaojun','1212@qq.com','7a278f3b7794afb6eee64aa372174edb','3007','1434188940','1444036032','127.0.0.1','goods_manage,cat_manage,cat_drop,attr_manage,virualcard,goods_type,remove_back,comment_priv,template_select,template_setup,library_manage,lang_edit,backup_setting,mail_template,wx_api,wx_menu,wx_config,wx_bonus,wx_regmsg,wx_lang,wx_keywords,wx_point,wx_fun,wx_prize,wx_zjd,wx_dzp,wx_qr,wx_order,wx_pay,wx_reorder,wx_fans,wx_oauth,wx_tuijian,wx_list,wx_autoreg,affiliate,affiliate_ck,danpin_tuiguang,zdy_parent,db_backup,db_optimize,sql_query','商品列表|goods.php?act=list,订单列表|order.php?act=list,用户评论|comment_manage.php?act=list,会员列表|users.php?act=list,商店设置|shop_config.php?act=list_edit','','0','0',NULL,'0');");

require("../../inc/footer.php");
?>