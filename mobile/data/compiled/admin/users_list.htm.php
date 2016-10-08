<?php if ($this->_var['full_page']): ?>
<!DOCTYPE html>
<html>

<?php echo $this->fetch('head.htm'); ?>
<link href="css/plugins/datapicker/datepicker3.css" rel="stylesheet">
<script src="js/plugins/datapicker/bootstrap-datepicker.js"></script>
<script type="text/javascript">
$(function(){
	$("#datepicker").datepicker({
		keyboardNavigation : !1,
		forceParse : !1,
		autoclose : !0
	});
	
	$('#start_date').datepicker({
		dateFormat : "yyyy-mm-dd"
	}).datepicker("setDate", '<?php echo $this->_var['start_date']; ?>');
	$('#end_date').datepicker({
		dateFormat : "yyyy-mm-dd"
	}).datepicker("setDate", '<?php echo $this->_var['end_date']; ?>');
	
	$("#query_form button[type=submit]").trigger('click');
});

function show_qrcode(href, nickname){
	$.get(href, function(data){
		if(data.errcode == 0){
			$('#qr_code_img').attr('src', '/mobile/' + data.qrcode);
			if(typeof(data.message) != 'undefined'){
				$('#qrModal div.modal-body').html(data.message);
			}else{
				$('#qrModal div.modal-body').html(nickname);
			}
			
			$('#qrModal').modal('show');
		}else{
			swal({
				title : "二维码生成失败",
				text : data.errmsg,
				type : "warning",
				confirmButtonColor : "#DD6B55",
				confirmButtonText : "确定",
				closeOnConfirm : true
			});
		}
	}, 'json')
	return false;
}

function download(){
	window.location.href="sale_product_list.php?act=download&start_date="+$('#start_date').val()+"&end_date="+$('#end_date').val(); 
}
</script>
<body class="gray-bg">

    <div class="wrapper wrapper-content animated fadeInUp">
        <div class="row">
            <div class="col-sm-12">

                <div class="ibox">
                    <div class="ibox-title">
                        <h5>会员列表(共<span id="total_count"></span>条记录)</h5>
                        <div class="ibox-tools">
                        	<!-- 分页大小设置 -->
                            <?php echo $this->fetch('page_setting.htm'); ?>
                        </div>
                    </div>
                    <div class="ibox-content">
                    	<div class="row m-b-sm m-t-sm">
	                        <!-- 查询表单，ajax查询通常都为act=query，添加属性id="query_form"，submit按钮不可缺少 -->
	                		<form action="users.php?act=query" method="post" class="form-inline" id="query_form">
	                			<div class="form-group">
	                               	<label class="control-label"><?php echo $this->_var['lang']['label_rank_name']; ?></label>
	                                <select name="rank" style="width:100px;" class="form-control"><option value="0"><?php echo $this->_var['lang']['all_option']; ?></option><?php echo $this->html_options(array('options'=>$this->_var['user_ranks'])); ?></select>
	                  			</div>
	                  			<div class="form-group">
	                  				<label class="control-label">消费积分大于</label>
	                                <input type="text" name="pay_points_gt" size="5" style="width:100px;" class="form-control"/>
	                  			</div>
	                  			<div class="form-group">
	                  				<label class="control-label">消费积分小于</label>
	                                <input type="text" name="pay_points_lt" size="5" style="width:100px;" class="form-control"/>
	                  			</div>
	                  			<div class="form-group">
	                  				<label class="control-label">昵称</label>
	                                <input type="text" name="keywords" style="width:100px;" class="form-control"/>
	                  			</div>
	                  			<div class="form-group">
	                  				<label class="control-label">会员名称</label>
	                                <input type="text" name="user_name" style="width:100px;" class="form-control"/>
	                  			</div>
	                  			<div class="form-group">
	                  				<label class="control-label">手机号码</label>
	                              	<input type="text" name="mobile_phone" style="width:120px;" class="form-control"/>
	                  			</div>
	                  			<div class="form-group">
	                  				<button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-search"></i> <?php echo $this->_var['lang']['button_search']; ?></button>
	                  			</div>
	                            <div>
	                            	<input type="hidden" id="page_size" name="page_size" value=""/>
	                            	<input type="hidden" id="page" name="page" value=""/>
	                            	<input type="hidden" id="sort_by" name="sort_by" value=""/> 
	                            	<input type="hidden" id="sort_order" name="sort_order" value=""/> 
	                            </div>
	                    	</form>
	                    </div>
						<hr/>
                        <div class="project-list">
                        	<form action="users.php?act=batch_remove" method="post" id="batch_form">
							<!-- 全查的表格数据，前台分页请添加dataTable样式 -->
                            <table class="table table-hover dataTable">
                            	<thead>
                            		<tr>
                            			<th><input type="checkbox" id="check_all"/></th>
									    <th>昵称</th>
									    <th>会员名称</th>
									    <th>手机号码</th>
									    <th sort-by="user_money"><?php echo $this->_var['lang']['user_money']; ?></th>
									    <th><?php echo $this->_var['lang']['frozen_money']; ?></th>
									    <th sort-by="rank_points"><?php echo $this->_var['lang']['rank_points']; ?></th>
									    <th sort-by="pay_points"><?php echo $this->_var['lang']['pay_points']; ?></th>
									    <th sort-by="reg_time"><?php echo $this->_var['lang']['reg_date']; ?><?php echo $this->_var['sort_reg_time']; ?></th>
										<th><font color="red">一级分销个数</font></th>
										<th><font color="red">分销详情</font></th>
									    <th><?php echo $this->_var['lang']['handler']; ?></th>
                            		</tr>
                            	</thead>
                                <tbody id="form_table">
								<?php endif; ?>
									<?php $_from = $this->_var['user_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'user');if (count($_from)):
    foreach ($_from AS $this->_var['user']):
?>
									<tr>
									    <td class="project-title"><input type="checkbox" name="checkboxes[]" value="<?php echo $this->_var['user']['user_id']; ?>" notice="<?php if ($this->_var['user']['user_money'] != 0): ?>1<?php else: ?>0<?php endif; ?>"/></td>
									    <td class="project-title"><a href="##" title="永久二维码" onclick="show_qrcode('/mobile/qrcode.php?act=user_limit_qr&id=<?php echo $this->_var['user']['user_id']; ?>', '<?php echo htmlspecialchars($this->_var['user']['nickname']); ?>')"><i class="glyphicon glyphicon-qrcode"></i> </a><?php echo htmlspecialchars($this->_var['user']['nickname']); ?></td>
									    <td class="project-title"><?php echo htmlspecialchars($this->_var['user']['user_name']); ?></td>
									    <td class="project-title"><?php echo htmlspecialchars($this->_var['user']['mobile_phone']); ?></td>
									    <td class="project-title"><?php echo $this->_var['user']['user_money']; ?></td>
									    <td class="visible-lg"><?php echo $this->_var['user']['frozen_money']; ?></td>
									    <td class="project-title"><?php echo $this->_var['user']['rank_points']; ?></td>
									    <td class="project-title"><?php echo $this->_var['user']['pay_points']; ?></td>
									    <td class="visible-lg"><?php echo $this->_var['user']['reg_time']; ?></td>
										<td class="project-title"><?php echo $this->_var['user']['number']; ?></td>
										<td class="project-title"><a href="users.php?act=share_list&id=<?php echo $this->_var['user']['user_id']; ?>" target="tab">点击查看</a></td>
										<td class="project-title">
									      <a href="users.php?act=edit&id=<?php echo $this->_var['user']['user_id']; ?>" title="<?php echo $this->_var['lang']['edit']; ?>" target="tab"><img src="images/icon_edit.gif" border="0" height="16" width="16" /></a>
									      <a href="users.php?act=address_list&id=<?php echo $this->_var['user']['user_id']; ?>" title="<?php echo $this->_var['lang']['address_list']; ?>" target="tab"><img src="images/book_open.gif" border="0" height="16" width="16" /></a>
									      <a href="order.php?act=list&user_id=<?php echo $this->_var['user']['user_id']; ?>" title="<?php echo $this->_var['lang']['view_order']; ?>" target="tab"><img src="images/icon_view.gif" border="0" height="16" width="16" /></a>
									      <a href="account_log.php?act=list&user_id=<?php echo $this->_var['user']['user_id']; ?>" title="<?php echo $this->_var['lang']['view_deposit']; ?>" target="tab"><img src="images/icon_account.gif" border="0" height="16" width="16" /></a>
									      <!-- <a href="users.php?act=remove&id=<?php echo $this->_var['user']['user_id']; ?>" title="<?php echo $this->_var['lang']['remove']; ?>" class="btn-del"><img src="images/icon_drop.gif" border="0" height="16" width="16" /></a> -->
									      <a href="users.php?act=remove_account_pwd&user_id=<?php echo $this->_var['user']['user_id']; ?>" title="删除提现密码" target="tab"><img src="images/icon_drop.gif" border="0" height="16" width="16" /></a>
									    </td>
								  </tr>
								  <?php endforeach; else: ?>
								  <tr><td class="no-records" colspan="12"><?php echo $this->_var['lang']['no_records']; ?></td></tr>
								  <?php endif; unset($_from); ?><?php $this->pop_vars();; ?>
<?php if ($this->_var['full_page']): ?>
                                    </tbody>
                                    <tfoot>
                                    	<tr>
                                    		<td colspan="12">
                                    			<div id="batch_btns">
                                    				<a href="##" value="confirm=1" checkbox_name="order_id" class="btn btn-primary"><i class="fa fa-remove"></i>&nbsp;&nbsp;删除</a>
												    <input name="batch" type="hidden" value="1" />
   													<input id="order_id" name="order_id" type="hidden" value="" />
                                    			</div>
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
        
        <div class="modal inmodal" id="qrModal" tabindex="-1" role="dialog" aria-hidden="true">
           <div class="modal-dialog">
               <div class="modal-content animated bounceInRight">
                   <div class="modal-header">
                       <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">关闭</span>
                       </button>
                       <img id="qr_code_img" src=""/>
                       </div>
                       <div class="modal-body">
                           <p>右键点击图片另存为本地图片。</p>
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