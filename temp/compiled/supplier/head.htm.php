<head>
	<title><?php echo $this->_var['_CFG']['shop_name']; ?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="keywords" content="青山老农，植物素生活，社群电商">
    <meta name="description" content="青山老农，植物素生活，社群电商">
    <link rel="shortcut icon" href="favicon.ico"> 
    <link href="css/bootstrap.min.css?v=3.3.5" rel="stylesheet">
    <link href="css/font-awesome.min.css?v=4.4.0" rel="stylesheet">
    <link href="css/animate.min.css" rel="stylesheet">
    <link href="js/plugins/gritter/jquery.gritter.css" rel="stylesheet">
    <link href="css/plugins/sweetalert/sweetalert.css" rel="stylesheet">
    <link href="css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">
    <link href="css/style.min.css?v=4.0.0" rel="stylesheet">
    <link href="css/plugins/toastr/toastr.min.css" rel="stylesheet">
    <style type="text/css">
    	hr{
    		margin-top:0;
    		margin-bottom:0;
    	}
    </style>
    <base target="_self">
    <script src="js/jquery.min.js?v=2.1.4"></script>
    <script src="js/bootstrap.min.js?v=3.3.5"></script>
    <script src="js/content.min.js?v=1.0.0"></script>
    <script src="js/modal.js"></script>
	<script src="js/plugins/sweetalert/sweetalert.min.js"></script>
    <script src="js/common_hplus.js?v=1.4"></script>
    <script src="js/plugins/gritter/jquery.gritter.min.js"></script>
    <script src="js/jquery.metadata.js"></script>
    <script src="js/plugins/validate/jquery.validate.min.js"></script>
    <script src="js/plugins/validate/messages_zh.min.js"></script>
    <script src="js/demo/form-validate-demo.min.js"></script>
    <!-- <script src="js/bootstrapValidator.min.js"></script> -->
    <script src="js/plugins/dataTables/jquery.dataTables.js"></script>
    <script src="js/plugins/dataTables/dataTables.bootstrap.js"></script>
    <script src="js/plugins/toastr/toastr.min.js"></script>
    <script type="text/javascript" src="js/plugins/layer/layer.js"></script>
    <script type="text/javascript">
    $(function(){
		var layer_idx;
		$(document).ajaxStart(function(){
			layer_idx = layer.open({
					type : 3,
					title: '操作中,请稍候...',
					time: 0,
					shade: 0.5
			});
		}).ajaxComplete(function(){
			layer.close(layer_idx);
		});
	});
    </script>
</head>
