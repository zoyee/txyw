<div class="popup popup-supplier">
	<header class="bar bar-nav">
		<a class="button button-link button-nav pull-right close-popup">
			关闭 </a>
		<h1 class="title">掌柜介绍</h1>
	</header>
	<div class="content">
		<div class="list-block media-list">
			<div class="item-content">
				<div class="item-media">
					<img width="120" src="<?php echo $this->_var['site_url']; ?><?php echo $this->_var['supplier']['head_img']; ?>">
				</div>
				<div class="item-inner">
					<div class="item-title-row">
						<div class="item-title"><?php echo $this->_var['supplier']['suppliers_name']; ?></div>
					</div>
					<div class="item-subtitle">微信号：<?php echo $this->_var['supplier']['weixin']; ?></div>
					<div class="item-text"><?php echo $this->_var['supplier']['city_name']; ?><?php echo $this->_var['supplier']['district_name']; ?></div>
				</div>
			</div>
		</div>

		<div class="content-block no-margin"><?php echo $this->_var['supplier']['suppliers_desc']; ?></div>
	</div>
</div>