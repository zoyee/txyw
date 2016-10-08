<!DOCTYPE html>
<html>

<?php echo $this->fetch('head.htm'); ?>


<body class="gray-bg">

    <div class="wrapper wrapper-content animated fadeInUp">
        <div class="row">
            <div class="col-sm-12">

                <div class="ibox">
                    <div class="ibox-title">
                    	<!-- 数据记录总条数，添加属性id="total_count" -->
                        <h5>订单快递信息上传</h5>
                        <div class="ibox-tools">
                        	
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="row m-b-sm m-t-sm">
                            <div class="col-md-1">
                                
                            </div>
                            <!-- 查询表单，ajax查询通常都为act=query，添加属性id="query_form"，submit按钮不可缺少 -->
                    		<form action="order.php?act=ImportExcel" method="post" name="theForm" enctype="multipart/form-data" class="bs-example bs-example-form" >
                    			<div class="col-md-2 form-group">
                    				<div class="input-group">
                                	<input type="hidden" name="leadExcel" value="true">
     <input type="file" name="inputExcel">
                                    </div>
                      			</div>
                      			<div class="col-md-2 form-group">
                      			<div class="input-group">
                      			<input type="submit" name="import" value="导入数据">
                      			</div>
                      			</div>
	                          
                            
                                
                        	</form>
                        </div>
						<hr/>
                        
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        
       

    </body>
</html>

