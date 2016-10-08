<?php if ($this->_var['full_page']): ?>
<!DOCTYPE html>
<html>
<!-- zmh 修改 -->
<script src="js/jquery.min.js?v=2.1.4"></script>
<script type="text/javascript">
	$(function(){
		$("#keyword").keypress(function(event){
			if(event.keyCode == 13){
				$("#query_form button[type=submit]").trigger('click');
			}
		});
		
		$("#query_form select").each(function(){
			$(this).change(function(){
				$("#query_form button[type=submit]").trigger('click');
			});
		});
	});
</script>
<?php echo $this->fetch('head.htm'); ?>
<!-- <?php echo $this->smarty_insert_scripts(array('files'=>'../data/static/js/utils.js,./js/listtableNew.js')); ?> -->
<script type="text/javascript">
	
	$(function(){
		//初始化页面分页
		initPagination({
			record_count : <?php echo $this->_var['record_count']; ?>,
			page : 1,
			page_count : <?php echo $this->_var['page_count']; ?>
		});
		
		//搜索框回车提交表单
		$("#keyword").keypress(function(){
			if(event.keyCode == 13){
				$("#query_form button[type=submit]").trigger('click');
			}
		});
		
	});
	
	
</script>

<body class="gray-bg">

    <div class="wrapper wrapper-content animated fadeInUp">
        <div class="row">
            <div class="col-sm-12">

                <div class="ibox">
                    <div class="ibox-title">
                    	<!-- 数据记录总条数，添加属性id="total_count" -->
                        <h5>所有商品 (共<span id="total_count"></span>条记录)</h5>
                        <div class="ibox-tools">
                        	<!-- 分页大小设置 -->
                            <?php echo $this->fetch('page_setting.htm'); ?>
                        </div>
                    </div>
                    <div class="ibox-content">
						<div class="row m-b-sm m-t-sm">
                        <!-- <div class="input-group">
                            <div class="col-md-1">
                                <button type="button" id="loading-example-btn" class="btn btn-white btn-sm"><i class="fa fa-refresh"></i> 刷新</button>
                            </div>
                       	</div> -->
						<!-- 查询表单，ajax查询通常都为act=query，添加属性id="query_form"，submit按钮不可缺少 -->
						<form action="goods.php?act=query" method="post" class="bs-example bs-example-form" id="query_form">
						<div class="col-md-12">
						    <div class="form-group">
						    	<!-- 刷新 -->
							    <div class="col-md-1">
	                                <button type="button" id="loading-example-btn" class="btn btn-white btn-sm"><i class="fa fa-refresh"></i> 刷新</button>
	                            </div>
						    	<!-- 分类 -->						        
						        <div class="col-sm-2">
						            <select class="form-control" name="cat_id"><option value="0"><?php echo $this->_var['lang']['goods_cat']; ?></option><?php echo $this->_var['cat_list']; ?></select>
						        </div>
						         <!-- 上架 -->
						        <div class="col-sm-2">
						            <select class="form-control" name="is_on_sale"><option value=''><?php echo $this->_var['lang']['intro_type']; ?></option><option value="1" selected="selected"><?php echo $this->_var['lang']['on_sale']; ?></option><option value="0"><?php echo $this->_var['lang']['not_on_sale']; ?></option></select>
						        </div>
						        <!-- 关键字-->
						        <div class="col-sm-4">
						            <input type="text" id="keyword" name="keyword" size="15" class="form-control" placeholder="关键字" data-form-un="1454381208301.4795">
						        </div>
						        <div class="col-sm-2">
						           <button type="submit" id="search" class="btn btn-sm btn-primary">
									<i class="fa fa-refresh"></i>&nbsp查询
								</button>
						        </div>
						    </div>
						</div>
						<div>
							<input type="hidden" id="extension_code" name="extension_code" value="<?php echo $this->_var['code']; ?>"/>
                           	<input type="hidden" id="consignee" name="consignee" value=""/>
                           	<input type="hidden" id="mobile" name="mobile" value=""/>
                           	<input type="hidden" id="page_size" name="page_size" value=""/>
                           	<input type="hidden" id="page" name="page" value=""/>
                           	<input type="hidden" id="sort_by" name="sort_by" value=""/> 
                           	<input type="hidden" id="sort_order" name="sort_order" value=""/> 
                        </div>
                   		</form>
                        </div>

                        <div class="project-list">
                        	<form action="goods.php?act=batch" method="post" class="bs-example bs-example-form" id="batch_form">
                            <table class="table table-hover dataTable">
                            	<thead>
                            		<tr>
                            			<th>编号</th>
                            			<th sort-by="goods_name"><?php echo $this->_var['lang']['goods_name']; ?></th>
									    <th sort-by="goods_sn"><?php echo $this->_var['lang']['goods_sn']; ?></th>
									    <th sort-by="shop_price"><?php echo $this->_var['lang']['shop_price']; ?></th>
									    <?php if ($this->_var['use_storage']): ?>
									    <th sort-by="goods_number"><?php echo $this->_var['lang']['goods_number']; ?></th>
									    <?php endif; ?>
									    <th sort-by="is_on_sale"><?php echo $this->_var['lang']['is_on_sale']; ?></th>
									    <th><?php echo $this->_var['lang']['handler']; ?></th>
                            		</tr>
                            	</thead>
                            	<!-- 查询表单查询结果填充的位置，添加属性id="form_table" -->
                                <tbody id="form_table">
<?php endif; ?>
                                	<?php $_from = $this->_var['goods_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('okey', 'goods');if (count($_from)):
    foreach ($_from AS $this->_var['okey'] => $this->_var['goods']):
?>
                                    <tr>
                                         <td><?php echo $this->_var['goods']['goods_id']; ?></td>
    									<td class="first-cell">
    										<span style="font-weight: bold;"><?php echo htmlspecialchars($this->_var['goods']['goods_name']); ?></span>
    										<br/><?php echo htmlspecialchars($this->_var['goods']['goods_guide']); ?>
    									</td>
    									<td><?php echo $this->_var['goods']['goods_sn']; ?></td>
    									<td><?php echo $this->_var['goods']['shop_price']; ?></td>
									    <?php if ($this->_var['use_storage']): ?>
									    <td><span class="editable" url="goods.php?act=edit_goods_number&id=<?php echo $this->_var['goods']['goods_id']; ?>&val="><?php echo $this->_var['goods']['goods_number']; ?></span></td>
									    <?php endif; ?>
									    <td><img src="images/<?php if ($this->_var['goods']['is_on_sale']): ?>yes<?php else: ?>no<?php endif; ?>.gif" /></td>
									    <td>
									      <a href="goods.php?act=edit&goods_id=<?php echo $this->_var['goods']['goods_id']; ?><?php if ($this->_var['code'] != 'real_goods'): ?>&extension_code=<?php echo $this->_var['code']; ?><?php endif; ?>" title="<?php echo $this->_var['lang']['edit']; ?>" target="tab" class="btn btn-primary btn-sm">编辑</a>
									    </td>
                                    </tr>
                                    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
<?php if ($this->_var['full_page']): ?>                                                                 
                                    </tbody>
                                    <tfoot>
                                    	<tr>
                                    		<td colspan="7">
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