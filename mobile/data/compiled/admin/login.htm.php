<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">

    <title><?php echo $this->_var['_CFG']['shop_name']; ?> - 登录</title>
    <meta name="keywords" content="青山老农，植物素生活，社群电商">
    <meta name="description" content="青山老农，植物素生活，社群电商">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css?v=4.4.0" rel="stylesheet">
    <link href="css/animate.min.css" rel="stylesheet">
    <link href="css/style.min.css" rel="stylesheet">
    <link href="css/login.min.css" rel="stylesheet">
    <!--[if lt IE 8]>
    <meta http-equiv="refresh" content="0;ie.html" />
    <![endif]-->
    <script>
        if(window.top!==window.self){window.top.location=window.location};
    </script>

</head>

<body class="signin">
    <div class="signinpanel">
        <div class="row">
            <div class="col-sm-7">
                <div class="signin-info">
                    <div class="logopanel m-b">
                        <h1><?php echo $this->_var['_CFG']['shop_name']; ?></h1>
                    </div>
                    <div class="m-b"></div>
                    <h4>欢迎使用 <strong><?php echo $this->_var['_CFG']['shop_name']; ?>自由商城后台管理系统</strong></h4>
                    <ul class="m-b">
                        <li><i class="fa fa-arrow-circle-o-right m-r-xs"></i> 智业</li>
                        <li><i class="fa fa-arrow-circle-o-right m-r-xs"></i> 电商</li>
                        <li><i class="fa fa-arrow-circle-o-right m-r-xs"></i> 资本</li>
                    </ul>
                    <strong>还没有账号？ <a href="#">立即注册&raquo;</a></strong>
                </div>
            </div>
            <div class="col-sm-5">
                <form method="post" action="privilege.php?act=signin">
                    <h4 class="no-margins">登录：</h4>
                    <p class="m-t-md">登录到自由商城后台管理系统</p>
                    <input type="text" name="username" class="form-control uname" placeholder="用户名" />
                    <input type="password" name="password" class="form-control pword m-b" placeholder="密码" />
                    <a href="">忘记密码了？</a>
                    <button class="btn btn-success btn-block">登录</button>
                </form>
            </div>
        </div>
        <div class="signup-footer">
            <div class="pull-left">
                &copy; 2016 <?php echo $this->_var['_CFG']['shop_name']; ?>版权所有
            </div>
        </div>
    </div>
</body>

</html>