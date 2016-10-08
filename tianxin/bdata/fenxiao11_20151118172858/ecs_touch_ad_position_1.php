<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `ecs_touch_ad_position`;");
E_C("CREATE TABLE `ecs_touch_ad_position` (
  `position_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `position_name` varchar(60) NOT NULL DEFAULT '',
  `ad_width` smallint(5) unsigned NOT NULL DEFAULT '0',
  `ad_height` smallint(5) unsigned NOT NULL DEFAULT '0',
  `position_desc` varchar(255) NOT NULL DEFAULT '',
  `position_style` text NOT NULL,
  PRIMARY KEY (`position_id`)
) ENGINE=MyISAM AUTO_INCREMENT=31 DEFAULT CHARSET=utf8");
E_D("replace into `ecs_touch_ad_position` values('1','手机端首页广告1-2','1','1','','{foreach from=\$ads item=ad}\r\n{\$ad}\r\n{/foreach}\r\n');");
E_D("replace into `ecs_touch_ad_position` values('2','手机端首页广告1-1','1','1','','{foreach from=\$ads item=ad}\r\n{\$ad}\r\n{/foreach}\r\n');");
E_D("replace into `ecs_touch_ad_position` values('3','wap首页幻灯广告','1','1','','{foreach from=\$ads item=ad}\r\n{\$ad}\r\n{/foreach}');");
E_D("replace into `ecs_touch_ad_position` values('4','wap团购页面广告','1','1','','<table cellpadding=\"0\" cellspacing=\"0\">\r\n{foreach from=\$ads item=ad}\r\n<tr><td>{\$ad}</td></tr>\r\n{/foreach}\r\n</table>');");
E_D("replace into `ecs_touch_ad_position` values('6','手机端首页广告1-3','1','1','','{foreach from=\$ads item=ad}\r\n{\$ad}\r\n{/foreach}');");
E_D("replace into `ecs_touch_ad_position` values('7','手机端首页广告2-1','1','1','','{foreach from=\$ads item=ad}\r\n{\$ad}\r\n{/foreach}\r\n');");
E_D("replace into `ecs_touch_ad_position` values('8','手机端首页广告2-2','1','1','','{foreach from=\$ads item=ad}\r\n{\$ad}\r\n{/foreach}\r\n');");
E_D("replace into `ecs_touch_ad_position` values('9','手机端首页广告3-2','1','1','','{foreach from=\$ads item=ad}\r\n{\$ad}\r\n{/foreach}\r\n');");
E_D("replace into `ecs_touch_ad_position` values('11','手机端首页广告3-1','1','1','','{foreach from=\$ads item=ad}\r\n{\$ad}\r\n{/foreach}');");
E_D("replace into `ecs_touch_ad_position` values('17','手机端首页广告3-3','1','1','','{foreach from=\$ads item=ad}\r\n{\$ad}\r\n{/foreach}\r\n');");
E_D("replace into `ecs_touch_ad_position` values('29','手机端首页精品推荐广告','1','1','','{foreach from=\$ads item=ad}\r\n{\$ad}\r\n{/foreach}');");
E_D("replace into `ecs_touch_ad_position` values('15','分类-1-促销广告','1','1','','<table cellpadding=\"0\" cellspacing=\"0\">\r\n{foreach from=\$ads item=ad}\r\n<tr><td>{\$ad}</td></tr>\r\n{/foreach}\r\n</table>');");
E_D("replace into `ecs_touch_ad_position` values('16','手机端首页广告4','1','1','','\r\n{foreach from=\$ads item=ad}\r\n{\$ad}\r\n{/foreach}\r\n');");
E_D("replace into `ecs_touch_ad_position` values('24','微分销店铺楼层广告4','1','1','','<table cellpadding=\"0\" cellspacing=\"0\">\r\n{foreach from=\$ads item=ad}\r\n<tr><td>{\$ad}</td></tr>\r\n{/foreach}\r\n</table>');");
E_D("replace into `ecs_touch_ad_position` values('25','微分销店铺楼层广告5','1','1','','<table cellpadding=\"0\" cellspacing=\"0\">\r\n{foreach from=\$ads item=ad}\r\n<tr><td>{\$ad}</td></tr>\r\n{/foreach}\r\n</table>');");
E_D("replace into `ecs_touch_ad_position` values('26','微分销店铺楼层广告6','1','1','','<table cellpadding=\"0\" cellspacing=\"0\">\r\n{foreach from=\$ads item=ad}\r\n<tr><td>{\$ad}</td></tr>\r\n{/foreach}\r\n</table>');");
E_D("replace into `ecs_touch_ad_position` values('27','微分销店铺楼层广告7','1','1','','<table cellpadding=\"0\" cellspacing=\"0\">\r\n{foreach from=\$ads item=ad}\r\n<tr><td>{\$ad}</td></tr>\r\n{/foreach}\r\n</table>');");
E_D("replace into `ecs_touch_ad_position` values('21','微分销店铺楼层广告1','1','1','','{foreach from=\$ads item=ad}\r\n{\$ad}\r\n{/foreach}\r\n');");
E_D("replace into `ecs_touch_ad_position` values('22','微分销店铺楼层广告2','1','1','','\r\n{foreach from=\$ads item=ad}\r\n{\$ad}\r\n{/foreach}\r\n');");
E_D("replace into `ecs_touch_ad_position` values('23','微分销店铺楼层广告3','1','1','','<table cellpadding=\"0\" cellspacing=\"0\">\r\n{foreach from=\$ads item=ad}\r\n<tr><td>{\$ad}</td></tr>\r\n{/foreach}\r\n</table>');");
E_D("replace into `ecs_touch_ad_position` values('28','微分销店铺楼层广告8','1','1','','<table cellpadding=\"0\" cellspacing=\"0\">\r\n{foreach from=\$ads item=ad}\r\n<tr><td>{\$ad}</td></tr>\r\n{/foreach}\r\n</table>');");
E_D("replace into `ecs_touch_ad_position` values('30','手机版首页Banner','360','168','','<ul>\r\n{foreach from=\$ads item=ad}\r\n  <li>{\$ad}</li>\r\n{/foreach}\r\n</ul>\r\n');");

require("../../inc/footer.php");
?>