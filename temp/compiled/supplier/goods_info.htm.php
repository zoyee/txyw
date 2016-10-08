<!DOCTYPE html>
<html>
<?php echo $this->fetch('head.htm'); ?>
<link href="css/plugins/summernote/summernote.css" rel="stylesheet">
<link href="css/plugins/summernote/summernote-bs3.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="js/plugins/mtoch/css/swiper-3.3.1.min.css"/>
<script src="js/plugins/summernote/summernote.min.js"></script>
<script src="js/plugins/summernote/summernote-zh-CN.js"></script>
<script type="text/javascript" src="js/plugins/ajaxfileupload/ajaxfileupload.js"></script> 
<script type="text/javascript" src="js/plugins/mtoch/js/swiper-3.3.1.jquery.min.js"></script>
<script type="text/javascript">
	var goods_gallery;
	var goods_gallery_win;
	$(document).ready(function(){
		//summernote富文本编辑器初始化
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
					if('<?php echo $this->_var['goods']['goods_id']; ?>' == '' || '<?php echo $this->_var['goods']['goods_id']; ?>' == '0'){
						swal({
							title : "请先保存商品基本信息",
							text : "",
							type : "warning",
							confirmButtonColor : "#DD6B55",
							confirmButtonText : "确定",
							closeOnConfirm : true
						}, function() {});
						return false;
					}
				    data = new FormData();
				    for(var im = 0 ; im < files.length; im++){
				    	data.append("upload_desc_image", files[im]);
					    data.append("fname", "upload_desc_image");
					    data.append("goods_id", '<?php echo $this->_var['goods']['goods_id']; ?>');
					    url = "upload_goods_image.php?act=upload_desc_image";
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
					        		$(".summernote").summernote('insertImage', '/' + json.content, function ($image){
					        			$image.css('width', '100%');
					        		});
					        		toastr.success("请点击保存按钮保存详情描述", "图片上传成功!")
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
		
		//商品轮播图片初始化
		goods_gallery = new Swiper('.swiper-container-list',{
			direction : "horizontal",
			watchSlidesProgress : true,
			watchSlidesVisibility : true,
			spaceBetween: 2,
			slidesPerView : 4.5,
			prevButton:'.swiper-button-prev',
			nextButton:'.swiper-button-next',
			onTap: function(){
				 /*mySwiper3.slideTo( mySwiper2.clickedIndex) */
				 if(typeof(goods_gallery.clickedIndex) != 'undefined'){
				 	goods_gallery_win.slideTo(goods_gallery.clickedIndex);
				 }
			}
		});
		
		//
		goods_gallery_win = new Swiper('.swiper-container-win',{
			direction : "horizontal",
			autoplay : 2000
		});
		
		gallery_bind_event();
	});
	
	//商品轮播图片删除按钮绑定事件
	function gallery_bind_event(){
		$(".swiper-container-list .image-box .overlay-to-top a").each(function(){
			var el = this;
			$(this).click(function(event){
				var img_id = $(el).attr('data-img-id');
				$.get('goods1.php?act=remove_gallery_img&img_id=' + img_id + "&goods_id=<?php echo $this->_var['goods']['goods_id']; ?>", function(json){
					if(json.error == 0) {
						//移除图片
						var gallery_length = goods_gallery.slides.length;
						for(var i = 0; i<gallery_length; i++){
							if($(goods_gallery.slides[i]).attr('data-img-id') == img_id){
								goods_gallery.removeSlide(i);
							}
						}
						
						var gallery_win_length = goods_gallery_win.slides.length;
						for(var i = 0; i<gallery_win_length; i++){
							if($(goods_gallery_win.slides[i]).attr('data-img-id') == img_id){
								goods_gallery_win.removeSlide(i);
							}
						}
						
		        		toastr.success("商品自动变更为未审核状态", "操作成功!")
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
				}, 'json');
			});
		});
	}
	
	//保存商品详情描述
	function save_desc(){
		if('<?php echo $this->_var['goods']['goods_id']; ?>' == '' || '<?php echo $this->_var['goods']['goods_id']; ?>' == '0'){
			swal({
				title : "请先保存商品基本信息",
				text : "",
				type : "warning",
				confirmButtonColor : "#DD6B55",
				confirmButtonText : "确定",
				closeOnConfirm : true
			}, function() {});
			return false;
		}
		
		var desc = $(".summernote").summernote('code');
		var data = new FormData();
	    data.append("goods_desc", desc);
	    data.append("goods_id", '<?php echo $this->_var['goods']['goods_id']; ?>');
	    var url = "goods1.php?act=save_goods_desc";
	    $.ajax({
	        data: data,
	        type: "POST",
	        dataType: 'json',
	        url: url,
	        cache: false,
	        contentType: false,
	        processData: false,
	        success: function (json) {
	        	if(json.error == 0) {
	        		toastr.success("商品自动变更为未审核状态", "保存成功!")
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
	
	//上传商品首图
	function upload_goods_img() {
		$("#loading")
		.ajaxStart(function(){
			$(this).show();
		})
		.ajaxComplete(function(){
			$(this).hide();
		});

		$.ajaxFileUpload ({
			url:'upload_goods_image.php?act=upload_goods_image&fname=upload_goods_image',
			secureuri:false,
			fileElementId:'upload_goods_image',
			dataType: 'json',
			data:{},
			success: function (json, status) {
				if(json.error == 0) {
					$("#goods_img").attr('src' , '/' + json.content);
					$("#goods_image").val(json.content);
					toastr.success("", "图片上传成功!")
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
	
	//追加轮播相册图片
	function upload_gallery_img(){
		if('<?php echo $this->_var['goods']['goods_id']; ?>' == '' || '<?php echo $this->_var['goods']['goods_id']; ?>' == '0'){
			swal({
				title : "请先保存商品基本信息",
				text : "",
				type : "warning",
				confirmButtonColor : "#DD6B55",
				confirmButtonText : "确定",
				closeOnConfirm : true
			}, function() {});
			return false;
		}
		
		$("#loading")
		.ajaxStart(function(){
			$(this).show();
		})
		.ajaxComplete(function(){
			$(this).hide();
		});

		$.ajaxFileUpload ({
			url:'upload_goods_image.php?act=upload_gallery_image&fname=upload_gallery_image&goods_id=<?php echo $this->_var['goods']['goods_id']; ?>',
			secureuri:false,
			fileElementId:'upload_gallery_image',
			dataType: 'json',
			data:{},
			success: function (json, status) {
				if(json.error == 0) {
					$('.swiper-container-win .swiper-wrapper').append(
							'<div class="swiper-slide image-box" data-img-id="' + json.img_id + '">                                                   '
							+ '	<img src="/' + json.content + '" alt="" style="width: 100%;">                       '
							+ '</div>'
					);
					$('.swiper-container-list .swiper-wrapper').append(
							'<div class="swiper-slide image-box" data-img-id="' + json.img_id + '">                                                   '
							+ '	<img src="/' + json.content + '" alt="" style="width: 100%;">                       '
							+ '	<div class="overlay-to-top"><a href="##" class="btn btn-primary btn-xs" data-img-id="' + json.img_id + '">删除</a></div>'
							+ '</div>                                                                                 '	
					);
					goods_gallery.updateSlidesSize();
					goods_gallery_win.updateSlidesSize();
					gallery_bind_event();
					//$("#goods_image").val(json.content);
					toastr.success("商品自动变更为未审核状态", "图片上传成功!")
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
	}
	
	//校验商品基本信息输入（首图） 
	function check_form(){
		if($('#goods_image').val() == ''){
			swal({
				title : "请上传商品首图",
				text : "",
				type : "warning",
				confirmButtonColor : "#DD6B55",
				confirmButtonText : "确定",
				closeOnConfirm : true
			}, function() {});
			return false;
		}
		return true;
	}
</script>
<style>
.overlay-container {
    display: block;
    overflow: hidden;
    position: relative;
    text-align: center;
}
.overlay-link {
    background-color: rgba(30, 30, 30, 0.5);
    bottom: 0;
    color: #ffffff;
    left: 0;
    opacity: 0;
    overflow: hidden;
    padding: 15px;
    position: absolute;
    right: 0;
    top: 0;
    transition: all 0.25s ease-in-out 0s;
    z-index: 10;
    -webkit-transition: opacity 0.3s;  
    -moz-transition: opacity 0.3s;  
    -webkit-animation-timing-function: ease-out;  
    -moz-animation-timing-function: ease-out;
}
.overlay-link:hover {  
	opacity: 1;  
} 
.overlay-link label {
    border: 1px solid #ffffff;
    color: #ffffff;
    font-size: 32px;
    left: 50%;
    margin: -40px 0 0 -50px;
    position: absolute;
    top: 50%;
    transition: all 0.2s ease-in-out 0s;
}
.swiper-container{
	margin-top: 3px;
}
.overlay-to-top {
    background-color: rgba(30, 30, 30, 0.5);
    bottom: -30px;
    color: #ffffff;
    left: 0;
    opacity: 0;
    padding: 3px;
    position: absolute;
    right: 0;
    top: auto;
    transition: all 0.25s ease-in-out 0s;
    text-align: center;
}
.overlay-container:hover .overlay-to-top, .image-box:hover .overlay-to-top {
    bottom: 0;
    opacity: 1;
}
</style>
<body class="gray-bg">
	<div class="wrapper wrapper-content">
		<div class="row">
			<div class="col-sm-4">
                <div class="ibox float-e-margins">
                	<?php if ($this->_var['goods']['goods_id']): ?>
                	<form action="goods1.php?act=update" method="POST" onsubmit="return check_form()" id="base_form">
                	<?php else: ?>
               		<form action="goods1.php?act=insert" method="POST" onsubmit="return check_form()" id="base_form">
                	<?php endif; ?>
                    <div class="ibox-title">
                        <h5>首图</h5>
                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content no-padding">
                        <div class="ibox-content no-padding border-left-right overlay-container">
                        	<?php if ($this->_var['goods']['goods_img']): ?>
                            <img src="/<?php echo $this->_var['goods']['goods_img']; ?>" class="img-responsive" alt="image" id="goods_img">
                            <a class="overlay-link popup-img-single" href="javascript:void(0)">
                       		<label for="upload_goods_image" class="btn btn-primary" id="upload_goods_image_btn">
	                            <input type="file" name="upload_goods_image" id="upload_goods_image" class="hide" value="" onchange="upload_goods_img()"><i class="fa fa-upload"></i> 上传
	                        </label>
                            </a>
                            <?php else: ?>
                            <img src="/supplier/img/no_photo.jpg" class="img-responsive" alt="image" id="goods_img">
                            <a class="overlay-link popup-img-single" href="javascript:void(0)">
                            <label for="upload_goods_image" class="btn btn-primary" id="upload_goods_image_btn">
	                            <input type="file" name="upload_goods_image" id="upload_goods_image" class="hide" value="" onchange="upload_goods_img()"><i class="fa fa-upload"></i> 上传
	                        </label>
                            </a>
                            <?php endif; ?>
                            <input type="hidden" name="goods_image" id="goods_image" value="<?php echo $this->_var['goods']['goods_img']; ?>"/>
                        </div>
                        <div class="ibox-content profile-content">
                            <h4><strong>商品名称</strong></h4>
                            <p><input type="text" class="form-control" name="goods_name" value="<?php echo htmlspecialchars($this->_var['goods']['goods_name']); ?>" required="" aria-required="true" placeholder="必填项"/></p>
                            <h5>导购信息</h5>
                            <p><input type="text" class="form-control" name="goods_guide" value="<?php echo htmlspecialchars($this->_var['goods']['goods_guide']); ?>"/></p>
                            <div class="row m-t">
                                <div class="col-sm-6">
                                	<h5><strong>供应商</strong></h5>
                                    <span><select class="form-control" name="suppliers_id" id="suppliers_id">
								        <?php echo $this->html_options(array('options'=>$this->_var['suppliers_list_name'],'selected'=>$this->_var['goods']['suppliers_id'])); ?>
								      </select>
								    </span>
                                </div>
                                <div class="col-sm-6">
                                    <h5><strong>商品货号</strong></h5>
                                    <span><input type="text" class="form-control" name="goods_sn" value="<?php echo htmlspecialchars($this->_var['goods']['goods_sn']); ?>" readonly="readonly" placeholder="自动生成"/></span>
                                </div>
                            </div>
                            <div class="row m-t">
                                <div class="col-sm-6">
                                	<h5><strong>库存(件)</strong></h5>
                                    <span><input type="text" class="form-control" name="goods_number" value="<?php echo $this->_var['goods']['goods_number']; ?>" required="" aria-required="true" placeholder="必填项"/></span>
                                </div>
                                <div class="col-sm-6">
                                    <h5><strong>单价(￥)</strong></h5>
                                    <span><input type="text" class="form-control" name="shop_price" value="<?php echo $this->_var['goods']['shop_price']; ?>" required="" aria-required="true" placeholder="必填项"/></span>
                                </div>
                            </div>
                            <div class="row m-t">
                                <div class="col-sm-6">
                                    <h5><strong>供货价(￥)</strong></h5>
                                    <span><input type="text" class="form-control" name="supply_price" value="<?php echo $this->_var['goods']['supply_price']; ?>" required="" aria-required="true" placeholder="必填项"/></span>
                                </div>
                            </div>
                            <br/>
                            <div class="user-button">
                                <div class="row">
                                    <div class="col-sm-6">
                                    	<input type="hidden" name="goods_id" value="<?php echo $this->_var['goods']['goods_id']; ?>"/>
                                        <button class="btn btn-primary btn-sm btn-block" type="submit"><i class="fa fa-check"></i> 提交</button>
                                    </div>
                                    <div class="col-sm-6">
                                        <button class="btn btn-default btn-sm btn-block" type="reset"><i class="fa fa-repeat"></i> 重置</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
	
	
			<div class="col-sm-8">
				<div class="ibox float-e-margins">
					<div class="ibox-title">
	                    <h5>轮播相册</h5>
	                    <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                        </div>
	                </div>
	                
	                <div class="ibox-content">
		                <div class="row">
		                	<div class="col-sm-5">
								<div class="swiper-container-win swiper-container swiper-free swiper-container-horizontal swiper-container-free-mode">
									<div class="swiper-wrapper">
										<?php $_from = $this->_var['img_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'img');$this->_foreach['img'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['img']['total'] > 0):
    foreach ($_from AS $this->_var['img']):
        $this->_foreach['img']['iteration']++;
?>
											<div class="swiper-slide image-box" data-img-id="<?php echo $this->_var['img']['img_id']; ?>">
												<img src="/<?php echo $this->_var['img']['img_url']; ?>" style="width:100%"/>
											</div>
										<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
									</div>
								</div>
								<div class="swiper-container-list swiper-container swiper-free swiper-container-horizontal swiper-container-free-mode">
									<div class="swiper-wrapper">
										<?php $_from = $this->_var['img_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'img');$this->_foreach['img'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['img']['total'] > 0):
    foreach ($_from AS $this->_var['img']):
        $this->_foreach['img']['iteration']++;
?>
											<div class="swiper-slide image-box" data-img-id="<?php echo $this->_var['img']['img_id']; ?>">
												<img src="/<?php echo $this->_var['img']['img_url']; ?>" alt="" style="width: 100%;">
												<div class="overlay-to-top"><a href="##" class="btn btn-primary btn-xs" data-img-id="<?php echo $this->_var['img']['img_id']; ?>">删除</a></div>
											</div>
										<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
									</div>
									<div class="swiper-button-prev"></div>
    								<div class="swiper-button-next"></div>
								</div>
							</div>
							
							<div class="col-sm-7">
								<p><h5>说明：</h5></p>
								<p>
								1、新增商品需保存商品基本信息后，才能添加轮播相册图片
								</p>
								<p>
								2、只能在最后追加图片，调整图片显示顺序请先删除后追加
								</p>
								<p>
								3、请上传正方形的图片，以免系统自动补白
								</p>
								<label for="upload_gallery_image" class="btn btn-primary btn-sm btn-block" id="upload_gallery_image_btn">
		                            <input type="file" name="upload_gallery_image" id="upload_gallery_image" class="hide" value="" onchange="upload_gallery_img()"><i class="fa fa-upload"></i> 追加图片
		                        </label>
							</div>
						</div>
					</div>
				</div>
				
				<div class="ibox float-e-margins">
					<div class="ibox-title">
	                    <h5>详情描述</h5>
	                    &nbsp;&nbsp;&nbsp;&nbsp;<button type="button" onclick="save_desc()" class="btn btn-primary  btn-xs"><i class="fa fa-save"></i> 保存</button>
	                    <?php if ($this->_var['goods']['goods_id']): ?>&nbsp;&nbsp;<a type="button" href="<?php echo $this->_var['_CFG']['web_site_url']; ?>/goods.php?id=<?php echo $this->_var['goods']['goods_id']; ?>" target="_blank" class="btn btn-primary  btn-xs"><i class="fa fa-eye"></i> 预览</a><?php endif; ?>
	                    <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                        </div>
	                </div>
	                
	                <div class="ibox-content no-padding">
                        <div class="summernote">
                        	<?php if ($this->_var['goods']['goods_desc']): ?>
                            	<?php echo $this->_var['goods']['goods_desc']; ?>
                            <?php else: ?>
                            	</br></br></br></br>
                            <?php endif; ?>
                        </div>
	                </div>
	            </div>
	                
			</div>
	</div>
</div>

    
</body>
    
</html>
<script src="js/content.min.js?v=1.0.0"></script>