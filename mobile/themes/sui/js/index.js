// 加载flag
var loading = false;
// 最多可加载的条目
var maxItems = 100;
// 模板所在目录
var template_url = "themes/sui/library/";
var template = "";
// 每次加载添加多少条目
var itemsPerLoad = $('.infinite-scroll-bottom').data('number');
var remote_url = $('.infinite-scroll-bottom').data('url');
var template = $('.infinite-scroll-bottom').data('template');

//获取模板
$.ajax({
	type : 'get',
	url : template_url + template,
	cache : false,
	async : false,
	dataType : 'text',
	success : function(html){
		template = html;
	}
});

function addItems(number, lastIndex) {
	// 生成新条目的HTML
	var html = '';
	
	$.ajax({
		type : 'post',
		url : remote_url,
		data : {
			amount : number,
			last : lastIndex
		},
		cache : false,
		async : false,
		dataType : 'json',
		success : function(data){
			var len = data.length;
			for(var i = 0; i < len; i++){
				var d = data[i];
				var el = $(template);
				for(key in d){
					el.find("." + key).html(d[key]);
				}
				// 添加新条目
				$('.infinite-scroll-bottom .list-container').append(el);
			}
			if(len < number){
				$(document).unbind('infinite');
				$('.infinite-scroll-preloader').remove();
			}
		}
	});
}
// 预先加载itemsPerLoad条
addItems(itemsPerLoad, 0);
// 上次加载的序号
var lastIndex = itemsPerLoad;

// 注册'infinite'事件处理函数
$(document).on('infinite', '.infinite-scroll-bottom', function() {
	// 如果正在加载，则退出
	if (loading) return;
	// 设置flag
	loading = true;
	// 模拟1s的加载过程
	setTimeout(function() {
		// 重置加载flag
		loading = false;
		if (lastIndex >= maxItems) {
			// 加载完毕，则注销无限加载事件，以防不必要的加载
			$.detachInfiniteScroll($('.infinite-scroll'));
			// 删除加载提示符
			$('.infinite-scroll-preloader').remove();
			return;
		}

		// 添加新条目
		addItems(itemsPerLoad, lastIndex);
		// 更新最后加载的序号
		lastIndex = $('.list-container').children().length;
		
		// 容器发生改变,如果是js滚动，需要刷新滚动
		$.refreshScroller();
	}, 1000);
});

