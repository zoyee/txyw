<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    

    <title>系统提示-青山老农</title>
    <meta name="keywords" content="">
    <meta name="description" content="">

    <link rel="shortcut icon" href="favicon.ico"> <link href="css/bootstrap.min.css?v=3.3.5" rel="stylesheet">
    <link href="css/font-awesome.min.css?v=4.4.0" rel="stylesheet">

    <link href="css/animate.min.css" rel="stylesheet">
    <link href="css/style.min.css?v=4.0.0" rel="stylesheet"><base target="_blank">

</head>

<body class="gray-bg">

    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-sm-12">
                <div class="middle-box text-center animated fadeInDown">
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
                        <br/>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="js/jquery.min.js?v=2.1.4"></script>
    <script src="js/bootstrap.min.js?v=3.3.5"></script>
    
</body>
</html>

<?php if ($this->_var['auto_redirect']): ?>
<script language="JavaScript">
<!--
var seconds = 3;
var defaultUrl = "<?php echo $this->_var['default_url']; ?>";


onload = function()
{
  if (defaultUrl == 'javascript:history.go(-1)' && window.history.length == 0)
  {
    document.getElementById('redirectionMsg').innerHTML = '';
    return;
  }

  window.setInterval(redirection, 1000);
}
function redirection()
{
  if (seconds <= 0)
  {
    window.clearInterval();
    return;
  }

  seconds --;
  document.getElementById('spanSeconds').innerHTML = seconds;

  if (seconds == 0)
  {
    window.clearInterval();
    location.href = defaultUrl;
  }
}
//-->
</script>

<?php endif; ?>