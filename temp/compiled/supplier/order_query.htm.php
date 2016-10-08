<!DOCTYPE html>
<html>

<?php echo $this->fetch('head.htm'); ?>
<link href="css/plugins/datapicker/datepicker3.css" rel="stylesheet">
<script src="js/plugins/datapicker/bootstrap-datepicker.js"></script>
<script src="js/region.js"></script>

<script type="text/javascript">
region.isAdmin = true;

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
		//alert("呵呵");
		//$("#signupForm").submit();
		$("#isDwonload").val("1");
		$("#signupForm").submit();
		$("#isDwonload").val("0");
	    });
	
});
</script>
<body class="gray-bg">
    <div class="wrapper wrapper-content animated fadeInRight">
        
        <div class="row">
            <div class="col-sm-8">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>订单高级查询</h5>
                        <div class="ibox-tools">
                            <a href="order.php?act=list" class="btn btn-primary btn-xs" target="tab"><i class="fa fa-list-alt"></i> 订单列表</a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <form class="form-horizontal m-t validation-form" id="signupForm" action="order.php?act=list" method="post">
                            <div class="form-group">
                                <label class="col-sm-2 control-label"><?php echo $this->_var['lang']['label_order_sn']; ?></label>
                                <div class="col-sm-4">
                                    <input id="order_sn" name="order_sn" class="form-control" type="text" >
                                </div>
                                <label class="col-sm-2 control-label">商品名称：</label>
                                <div class="col-sm-4">
                                    <input id="goods_name" name="goods_name" class="form-control" type="text" >
                                </div>
                                <!-- <label class="col-sm-2 control-label"><?php echo $this->_var['lang']['label_email']; ?></label>
                                <div class="col-sm-4">
                                    <input id="email" name="email" class="form-control" type="text" >
                                </div> -->
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label"><?php echo $this->_var['lang']['label_user_name']; ?></label>
                                <div class="col-sm-4">
                                    <input id="user_name" name="user_name" class="form-control" type="text" >
                                </div>
                                <label class="col-sm-2 control-label"><?php echo $this->_var['lang']['label_consignee']; ?></label>
                                <div class="col-sm-4">
                                    <input id="consignee" name="consignee" class="form-control" type="text" >
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label"><?php echo $this->_var['lang']['label_address']; ?></label>
                                <div class="col-sm-4">
                                    <input id="address" name="address" class="form-control" type="text" >
                                </div>
                                <label class="col-sm-2 control-label"><?php echo $this->_var['lang']['label_zipcode']; ?></label>
                                <div class="col-sm-4">
                                    <input id="zipcode" name="zipcode" class="form-control" type="text" >
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label"><?php echo $this->_var['lang']['label_tel']; ?></label>
                                <div class="col-sm-4">
                                    <input id="tel" name="tel" class="form-control" type="text" >
                                </div>
                                <label class="col-sm-2 control-label"><?php echo $this->_var['lang']['label_mobile']; ?></label>
                                <div class="col-sm-4">
                                    <input id="mobile" name="mobile" class="form-control" type="text" >
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label"><?php echo $this->_var['lang']['label_area']; ?></label>
                               	<div class="col-sm-2">
	                               	<div class="input-group">
	                                    <select name="country" id="selCountries" onchange="region.changed(this, 1, 'selProvinces')" class="form-control m-b">
									          <option value="0"><?php echo $this->_var['lang']['select_please']; ?></option>
									          <?php $_from = $this->_var['country_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'country');if (count($_from)):
    foreach ($_from AS $this->_var['country']):
?>
									          <option value="<?php echo $this->_var['country']['region_id']; ?>"><?php echo $this->_var['country']['region_name']; ?></option>
									          <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
									     </select>
									 </div>
							     </div>
							     <div class="col-sm-2">
	                               	<div class="input-group">
									     <select name="province" id="selProvinces" onchange="region.changed(this, 2, 'selCities')" class="form-control m-b">
								          <option value="0"><?php echo $this->_var['lang']['select_please']; ?></option>
								        </select>
								     </div>
							     </div>
							     <div class="col-sm-2">
	                               	<div class="input-group">
								        <select name="city" id="selCities" onchange="region.changed(this, 3, 'selDistricts')" class="form-control m-b">
								          <option value="0"><?php echo $this->_var['lang']['select_please']; ?></option>
								        </select>
								     </div>
							     </div>
							     <div class="col-sm-2">
	                               	<div class="input-group">
								        <select name="district" id="selDistricts" class="form-control m-b">
								          <option value="0"><?php echo $this->_var['lang']['select_please']; ?></option>
								        </select>
							        </div>
						         </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label"><?php echo $this->_var['lang']['label_shipping']; ?></label>
                                <div class="col-sm-4">
	                               	<div class="input-group">
	                                    <select name="shipping_id" id="select4" class="form-control m-b">
									        <option value="0"><?php echo $this->_var['lang']['select_please']; ?></option>
									        <?php $_from = $this->_var['shipping_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'shipping');if (count($_from)):
    foreach ($_from AS $this->_var['shipping']):
?>
									        <option value="<?php echo $this->_var['shipping']['shipping_id']; ?>"><?php echo $this->_var['shipping']['shipping_name']; ?></option>
									        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
							            </select>
						            </div>
                                </div>
                                <label class="col-sm-2 control-label"><?php echo $this->_var['lang']['label_payment']; ?></label>
                                <div class="col-sm-4">
	                               	<div class="input-group">
	                                    <select name="pay_id" id="select5" class="form-control m-b">
									        <option value="0"><?php echo $this->_var['lang']['select_please']; ?></option>
									        <?php $_from = $this->_var['pay_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'pay');if (count($_from)):
    foreach ($_from AS $this->_var['pay']):
?>
									        <option value="<?php echo $this->_var['pay']['pay_id']; ?>"><?php echo $this->_var['pay']['pay_name']; ?></option>
									        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
							            </select>
						            </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label"><?php echo $this->_var['lang']['label_time']; ?></label>
                                <div class="input-daterange col-sm-5 input-group" id="datepicker" style="padding-left:15px;">
                                    <input id="start_time" name="start_time" class="form-control" type="text">
									<span class="input-group-addon">至</span>
                                    <input id="end_time" name="end_time" class="form-control" type="text">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label"><?php echo $this->_var['lang']['label_order_status']; ?></label>
                                <div class="col-sm-2">
	                               	<div class="input-group">
	                                    <select name="order_status" id="select9" class="form-control m-b">
								          <option value="-1"><?php echo $this->_var['lang']['select_please']; ?></option>
								          <?php echo $this->html_options(array('options'=>$this->_var['os_list'],'selected'=>'-1')); ?>
								        </select>
							        </div>
                                </div>
                                <label class="col-sm-2 control-label"><?php echo $this->_var['lang']['label_pay_status']; ?></label>
                                <div class="col-sm-2">
	                               	<div class="input-group">
	                                    <select name="pay_status" id="select11" class="form-control m-b">
								          <option value="-1"><?php echo $this->_var['lang']['select_please']; ?></option>
								          <?php echo $this->html_options(array('options'=>$this->_var['ps_list'],'selected'=>'-1')); ?>
								        </select>
							        </div>
                                </div>
                                <label class="col-sm-2 control-label"><?php echo $this->_var['lang']['label_shipping_status']; ?></label>
                                <div class="col-sm-2">
	                               	<div class="input-group">
	                                    <select name="shipping_status" id="select10" class="form-control m-b">
								          <option value="-1"><?php echo $this->_var['lang']['select_please']; ?></option>
								          <?php echo $this->html_options(array('options'=>$this->_var['ss_list'],'selected'=>'-1')); ?>
								        </select>
							        </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-8 col-sm-offset-5">
                                    <button class="btn btn-primary" type="submit"><i class="fa fa-search-plus"></i> 提交</button>
                                    <button class="btn btn-warning" type="reset"><i class="fa fa-repeat"></i> 重置</button>
                                    <input type="hidden" name="isDwonload" id="isDwonload" value="0" />
                                    <button type="button" id="dwonload_excel"  class="btn btn-primary btn-sm"><i class="fa fa-file-excel-o"></i> Excel导出</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>