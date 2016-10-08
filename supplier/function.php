<?

//导入Excel文件
function uploadFile($file,$filetempname) 
{
	//自己设置的上传文件存放路径
	$filePath = 'upFile/';
	$str = "";
	//echo PATH_SEPARATOR."<br>";
	define('BASE_PATH',str_replace('\\','/',realpath(dirname(__FILE__).'/'))."/");
	//下面的路径按照你PHPExcel的路径来修改
	set_include_path('.'. PATH_SEPARATOR .BASE_PATH.'\PHPExcel' . PATH_SEPARATOR .get_include_path()); 
	//define('BASE_PATH',str_replace('\\','/',realpath(dirname(__FILE__).'/'))."/");


	//echo '.'. PATH_SEPARATOR .BASE_PATH.'\PHPExcel' . PATH_SEPARATOR .get_include_path().'<br>';
    //echo PATH_SEPARATOR."nihao<br>";
	require_once 'PHPExcel.php';
	require_once 'PHPExcel/IOFactory.php';
	//require_once 'PHPExcel\Reader\Excel5.php';//excel 2003
	require_once 'PHPExcel/Reader/Excel2007.php';//excel 2007

	$filename=explode(".",$file);//把上传的文件名以“.”好为准做一个数组。
	$time=date("y-m-d-H-i-s");//去当前上传的时间 
	$filename[0]=$time;//取文件名t替换 
	$name=implode(".",$filename); //上传后的文件名 
	$uploadfile=$filePath.$name;//上传后的文件名地址  

  
	//move_uploaded_file() 函数将上传的文件移动到新位置。若成功，则返回 true，否则返回 false。
    $result=move_uploaded_file($filetempname,$uploadfile);//假如上传到当前目录下
    if($result) //如果上传文件成功，就执行导入excel操作
    {
	   //$objReader = PHPExcel_IOFactory::createReader('Excel5');//use excel2003
	   $objReader = PHPExcel_IOFactory::createReader('Excel2007');//use excel2003  和  2007 format
	   //$objPHPExcel = $objReader->load($uploadfile); //这个容易造成httpd崩溃
	   $objPHPExcel = PHPExcel_IOFactory::load($uploadfile);//改成这个写法就好了

	   $sheet = $objPHPExcel->getSheet(0); 
	   $highestRow = $sheet->getHighestRow(); // 取得总行数 
	   $highestColumn = $sheet->getHighestColumn(); // 取得总列数
	   
	   //echo 'C'<$highestColumn;
		//echo $highestColumn."hehe<br>";
		//循环读取excel文件,读取一条,插入一条
		for($j=2;$j<=$highestRow;$j++)
		{ 
			//echo $j."<br> :";
			for($k='A';$k<=$highestColumn;$k++)
			{ 
				//echo $k."<br>";
				$str .= iconv('utf-8','utf-8',$objPHPExcel->getActiveSheet()->getCell("$k$j")->getValue()).'\\';//读取单元格
			} 
			//explode:函数把字符串分割为数组。
			$strs = explode("\\",$str);
			//var_dump($strs)."<Br>";
			$order_sn = str_replace("`","",$strs[0]);//上传订单号
//			$express_com = str_replace("`","",$strs[13]);//上传快递公司
			$express_sn = str_replace("`","",$strs[16]);//上传快递号
			$send_number = str_replace("`","",$strs[12]);//上传发货信息
			$goods_sn = str_replace("`","",$strs[8]);//上传商品编码
			
			$supplier_id = array();
			if(!$goods_sn){
				continue;
			}
			//查询订单信息 
			$orders = $GLOBALS['db']->getRow("select o.order_id,o.invoice_no,o.shipping_id,IFNULL(g.suppliers_id, 0) as suppliers_id,o.order_status,o.pay_status,shipping_status " .
					"from ecs_order_info o, ecs_order_goods og, ecs_goods g " .
					"where o.order_id=og.order_id and og.goods_id=g.goods_id and order_sn = '$order_sn' and  g.goods_sn = '$goods_sn'");
			//echo $order_sn.":".$send_number.":".$orders['order_id']."<br>";
			//判断有没有上传快递信息  没有不做订单快递信息修改
			if($express_sn){
				//查询对应快递公司
				//$express_code= $GLOBALS['db']->getOne("select express_code from ecs_express where express_com = '$express_com'");
				//判断快递公司是否正确
//				if($express_code)
//				{
//$msg = $msg.$orders['suppliers_id'].":".$goods_sn."<BR>";
					
					if(empty($orders['suppliers_id'])) $orders['suppliers_id'] = '';
					$supplier_id[$goods_sn] = $orders['suppliers_id'];
					//$orders = $GLOBALS['db']->getRow("select invoice_no,shipping_id from ecs_order_info where order_sn = '$order_sn'");
					try{
						$admin_info = admin_info();
						$strkd = "{ \"kd\": [";
						if($orders['invoice_no']){
							
							$num = 0;
							$dh = ",";
							if(strstr($orders['invoice_no'],"{")){
								//$msg = $msg.$order_sn."<BR>";
								//$msg = $msg.$orders['invoice_no']."<BR>";
								$kdjson = json_decode($orders['invoice_no'], true);
								//var_dump($kdjson."<BR>");
								//$dh = ",";
								$i = 1;
								//$msg = $msg.count($kdjson['kd'])."<BR>";
								foreach ($kdjson['kd'] as $item){
									if(count($kdjson['kd']) == $i ){
										$dh = "";
									}
									//$msg = $msg."订单1：".$item['supplier']."订单1：".$orders['suppliers_id']."<br/>";
//									if($item['invoice']==$express_sn){
									
									if($item['supplier']==$orders['suppliers_id'] 
											|| (empty($item['supplier']) && empty($orders['suppliers_id']))){	
										$num = 1;
										$strkd = $strkd."{ \"shipping\": \"\", \"invoice\": \"" . $express_sn . "\", \"supplier\": \"" . $item['supplier'] . "\" }" . $dh;
									}else{
									$strkd = $strkd."{ \"shipping\": \"\", \"invoice\": \"" . $item['invoice'] . "\", \"supplier\": \"" . $item['supplier'] . "\" }" . $dh;
									}
									$i++;
								}
								
								
								
							}else{
								$invoiceList = explode('<br>',$orders['invoice_no']);
								
								//$shipping = get_shipping_object($supplier_id);
								//$sql = "select shipping_com from ecs_touch_shipping where shipping_id = ".$orders['shipping_id'];
								//$shipping_com= $GLOBALS['db']->getOne("select shipping_com from ecs_touch_shipping where shipping_id = ".$orders['shipping_id']);
								for($i = 0;$i < count($invoiceList);$i++)
								{
									
									if($i == (count($invoiceList)-1)){
										$dh="";
									}
									if($item['supplier']==$orders['suppliers_id'] 
											|| (empty($item['supplier']) && empty($orders['suppliers_id']))){	
										$num = 1;
										
										$strkd = $strkd."{ \"shipping\": \"\", \"invoice\": \"" . $express_sn.  "\", \"supplier\": \"". $supplier_id[$goods_sn] . "\" }" . $dh;
									}else{
										$strkd = $strkd."{ \"shipping\": \"\", \"invoice\": \"" . $item['invoice'].  "\", \"supplier\": \"". $supplier_id[$goods_sn] . "\" }" . $dh;
									}
									//echo  $invoiceList[$i]."<br>";
									
									//echo "1:".$strkd."<br>";
								}								
							}
							
							if ($num == 0)
							{
								$strkd = $strkd.",{ \"shipping\": \"\", \"invoice\": \"" . $express_sn . "\", \"supplier\": \"" . $supplier_id[$goods_sn] . "\" }";
								//echo "2:".$strkd."<br>";
							}
						}else{
							$strkd = $strkd."{ \"shipping\": \"\", \"invoice\": \"" . $express_sn . "\", \"supplier\": \"" .$supplier_id[$goods_sn]. "\" }";
							//echo "3:".$strkd."<br>";
						}
						
						$strkd =$strkd. " ] }";
						
						$GLOBALS['db']->query("update ecs_order_info set invoice_no = '$strkd' where order_sn = '$order_sn'");
						//$msg=$msg."update ecs_order_info set invoice_no = '$strkd' where order_sn = '$order_sn'"."<br>";
						
					}catch (Exception $e) {
						//echo $e->getMessage()."<br/>";
						$msg = $msg."错误信息：".$e->getMessage()."<br/>";
					}
					
//				}else{
//					$msg = $msg."订单：$order_sn  快递公司《".$express_com."》 不匹配<br>";
					//echo $msg;
//				}
				//echo $express_code."<br>";
				
					$GLOBALS['db']->query("update ecs_order_goods set send_number = goods_number where order_id = ".$orders['order_id']." and goods_sn = '".$goods_sn."'");
						
						
					$ognum = $GLOBALS['db']->getOne("select count(*) from ecs_order_goods where order_id = $orders[order_id] and goods_number   > send_number");
					admin_log("上传订单快递信息：".$order_sn."=>".'订单单号：'.$express_sn, 'act', 'ImportExcel');
					if($ognum){
						$GLOBALS['db']->query("update ecs_order_info set shipping_status = 4 where order_sn = '$order_sn'");
						order_action($order_sn, $orders['order_status'], 4, $orders['pay_status'], '供应商发货', $admin_info['admin_name']);
					}else{
						$GLOBALS['db']->query("update ecs_order_info set shipping_status = 1 where order_sn = '$order_sn'");
						order_action($order_sn, $orders['order_status'], 1, $orders['pay_status'], '供应商发货', $admin_info['admin_name']);
					}
			}
			
			
			//echo "update ecs_order_goods set send_number = $send_number where order_id = $orders[order_id] and goods_sn = '$goods_sn'";
			//die();
			//$sql = "INSERT INTO z_test_importexcel(duty_date,name_am,name_pm) VALUES('".$strs[0]."','".$strs[1]."','".$strs[2]."')"; 
			//$GLOBALS['db']->query($sql);
			//echo '<Br>'.$sql;
			//mysql_query("set names GBK");//这就是指定数据库字符集，一般放在连接数据库后面就系了 
			//if(!mysql_query($sql)){
				//return false;
			//}
			$str = "";
	   } 
   
   	   	unlink($uploadfile); //删除上传的excel文件
   	   	if(!$msg){
       		$msg = "导入成功！";
    	}
    }else{
       $msg = "导入失败！";
    }
    //echo $msg;
    return $msg;
}
?>