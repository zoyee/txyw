<?php
	/* ���ܺ�������� functions and flows
	   by liaozhihua
	   201510
	   Version 1.0 ֧��mysql��des�㷨
	*/
	require_once("mysql_connect.php");
	// $config = include_once("conf.php");

	#���� �ص��������ο���
	#���� ��ݿ����ƣ��û�id�ֶΣ�����ֶΣ��û�id���������ֵ, ȫ�ִ������� $config
	#���� ��
	function callBack() {
		$params=array();
		$params=$_POST;
		$db = new Mysql();
		if($_POST['action']=='pay')//�ۻ��
		{	
			$data[USER_POINTS]=USER_POINTS.'-'.$params['points'];
			change_points_log($params['user_id'],$params['points']);//��ֶһ��ռ�д��
		}
		elseif($_POST['action']=='back')//�������
		{
		$data[USER_POINTS]=USER_POINTS.'+'.$params['points'];
		}
		$where=USER_ID.' = '.$params['user_id'];
		$result=$db->update(USER_TABLE,$data,$where);
		 // --  ���ñ���Ŀ¼ --
		if($result)
		{
			$log['state']='1';//���������ɹ�
			if($params['action']=='pay')
			{
				$log['msg']=urlencode('��ȡ��ֳɹ�');
			}
			elseif($params['action']=='back')
			{
				$log['msg']=urlencode('������ֳɹ�');
			}
		}
		else
		{
			$log['state']='-1';//���������ɹ�
			if($params['action']=='pay')
			{
				$log['msg']=urlencode('��ȡ���ʧ��');
			}
			elseif($params['action']=='back')
			{
				$log['msg']=urlencode('�������ʧ��');
			}
		}
		$log['Water_Account']=$params['Water_Account'];
		$log['action']=$params['action'];
		$path = EXCHANGEPATH.date('Y-m');
		$folder = Mk_Folder($path);
		$folder .= $path.'/'.date('d').'.txt';
		$log['time']=date('Y-m-d h:i:s',time());
		file_put_contents($folder,urldecode(json_encode($log)).PHP_EOL,FILE_APPEND);
		 // --  ���ñ���Ŀ¼ --
		return $result;

		
	}


	function change_points_log($user_id,$pay_points){
		if(EXCHANGE_IS_USE){//EXCHANGE_IS_USE�Ƿ�����ֶһ��ռ�д��
			$data=array();
			$data[EXCHANGE_USERID]=$user_id;//�û�id
			$data[EXCHANGE_POINTS]=$pay_points;//�һ����
			if(EXCHANGE_SYMBOL){$data[EXCHANGE_POINTS]=EXCHANGE_SYMBOL.$pay_points;}//���ǰ��ƴ���
			
			//ʱ���ʽ����
			switch(EXCHANGE_TIME_TYPE){
				case 1:
					$time1=time();
				break;
				
				case 2:
					$time1=data('Y-m-d',time());
				break;

				case 3:
					$time1=data('Y-m-d H:i:s',time());
				break;
			}
			if(EXCHANGE_TIME_TYPE){$data[EXCHANGE_TIME]=$time1;}
			//ʱ���ʽ����end


			if(EXCHANGE_TYPE){$data[EXCHANGE_TYPE]=EXCHANGE_TYPE_VALUE;}//�������
			

			$db = new Mysql();
			$db->insert(EXCHANGE_TABLE,$data);
		}
	}



	#���� �ۻ�֣������ο���
	#���� ��ݿ����ƣ��û�id�ֶΣ�����ֶΣ��û�id���������ֵ, ȫ�ִ������� $config
	#���� ��
/* 	function payReduceCredit($userid, $credit) {
		global $config;
		$db = new Mysql($config['db']);
		#ecshop
		$table = 'ecs_users';
		$ccol   = 'pay_points';
		$ucol  = 'user_id';
		$sql = "UPDATE {$table} SET {$ccol}={$ccol}-{$credit} WHERE  {$ucol} = {$userid}";
		$flag = $db->query($sql);
		if($flag == false)
			return false;
		else 
			return true;
	} 
	
	#���� ������֣������ο���
	#���� ��ݿ����ƣ��û�id�ֶΣ�����ֶΣ��û�id���������ֵ, ȫ�ִ������� $config
	#���� ��
	function  payBakCredit($table, $ucol, $ccol, $userid, $credit) {
		global $config;
		$db = new Mysql($config['db']);
		#ecshop
		$table = 'ecs_users';
		$col   =  'credit_line';
		$ucol  = 'user_id';
		$sql = "UPDATE {$table} SET {$ccol}={$ccol}+{$credit} WHERE  {$ucol} = {$userid}";
		$flag = $db->query($sql);
		if($flag == false)
			return false;
		else 
			return true;
	}

	#���� ��ת�������ο���
	#���� userid�û�id, ȫ�ִ������� $config
	#���� ��
	/* function jumpToExchage($userid, $usermobile, $credit, $type) {
		global $config;
		$key = $config['business']['AppSecret'];
		$DES = new DES($key);
		//����url����ת
		$MpID = $config['business']['MPID'];
		$AppId = $config['business']['AppId'];
		$param = "{\"BizId\":\"{$MpID}\", 
				   \"UserId\":\"{$userid}\",
				   \"UserMobile\":\"{$usermobile}\",
				   \"UserCredit\":\"{$credit}\",
				   \"AppId\":\"{$AppId}\",
				   \"Type\":\"{$type}\"
				  }";
		//$param = "MPID={$MpID}&UserID={$userid}&UserMobile={$usermobile}&Credit={$credit}&AppId={$AppId}";
		$cryptParam = $DES->encrypt($param);
		//$data = $DES->decrypt($cryptParam); echo $data; exit;
		$url = $config['business']['exchange_url'] . "?data={$cryptParam}&bizId={$MpID}";
		header('Location: ' . $url);
	}
	 */
	
	function curl_get($url){
	// ��ʼ��һ�� cURL ����
	$curl = curl_init();
	// echo $url;
	// ��������Ҫץȡ��URL
	curl_setopt($curl, CURLOPT_URL, $url);

	// ����
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	//curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($curl, CURLOPT_HEADER, 0);

	// ����cURL ����Ҫ����浽�ַ��л����������Ļ�ϡ�
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

	// ����cURL��������ҳ
	$data = curl_exec($curl);

	// �ر�URL����
	curl_close($curl);
	// echo $data;exit;
	// ��ʾ��õ����
	return $data;
	}


	function getUserMessage( $openid ){             //ͨ��openid��ѯ�û���Ϣ�����û���Ϣ
		$db = new Mysql();//ʵ����ݿ��� 
	   $result =$db->get_one('SELECT '.WECHAT_TO_USER.' FROM '.WECHAT_TABLE.' WHERE '.OPENID.'=\''.$openid.'\' limit 1');//��ȡuserid
	   if( empty($result) ){  //��������û��Ļ���������Ӧid��openid
		   $data[OPENID]=$openid;
		   $db->insert(USER_TABLE,$data);//�����û���Ϣ�������
		   $userid=$db->insert_id();//��ȡ�������һ����ݵ���������userid
		   $data[USER_ID]= $userid;
		   $db->insert(WECHAT_TABLE,$data);//����΢����Ϣ��
	   }
	   else
	   {
		  $wechat_to_user= $result[WECHAT_TO_USER];
	   }
	  
	  return $db->get_one('SELECT '.USER_ID.','.USER_POINTS.','.USER_NAME.' FROM '.USER_TABLE.' WHERE '.USER_TO_WECHAT.'=\''.$wechat_to_user.'\' limit 1');//��ѯ��Ӧuserid����Ϣ����
	}
	
	// --�ļ��д���  --
	function Mk_Folder($Folder){

		if(!is_readable($Folder)){

		Mk_Folder( dirname($Folder) );

		if(!is_file($Folder)) mkdir($Folder,0777);

    }

	
}

function get_openid($url,$num=0){
	$info=curl_get($url);
	$num++;
	$info_arr=json_decode($info, true);
	if(empty($info_arr['openid'])){
		if($num>3){echo '获取微信openid失败，请重新进入';exit;}
		$info=get_openid($url,$num);
	}
	$info=json_decode($info, true);
	return $info;
}
?>
