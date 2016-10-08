/**
 * 
 */
var chars = [ '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'A', 'B', 'C',
		'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q',
		'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z' ];
var debug_status = 1;
var tab_base_idx = '_' + generateMixed(2);
var page_a_idx = 0;
function debug(n, s) {
	if (debug_status == 1) {
		$.gritter.add({
			title : n,
			text : s,
			time : 10000
		});
	}
}

function generateMixed(n) {
	var res = "";
	for (var i = 0; i < n; i++) {
		var id = Math.ceil(Math.random() * 35);
		res += chars[id];
	}
	return res;
}

function gritter(param){
	$.gritter.add(param);
}

function loadTab(title, url, idx) {
	var t = url, a = idx, i = title, n = !0;

	$(".J_menuTab").each(function() {
		if (t == $(this).attr("data-id")) {
			$(this).click();
			n = !1;
		}
	});

	if (n) {
		var s = '<a href="javascript:;" class="active J_menuTab" data-id="' + t
				+ '">' + i + ' <i class="fa fa-times-circle"></i></a>';
		$(".J_menuTab").removeClass("active");
		var r = '<iframe class="J_iframe" name="iframe' + a
				+ '" width="100%" height="100%" src="' + t
				+ (t.indexOf('?') > -1 ? '&' : '?')
				+ 'v=4.0" frameborder="0" data-id="' + t
				+ '" seamless></iframe>';
		$(".J_mainContent").find("iframe.J_iframe").hide().parents(
				".J_mainContent").append(r);
		var o = layer.load();
		$(".J_mainContent iframe:visible").load(function() {
			layer.close(o)
		}), $(".J_menuTabs .page-tabs-content").append(s)
	}
	return !1
}

function simpleLoad(btn) {
	location.reload();
	btn.children().addClass("fa-spin");
	btn.contents().last().replaceWith(" 刷新")
};

// 初始化分页显示
function initPagination(page_filter) {
	$("#total_count").html(page_filter.record_count);
	var page_html = "";
	if (page_filter.page_count <= 1) {
		page_html = '<li class="active"><a href="##">1</a></li>';
	} else {
		if (page_filter.page == 1) {
			page_html = '<li class="disabled first"><a href="##">&laquo;</a></li>';
			page_html += '<li class="disabled previous"><a href="##">&lt;</a></li>';
		} else {
			page_html = '<li class="first"><a href="##">&laquo;</a></li>';
			page_html += '<li class="previous"><a href="##">&lt;</a></li>';
		}

		var btn_count = page_filter.page_count > 5 ? 5 : page_filter.page_count;
		var min_page = page_filter.page - 2 > 0 ? page_filter.page - 2 : 1;
		for (var idx = min_page; idx < min_page + btn_count; idx++) {
			if (idx > page_filter.page_count)
				break;
			page_html += '<li '
					+ (idx == page_filter.page ? 'class="active"' : '')
					+ '><a href="##">' + idx + '</a></li>';
		}

		if (page_filter.page == page_filter.page_count) {
			page_html += '<li class="disabled next"><a href="##">&gt;</a></li>';
			page_html += '<li class="disabled last"><a href="##">&raquo;</a></li>';
		} else {
			page_html += '<li class="next"><a href="##">&gt;</a></li>';
			page_html += '<li class="last"><a href="##">&raquo;</a></li>';
		}
	}
	$("ul.pagination").html(page_html);
	bindPageBtnEvent(page_filter.page_count, page_filter.page);
}

// 分页按钮加事件
function bindPageBtnEvent(page_count, page) {
	$(".pagination li").each(function() {
		if ($(this).hasClass('disabled') || $(this).hasClass('active')) {

		} else if ($(this).hasClass('first')) {
			$(this).click(function() {
				$("#query_form #page").val(1);
				return ajaxFormQuery();
			});
		} else if ($(this).hasClass('last')) {
			$(this).click(function() {
				$("#query_form #page").val(page_count);
				return ajaxFormQuery();
			});
		} else if ($(this).hasClass('previous')) {
			$(this).click(function() {
				$("#query_form #page").val(page - 1);
				return ajaxFormQuery();
			});
		} else if ($(this).hasClass('next')) {
			$(this).click(function() {
				$("#query_form #page").val(page + 1);
				return ajaxFormQuery();
			});
		} else {
			$(this).click(function() {
				$("#query_form #page").val($(this).find("a").html());
				return ajaxFormQuery();
			});
		}
	});
}

function bindEvent(parentSelector){
	$(parentSelector + " a[target=tab]").each(function(idx) {
		this.onclick = function() {
			var title = $(this).html();
			title = title.replace(/<i.*i>/g, '');
			title = title.replace(/<i.*\/>/g, '');
			if(title.match(/^<img.*>$/)){
				title = $(this).attr('title');
			}
			var url = $(this).attr('href');
			if ($(this).attr('data-index') == undefined) {
				var idx = tab_base_idx + (page_a_idx++);
				$(this).attr('data-index', idx);
			} else {
				var idx = $(this).attr('data-index');
			}
			if(self.location==top.location){
				loadTab(title, url, idx);
				return false;
			}else if (parent.loadTab) {
				parent.loadTab(title, url, idx);
				return false;
			} else {
				return true;
			}

		};
	});
	
	// 删除按钮的事件ajax提交
	$(parentSelector + " a.btn-del").each(function() {
		this.onclick = function() {
			var url = $(this).attr('href');
			
			swal({
				title : "是否删除记录？",
				text : "数据将无法恢复，请谨慎操作！",
				type : "warning",
				showCancelButton : true,
				confirmButtonColor : "#DD6B55",
				confirmButtonText : "确定",
				cancelButtonText : "取消",
				closeOnConfirm : true,
				closeOnCancel : true
			}, function(isConfirm) {
				if (isConfirm) {
					$.get(url, function(data){
						if(data.error == 0){
							if (parent.gritter){
								parent.gritter({
									title : '操作提示',
									text : '操作成功',
									time : 10000
								});
							}
							
							if($("#query_form").length > 0){
								if($("#query_form button[type=submit]").length > 0){
									$("#query_form button[type=submit]").trigger('click');
								}else{
									ajaxFormQuery();
								}
							}else{
								location.reload();
							}
						}else{
							var message = data.message;
							if (parent.gritter){
								parent.gritter({
									title : '操作失败',
									text : message,
									time : 10000
								});
							}
						}
					}, 'json');
				}
			});
			return false;
		}
	});
	
	// 表格链接的ajax提交
	$(parentSelector + " a[target=ajax]").each(function() {
		this.onclick = function() {
			var url = $(this).attr('href');
			$.get(url, function(data){
				if(data.error == 0){
					var message = '操作成功';
					if(data.message != ''){
						message += ',' + data.message
					}
					if (parent.gritter){
						parent.gritter({
							title : '操作提示',
							text : message,
							time : 10000
						});
					}
					
					if($("#query_form").length > 0){
						$("#query_form button[type=submit]").trigger('click');
					}else{
						location.reload();
					}
				}else{
					if (parent.gritter){
						parent.gritter({
							title : '操作提示',
							text : '操作失败',
							time : 10000
						});
					}
				}
			}, 'json');
			return false;
		}
	});
	
	//可编辑区域
	$(parentSelector + " span.editable").each(function(){
		this.onclick = function(){
			if($(this).children().length == 0){
				var old_val = $(this).html();
				var width = $(this).parent().width();
				$(this).html('');
				var box = $('<input type="text" value="' + old_val + '" style="width:'+width+'px;"/>').appendTo(this);
				
				var action = $(this).attr('url');
				box.bind('keypress', function(e){
					if(e.keyCode == 13){
						var val = $(this).val();
						$.get(action + val, function(data){
							if(data.error == 0){
								if (parent.gritter){
									parent.gritter({
										title : '操作提示',
										text : '操作成功',
										time : 10000
									});
								}
								
								if($("#query_form").length > 0){
									$("#query_form button[type=submit]").trigger('click');
								}else{
									location.reload();
								}
							}
						}, 'json');
						return false;
					}
				});
				box.bind('blur', function(){
					var val = $(this).val();
					$.get(action + val, function(data){
						if(data.error == 0){
							if (parent.gritter){
								parent.gritter({
									title : '操作提示',
									text : '操作成功',
									time : 10000
								});
							}
							
							if($("#query_form").length > 0){
								$("#query_form button[type=submit]").trigger('click');
							}else{
								location.reload();
							}
						}
					}, 'json');
				});
				
				box.focus();
			}
			
		}
	});
	
	//模态框
	$(parentSelector + " a[target=modal]").each(function(){
		this.onclick = function() {
			var title = $(this).attr('title');
			var url = $(this).attr('href');
			$.ajax({
			    type: 'POST',
			    url: url ,
			    data: null ,
			    success: function(data) {
					$('#modal div.modal-title').html(title);
					$('#modal div.modal-body').html(data.content[0].str);
					$('#modal').modal('show');
				},
				error: function(response){
					//debug(response.responseText);
					var text = response.responseText
					var data = eval("(" + text + ")");
					$('#modal div.modal-title').html(title);
					$('#modal div.modal-body').html(data.content[0].str);
					$('#modal').modal('show');
				},
			    dataType: "json"
			});
			/*$.get(url, function(data){
				$('#modal div.modal-title').html(title);
				$('#modal div.modal-body').html(data.content[0].str);
				$('#modal').modal('show');
			}, 'json');*/
			return false;
		};
	});
	
}

// ajax表单查询
function ajaxFormQuery() {
	var url = $("#query_form").attr('action') + "&"
			+ $("#query_form").serialize();
	//debug("url", url);
	
	$.ajax({
	    type: 'POST',
	    url: url ,
	    data: null ,
	    success: function(data) {
			//debug('data.content', data.content);
			//alert(data);
	    	if(data.error != 0){
	    		layer.alert(data.message);
	    	}
			$("#form_table").html(data.content);
			$("#check_all").attr('checked', false);
			initPagination(data.filter);
			bindEvent("#form_table");
		},
		error: function(response){
			//debug(response.responseText);
			var text = response.responseText
			var data = eval("(" + text + ")");
			$("#form_table").html(data.content);
			$("#check_all").attr('checked', false);
			initPagination(data.filter);
			bindEvent("#form_table");
		},
	    dataType: "json"
	});
	/*$.get(url, function(data) {
		//debug('data.content', data.content);
		//alert(data);
		$("#form_table").html(data.content);
		$("#check_all").attr('checked', false);
		initPagination(data.filter);
	
		bindEvent("#form_table");
	}, "json");*/
	return false;
}

$(function() {
	bindEvent("");
	
	$("#batch_btns a").each(function(){
		this.onclick = function() {
			var snArray = new Array();
			var el = this;
			$("#batch_form input[type=checkbox]").each(function(){
				if(this.checked){
					snArray.push($(this).attr('value'));
				}
			});
			var checkbox_name = $(this).attr('checkbox_name');
			var checkbox_value = snArray.toString();
			if(checkbox_name != 'undefined'){
				$("#" + checkbox_name).val(checkbox_value);
			}
			
			
			var cond = $(this).attr('value');
			
			if(checkbox_value != ""){
				if(cond != 'undefined'){
					if(cond.indexOf('&') > 0){
						var kva = cond.split('&');
						for(var idx in kva){
							var cond_arr = kva[idx].split("=");
							$("#batch_form").append('<input name="' + cond_arr[0] + '" type="hidden" value="' + cond_arr[1] + '" />');	
						}
					} else {
						var cond_arr = cond.split("=");
						$("#batch_form").append('<input name="' + cond_arr[0] + '" type="hidden" value="' + cond_arr[1] + '" />');	
					}
				}
				
				swal({
					title : "确认进行批量操作？",
					text : "数据将无法恢复，请谨慎操作！",
					type : "warning",
					showCancelButton : true,
					confirmButtonColor : "#DD6B55",
					confirmButtonText : "确定",
					cancelButtonText : "取消",
					closeOnConfirm : true,
					closeOnCancel : true
				}, function(isConfirm) {
					if (isConfirm) {
						if($(el).attr("target") == "_blank"){
							$("#batch_form").attr("target", "_blank");
						}
						debug("url", $("#batch_form").attr('action'));
						$("#batch_form").submit();
						$("#batch_form").attr("target", "_self");
					}
				});
			}else{
				swal({
					title : "您还没有勾选任何数据",
					text : "请先选择需要操作的记录",
					type : "warning",
					confirmButtonColor : "#DD6B55",
					confirmButtonText : "确定",
					closeOnConfirm : true
				}, function() {});
			}
			return false;
		}
	});

	// 刷新按钮加事件
	$("#loading-example-btn").click(function() {
		btn = $(this);
		simpleLoad(btn);
//		$("#query_form button[type=submit]").trigger('click');
	});

	// 列表查询表单ajax提交
	$("#query_form button[type=submit]").click(function() {
		$("#query_form #page").val(1);
		return ajaxFormQuery();
	});

	// 分页大小设置加事件
	$("#page_size_setting a").each(function(idx) {
		this.onclick = function() {
			$("#query_form #page_size").val($(this).attr("value"));
			document.cookie = "ECSCP[page_size]=" + $(this).attr("value") + ";";
			$("#query_form #page").val(1);
			return ajaxFormQuery();
		}
	});
	
	
	//全选按钮事件
	$("#check_all").click(function(){
		//debug(this.checked);
		var c = this.checked;
		$("#form_table input[type=checkbox]").each(function(){
			if(c){
				this.checked = true;
			}else{
				this.checked = false;
			}
		});
	});
	

	//获取表头配置
	var t_head = new Array();
	var noSortColumn = new Array();
	var table_head;
	
	if($("#batch_form table.dataTable").length > 0){
		$("#batch_form table.dataTable").each(function(){
			t_head = new Array();
			noSortColumn = new Array();
			table_head = $(this).find("thead th");
			table_head.each(function(idx){
				if(typeof($(this).attr('sort-by')) != 'undefined'){
					t_head.push($(this).attr('sort-by'));
				}else{
					noSortColumn.push(idx);
					t_head.push(null);
				}
			});
			
			$(this).dataTable({
				sort: true,
				columnDefs: [{ "sortable": false, "targets": noSortColumn}],
				paginate: false,
				filter: false,
				lengthChange: false,
				info: false,
				serverSide: true,
				serverData: function ( sSource, aoData, fnCallback, oSettings ){
					var order = aoData.order[0];
					if(t_head[order.column] == null) return;
					//debug(order.column + " " + order.dir);
					$("#query_form #sort_by").val(t_head[order.column]);
					$("#query_form #sort_order").val(order.dir);
					table_head.each(function(idx){
						if(t_head[idx] == null){
							//do nothing
						}else if(order.column == idx){
							$(this).removeClass("sorting");
							$(this).removeClass("sorting_desc");
							$(this).removeClass("sorting_asc");
							$(this).addClass("sorting" + "_" + order.dir);
						}else{
							$(this).removeClass("sorting");
							$(this).removeClass("sorting_desc");
							$(this).removeClass("sorting_asc");
							$(this).addClass("sorting");
						}
					});
					
					$("#query_form button[type=submit]").trigger('click');
				}
			});
		});
	}else if($("table.dataTable").length > 0) {
		$("table.dataTable").each(function(){
			t_head = new Array();
			noSortColumn = new Array();
			table_head = $(this).find("thead th");
			table_head.each(function(idx){
				if(typeof($(this).attr('sort-by')) != 'undefined'){
					t_head.push($(this).attr('sort-by'));
				}else{
					noSortColumn.push(idx);
					t_head.push(null);
				}
			});
			
			$(this).dataTable({
				sort: true,
				columnDefs: [{ "sortable": false, "targets": noSortColumn}],
				sorting: [[0, 'asc']],
				paginate: false,
				filter: false,
				lengthChange: false,
				info: true
			});
		});
	}
	
	/*var table_head = $("table.dataTable thead tr").children();
	table_head.each(function(idx){
		if(typeof($(this).attr('sort-by')) != 'undefined'){
			t_head.push($(this).attr('sort-by'));
		}else{
			noSortColumn.push(idx);
			t_head.push(null);
		}
	});
	
	//debug(t_head);
	//表头排序查询
	if($("#page_size").length > 0){
		$("#batch_form table.dataTable").dataTable({
			sort: true,
			columnDefs: [{ "sortable": false, "targets": noSortColumn}],
			paginate: false,
			filter: false,
			lengthChange: false,
			info: false,
			serverSide: true,
			serverData: function ( sSource, aoData, fnCallback, oSettings ){
				var order = aoData.order[0];
				if(t_head[order.column] == null) return;
				//debug(order.column + " " + order.dir);
				$("#query_form #sort_by").val(t_head[order.column]);
				$("#query_form #sort_order").val(order.dir);
				table_head.each(function(idx){
					if(t_head[idx] == null){
						//do nothing
					}else if(order.column == idx){
						$(this).removeClass("sorting");
						$(this).removeClass("sorting_desc");
						$(this).removeClass("sorting_asc");
						$(this).addClass("sorting" + "_" + order.dir);
					}else{
						$(this).removeClass("sorting");
						$(this).removeClass("sorting_desc");
						$(this).removeClass("sorting_asc");
						$(this).addClass("sorting");
					}
				});
				
				$("#query_form button[type=submit]").trigger('click');
			}
		});
	}else if($("table.dataTable").length > 0){
		$("table.dataTable").each(function(){
			$(this).dataTable({
				sort: true,
				columnDefs: [{ "sortable": false, "targets": noSortColumn}],
				sorting: [[0, 'asc']],
				paginate: false,
				filter: false,
				lengthChange: false,
				info: true
			});
		});
	}*/
	
	if($("#query_form #sort_by").length > 0){
		var sortBy = $("#query_form #sort_by").val();
		var sortDir = $("#query_form #sort_order").val();
		if(sortBy != ""){
			//debug(sortBy);
			table_head.each(function(idx){
				if(t_head[idx] == null){
					//do nothing
				}else if(t_head[idx] == sortBy){
					$(this).removeClass("sorting");
					$(this).removeClass("sorting_desc");
					$(this).removeClass("sorting_asc");
					$(this).addClass("sorting" + "_" + sortDir);
				}else{
					$(this).removeClass("sorting");
					$(this).removeClass("sorting_desc");
					$(this).removeClass("sorting_asc");
					$(this).addClass("sorting");
				}
			});
		}
	}
	
	//$("form.validation-form").bootstrapValidator();
});