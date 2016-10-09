<!DOCTYPE html>
<html>

<?php echo $this->fetch('head.htm'); ?>
<script type="text/javascript">
$(function(){
});
function change_name(){
	var module_en_name = $('#module_en_name').val();
	var php_file = $('#php_file').val();
	var menu_url = $('#menu_url').val();
	var htm_file = $('#htm_file').val();
	if(module_en_name == ''){
		$('#php_file').val('');
		$('#menu_url').val();
		$('#htm_file').val('');
	}else{
		$('#php_file').val(module_en_name + ".php");
		$('#menu_url').val(module_en_name + ".php");
		$('#htm_file').val(module_en_name + ".htm");
	}
}
</script>
<body class="gray-bg">
	<div class="wrapper wrapper-content">
		<div class="container no-padding col-sm-offset-2">
			<form action="add_module.php?act=do_add" method="POST" class="form-horizontal" id="setting_form" target="_self">
				<div class="ibox float-e-margins col-sm-8">
					<div class="ibox-title">
                        <h5>添加功能模块--请在开发环境使用</h5>
                    </div>
                    <div class="ibox-content">
	                    <div class="row m-t">
	                         <div class="col-sm-4 col-sm-offset-2">
	                         	<h4><strong>所属一级权限模块：</strong></h4>
	                             <span>
	                             	<select class="form-control" id="parent_module" name="parent_module">
	                             		<?php $_from = $this->_var['parent_modules']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'module');if (count($_from)):
    foreach ($_from AS $this->_var['module']):
?>
		                             	<option value="<?php echo $this->_var['module']['action_id']; ?>"><?php echo $this->_var['module']['action_name']; ?></option>
		                             	<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
	                             	</select>
	                             </span>
	                         </div>
	                         <div class="col-sm-4">
	                         	<h4><strong>所属一级菜单模块：</strong></h4>
	                             <span>
	                             	<select class="form-control" id="parent_menu" name="parent_menu">
	                             		<?php $_from = $this->_var['parent_menu']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'menu');if (count($_from)):
    foreach ($_from AS $this->_var['menu']):
?>
		                             	<option value="<?php echo $this->_var['menu']['key']; ?>"><?php echo $this->_var['menu']['key_name']; ?></option>
		                             	<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
	                             	</select>
	                             </span>
	                         </div>
	                    </div>
	                    <div class="row m-t">
	                         <div class="col-sm-4 col-sm-offset-2">
	                         	<h4><strong>模块英文标识：</strong></h4>
	                             <span>
	                             	<input type="text" class="form-control" id="module_en_name" name="module_en_name" placeholder="请输入英文标识" required="" onchange="change_name()"/>
	                             </span>
	                         </div>
	                         <div class="col-sm-4">
	                         	<h4><strong>模块中文名称：</strong></h4>
	                             <span>
	                             	<input type="text" class="form-control" id="module_zh_cn_name" name="module_zh_cn_name" placeholder="请输入中文名称" required=""/>
	                             </span>
	                         </div>
	                    </div>
	                    <div class="row m-t">
	                         <div class="col-sm-4 col-sm-offset-2">
	                         	<h4><strong>文件名称：</strong></h4>
	                             <span>
	                             	<input type="text" class="form-control" id="php_file" name="php_file" placeholder="请输入文件英文名称" required=""/>
	                             </span>
	                         </div>
	                         <div class="col-sm-4">
	                         	<h4><strong>菜单路径：</strong></h4>
	                             <span>
	                             	<input type="text" class="form-control" id="menu_url" name="menu_url" placeholder="请输入请求url" required=""/>
	                             </span>
	                         </div>
	                    </div>
	                    <div class="row m-t">
	                         <div class="col-sm-4 col-sm-offset-2">
	                         	<h4><strong>返回页面：</strong></h4>
	                             <span>
	                             	<input type="text" class="form-control" id="htm_file" name="htm_file" placeholder="请输入htm页面名称" required=""/>
	                             </span>
	                         </div>
	                    </div>
	                    <div class="row m-t">
	                         <div class="col-sm-8 col-sm-offset-5">
	                         	<button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> 提交</button>
	                         </div>
	                    </div>
                    </div>
				</div>
			</form>
		</div>
	</div>
</body>
</html>