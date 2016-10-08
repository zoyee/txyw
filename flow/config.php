<?php
header("Content-type: text/html; charset=utf-8"); 
define('DB_CONFIG_PATH', '../data/config.php');		//数据库配置文件路径
require(DB_CONFIG_PATH);
//流量商户中心的配置加密部分-----需要配置
define('APPID', 'wx8e70f6100821dd9e');						//微信APPID
define('APPSECRET', 'c7b2704a4549511abcb96d13e9cc5363');	//微信APPSECRET
define('SITE_API_KEY', '9k6w67df');						//加密秘钥
define('MPID', 6);	                                     //流量平台分配给商户的ID
define('REFERER', 'http://shop.byhill.com/mobile/');          //用来判断是从哪个网址过来的，暂时没有用
define('OPENID_PREFIX','0');//微信openid的cookie前缀，为0则不开启
define('THIS_URL','http://shop.byhill.com');//流量插件所在根目录网址（后尾不带“/”）


//以下是针对ecshop微商城的-不同系统可自定义更改；
define('ASSIGN_NAME', 'jgzb');       //用户表的指定用户名---------默认前辍
define('ASSIGN_PASSWORD', 'jgzba');//用户表的指定密码（密码默认MD5）-----------指定密码

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

//微信站用户表与微信信息表关联字段
define('USER_TO_WECHAT', 'user_name');       			//用户表通过哪个字段与微信表关联
define('WECHAT_TO_USER', 'uname');       			//微信表通过哪个字段与主用户表关联
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
//积分兑换参数设置
define('EXCHANGE_IS_USE', '1');       			//是否启用积分兑换记录写入，1：写入;0:不写入
define('EXCHANGE_TABLE', 'ecs_account_log');       			//积分兑换表名
define('EXCHANGE_USERID', 'user_id');       			//用户ID字段名
define('EXCHANGE_POINTS', 'pay_points');       			//积分字段
define('EXCHANGE_SYMBOL', '0');       			//扣积分值时是否写入加符号"-";0:默认不写入
define('EXCHANGE_TIME_TYPE', '1');       			//写入时间格式，1：时间戳，2：时间日期，2012-12-30 ，3：带时间秒，0：不写入
define('EXCHANGE_TIME', 'change_time');       			//兑换时间字段
define('EXCHANGE_TYPE', 'change_type');       			//消费类型字段名,0为没有
define('EXCHANGE_TYPE_VALUE', '99');       			//消费类型字段值
//流量中心的接口网址-----不得改动
define('PCONNECT',1);
define('LOG',1);
define('LOGFILEPATH','./log/dblog/');//兑换日志路径
define('EXCHANGEPATH','./log/exchange_log/');//数据库操作日志路径


?>