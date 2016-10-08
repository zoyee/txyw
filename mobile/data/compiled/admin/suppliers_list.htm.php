<?php if ($this->_var['full_page']): ?>
<!DOCTYPE html>
<html>
<?php echo $this->fetch('head.htm'); ?>
<script type="text/javascript">
	$(function(){
		var layer_idx;
		$(document).ajaxStart(function(){
			layer_idx = layer.open({
					type : 3,
					title: '操作中,请稍候...',
					time: 0,
					shade: 0.5
			});
		}).ajaxComplete(function(){
			layer.close(layer_idx);
		});
		
		//搜索框回车提交表单
		$("#keyword").keypress(function(event){
			if(event.keyCode == 13){
				$("#query_form button[type=submit]").trigger('click');
			}
		});
		
		//初始化页面分页
		initPagination({
			record_count : <?php echo $this->_var['record_count']; ?>,
			page : 1,
			page_count : <?php echo $this->_var['page_count']; ?>
		});
		//$("#query_form button[type=submit]").trigger('click');
	});
	
</script>

<body class="gray-bg">

    <div class="wrapper wrapper-content animated fadeInUp">
        <div class="row">
            <div class="col-sm-12">

                <div class="ibox">
                    <div class="ibox-title">
                    	<!-- 数据记录总条数，添加属性id="total_count" -->
                        <h5>所有供货商 (共<span id="total_count"></span>条记录)</h5>
                        <div class="ibox-tools">
                        	<a href="suppliers.php?act=add" class="btn btn-primary btn-xs" target="tab"><i class="fa fa-plus"></i> 添加供货商</a>
                        	<!-- 分页大小设置 -->
                            <?php echo $this->fetch('page_setting.htm'); ?>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="row m-b-sm m-t-sm">
                        	<!-- 查询表单，ajax查询通常都为act=query，添加属性id="query_form"，submit按钮不可缺少 -->
                    		<form action="suppliers.php?act=query" method="post" class="bs-example bs-example-form" id="query_form">
	                            <div class="col-md-1">
	                                <button type="button" id="loading-example-btn" class="btn btn-primary btn-sm"><i class="fa fa-refresh"></i> 刷新</button>
	                            </div>
	                            
	                            <div class="col-md-5">
	                                <div class="input-group">
	                                    <input name="keyword" id="keyword" type="text" placeholder="回车提交" class="input-sm form-control"> 
	                                    <span class="input-group-btn">
	                                    <button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-search-plus"></i> 搜索</button> 
	                                    </span>
	                                </div>
	                                <span class="help-block m-b-none"><i class="fa fa-info-circle"></i> 请输入供货商名称</span>
	                            </div>
                            
                                <div>
                                	<input type="hidden" id="page_size" name="page_size" value=""/>
                                	<input type="hidden" id="page" name="page" value=""/>
                                	<input type="hidden" id="sort_by" name="sort_by" value=""/> 
                                	<input type="hidden" id="sort_order" name="sort_order" value=""/>
                                	<input type="hidden" name="is_ajax" value="1" />
                                </div>
                        	</form>
                        </div>
						<hr/>
                        <div class="project-list">
                        	<form action="" method="post" class="bs-example bs-example-form" id="batch_form">
                        	<!-- 表头排序请添加dataTable样式 -->
                            <table class="table table-hover dataTable">
                            	<thead>
                            		<tr>
                            			<th style="width: 50px;"><input type="checkbox" id="check_all"/><?php echo $this->_var['lang']['record_id']; ?></th>
                            			<!-- 需要排序的字段表头添加sort-by属性 -->
                            			<th sort-by="suppliers_name"><?php echo $this->_var['lang']['suppliers_name']; ?></th>
                            			<th sort-by="phone">联系电话</th>
                            			<th sort-by="weixin">微信号</th>
                            			<th sort-by="is_check"><?php echo $this->_var['lang']['suppliers_check']; ?></th>
                            			<th style="width: 150px;"><?php echo $this->_var['lang']['handler']; ?></th>
                            		</tr>
                            	</thead>
                            	<!-- 查询表单查询结果填充的位置，添加属性id="form_table" -->
                                <tbody id="form_table">
<?php endif; ?>
                                	<?php $_from = $this->_var['suppliers_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('okey', 'suppliers');if (count($_from)):
    foreach ($_from AS $this->_var['okey'] => $this->_var['suppliers']):
?>
                                	
                                    <tr>
                                    	<td class="project-title" style="width: 50px;">
                                    		<input type="checkbox" name="checkboxes" value="<?php echo $this->_var['suppliers']['suppliers_id']; ?>" /><?php echo $this->_var['suppliers']['suppliers_id']; ?>
                                    	</td>
                                    	<td class="project-title">
                                    		<span url="suppliers.php?act=edit_suppliers_name&amp;id=<?php echo $this->_var['suppliers']['suppliers_id']; ?>&amp;val=" class="editable"><?php echo htmlspecialchars($this->_var['suppliers']['suppliers_name']); ?></span>
                                        </td>
                                    	<td class="project-title">
                                    		<?php echo htmlspecialchars($this->_var['suppliers']['phone']); ?>
                                        </td>
                                    	<td class="project-title">
                                    		<?php echo htmlspecialchars($this->_var['suppliers']['weixin']); ?>
                                        </td>
                                        <td class="project-title">
                                            <img src="images/<?php if ($this->_var['suppliers']['is_check'] == 1): ?>yes<?php else: ?>no<?php endif; ?>.gif"/>
                                        </td>
                                        <td>
                                        	<a class="btn btn-info btn-sm" href="suppliers.php?act=edit&id=<?php echo $this->_var['suppliers']['suppliers_id']; ?>" target="tab"><i class="fa fa-edit"></i> <?php echo $this->_var['lang']['edit']; ?></a>
                                            <a class="btn btn-danger btn-sm btn-del" href="suppliers.php?act=remove&id=<?php echo $this->_var['suppliers']['suppliers_id']; ?>"><i class="fa fa-trash"></i> <?php echo $this->_var['lang']['remove']; ?></a>
                                        </td>
                                    </tr>
                                    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
<?php if ($this->_var['full_page']): ?>                                                                 
                                    </tbody>
                                    <tfoot>
                                    	<tr>
                                    		<td colspan="6">
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
           <div class="modal-dialog">
               <div class="modal-content animated fadeInDown">
                   <div class="modal-header">
                       <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">关闭</span>
                       </button>
                       <div class="modal-title">推广链接</div>
                   </div>
                   <div class="modal-body">
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