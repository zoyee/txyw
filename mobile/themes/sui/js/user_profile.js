$(function(){
	var birthday = $("#birthday").val();
	if(birthday != ''){
		$("#birthday").calendar({
		    value: [birthday]
		});
	}else{
		$("#birthday").calendar({value: ''});
	}
});