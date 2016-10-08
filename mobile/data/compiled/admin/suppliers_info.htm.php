<!DOCTYPE html>
<html>
<?php echo $this->fetch('head.htm'); ?>
<link href="css/plugins/summernote/summernote.css" rel="stylesheet">
<link href="css/plugins/summernote/summernote-bs3.css" rel="stylesheet">
<script src="js/plugins/summernote/summernote.min.js"></script>
<script src="js/plugins/summernote/summernote-zh-CN.js"></script>
<script type="text/javascript" src="js/plugins/ajaxfileupload/ajaxfileupload.js"></script> 
<style>
.notice-span {
    color: #666;
}
#img_head{
	width: 70px;
}
</style>
<script type="text/javascript">
$(document).ready(function(){
	$(".summernote").summernote({
		lang:"zh-CN",
		disableDragAndDrop: true,
		codemirror: {
	      mode: 'text/html',
	      htmlMode: true,
	      lineNumbers: true,
	      theme: 'monokai'
	    },
	    callbacks: {
			onImageUpload: function(files, editor, $editable) {
			    data = new FormData();
			    for(var im = 0 ; im < files.length; im++){
			    	data.append("upload_desc_image", files[im]);
				    data.append("fname", "upload_desc_image");
				    url = "suppliers.php?act=upload_desc_image";
				    $.ajax({
				        data: data,
				        type: "POST",
				        dataType: 'json',
				        url: url,
				        async: false,
				        cache: false,
				        contentType: false,
				        processData: false,
				        success: function (json) {
				        	if(json.error == 0) {
				        		//editor.insertImage($editable, '/' + json.content);
				        		$(".summernote").summernote('insertImage', '/mobile/' + json.content, function ($image){
				        			//$image.css('width', '100%');
				        		});
				        		if (parent.gritter){
									parent.gritter({
										title : '操作提示',
										text : '图片上传成功!',
										time : 10000
									});
								}
							}else{
								swal({
									title : json.message,
									text : '',
									type : "warning",
									confirmButtonColor : "#DD6B55",
									confirmButtonText : "确定",
									closeOnConfirm : true
								}, function() {});
							}
				        }
				    });
			    }
			    
			}
	    }
	});
	
	change_province();
});

function upload_img(keyword) {
	$("#loading")
	.ajaxStart(function(){
		$(this).show();
	})
	.ajaxComplete(function(){
		$(this).hide();
	});

	$.ajaxFileUpload ({
		url:'upload_image.php?act=ajax_upload&fname=upload_image_' + keyword,
		secureuri:false,
		fileElementId:'upload_image_' + keyword,
		dataType: 'json',
		data:{},
		success: function (json, status) {
			if(json.error == 0) {
				$("#head_img").val("mobile/" + json.content);
				if($("#img_" + keyword).length >0){
					$("#img_" + keyword).attr("src", "<?php echo $this->_var['_CFG']['site_url']; ?>" + json.content);
				}else{
					$("#head_img").after('<img id="img_' + keyword + '" src="<?php echo $this->_var['_CFG']['site_url']; ?>' + json.content + '"/>');
				}
				
				
				
				//$("#tab-user-haibao #image").val(json.content);
				//$("#backgroud_img").attr("src", "<?php echo $this->_var['_CFG']['site_url']; ?>" + json.content);
			}else{
				swal({
					title : "操作失败",
					text : json.message,
					type : "warning",
					confirmButtonColor : "#DD6B55",
					confirmButtonText : "确定",
					closeOnConfirm : true
				}, function() {});
			}
		},
		error: function (data, status, e) {
			//alert(e);
			swal({
				title : "异常",
				text : e,
				type : "warning",
				confirmButtonColor : "#DD6B55",
				confirmButtonText : "确定",
				closeOnConfirm : true
			}, function() {});
		}
	});
	return false;
};

function change_province(){
	var province = $("#province").val();
	if(province != ''){
		$.get("suppliers.php?act=get_regions&pid=" + province, function(data){
			$("#city").empty();
			$("#district").empty();
			$("#city").append('<option value="">请选择</option>');
			for(d in data){
				if(data[d].region_id == '<?php echo $this->_var['suppliers']['city']; ?>'){
					$("#city").append('<option value="'+data[d].region_id+'" selected>'+data[d].region_name+'</option>');
				}else{
					$("#city").append('<option value="'+data[d].region_id+'">'+data[d].region_name+'</option>');
				}
			}
			
			change_city();
		}, 'json');
	}
}

function change_city(){
	var city = $("#city").val();
	if(city != ''){
		$.get("suppliers.php?act=get_regions&pid=" + city, function(data){
			$("#district").empty();
			$("#district").append('<option value="">请选择</option>');
			for(d in data){
				if(data[d].region_id == '<?php echo $this->_var['suppliers']['district']; ?>'){
					$("#district").append('<option value="'+data[d].region_id+'" selected>'+data[d].region_name+'</option>');
				}else{
					$("#district").append('<option value="'+data[d].region_id+'">'+data[d].region_name+'</option>');
				}
			}
		}, 'json');
	}
}

function make_desc(){
	var desc = $(".summernote").summernote('code');
	$("#suppliers_desc").val(desc);
	return true;
}
</script>
<body class="gray-bg">
	<div class="wrapper wrapper-content">
		<div class="container no-padding">
			<form action="suppliers.php?act=<?php echo $this->_var['form_action']; ?>" method="POST" class="form-horizontal" id="setting_form" target="_self" onsubmit="return make_desc()">
				<div class="ibox float-e-margins">
					<div class="ibox-title">
                        <h5><?php if ($this->_var['form_action'] == 'insert'): ?>新增<?php else: ?>修改<?php endif; ?>供货商</h5>
                    </div>
                    <div class="ibox-content">
	                    <div class="row m-t">
	                         <div class="col-sm-3 col-sm-offset-2">
	                         	<h4><strong><?php echo $this->_var['lang']['label_suppliers_name']; ?></strong></h4>
	                             <span><input type="text" class="form-control" value="<?php echo $this->_var['suppliers']['suppliers_name']; ?>" name="suppliers_name" required=""/></span>
	                         </div>
	                         <div class="col-sm-3 col-sm-offset-2">
	                         	<h4><strong>头像</strong></h4>
	                             <span>
	                             	<label for="upload_image_head" class="btn btn-primary btn-sm" id="upload_btn_head">
                                        <input type="file" name="upload_image_head" id="upload_image_head" class="hide" value="" onchange="upload_img('head')"><i class="fa fa-upload"></i> 上传
                                    </label>
                               		<input type="hidden" name="head_img" id="head_img" value="<?php echo $this->_var['suppliers']['head_img']; ?>"/>
                                    <?php if ($this->_var['suppliers']['head_img']): ?>
                               		<img id="img_head" alt="头像" src="../../<?php echo $this->_var['suppliers']['head_img']; ?>"/>
                               		<?php endif; ?>
	                             </span>
	                         </div>
	                    </div>
	                    <div class="row m-t">
	                         <div class="col-sm-3 col-sm-offset-2">
	                         	<h4><strong>联系电话：</strong></h4>
	                             <span><input type="text" class="form-control" value="<?php echo $this->_var['suppliers']['phone']; ?>" name="phone" required=""/></span>
	                         </div>
	                         <div class="col-sm-3 col-sm-offset-2">
	                         	<h4><strong>微信号：</strong></h4>
	                             <span><input type="text" class="form-control" value="<?php echo $this->_var['suppliers']['weixin']; ?>" name="weixin" required=""/></span>
	                         </div>
	                    </div>
	                    <div class="row m-t">
	                         <div class="col-sm-6 col-sm-offset-2">
	                         	<h4><strong>所在地：</strong></h4>
	                             <span>
	                             	<select class="form-control" name="province" id="province" style="width: 120px; display:inline-block" onchange="change_province()">
	                             		<option value="">请选择</option>
	                             		<?php $_from = $this->_var['province_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'region');if (count($_from)):
    foreach ($_from AS $this->_var['region']):
?>
	                             		<option value="<?php echo $this->_var['region']['region_id']; ?>" <?php if ($this->_var['region']['region_id'] == $this->_var['suppliers']['province']): ?>selected<?php endif; ?>><?php echo $this->_var['region']['region_name']; ?></option>
	                             		<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
	                             	</select>
	                             	
	                             	<select class="form-control" name="city" id="city" style="width: 120px; display:inline-block" onchange="change_city()">
	                             		<option value="">请选择</option>
	                             	</select>
	                             	
	                             	<select class="form-control" name="district" id="district" style="width: 120px; display:inline-block">
	                             		<option value="">请选择</option>
	                             	</select>
	                             </span>
	                         </div>
	                    </div>
	                    <div class="row m-t">
	                         <div class="col-sm-8 col-sm-offset-2">
	                         	<h4><strong><?php echo $this->_var['lang']['label_suppliers_desc']; ?></strong></h4>
	                             <div class="summernote">
		                        	<?php if ($this->_var['suppliers']['suppliers_desc']): ?>
		                            	<?php echo $this->_var['suppliers']['suppliers_desc']; ?>
		                            <?php else: ?>
		                            	</br></br></br></br>
		                            <?php endif; ?>
		                        </div>
	                         </div>
	                    </div>
	                    <div class="row m-t">
	                         <div class="col-sm-8 col-sm-offset-2">
	                         	<h4><strong><?php echo $this->_var['lang']['label_admins']; ?></strong></h4>
	                             <?php $_from = $this->_var['suppliers']['admin_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'admin');if (count($_from)):
    foreach ($_from AS $this->_var['admin']):
?>
							      <input type="checkbox" name="admins[]" value="<?php echo $this->_var['admin']['user_id']; ?>" <?php if ($this->_var['admin']['type'] == "this"): ?>checked="checked"<?php endif; ?> />
							      <?php echo $this->_var['admin']['user_name']; ?><?php if ($this->_var['admin']['type'] == "other"): ?>(*)<?php endif; ?>&nbsp;&nbsp;
							    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
							    <br/><small><?php echo $this->_var['lang']['notice_admins']; ?></small>
	                         </div>
	                    </div>
	                    <div class="row m-t">
	                         <div class="col-sm-6 col-sm-offset-5">
	                         	<input type="hidden" name="id" value="<?php echo $this->_var['suppliers']['suppliers_id']; ?>" />
	                         	<button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> 提交</button>
	                         	<input type="hidden" name="suppliers_desc" id="suppliers_desc" value="" />
	                         	<button type="reset" class="btn btn-warning"><i class="fa fa-repeat"></i> <?php echo $this->_var['lang']['button_reset']; ?></button>
	                         </div>
	                    </div>
                    </div>
				</div>
			</form>
		</div>
	</div>
</body>
</html>