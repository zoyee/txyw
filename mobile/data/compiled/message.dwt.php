<!DOCTYPE html>
<html>
<head>
<meta name="Generator" content="ECSHOP v2.7.3" />
<?php echo $this->fetch('library/include_head.lbi'); ?>

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
				<h1 class="title">系统提示</h1>
			</header>
			<div class="content">
				<div class="list-block message_block">
				<?php echo $this->_var['message']['content']; ?>
				</div>
			</div>
		</div>

		<div class="panel-overlay"></div>
		<?php echo $this->fetch('library/user_profile.lbi'); ?>
		<?php echo $this->fetch('library/search_panel.lbi'); ?>
		<?php echo $this->fetch('library/subscribe.lbi'); ?>
		
	</div>
	<?php echo $this->fetch('library/include_footer.lbi'); ?>
</body>
</html>
