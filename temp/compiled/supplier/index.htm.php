<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="renderer" content="webkit">
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <title><?php echo $this->_var['_CFG']['shop_name']; ?></title>
    <meta name="keywords" content="">
    <meta name="description" content="">
    <!--[if lt IE 8]>
    <meta http-equiv="refresh" content="0;ie.html" />
    <![endif]-->

    <link rel="shortcut icon" href="favicon.ico">
    <link href="css/bootstrap.min.css?v=3.3.5" rel="stylesheet">
    <link href="css/font-awesome.min.css?v=4.4.0" rel="stylesheet">
    <link href="css/animate.min.css" rel="stylesheet">
    <link href="css/style.min.css?v=4.0.0" rel="stylesheet">
    <link href="css/side_menu.css" rel="stylesheet">
    <link href="js/plugins/gritter/jquery.gritter.css" rel="stylesheet">
    
</head>

<body class="fixed-sidebar full-height-layout gray-bg skin-3" style="overflow:hidden">
    <div id="wrapper">
        <!--左侧导航开始-->
        <nav class="navbar-default navbar-static-side" role="navigation">
            <div class="nav-close"><i class="fa fa-times-circle"></i>
            </div>
            <div class="sidebar-collapse">
                <ul class="nav" id="side-menu">
                    <li class="nav-header">
                        <div class="dropdown profile-element">
                            <span>
                            	<?php if ($this->_var['supplers_info']['head_img']): ?>
                            	<a href="privilege.php?act=edit" data-index="edit_pwd" id="edit_pwd" title="修改掌柜信息" data-title="修改掌柜信息" class="tab-open">
                            	<img alt="image" style="height: 50px;" src="/<?php echo $this->_var['supplers_info']['head_img']; ?>" />
                            	</a>
                            	<?php else: ?>
                            	<img alt="image" style="height: 50px;" src="http://shop.byhill.com/themes/byhilltheme/images/logo.png" />
                            	<?php endif; ?>
                            </span>
                            	<?php if ($this->_var['supplers_info']): ?>
                            	<a href="privilege.php?act=edit" data-index="edit_pwd" id="edit_pwd" title="修改掌柜信息" data-title="修改掌柜信息" class="tab-open">
                            	<?php endif; ?>
                                <span class="clear">
                               <span class="block m-t-xs">
                               	<strong class="font-bold">
                               	<?php if ($this->_var['supplers_info']): ?>
                               		<?php echo $this->_var['supplers_info']['suppliers_name']; ?>
                               	<?php else: ?>
                               		<?php echo $_SESSION['admin_name']; ?>
                               	<?php endif; ?>
                               	</strong>
                               </span>
                               <?php if ($this->_var['supplers_info']): ?></a><?php endif; ?>
                        </div>
                        <div class="logo-element">>>
                        </div>
                    </li>
                                      
                    <?php $_from = $this->_var['menus']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('k', 'menu');if (count($_from)):
    foreach ($_from AS $this->_var['k'] => $this->_var['menu']):
?>
                    <li>
                    	<?php if ($this->_var['menu']['action']): ?>
                    	<a href="<?php echo $this->_var['menu']['action']; ?>">
                            <i class="fa fa fa-bar-chart-o"></i>
                            <span class="nav-label"><?php echo $this->_var['menu']['label']; ?></span>
                            <span class="fa arrow"></span>
                        </a>
                    	<?php else: ?>
                    	<a href="#">
                             <i class="fa fa-hand-o-right"></i>
                            <span class="nav-label"><?php echo $this->_var['menu']['label']; ?></span>
                            <span class="fa arrow"></span>
                        </a>
                        <?php if ($this->_var['menu']['children']): ?>
                        <ul class="nav nav-second-level">
                        	<?php $_from = $this->_var['menu']['children']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'child');if (count($_from)):
    foreach ($_from AS $this->_var['child']):
?>
                        	<li>
                                <a class="J_menuItem" href="<?php echo $this->_var['child']['action']; ?>"><?php echo $this->_var['child']['label']; ?></a>
                            </li>
                        	<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                        </ul>
                        <?php endif; ?>
                    	<?php endif; ?>
                    </li>
                    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                    
                </ul>
            </div>
        </nav>
        <!--左侧导航结束-->
        <!--右侧部分开始-->
        <div id="page-wrapper" class="gray-bg dashbard-1">
            <div class="row border-bottom">
                <nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
                    <!-- <div class="navbar-header"><a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i class="fa fa-bars"></i> </a>
                        <form role="search" class="navbar-form-custom" method="post" action="search_results.html">
                            <div class="form-group">
                                <input type="text" placeholder="请输入您需要查找的内容 …" class="form-control" name="top-search" id="top-search">
                            </div>
                        </form>
                    </div> -->
                    <ul class="nav navbar-top-links navbar-right">
                    	<?php if ($this->_var['supplers_info']): ?>
                    	<li class="hidden-xs">
                            <a data-index="0" class="J_menuItem" href="privilege.php?act=edit_pwd"><i class="fa fa-cog"></i> 修改密码</a>
                        </li>
                        <?php endif; ?>
                        <li class="dropdown" id="notice">
                        	<a class="dropdown-toggle count-info" data-toggle="dropdown" href="#">
						        <i class="fa fa-bell"></i> <span class="label label-primary"></span>消息
						    </a>
						    <ul class="dropdown-menu dropdown-alerts">
						        <li id="new_order_notice">
						            <a href="#">
						                <div>
						                    <i class="fa fa-envelope fa-fw"></i> <span>您有0个未处理订单</span>
						                </div>
						            </a>
						        </li>
						        <li class="divider"></li>
						        <li id="paid_order_notice">
						            <a href="#">
						                <div>
						                    <i class="glyphicon glyphicon-usd"></i> <span>您有0个未处理订单</span>
						                </div>
						            </a>
						        </li>
						        <!-- <li class="divider"></li>
						        <li>
						            <div class="text-center link-block">
						                <a class="J_menuItem" href="notifications.html">
						                    <strong>查看所有 </strong>
						                    <i class="fa fa-angle-right"></i>
						                </a>
						            </div>
						        </li> -->
						    </ul>
                        </li>
                        
				        <!-- <li class="hidden-xs">
                            <a data-index="0" href="index.php?act=clear_cache" target="tab"><i class="glyphicon glyphicon-repeat"></i> 刷新缓存</a>
                        </li> -->
                        <!-- <li class="hidden-xs">
                            <a href="index_v1.html" class="J_menuItem" data-index="0"><i class="fa fa-cart-arrow-down"></i> 购买</a>
                        </li> -->
                        <!-- <li class="dropdown hidden-xs">
                            <a class="right-sidebar-toggle" aria-expanded="false">
                                <i class="fa fa-tasks"></i> 主题
                            </a>
                        </li> -->
                    </ul>
                </nav>
            </div>
            <div class="row content-tabs">
                <button class="roll-nav roll-left J_tabLeft"><i class="fa fa-backward"></i>
                </button>
                <nav class="page-tabs J_menuTabs">
                    <div class="page-tabs-content">
                        <a href="javascript:;" class="active J_menuTab" data-id="index.php?act=welcome">欢迎</a>
                    </div>
                </nav>
                <button class="roll-nav roll-right J_tabRight"><i class="fa fa-forward"></i>
                </button>
                <div class="btn-group roll-nav roll-right">
                    <button class="dropdown J_tabClose" data-toggle="dropdown">关闭操作<span class="caret"></span>

                    </button>
                    <ul role="menu" class="dropdown-menu dropdown-menu-right">
                        <li class="J_tabShowActive"><a>定位当前选项卡</a>
                        </li>
                        <li class="divider"></li>
                        <li class="J_tabCloseAll"><a>关闭全部选项卡</a>
                        </li>
                        <li class="J_tabCloseOther"><a>关闭其他选项卡</a>
                        </li>
                    </ul>
                </div>
                <a href="privilege.php?act=logout" class="roll-nav roll-right J_tabExit"><i class="fa fa fa-sign-out"></i> 退出</a>
            </div>
            <div class="row J_mainContent" id="content-main">
                <iframe class="J_iframe" name="iframe0" width="100%" height="100%" src="index.php?act=welcome" frameborder="0" data-id="index.php?act=welcome" seamless></iframe>
            </div>
            <div class="footer">
                <div class="pull-right">&copy; 2016 <a><?php echo $this->_var['_CFG']['shop_name']; ?></a>
                </div>
            </div>
        </div>
        <!--右侧部分结束-->
        <!--右侧边栏开始-->
        <div id="right-sidebar">
            <div class="sidebar-container">

                <ul class="nav nav-tabs navs-3">

                    <li class="active">
                        <a data-toggle="tab" href="#tab-1">
                            <i class="fa fa-gear"></i> 主题
                        </a>
                    </li>
                    <!-- <li class="active"><a data-toggle="tab" href="#tab-2">
                        通知
                    </a>
                    </li>
                    <li><a data-toggle="tab" href="#tab-3">
                        项目进度
                    </a>
                    </li> -->
                </ul>

                <div class="tab-content">
                    <div id="tab-1" class="tab-pane active">
                        <div class="sidebar-title">
                            <h3> <i class="fa fa-comments-o"></i> 主题设置</h3>
                            <small><i class="fa fa-tim"></i> 你可以从这里选择和预览主题的布局和样式，这些设置会被保存在本地，下次打开的时候会直接应用这些设置。</small>
                        </div>
                        <div class="skin-setttings">
                            <div class="title">主题设置</div>
                            <div class="setings-item">
                                <span>收起左侧菜单</span>
                                <div class="switch">
                                    <div class="onoffswitch">
                                        <input type="checkbox" name="collapsemenu" class="onoffswitch-checkbox" id="collapsemenu">
                                        <label class="onoffswitch-label" for="collapsemenu">
                                            <span class="onoffswitch-inner"></span>
                                            <span class="onoffswitch-switch"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="setings-item">
                                <span>固定顶部</span>

                                <div class="switch">
                                    <div class="onoffswitch">
                                        <input type="checkbox" name="fixednavbar" class="onoffswitch-checkbox" id="fixednavbar">
                                        <label class="onoffswitch-label" for="fixednavbar">
                                            <span class="onoffswitch-inner"></span>
                                            <span class="onoffswitch-switch"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="setings-item">
                                <span>
                        固定宽度
                    </span>

                                <div class="switch">
                                    <div class="onoffswitch">
                                        <input type="checkbox" name="boxedlayout" class="onoffswitch-checkbox" id="boxedlayout">
                                        <label class="onoffswitch-label" for="boxedlayout">
                                            <span class="onoffswitch-inner"></span>
                                            <span class="onoffswitch-switch"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <!-- <div class="title">皮肤选择</div>
                            <div class="setings-item default-skin nb">
                                <span class="skin-name ">
                         <a href="#" class="s-skin-0">
                             默认皮肤
                         </a>
                    </span>
                            </div>
                            <div class="setings-item blue-skin nb">
                                <span class="skin-name ">
                        <a href="#" class="s-skin-1">
                            蓝色主题
                        </a>
                    </span>
                            </div>
                            <div class="setings-item yellow-skin nb">
                                <span class="skin-name ">
                        <a href="#" class="s-skin-3">
                            黄色/紫色主题
                        </a>
                    </span>
                            </div> -->
                        </div>
                    </div>
                    <div id="tab-2" class="tab-pane">

                        <div class="sidebar-title">
                            <h3> <i class="fa fa-comments-o"></i> 最新通知</h3>
                            <small><i class="fa fa-tim"></i> 您当前有10条未读信息</small>
                        </div>

                        <div>
                            <div class="sidebar-message">
                                <a href="#">
                                    <div class="pull-left text-center">
                                        <img alt="image" class="img-circle message-avatar" src="img/a1.jpg">

                                        <div class="m-t-xs">
                                            <i class="fa fa-star text-warning"></i>
                                            <i class="fa fa-star text-warning"></i>
                                        </div>
                                    </div>
                                    <div class="media-body">

                                        据天津日报报道：瑞海公司董事长于学伟，副董事长董社轩等10人在13日上午已被控制。
                                        <br>
                                        <small class="text-muted">今天 4:21</small>
                                    </div>
                                </a>
                            </div>
                        </div>

                    </div>
                    <div id="tab-3" class="tab-pane">

                        <div class="sidebar-title">
                            <h3> <i class="fa fa-cube"></i> 最新任务</h3>
                            <small><i class="fa fa-tim"></i> 您当前有14个任务，10个已完成</small>
                        </div>

                        <ul class="sidebar-list">
                            <li>
                                <a href="#">
                                    <div class="small pull-right m-t-xs">9小时以后</div>
                                    <h4>市场调研</h4> 按要求接收教材；

                                    <div class="small">已完成： 22%</div>
                                    <div class="progress progress-mini">
                                        <div style="width: 22%;" class="progress-bar progress-bar-warning"></div>
                                    </div>
                                    <div class="small text-muted m-t-xs">项目截止： 4:00 - 2015.10.01</div>
                                </a>
                            </li>
                        </ul>

                    </div>
                </div>

            </div>
        </div>
        <!--右侧边栏结束-->
        <!--mini聊天窗口开始-->
        <!-- <div class="small-chat-box fadeInRight animated">

            <div class="heading" draggable="true">
                <small class="chat-date pull-right">
                    2015.9.1
                </small> 与 Beau-zihan 聊天中
            </div>

            <div class="content">

                <div class="left">
                    <div class="author-name">
                        Beau-zihan <small class="chat-date">
                        10:02
                    </small>
                    </div>
                    <div class="chat-message active">
                        你好
                    </div>

                </div>
                <div class="right">
                    <div class="author-name">
                        游客
                        <small class="chat-date">
                            11:24
                        </small>
                    </div>
                    <div class="chat-message">
                        你好，请问H+有帮助文档吗？
                    </div>
                </div>
                <div class="left">
                    <div class="author-name">
                        Beau-zihan
                        <small class="chat-date">
                            08:45
                        </small>
                    </div>
                    <div class="chat-message active">
                        有，购买的H+源码包中有帮助文档，位于docs文件夹下
                    </div>
                </div>
                <div class="right">
                    <div class="author-name">
                        游客
                        <small class="chat-date">
                            11:24
                        </small>
                    </div>
                    <div class="chat-message">
                        那除了帮助文档还提供什么样的服务？
                    </div>
                </div>
                <div class="left">
                    <div class="author-name">
                        Beau-zihan
                        <small class="chat-date">
                            08:45
                        </small>
                    </div>
                    <div class="chat-message active">
                        1.所有源码(未压缩、带注释版本)；
                        <br> 2.说明文档；
                        <br> 3.终身免费升级服务；
                        <br> 4.必要的技术支持；
                        <br> 5.付费二次开发服务；
                        <br> 6.授权许可；
                        <br> ……
                        <br>
                    </div>
                </div>


            </div>
            <div class="form-chat">
                <div class="input-group input-group-sm">
                    <input type="text" class="form-control"> <span class="input-group-btn"> <button
                        class="btn btn-primary" type="button">发送
                </button> </span>
                </div>
            </div>

        </div>
        <div id="small-chat">
            <span class="badge badge-warning pull-right">5</span>
            <a class="open-small-chat">
                <i class="fa fa-comments"></i>

            </a>
        </div> -->
    </div>
    <script src="js/jquery.min.js?v=2.1.4"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/plugins/metisMenu/jquery.metisMenu.js"></script>
    <script src="js/plugins/slimscroll/jquery.slimscroll.min.js"></script>
    <script src="js/plugins/layer/layer.min.js"></script>
    <script src="js/hplus.min.js?v=4.0.0"></script>
    <script type="text/javascript" src="js/contabs.min.js"></script>
    <script src="js/plugins/pace/pace.min.js"></script>
    <script src="js/common_hplus.js"></script>
    <script src="js/plugins/gritter/jquery.gritter.min.js"></script>
	<script type="text/javascript">
		$(function(){
			var ca = 0;
			function getNotice(){
				$.get('index.php?act=check_order', function(data){
					data.total_num = 0;
					data.total_num += parseInt(data.new_orders);
					var s = "您有" + data.new_orders + "个新订单";
					$("#new_order_notice div span").html(s);
					
					data.total_num += parseInt(data.new_paid);
					var s = "您有" + data.new_orders + "个新付款的订单";
					$("#paid_order_notice div span").html(s);
					
					if(data.total_num > 0){
						$("#notice .count-info span").html(data.total_num);
						if(ca++ == 0){
							$.gritter.add({
								title:"您有" + data.total_num + "条未处理消息",
								text:'请点击右上角的<span class="text-warning">消息</span>按钮查看',
								time:10000
							});
						}
					}
										
					/* setTimeout(function(){
						getNotice();
					}, 5000); */
				}, 'json');
			}
			
			$('.tab-open').each(function(){
				$(this).click(function(){
					var t = $(this).attr("href"),
						a = $(this).data("index"),
						//i = $.trim($(this).text()),
						i = $(this).data("title"),
						n = !0;
					if (void 0 == t || 0 == $.trim(t).length) return !1;
					if ($(".J_menuTab").each(function() {
						return $(this).data("id") == t ? ($(this).hasClass("active") || ($(this).addClass("active").siblings(".J_menuTab").removeClass("active"), e(this), $(".J_mainContent .J_iframe").each(function() {
							return $(this).data("id") == t ? ($(this).show().siblings(".J_iframe").hide(), !1) : void 0
						})), n = !1, !1) : void 0
					}), n) {
						var s = '<a href="javascript:;" class="active J_menuTab" data-id="' + t + '">' + i + ' <i class="fa fa-times-circle"></i></a>';
						$(".J_menuTab").removeClass("active");
						var r = '<iframe class="J_iframe" name="iframe' + a + '" width="100%" height="100%" src="' + t + (t.indexOf('?') > -1 ? '&' : '?') + 'v=4.0" frameborder="0" data-id="' + t + '" seamless></iframe>';
						$(".J_mainContent").find("iframe.J_iframe").hide().parents(".J_mainContent").append(r);
						var o = layer.load();
						$(".J_mainContent iframe:visible").load(function() {
							layer.close(o)
						}), $(".J_menuTabs .page-tabs-content").append(s), e($(".J_menuTab.active"))
					}
					return !1;
				});
			});
			
			getNotice();
		});
		
		function e(e) {
			var a = t($(e).prevAll()),
				i = t($(e).nextAll()),
				n = t($(".content-tabs").children().not(".J_menuTabs")),
				s = $(".content-tabs").outerWidth(!0) - n,
				r = 0;
			if ($(".page-tabs-content").outerWidth() < s) r = 0;
			else if (i <= s - $(e).outerWidth(!0) - $(e).next().outerWidth(!0)) {
				if (s - $(e).next().outerWidth(!0) > i) {
					r = a;
					for (var o = e; r - $(o).outerWidth() > $(".page-tabs-content").outerWidth() - s;) r -= $(o).prev().outerWidth(), o = $(o).prev()
				}
			} else a > s - $(e).outerWidth(!0) - $(e).prev().outerWidth(!0) && (r = a - $(e).prev().outerWidth(!0));
			$(".page-tabs-content").animate({
				marginLeft: 0 - r + "px"
			}, "fast")
		}
		
		function t(t) {
			var e = 0;
			return $(t).each(function() {
				e += $(this).outerWidth(!0)
			}), e
		}
	</script>
</body>
</html>