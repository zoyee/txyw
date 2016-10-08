<?php
define('API_DOMAIN', 'http://user.api.weihaima.com/api.php/');	//接口请求地址
define('USER_DOMAIN', 'http://user.weihaima.com/');	       //流量中心前端页面地址
//数据库配置文件路径
require('config.php'); //流量平台分配给商户的ID		

header("Content-type: text/html; charset=utf-8");

			if(OPENID_PREFIX&&isset($_COOKIE[OPENID_PREFIX.'openid'])){
				$openid_cookie=$_COOKIE[OPENID_PREFIX.'openid'];
			}//判断是否开启openid的cookie

if(OPENID_PREFIX){
	if(!empty($_COOKIE[OPENID_PREFIX.'num'])){
		if($_COOKIE[OPENID_PREFIX.'num']>5){
			setcookie(OPENID_PREFIX.'num');
			setcookie(OPENID_PREFIX.'openid');

		}
		setcookie(OPENID_PREFIX.'num',(int)$_COOKIE[OPENID_PREFIX.'num']+1);
	}else{
		setcookie(OPENID_PREFIX.'num',1);
	}
}

//$callbacksUrl = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];	//微信授权回调地址

$url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.APPID.'&redirect_uri='.THIS_URL.'/flow/sign.php'.'&response_type=code&scope=snsapi_base&state= #wechat_redirect';
if( empty($_GET['code']) ){
	header('location:'.$url);
}else{
	
	$get_code_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.APPID.'&secret='.APPSECRET.'&code='.$_GET['code'].'&grant_type=authorization_code';
	
		if(isset($openid_cookie)&&!empty($openid_cookie)){
					$openId =	$openid_cookie;
		}else{
			$data = get_openid($get_code_url);
			$openId = $data['openid'];
			if(OPENID_PREFIX){setcookie(OPENID_PREFIX.'openid',$openId);}//是否生成openid的session
			 
			 
		}
		
	
	if($openId){
		if( empty($_GET['userid']) ){
			$mpSiteUserId = getUserId($openId);
		}else{
			$mpSiteUserId = (int)$_GET['userid'];
		}
		$enData = '?siteApiKey='.SITE_API_KEY.'&data[mpSiteUserId]='.$mpSiteUserId.'&data[mpId]='.MPID.'&data[openId]='.$openId;
		$token = curl_get(API_DOMAIN.'Encryption/encode'.$enData);
		$token = json_decode($token, true);

		if($token['returnCode']==1){
			  $token = $token['data'];
				$params = 'mpId='.MPID.'&data='.$token;
				$signInUrl = API_DOMAIN.'SignIn/executeApi?'.$params;

					setcookie(OPENID_PREFIX.'num',"",time()-11);
				header('location:'.$signInUrl);
		}else{


			  header('location:'.THIS_URL.'/flow/sign.php');
		}


	}else{
		echo '微信授权失败！';
	}
}


function curl_get($url){
	// 初始化一个 cURL 对象
	$curl = curl_init();

	// 设置你需要抓取的URL
	curl_setopt($curl, CURLOPT_URL, $url);

	// 设置
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	//curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($curl, CURLOPT_HEADER, 0);

	// 设置cURL 参数，要求结果保存到字符串中还是输出到屏幕上。
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

	// 运行cURL，请求网页
	$data = curl_exec($curl);

	// 关闭URL请求
	curl_close($curl);

	// 显示获得的数据
	return $data;
}



function get_openid($url,$num=0){
	$info=curl_get($url);
	$num++;
	$info_arr=json_decode($info, true);
	if(empty($info_arr['openid'])){
		if($num>3){echo '微信服务器繁忙，请稍后再试';}
		$info=get_openid($url,$num);

	}
	$info=json_decode($info, true);
	return $info;
}

function getUserId( $openid ){
  //$con = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);//旧的
  $con=mysql_connect(DB_HOST, DB_USER, DB_PASS);
  mysql_select_db(DB_NAME,$con); 
	
  if (!$con)
  {
  die('Could not connect: ' . mysql_error());
  }
  
  //mysql_set_charset($con, "utf8");
  mysql_query("set names 'utf8'"); 
  $result = mysql_query("SELECT ".WECHAT_TO_USER." FROM ".WECHAT_TABLE." WHERE ".OPENID."='{$openid}' limit 1",$con);
	
  $row = mysql_fetch_assoc($result);
  if (!empty($row)) {
     $result = mysql_query( "SELECT ".USER_ID.",".USER_POINTS.",".USER_NAME." FROM ".USER_TABLE." WHERE ".USER_TO_WECHAT."='{$row[WECHAT_TO_USER]}' limit 1",$con);
      $row = mysql_fetch_assoc($result);
  }
	

  //写入新用户的数据，不详细，可能存在从这个链接过去新建的用户，在其它按钮重新进入商城，

  if( empty($row) ){
  	  //用户表
  	  $maxId = mysql_query( "SELECT MAX(".USER_ID.") AS id FROM ".USER_TABLE,$con);
  	  $maxId = mysql_fetch_assoc($maxId);
  	  $maxId = ++$maxId['id'];
  	  $userAddArray = array();
  	  $userAddArray[USER_NAME] = ASSIGN_NAME.$maxId;
  	  $userAddArray[USER_PASSWORD] = md5(ASSIGN_PASSWORD.PASS_SALT);
  	  $userAddData = jointSqlAdd($userAddArray);

      $userAdd = "INSERT INTO ".USER_TABLE.$userAddData;
      mysql_query($userAdd,$con);
      $userId = mysql_insert_id($con);

      $getAccessTokenUrl = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.APPID.'&secret='.APPSECRET;
      $accessToken = json_decode(curl_get($getAccessTokenUrl), true);
      $accessToken = $accessToken['access_token'];
      $getWechatInfoUrl = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$accessToken.'&openid='.$openid.'&lang=zh_CN';
      
      $wechatInfo = curl_get($getWechatInfoUrl);
      $wechatInfo = json_decode($wechatInfo, true);

      $wcUserAddArray = array();
      if(USERID){
      	$wcUserAddArray[USERID] = $userId;
      }
      if(OPENID){
      	$wcUserAddArray[OPENID] = $wechatInfo['openid'];
      }
      if(WECHAT_SUBSCRIBE){
      	$wcUserAddArray[WECHAT_SUBSCRIBE] = $wechatInfo['subscribe'];
      }
      if(WECHAT_NICKNAME){
      	$wcUserAddArray[WECHAT_NICKNAME] = $wechatInfo['nickname'];
      }
      if(WECHAT_SEX){
      	$wcUserAddArray[WECHAT_SEX] = $wechatInfo['sex'];
      }
      if(WECHAT_CITY){
      	$wcUserAddArray[WECHAT_CITY] = $wechatInfo['city'];
      }
      if(WECHAT_COUNTRY){
      	$wcUserAddArray[WECHAT_COUNTRY] = $wechatInfo['country'];
      }
      if(WECHAT_PROVINCE){
      	$wcUserAddArray[WECHAT_PROVINCE] = $wechatInfo['province'];
      }
      if(WECHAT_UNAME){
        $wcUserAddArray[WECHAT_UNAME] = ASSIGN_NAME.$maxId;
      }
      if(WECHAT_LANGUAGE){
      	$wcUserAddArray[WECHAT_LANGUAGE] = $wechatInfo['language'];
      }
      if(WECHAT_HEADIMGURL){
      	$wcUserAddArray[WECHAT_HEADIMGURL] = $wechatInfo['headimgurl'];
      }
      if(WECHAT_SUBSCRIBE_TIME){
      	$wcUserAddArray[WECHAT_SUBSCRIBE_TIME] = $wechatInfo['subscribe_time'];
      }
      $wechatAddData = jointSqlAdd($wcUserAddArray);

      $wcUserAdd = "INSERT INTO ".WECHAT_TABLE.$wechatAddData;
      mysql_query($con, $wcUserAdd);
      return $userId;
  }else{
      return $row[USER_ID];
  }
}

function jointSqlAdd($array){
	if( !is_array($array) ){
		return false;
	}
	$sql = ' (';
	$field = '';
	$value = '';
	foreach($array as $k=>$v){
		$field .= $k.',';
		$value .= '\''.$v.'\',';
	}
	$field = rtrim($field, ',');
	$value = rtrim($value, ',');
	$sql .= $field.') ';
	$sql .= ' VALUES (';
	$sql .= $value.') ';


	return $sql;
}
