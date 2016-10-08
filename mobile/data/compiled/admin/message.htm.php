<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    

    <title>提示 - <?php echo $this->_var['_CFG']['shop_name']; ?></title>
    <meta name="keywords" content="青山老农，植物素生活，社群电商">
    <meta name="description" content="青山老农，植物素生活，社群电商">
    <link rel="shortcut icon" href="favicon.ico"> <link href="css/bootstrap.min.css?v=3.3.5" rel="stylesheet">
    <link href="css/font-awesome.min.css?v=4.4.0" rel="stylesheet">

    <link href="css/animate.min.css" rel="stylesheet">
    <link href="css/style.min.css?v=4.0.0" rel="stylesheet">
    <base target="_self">

</head>

<body class="gray-bg">
    <!-- <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-4">
            <h2>标题</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="index.html">主页</a>
                </li>
                <li>
                    <strong>包屑导航</strong>
                </li>
            </ol>
        </div>
        <div class="col-sm-8">
            <div class="title-action">
                <a href="empty_page.html" class="btn btn-primary">活动区域</a>
            </div>
        </div>
    </div> -->

    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-sm-12">
                <div class="middle-box text-center animated fadeInRightBig">
                    <h3 class="font-bold">
                    <?php if ($this->_var['msg_type'] == 0): ?>
			          <img src="images/information.gif" width="32" height="32" border="0" alt="information" />
			        <?php elseif ($this->_var['msg_type'] == 1): ?>
			          <img src="images/warning.gif" width="32" height="32" border="0" alt="warning" />
			        <?php else: ?>
			          <img src="images/confirm.gif" width="32" height="32" border="0" alt="confirm" />
			        <?php endif; ?>
                    <?php echo $this->_var['msg_detail']; ?>
                    </h3>

                    <div class="error-desc">
                        <?php if ($this->_var['auto_redirect']): ?><?php echo $this->_var['lang']['auto_redirection']; ?><?php endif; ?>
                        <br/><a href="javascript:history.back()" class="btn btn-primary m-t" id="goback_btn">返回</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="js/jquery.min.js?v=2.1.4"></script>
    <script src="js/bootstrap.min.js?v=3.3.5"></script>
    <script src="js/content.min.js?v=1.0.0"></script>
    <script type="text/javascript">
    	$(function(){
    		var second = 3;
    		var txt = $("#goback_btn").html();
    		$("#goback_btn").html(txt + "(" + second + ")");
    		function checkSecondGoBack(){
				$("#goback_btn").html(txt + "(" + second + ")");
    			if(second-- == 0){
    				history.back();
    			}else{
    				setTimeout(function(){
    					checkSecondGoBack();
            		}, 1000);
    			}
    		}
    		checkSecondGoBack();
    	});
    </script>
</body>

</html>