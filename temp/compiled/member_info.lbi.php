<div id="append_parent"></div>
<?php if ($this->_var['user_info']): ?>
<font style="position:relative; top:10px;">
<?php echo $this->_var['lang']['hello']; ?>，
<?php if ($_COOKIE['ECS']['nickname']): ?>
<?php if ($_COOKIE['ECS']['qq_figureurl']): ?><img src="<?php echo $_COOKIE['ECS']['qq_figureurl']; ?>" /><?php endif; ?>
<font class="f4_b"><?php echo htmlspecialchars($_COOKIE['ECS']['nickname']); ?></font>
<?php else: ?>
<font class="f4_b"><?php echo $this->_var['user_info']['username']; ?></font>，<?php endif; ?><?php echo $this->_var['lang']['welcome_return']; ?>！
<a href="user.php"><?php echo $this->_var['lang']['user_center']; ?></a>|
 <a href="user.php?act=logout"><?php echo $this->_var['lang']['user_logout']; ?></a>
</font>

<?php else: ?>
 
 <!-- <a href="user.php"><img src="themes/miqinew/images/bnt_log.gif" /></a>
 <a href="user.php?act=register"><img src="themes/miqinew/images/bnt_reg.gif" /></a>
 <a href="api/qqconnect/interface.php"><img src="api/qqconnect/qq_login_120_24.png" /></a> -->
 <li><a href="user.php">登录</a></li>
 <li><a href="user.php?act=register">免费注册</a></li>
 <li><a href="user.php?act=oath&type=qq">使用QQ登录</a></li>
 <li><?php echo $this->_var['lang']['welcome']; ?></li>
<?php endif; ?>
