<!DOCTYPE html>
<html>
<head>
<meta name="Generator" content="ECSHOP v2.7.3" />
<?php echo $this->fetch('library/include_head.lbi'); ?>
<link rel="stylesheet" href="<?php echo $this->_var['ectouch_themes']; ?>/css/index.css">

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
				<a class="icon icon-me pull-left open-popup" data-panel=".popup-subscribe"></a>
				<?php endif; ?>
				
				<a class="button button-link button-nav pull-right open-popup"
					data-popup=".popup-search"> <span class="icon icon-search"></span>
				</a>
				<h1 class="title"><?php echo $this->_var['shop_name']; ?></h1>
			</header>
			<div class="content infinite-scroll infinite-scroll-bottom"
				data-distance="100" data-url="category.php?id=191&ajax_more=1" 
				data-number="6" data-template="goods_card.lbi">
				<?php echo $this->fetch('library/index_ad.lbi'); ?>
				<?php echo $this->fetch('library/index_icon.lbi'); ?>

				<div class="list-block">
					<div class="list-container">
					</div>
				</div>
				
				<div class="infinite-scroll-preloader">
					<div class="preloader"></div>
				</div>
			</div>
		</div>

		<div class="panel-overlay"></div>
		<?php echo $this->fetch('library/user_profile.lbi'); ?>
		<?php echo $this->fetch('library/search_panel.lbi'); ?>
		<?php echo $this->fetch('library/subscribe.lbi'); ?>

	</div>
	<?php echo $this->fetch('library/include_footer.lbi'); ?>

	<script src="<?php echo $this->_var['ectouch_themes']; ?>/js/index.js"></script>
	<script src="<?php echo $this->_var['ectouch_themes']; ?>/js/echarts.min.js"></script>
	<script src="<?php echo $this->_var['ectouch_themes']; ?>/js/china.js"></script>
	<script src="<?php echo $this->_var['ectouch_themes']; ?>/js/search.js"></script>
</body>
</html>
