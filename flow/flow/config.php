<?php
header("Content-type: text/html; charset=utf-8"); 
define('DB_CONFIG_PATH', '../data/config.php');		//数据库配置文件路径
require(DB_CONFIG_PATH);
//流量商户中心的配置加密部分-----需要配置
define('APPID', 'wx8e70f6100821dd9e');						//微信APPID
define('APPSECRET', 'c7b2704a4549511abcb96d13e9cc5363');	//微信APPSECRET
define('SITE_API_KEY', '9k6w67df');						//积分兑换加密秘钥
define('MPID', 6);                                        //流量平台分配给商户的ID
define('REFERER', 'http://WWW.TIANXIN.COM/');          //用来判断是从哪个网址过来的，暂时没有用


//以下是针对ecshop微商城的-不同系统可自定义更改--暂时用不上；
define('ASSIGN_NAME', 'TIANXIN');       //用户表的指定用户名---------默认前辍
define('ASSIGN_PASSWORD', 'TIANXIN');//用户表的指定密码（密码默认MD5）-----------指定密码

//以下针对ecshop微商城接口字段配置，不需要做更改
define('DB_HOST', $db_host);          //数据库地址
define('DB_USER', $db_user);          //数据库用户名
define('DB_PASS', $db_pass);          //密码
define('DB_NAME', $db_name);          //数据库名
define('PASS_SALT', '');              //加密密码的盐值，可留空
//商户微信站库存用户信息字段
define('USER_TABLE', 'ecs_users');    //用户表表名
define('USER_ID', 'user_id');              //用户表的用户ID字段名
define('USER_NAME', 'user_name');     //用户表的用户名字段
define('USER_PASSWORD', 'password');       //用户表的密码字段
define('USER_POINTS', 'pay_points');       //用户表的积分字段
//商户微信站库存微信信息字段
define('WECHAT_TABLE', 'wxch_user');  //微信信息表表名
define('USERID', 'uid');              				//微信表的用户ID字段名
define('OPENID', 'wxid');             				//微信表的openid字段名
define('WECHAT_SUBSCRIBE', 'subscribe');       		//微信表的订阅字段
define('WECHAT_NICKNAME', 'nickname');       		//微信表的昵称字段
define('WECHAT_SEX', 'sex');       					//微信表的性别字段
define('WECHAT_CITY', 'city');       				//微信表的城市字段
define('WECHAT_COUNTRY', 'country');            //微信表的国家字段
define('WECHAT_UNAME', 'uname');       			//微信表的用户名字段名
define('WECHAT_PROVINCE', 'province');       		//微信表的省字段
define('WECHAT_LANGUAGE', 'language');       		//微信表的语言字段
define('WECHAT_HEADIMGURL', 'headimgurl');      	//微信表的头像字段
define('WECHAT_SUBSCRIBE_TIME', 'subscribe_time');  //微信表的订阅时间字段
//流量中心的接口网址-----不得改动
define('PCONNECT',1);
define('LOG',1);
define('LOGFILEPATH','./log/dblog/');//兑换日志路径
define('EXCHANGEPATH','./log/exchange_log/');//数据库操作日志路径

?>