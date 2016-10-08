<!DOCTYPE html>
<html>

<?php echo $this->fetch('head.htm'); ?>
<script src="js/plugins/suggest/bootstrap-suggest.min.js"></script>
<script src="js/kuaidi_company.js"></script>
<link href="css/plugins/datapicker/datepicker3.css" rel="stylesheet">
<script src="js/plugins/datapicker/bootstrap-datepicker.js"></script>

<body class="gray-bg">
	<div class="wrapper wrapper-content">
		<div class="container no-padding col-sm-offset-3">
			<form action="privilege.php?act=save_pwd" method="POST" class="form-horizontal" id="setting_form" target="_self">
				<div class="ibox float-e-margins col-sm-5">
					<div class="ibox-title">
                        <h5>修改管理员密码</h5>
                    </div>
                    <div class="ibox-content">
	                    <div class="row m-t">
	                         <div class="col-sm-6 col-sm-offset-3">
	                         	<h4><strong>账号：</strong></h4>
	                             <span><input type="text" class="form-control" value="<?php echo $this->_var['admin_info']['user_name']; ?>" disabled="disabled"/></span>
	                         </div>
	                    </div>
	                    <div class="row m-t">
	                         <div class="col-sm-6 col-sm-offset-3">
	                         	<h4><strong>原密码：</strong></h4>
	                             <span><input type="password" class="form-control" name="pwd_old" value="" placeholder="请输入原密码" required=""/></span>
	                         </div>
	                    </div>
	                    <div class="row m-t">
	                         <div class="col-sm-6 col-sm-offset-3">
	                         	<h4><strong>新密码：</strong></h4>
	                             <span><input type="password" class="form-control" name="pwd_new" value="" placeholder="请输入新密码" required=""/></span>
	                         </div>
	                    </div>
	                    <div class="row m-t">
	                         <div class="col-sm-6 col-sm-offset-3">
	                         	<h4><strong>确认新密码：</strong></h4>
	                             <span><input type="password" class="form-control" name="pwd_confirm" value="" placeholder="请再次输入新密码" required=""/></span>
	                         </div>
	                    </div>
	                    <div class="row m-t">
	                         <div class="col-sm-6 col-sm-offset-5">
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