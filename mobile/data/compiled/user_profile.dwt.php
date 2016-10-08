<!DOCTYPE html>
<html>
<head>
<meta name="Generator" content="ECSHOP v2.7.3" />
<?php echo $this->fetch('library/include_head.lbi'); ?>
<link rel="stylesheet" href="<?php echo $this->_var['ectouch_themes']; ?>/css/user_profile.css">

</head>

<body>
	<div class="page-group">
		<div id="page-index" class="page">
			<header class="bar bar-nav">
				<?php if ($this->_var['login_user']): ?>
				<a class="icon pull-left open-panel" data-panel=".panel-uprofile">
					<img class="login_user_head" src="<?php echo $this->_var['login_user']['headimgurl']; ?>" />
				</a>
				<?php else: ?>
				<a class="icon icon-me pull-left open-panel" data-panel=".panel-subscribe"></a>
				<?php endif; ?>
				<a class="button button-link button-nav pull-right open-popup"
					data-popup=".popup-search"> <span class="icon icon-search"></span>
				</a>
				<h1 class="title">个人信息</h1>
			</header>
			<div class="content">
				<form action="user.php?act=act_edit_profile" method="POST">
				<img src="themes/sui/images/spring.jpg" class="user_profile_bg"/>
				<div class="text-center">
					<img class="user_profile_head" src="<?php echo $this->_var['login_user']['headimgurl']; ?>" />
				</div>
								
				 <div class="list-block">
				    <ul>
				      
				      <li>
				        <div class="item-content">
				          <div class="item-media"><i class="icon icon-form-name"></i></div>
				          <div class="item-inner">
				            <div class="item-title label">昵称</div>
				            <div class="item-input">
				              <?php echo $this->_var['profile']['nickname']; ?>
				            </div>
				          </div>
				        </div>
				      </li>
				      <li>
				        <div class="item-content">
				          <div class="item-media"><i class="icon icon-form-name"></i></div>
				          <div class="item-inner">
				            <div class="item-title label">账号</div>
				            <div class="item-input">
				              <?php echo $this->_var['profile']['user_name']; ?>
				            </div>
				          </div>
				        </div>
				      </li>
				      <li>
				        <div class="item-content">
				          <div class="item-media"><i class="icon icon-form-email"></i></div>
				          <div class="item-inner">
				            <div class="item-title label">邮箱</div>
				            <div class="item-input">
				              <input type="email" name="email" placeholder="E-mail" value="<?php echo $this->_var['profile']['email']; ?>">
				            </div>
				          </div>
				        </div>
				      </li>
				      <li>
				        <div class="item-content">
				          <div class="item-media"><i class="icon icon-form-gender"></i></div>
				          <div class="item-inner">
				            <div class="item-title label">性别</div>
				            <div class="item-input">
				              <select name="sex">
				                <option value="0">保密</option>
				                <option <?php if ($this->_var['profile']['sex'] == '1'): ?>selected<?php endif; ?> value="1">男</option>
				                <option <?php if ($this->_var['profile']['sex'] == '2'): ?>selected<?php endif; ?> value="2">女</option>
				              </select>
				            </div>
				          </div>
				        </div>
				      </li>
				      
				      <li>
				        <div class="item-content">
				          <div class="item-media"><i class="icon icon-form-calendar"></i></div>
				          <div class="item-inner">
				            <div class="item-title label">生日</div>
				            <div class="item-input">
				              <input type="date" name="birthday" placeholder="生日" value="<?php echo $this->_var['profile']['birthday']; ?>" id="birthday">
				            </div>
				          </div>
				        </div>
				      </li>
				      <li>
				        <div class="item-content">
				          <div class="item-media"><i class="icon icon-form-email"></i></div>
				          <div class="item-inner">
				            <div class="item-title label">手机号</div>
				            <div class="item-input">
				              <input type="text" name="mobile" placeholder="11位手机号码" value="<?php echo $this->_var['profile']['mobile_phone']; ?>">
				            </div>
				          </div>
				        </div>
				      </li>
				      <li>
				        <div class="item-content">
				          <div class="item-media"><i class="icon icon-form-email"></i></div>
				          <div class="item-inner">
				            <div class="item-title label">微信号</div>
				            <div class="item-input">
				              <input type="text" name="weixin" placeholder="输入微信号方便掌柜主动联系您" value="<?php echo $this->_var['profile']['wxh']; ?>" required="required">
				            </div>
				          </div>
				        </div>
				      </li>
				    </ul>
				  </div>
				  <div class="content-block">
				  	<input type="hidden" name="user_id" value="<?php echo $this->_var['profile']['user_id']; ?>"/>
				  	<input type="hidden" name="username" value="<?php echo $this->_var['profile']['user_name']; ?>"/>
				    <button type="submit" style="width:100%" class="button button-big button-fill button-success">提交</button>
				  </div>
				  </form> 
			</div>
		</div>

		<div class="panel-overlay"></div>
		<?php echo $this->fetch('library/user_profile.lbi'); ?>
		<?php echo $this->fetch('library/search_panel.lbi'); ?>
		<?php echo $this->fetch('library/subscribe.lbi'); ?>
		
	</div>
	<?php echo $this->fetch('library/include_footer.lbi'); ?>
	<script src="<?php echo $this->_var['ectouch_themes']; ?>/js/user_profile.js"></script>
</body>
</html>
