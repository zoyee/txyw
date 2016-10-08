<?php

header("Content-type: text/html; charset=utf-8"); 
define('API_DOMAIN', 'http://user.api.weihaima.com/api.php/');	//接口请求地址
define('USER_DOMAIN', 'http://user.weihaima.com/');	       //流量中心前端页面地址	
// require( '../data/config.php');//数据库配置文件路径
//数据库配置文件路径
require('config.php');
require('mysql_connect.php');		//载入数据库功能文件
require('exchange_function.php');		//载入方法文件
			header("Content-type: text/html; charset=utf-8");
			
			if(OPENID_PREFIX&&isset($_COOKIE[OPENID_PREFIX.'openid'])){
				$openid_cookie=$_COOKIE[OPENID_PREFIX.'openid'];
			}//判断是否开启openid的cookie

if(OPENID_PREFIX){
	if(!empty($_COOKIE[OPENID_PREFIX.'num'])){
		if($_COOKIE[OPENID_PREFIX.'num']>5){
			setcookie(OPENID_PREFIX.'num');
			setcookie(OPENID_PREFIX.'num',"",time()-11);
			setcookie(OPENID_PREFIX.'openid',"",time()-11);

		}
		setcookie(OPENID_PREFIX.'num',(int)$_COOKIE[OPENID_PREFIX.'num']+1);
	}else{
		setcookie(OPENID_PREFIX.'num',1);
	}
}





			if(isset($_POST['action']))//如果有发送请求过来
			{	
				if($_POST['verify']==md5(SITE_API_KEY.APPID.APPSECRET))//验证头信息和md5加密信息
				{
				$flag = callBack();
				echo $flag;//数据库成功返回1，失败返回空
				}
				else{
					echo -1;//验证失败返回-1
				}
			}
			else{


			 //$callbacksUrl = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];	//微信授权回调地址

			
			 $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.APPID.'&redirect_uri='.THIS_URL.'/flow/exchange.php'.'&response_type=code&scope=snsapi_base&state= #wechat_redirect';
			 if( empty($_GET['code']) ){
				header('location:'.$url);
			 }else{
			 $get_openid_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.APPID.'&secret='.APPSECRET.'&code='.$_GET['code'].'&grant_type=authorization_code';
			 
			 if(isset($openid_cookie)&&!empty($openid_cookie)){
					$openId =	$openid_cookie;
			 }else{
					$data = get_openid($get_openid_url);
					$openId = $data['openid'];
					if(OPENID_PREFIX){setcookie(OPENID_PREFIX.'openid',$openId);}//是否生成openid的session
			 }
			 
			

			if($openId){

				if( empty($_GET['userid']) ){$user = getUserMessage($openId);}
				else{$user = (int)$_GET['userid'];}
				//$user = getUserMessage($openId);20160204增加会员中心带userid直接进入
				if($user)
				{
					$user['mpid']=MPID;//$user里面也放入MPID用于用户端的验证，两个mpid相等才通过验证
					$user['openid']=$openId;
					$user =json_encode($user);
					$enData = '?siteApiKey='.SITE_API_KEY.'&data='.$user.'&mpid='.MPID.'&APPID='.APPID.'&APPSECRET='.APPSECRET.'&SITE_API_KEY='.SITE_API_KEY;//发送过去加密encode，urlencode，下标data
					$data = curl_get(API_DOMAIN.'EncryptionExchange/encode'.$enData);
					$data=json_decode($data,true);
					
					if($data['returnCode']!=1) //如果returnCode不等于0(即该公众号通过了各项验证，包括开通了积分兑换等)
					{	
						echo '跳转中请稍后';
						header('location:'.THIS_URL.'/flow/exchange.php');exit;
						echo '<!doctype html>
						<html>
						<head>
						<meta charset="utf-8">
						<title>自动跳转</title>
						<style>
						*{margin:0;padding:0;outline:none;font-family:\5FAE\8F6F\96C5\9ED1,宋体;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;-khtml-user-select:none;user-select:none;cursor:default;font-weight:lighter;}
						.center{margin:0 auto;}
						.whole{width:100%;height:100%;line-height:100%;position:fixed;bottom:0;left:0;z-index:-1000;overflow:hidden;}
						.whole img{width:100%;height:100%;}
						.mask{width:100%;height:100%;position:absolute;top:0;left:0;background:#000;opacity:0.6;filter:alpha(opacity=60);}
						.b{width:100%;text-align:center;height:400px;position:absolute;top:50%;margin-top:-230px}.a{width:150px;height:50px;margin-top:30px}.a a{display:block;float:left;width:150px;height:50px;background:#fff;text-align:center;line-height:50px;font-size:18px;border-radius:25px;color:#333}.a a:hover{color:#000;box-shadow:#fff 0 0 20px}
						p{color:#fff;margin-top:40px;font-size:24px;}
						#num{margin:0 5px;font-weight:bold;}
						</style>
						<script type="text/javascript">
							var num=5;
							function redirect(){
								num--;
								document.getElementById("num").innerHTML=num;
								if(num<0){
									document.getElementById("num").innerHTML=0;
									location.href="'.$data['url'].'";
									}
								}
							setInterval("redirect()", 1000);
						</script>
						</head>

						<body onLoad="redirect();">
						<div class="whole">
							<div class="mask"></div>
						</div>
						<div class="b">
								<h1 style="color:#fff;font-size:60px;">'.$data['returnMsg'].'</h1>
								<p style="font-size:40px;">
									<br>
									<span id="num">5</span>秒后自动跳转...
								</p>
								<p><a href="'.$data['url'].'" style="color:white;font-size:60px;">点此手动跳转...</a></p>
								</div>

						</body>
						</html>';
						exit;
					}
					else{
						setcookie(OPENID_PREFIX.'num',"",time()-11);
						$params = '?mpid='.$data['mpid'].'&data='.$data['data'];
						// $ExchangeUrl = JING_DOMAIN.$params;
						$ExchangeUrl = API_DOMAIN.'Exchange/exchange'.$params;
						
						header('location:'.$ExchangeUrl);
					}
				}
				else
				{		setcookie(OPENID_PREFIX.'num');
						echo '用户不存在';
				}
			}
			else{

				header('location:'.$url);
			}
		 }
		}

?>
