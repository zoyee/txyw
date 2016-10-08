<?php if ($this->_var['full_page']): ?>
<!DOCTYPE html>
<html>

<?php echo $this->fetch('head.htm'); ?>
<script src="js/plugins/suggest/bootstrap-suggest.min.js"></script>
<script src="js/kuaidi_company.js"></script>
<link href="css/plugins/datapicker/datepicker3.css" rel="stylesheet">
<script src="js/plugins/datapicker/bootstrap-datepicker.js"></script>

<script type="text/javascript">
	var kuaidi_com = new Array();
	for(var i = 0; i<jsoncom.company.length; i++){
		kuaidi_com.push({
			'id': jsoncom.company[i]['code'],
			'word': jsoncom.company[i]['companyname']
		});
	}

	$(function(){
		$("#datepicker").datepicker({
			keyboardNavigation : !1,
			forceParse : !1,
			autoclose : !0
		});
		
		$('#start_time').datepicker({
			dateFormat : "yyyy-mm-dd"
		});
		$('#end_time').datepicker({
			dateFormat : "yyyy-mm-dd"
		});
		
		$("#dwonload_excel").click(function(){
			$("#isDwonload").val("1");
			var qurey = $("#query_form").serialize();
			location.href='order.php?act=list&' + qurey;
			$("#isDwonload").val("0");
	    });
		
		//初始化页面分页
		initPagination({
			record_count : <?php echo $this->_var['record_count']; ?>,
			page : 1,
			page_count : <?php echo $this->_var['page_count']; ?>
		});
		
		//搜索框输入查询条件事件
		$("#keyword").change(function(){
			var keyword = $.trim($(this).val());
			if(keyword.match(/^1\d{10}$/) != null){
				$("#mobile").val(keyword);
				$("#consignee").val('');
				$("#order_sn").val('');
			}else if(keyword.match(/^[a-zA-Z]?\d{13}$/) != null){
				$("#mobile").val('');
				$("#consignee").val('');
				$("#order_sn").val(keyword);
			}else{
				$("#mobile").val('');
				$("#consignee").val(keyword);
				$("#order_sn").val('');
			}
		});
		
		//搜索框回车提交表单
		/* $("#keyword").keypress(function(event){
			if(event.keyCode == 13){
				$("#query_form button[type=submit]").trigger('click');
			}
		}); */
		
		$("#composite_status").change(function(){
			$("#query_form button[type=submit]").trigger('click');
		});
		
		var testdataBsSuggest = $("#kuaidi_com").bsSuggest({
			indexId : 0,
			indexKey : 1,
			data : {
				"value" : kuaidi_com,
				"defaults" : "http://lzw.me"
			}
		});
	});
	
	function fahuo(order_id, suppliers_id){
		//ajax查询快递公司和快递号
		$.get('order.php?act=get_kuaidi&order_id=' + order_id + '&suppliers_id=' + suppliers_id, function(result){
			if(result.invoice != ''){
				$('#kuaidi_com').attr('data-id', result.shipping);
				$('#invoice_no').val(result.invoice);
				for(var i = 0; i<kuaidi_com.length; i++){
					if(kuaidi_com[i]['id'] == result.shipping){
						$('#kuaidi_com').val(kuaidi_com[i]['word']);
						break;
					}
				}
			}else{
				$('#kuaidi_com').attr('data-id', result.shipping);
				$('#kuaidi_com').val('');
				$('#invoice_no').val(result.invoice);
			}
			$("#delivery_order_id").val(order_id);
			$("#delivery_suppliers_id").val(suppliers_id);
			$("#fahuo_modal").modal('show');
		},'json');
	}
	
	function delivery_order(){
		var kuaidi_com = $('#kuaidi_com').attr('data-id');
		var invoice_no = $('#invoice_no').val();
		var order_id = $('#delivery_order_id').val();
		var suppliers_id = $('#delivery_suppliers_id').val();
		/* if(typeof(kuaidi_com) == 'undefined' || kuaidi_com == ''){
			swal({
		        title: "请选择快递公司",
		        type: "warning",
		        confirmButtonColor: "#DD6B55",
		        confirmButtonText: "确定",
		        closeOnConfirm: false
		    });
			return false;
		} */
		if(invoice_no == ''){
			swal({
		        title: "请输入快递号",
		        type: "warning",
		        confirmButtonColor: "#DD6B55",
		        confirmButtonText: "确定",
		        closeOnConfirm: false
		    });
			return false;
		}
		
		var url = "order.php?act=delivery_order&kuaidi_com=" + kuaidi_com + "&invoice_no=" 
				+ invoice_no + "&order_id=" + order_id + "&suppliers_id=" + suppliers_id;
		$.get(url, function(result){
			if(result.error == 0){
				$("#fahuo_modal").modal('hide');
				swal({
			        title: "操作成功！",
			        type: "success",
			        confirmButtonText: "确定",
			        confirmButtonColor: "#1ab394",
			        closeOnConfirm: false
			    }, function(){
			    	location.reload();
			    });
			}else{
				swal({
			        title: result.message,
			        type: "warning",
			        confirmButtonColor: "#DD6B55",
			        confirmButtonText: "确定",
			        closeOnConfirm: false
			    });
			}
		}, 'json')
		return false;
	}
	
	function kdmoal(invoice){
		//invoice = '710272815725';
		jQuery("#kd_title").html(invoice);
		$.get('../mobile/plugins/kuaidi1000.php?com=&nu=' + invoice + '&showtest=showtest', function(data){
				//var result = jQuery.parseJSON(data);
				//jQuery("#tab-shipping-info").append("<div><h3>快递单号:"+data.nu+"</h3></div>");
				var result = data;
				//debug(result.success);
				if(!result.status){
					jQuery("#tab-shipping-info").append('<div class="middle-box text-center animated fadeInDown">' + result.reason + '</div>');
					$("#kd_modal").modal('show');
				}else if(result.status == 0){
					jQuery("#tab-shipping-info").append('<div class="middle-box text-center animated fadeInDown">物流单号暂无结果，请稍后再来查询</div>');
					$("#kd_modal").modal('show');
				}else if(result.status == 4){
					jQuery("#tab-shipping-info").append('<div class="middle-box text-center animated fadeInDown">快递已被快递公司揽收</div>');
					$("#kd_modal").modal('show');
				}else if(result.status == 5){
					jQuery("#tab-shipping-info").append('<div class="middle-box text-center animated fadeInDown">快递邮寄过程中出现问题</div>');
					$("#kd_modal").modal('show');
				}else{
					var data = result.data;
					//jQuery("#retData .message").html('订单物流查询成功！');
					//debug("订单物流查询成功！");
					var s = '<div class="vertical-container dark-timeline" id="vertical-timeline">';
					for (var i=0;i<data.length;i++){
						
						var cl = "";
						var ic = "";
						if(i==0){
							cl = "navy-bg";
							ic = "fa-truck";
						} else {
							cl = "yellow-bg";
							ic = "fa-hand-o-right";
						}
						s += '        <div class="vertical-timeline-block">                         '
							+ '        <div class="vertical-timeline-icon ' + cl +'">                  '
							+ '            <i class="fa ' + ic + '"></i>                           '
							+ '        </div>                                                        '
							+ '        <div class="vertical-timeline-content">                       '
							+ '            <h4>'+data[i].context+'</h4>                                             '
							+ '            <span class="vertical-date">                              '
							+ '        		<small>'+data[i].time+'</small>                                      '
							+ '    		</span>                                                        '
							+ '        </div>                                                        '
							+ '    </div>                                                            ';
					}
					s += '</div>';
					jQuery("#kd_body").html(s);
					$("#kd_modal").modal('show');
				}
			}, 'json');
		
	}
</script>

<body class="gray-bg">

    <div class="wrapper wrapper-content animated fadeInUp">
        <div class="row">
            <div class="col-sm-12">

                <div class="ibox">
                    <div class="ibox-title">
                    	<!-- 数据记录总条数，添加属性id="total_count" -->
                        <h5>所有订单 (共<span id="total_count"></span>条记录)</h5>
                        <div class="ibox-tools">
                        	<!-- 分页大小设置 -->
                            <?php echo $this->fetch('page_setting.htm'); ?>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="row m-b-sm m-t-sm">
                            <div class="col-md-1">
                                <button type="button" id="loading-example-btn" class="btn btn-primary btn-sm"><i class="fa fa-refresh"></i> 刷新</button>
                            </div>
                            <!-- 查询表单，ajax查询通常都为act=query，添加属性id="query_form"，submit按钮不可缺少 -->
                    		<form action="order.php?act=query" method="post" class="bs-example bs-example-form" id="query_form">
                    			<div class="col-md-2 form-group">
                    				<div class="input-group">
                                	<select name="composite_status" id="composite_status" class="form-control m-b">
                                		<option value="-1">--请选择--</option>
                                        <option value="101">待发货</option>
                                        <option value="102">已完成</option>
                                    </select>
                                    </div>
                                    <span class="help-block m-b-none"><i class="fa fa-info-circle"></i> 请选择订单状态</span>
                      			</div>
                      			<div class="col-md-3">
	                                <div class="input-daterange input-group" id="datepicker">
	                                    <input id="start_time" name="start_time" class="form-control" type="text">
										<span class="input-group-addon">至</span>
	                                    <input id="end_time" name="end_time" class="form-control" type="text">
	                                </div>
	                                <span class="help-block m-b-none"><i class="fa fa-info-circle"></i> 请选择下单时间</span>
	                            </div>
	                            <div class="col-md-5">
	                                <div class="input-group">
	                                    <input name="keyword" id="keyword" type="text" placeholder="回车提交" class="input-sm form-control"> 
	                                    <span class="input-group-btn">
	                                    <button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-search-plus"></i> 搜索</button> 
	                                    <button type="button" class="btn btn-sm btn-warning" id="dwonload_excel"><i class="fa fa-file-excel-o"></i> 导出</button>
	                                    </span>
	                                </div>
	                                <span class="help-block m-b-none"><i class="fa fa-info-circle"></i> 请输入订单号或手机号或收货人名称</span>
	                            </div>
	                            
                            
                                <div>
                                	<input type="hidden" id="order_sn" name="order_sn" value=""/>
                                	<input type="hidden" id="consignee" name="consignee" value=""/>
                                	<input type="hidden" id="mobile" name="mobile" value=""/>
                                	<input type="hidden" id="page_size" name="page_size" value=""/>
                                	<input type="hidden" id="page" name="page" value=""/>
                                	<input type="hidden" id="sort_by" name="sort_by" value=""/> 
                                	<input type="hidden" id="sort_order" name="sort_order" value=""/>
                                	<input type="hidden" name="isDwonload" id="isDwonload" value="0" />
                                </div>
                                
                        	</form>
                        </div>
						<hr/>
                        <div class="project-list">
                        	<form action="order.php?act=operate" method="post" class="bs-example bs-example-form" id="batch_form">
                        	<!-- 表头排序请添加dataTable样式 -->
                            <table class="table table-hover dataTable">
                            	<thead>
                            		<tr>
                            			<th><input type="checkbox" id="check_all"/></th>
                            			<!-- 需要排序的字段表头添加sort-by属性 -->
                            			<th sort-by="order_id">订单号</th>
                            			<th>商品图片</th>
                            			<th sort-by="goods_name">商品名称</th>
                            			<th class="visible-lg" sort-by="buyer">购买用户</th>
                            			<?php if ($this->_var['supper_admin']): ?>
                            			<th class="visible-lg">供应商</th>
                            			<?php endif; ?>
                            			<th class="visible-lg">寄送地址</th>
                            			<th class="visible-lg" sort-by="add_time">时间</th>
                            			<th class="visible-lg">备注</th>
                            			<!-- <th sort-by="total_fee">金额</th> -->
                            			<th></th>
                            		</tr>
                            	</thead>
                            	<!-- 查询表单查询结果填充的位置，添加属性id="form_table" -->
                                <tbody id="form_table">
<?php endif; ?>
                                	<?php $_from = $this->_var['order_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('okey', 'order');if (count($_from)):
    foreach ($_from AS $this->_var['okey'] => $this->_var['order']):
?>
                                	
                                    <tr>
                                    	<td class="project-title">
                                    		<input type="checkbox" name="checkboxes" value="<?php echo $this->_var['order']['order_id']; ?>" />
                                    	</td>
                                        <td class="project-status">
                                        	<?php echo $this->_var['order']['order_sn']; ?>
                                        	<br/>
                                            <!-- <span class="label <?php if ($this->_var['order']['order_status'] > 0): ?>label-primary<?php else: ?>label-default<?php endif; ?>"><?php echo $this->_var['lang']['os'][$this->_var['order']['order_status']]; ?></span> -->
                                            <span class="label <?php if ($this->_var['order']['pay_status'] == 2): ?>label-primary<?php else: ?>label-default<?php endif; ?>"><?php echo $this->_var['lang']['ps'][$this->_var['order']['pay_status']]; ?></span>
                                            <span class="label <?php if ($this->_var['order']['fahuo']): ?>label-primary<?php else: ?>label-default<?php endif; ?>"><?php if ($this->_var['order']['fahuo']): ?>已发货<?php else: ?>未发货<?php endif; ?></span>
                                            <br/>
                                            <?php if ($this->_var['order']['kdh']): ?>                                            
                                            <a onclick="kdmoal(<?php echo $this->_var['order']['kdh']; ?>)">快递：<?php echo $this->_var['order']['kdh']; ?></a>
                                            <?php endif; ?>
                                        </td>
                                        <td class="project-title visible-lg">
                                            <img src="../<?php echo $this->_var['order']['goods_thumb']; ?>" width="100px"></img>
                                        </td>
                                        <td class="project-title">
                                        	<strong><?php echo htmlspecialchars($this->_var['order']['goods_name']); ?></strong><br/>
                                        	<?php if ($this->_var['order']['goods_attr']): ?>属性：<?php echo htmlspecialchars($this->_var['order']['goods_attr']); ?><br/><?php endif; ?>
                                        	数量：<?php echo $this->_var['order']['goods_number']; ?>
                                        </td>
                                        <td class="project-title visible-lg">
                                            <strong>收件人</strong>：<?php echo htmlspecialchars($this->_var['order']['consignee']); ?>
                                            <div>手机：<?php echo htmlspecialchars($this->_var['order']['mobile']); ?></div>
                                        </td>
                                        <?php if ($this->_var['supper_admin']): ?>
                                        <td class="project-title visible-lg">
                                           	<div><?php echo htmlspecialchars($this->_var['order']['suppliers_name']); ?></div>
                                        </td>
                                        <?php endif; ?>
                                        <td class="project-title visible-lg">
                                           	<div><?php echo htmlspecialchars($this->_var['order']['region_name']); ?></div>
                                            <div><?php echo htmlspecialchars($this->_var['order']['address']); ?></div>
                                            
                                        </td>
                                        <td class="project-title visible-lg">
                                           	 下单： <?php echo $this->_var['order']['short_order_time']; ?>
                                           	 <?php if ($this->_var['order']['short_pay_time']): ?>
                                            <br/>
                                            	付款： <?php echo $this->_var['order']['short_pay_time']; ?>
                                            <?php endif; ?>
                                            <?php if ($this->_var['order']['short_shipping_time']): ?>
                                            <br/>
                                            	发货： <?php echo $this->_var['order']['short_shipping_time']; ?>
                                            <?php endif; ?>
                                            <?php if ($this->_var['order']['shouhuo_time']): ?>
                                            <br/>
                                            	收货：<?php echo $this->_var['order']['shouhuo_time']; ?>
                                            <?php endif; ?>
                                        </td> 
                                         <td class="project-title visible-lg">
                                         	<?php echo $this->_var['order']['postscript']; ?>
                                         </td>                                       
                                        <!-- <td class="project-title">
                                                <?php echo $this->_var['lang']['total_fee']; ?>:<?php echo $this->_var['order']['formated_total_fee']; ?><br/>
                                               	 实付金额:<?php echo $this->_var['order']['formated_money_paid']; ?><br/>
                                               	 使用红包:<?php echo $this->_var['order']['formated_bonus']; ?><br/>
                                               	<?php echo $this->_var['lang']['order_amount']; ?>:<?php echo $this->_var['order']['formated_order_amount']; ?>
                                        </td> -->
                                        <td class="project-actions">
                                            <!-- <a href="order.php?act=info&order_id=<?php echo $this->_var['order']['order_id']; ?>&order_status=<?php echo $this->_var['order']['order_status']; ?>" class="btn btn-info btn-sm" target="tab"><i class="fa fa-paste"></i> 查看 </a>
                                            <a href="wxch_users.php?act=send&uid=<?php echo $this->_var['order']['uid']; ?>" class="btn btn-info btn-sm" target="tab">
											<i class="fa fa-paste"></i> 发送消息
											</a> -->
											<?php if ($this->_var['order']['pay_status'] == 2): ?>
											<a class="btn btn-primary btn-sm" onclick="fahuo(<?php echo $this->_var['order']['order_id']; ?>, <?php echo $this->_var['order']['suppliers_id']; ?>)">发货</a>
											<?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
<?php if ($this->_var['full_page']): ?>                                                                 
                                    </tbody>
                                    <tfoot>
                                    	<tr>
                                    		<td colspan="<?php if ($this->_var['supper_admin']): ?>9<?php else: ?>8<?php endif; ?>">
                                    			<!-- <div class="btn-group">
                                    				<button type="button" class="btn btn-sm btn-primary dropdown-toggle" 
												      data-toggle="dropdown">
												      	批量操作
												      <span class="caret"></span>
												    </button>
												    <ul class="dropdown-menu" id="batch_btns">
												      <li><a href="##" value="confirm=1" checkbox_name="order_id"><i class="glyphicon glyphicon-usd"></i>&nbsp;&nbsp;订单确认</a></li>
												      <li><a href="##" value="invalid=1" checkbox_name="order_id"><i class="glyphicon glyphicon-minus-sign"></i>&nbsp;&nbsp;订单无效</a></li>
												      <li><a href="##" value="cancel=1" checkbox_name="order_id"><i class="glyphicon glyphicon-remove-sign"></i>&nbsp;&nbsp;订单取消</a></li>
												      <li><a href="##" value="remove=1" checkbox_name="order_id"><i class="glyphicon glyphicon-remove"></i>&nbsp;&nbsp;订单移除</a></li>
												      <li><a href="##" value="print=1" checkbox_name="order_id" target="_blank"><i class="glyphicon glyphicon-print"></i>&nbsp;&nbsp;订单打印</a></li>
												      <li><a href="##" value="export=1" checkbox_name="order_id" ><i class="glyphicon glyphicon-export"></i>&nbsp;&nbsp;订单下载</a></li>
												    </ul>
												    <input name="batch" type="hidden" value="1" />
   													<input id="order_id" name="order_id" type="hidden" value="" />
                                    			</div> -->
                                    			<div class="ibox-tools">
                                    				<!-- 分页标签 -->
	                                    			<ul class="pagination"></ul>
												</div>
                                    		</td>
                                    	</tr>
                                    </tfoot>
                                </table>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="modal inmodal" id="modal" tabindex="-1" role="dialog" aria-hidden="true">
           <div class="modal-dialog modal-lg">
               <div class="modal-content animated fadeInDown">
                   <div class="modal-header">
                       <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">关闭</span>
                       </button>
                       <div class="modal-title"></div>
                   </div>
                   <div class="modal-body">
                   </div>
                   <div class="modal-footer">
                       <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
                   </div>
               </div>
           </div>
       </div>
       
       <div class="modal inmodal" id="fahuo_modal" tabindex="-1" role="dialog" aria-hidden="true">
           <div class="modal-dialog">
               <div class="modal-content animated fadeInDown">
                   <div class="modal-header">
                       <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">关闭</span>
                       </button>
                       <div class="modal-title">请填写发货信息</div>
                   </div>
                   <div class="modal-body">
	                   <div>
	                        <form class="form-horizontal" action="order.php?act=delivery_order" method="POST" target="_blank" onsubmit="return delivery_order();">
	                            <div class="form-group">
	                                <label class="col-sm-3 control-label">快递公司：</label>
	
	                                <div class="col-sm-5 input-group" style="padding-left: 15px; padding-right: 15px;">
                                        <input type="text" class="form-control" id="kuaidi_com" name="kuaidi_com">
                                        <div class="input-group-btn">
                                            <button type="button" class="btn btn-white dropdown-toggle" data-toggle="dropdown">
                                                <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                            </ul>
                                        </div>
                                    </div>
	                            </div>
	                            
	                            <div class="form-group">
	                                <label class="col-sm-3 control-label">快递号：</label>
	                                <div class="col-sm-5">
	                                    <input type="text" class="form-control" placeholder="快递号码" name="invoice_no" id="invoice_no">
	                                </div>
	                            </div>
	                            <div class="form-group">
	                                <div class="col-sm-offset-5 col-sm-8">
	                                    <button type="submit" class="btn btn-primary">一键发货</button>
	                                    <input type="hidden" name="order_id" id="delivery_order_id" value=""/>
	                                    <input type="hidden" name="suppliers_id" id="delivery_suppliers_id" value=""/>
	                                </div>
	                            </div>
	                        </form>
	                    </div>
                   </div>
                   <div class="modal-footer">
                       <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
                   </div>
               </div>
           </div>
       </div>
       
       <div class="modal inmodal" id="kd_modal" tabindex="-1" role="dialog" aria-hidden="true">
           <div class="modal-dialog modal-lg">
               <div class="modal-content animated fadeInDown">
                   <div class="modal-header">
                       <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">关闭</span>
                       </button>
                       <div class="modal-title" id="kd_title"></div>
                   </div>
                   <div class="modal-body" id="kd_body">
                   </div>
                   <div class="modal-footer">
                       <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
                   </div>
               </div>
           </div>
       </div>
    </body>
</html>
<?php endif; ?>