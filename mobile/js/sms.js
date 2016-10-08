function trim(str) {
	return str.replace(/^\s*(.*?)[\s\n]*$/g, '$1');
}
var time_s;
function getverifycode1(field_id, field_name, button, refresh_time) {
	var mobile = trim(document.getElementById(field_id).value);
	if(mobile == '') {
//		alert(field_name+" 不能为空！");
		openAlertDiv(field_name+" 不能为空！");
//		ecsAlert({
//	  		'tips': field_name +" 不能为空！",
//	  		'button_text': '确定',
//	  		'click': function(){
//	  			document.getElementById(field_id).focus();
//	  		}
//	  	});
		
		return;
	}
	
	jQuery(button).attr("disabled","disabled"); 
	Ajax.call('sms.php?step=getverifycode1&r=' + Math.random(), 'mobile=' + mobile, function(data){
		var result = jQuery.parseJSON(data);
		if(result.error == 0){
			//成功
			for(var i=refresh_time;i>=0;i--) { 
		        window.setTimeout('doRefresh("' + jQuery(button).attr('id') + '",' + i + ', "' + jQuery(button).attr('value') + '")', (refresh_time-i) * 1000); 
		    } 
		}else{
			//失败
			jQuery(button).removeAttr("disabled");
		}
//		alert(result.message);
		openAlertDiv(result.message);
//		ecsAlert({
//	  		'tips': result.message,
//	  		'button_text': '确定'
//	  	});
	}, 'POST', 'TEXT');
}

function getverifycode2(field_id, field_name, button, refresh_time) {
	var mobile = trim(document.getElementById(field_id).value);
	if(mobile == '') {
//		alert(field_name+" 不能为空！");
		openAlertDiv(field_name+" 不能为空！");
//		ecsAlert({
//	  		'tips': field_name +" 不能为空！",
//	  		'button_text': '确定',
//	  		'click': function(){
//	  			document.getElementById(field_id).focus();
//	  		}
//	  	});
		
		return;
	}
	
	jQuery(button).attr("disabled","disabled"); 
	Ajax.call('sms.php?step=getverifycode2&r=' + Math.random(), 'mobile=' + mobile, function(data){
		var result = jQuery.parseJSON(data);
		if(result.error == 0){
			//成功
			for(var i=refresh_time;i>=0;i--) { 
		        window.setTimeout('doRefresh("' + jQuery(button).attr('id') + '",' + i + ', "' + jQuery(button).attr('value') + '")', (refresh_time-i) * 1000); 
		    } 
		}else{
			//失败
			jQuery(button).removeAttr("disabled");
		}
//		alert(result.message);
		openAlertDiv(result.message);
//		ecsAlert({
//	  		'tips': result.message,
//	  		'button_text': '确定'
//	  	});
	}, 'POST', 'TEXT');
}

function crtVerifyCodeForGetPwd(field_id, field_name, button, refresh_time){
	var mobile = trim(document.getElementById(field_id).value);
	if(mobile == '') {
//		alert(field_name+" 不能为空！");
		openAlertDiv(field_name+" 不能为空！");
//		ecsAlert({
//	  		'tips': field_name +" 不能为空！",
//	  		'button_text': '确定',
//	  		'click': function(){
//	  			document.getElementById(field_id).focus();
//	  		}
//	  	});
		
		return;
	}
	
	jQuery(button).attr("disabled","disabled"); 
	Ajax.call('sms.php?step=crtVerifyCodeForGetPwd&r=' + Math.random(), 'mobile=' + mobile, function(data){
		var result = jQuery.parseJSON(data);
		if(result.error == 0){
			//成功
			for(var i=refresh_time;i>=0;i--) { 
		        window.setTimeout('doRefresh("' + jQuery(button).attr('id') + '",' + i + ', "' + jQuery(button).attr('value') + '")', (refresh_time-i) * 1000); 
		    } 
		}else{
			//失败
			jQuery(button).removeAttr("disabled");
		}
//		alert(result.message);
		openAlertDiv(result.message);
//		ecsAlert({
//	  		'tips': result.message,
//	  		'button_text': '确定'
//	  	});
	}, 'POST', 'TEXT');
}

function doRefresh(button_id, time, original_value) { 
	jQuery("#" + button_id).attr("value", time + "秒"); 
    if(time == 0) { 
    	jQuery("#" + button_id).removeAttr("disabled");
    	jQuery("#" + button_id).attr("value", original_value);
    } 
} 