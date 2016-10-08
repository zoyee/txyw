<!DOCTYPE html>
<html>
<head>
<meta name="Generator" content="ECSHOP v2.7.3" />
<?php echo $this->fetch('library/include_head.lbi'); ?>
<link rel="stylesheet" href="<?php echo $this->_var['ectouch_themes']; ?>/css/goods.css">

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
				<h1 class="title"><?php echo $this->_var['goods']['goods_name']; ?></h1>
			</header>
			<div class="content">
				<?php echo $this->fetch('library/goods_gallery.lbi'); ?>
				
				<div class="goods_info">
					<div class="goods_name">
						<?php echo $this->_var['goods']['goods_name']; ?>
					</div>
					<div class="goods_price">
						<span><?php echo $this->_var['goods']['shop_price_formated']; ?></span>
					</div>
					<div class="content-block goods_guide">
						<span class="mark">“</span><br/><?php echo $this->_var['goods']['goods_brief']; ?>
					</div>
				</div>
				
				  <div class="buttons-tab">
				    <a href="#tab1" class="tab-link active button">细节描述</a>
				    <a href="#tab2" class="tab-link button">规格参数</a>
				    <a href="#tab3" class="tab-link button">最新评价</a>
				  </div>
				  
				  <div class="content-block no-padding no-margin">
				    <div class="tabs">
				      <div id="tab1" class="tab active">
				        <div class="goods_desc">
				          <?php echo $this->_var['goods']['goods_desc']; ?>
				        </div>
				      </div>
				      <div id="tab2" class="tab">
				        <div class="list-block">
						    <ul>
						    	<?php $_from = $this->_var['properties']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('prop_key', 'property');$this->_foreach['specification'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['specification']['total'] > 0):
    foreach ($_from AS $this->_var['prop_key'] => $this->_var['property']):
        $this->_foreach['specification']['iteration']++;
?>
						    	<?php if ($this->_var['prop_key'] == '商品属性'): ?>
						    	<?php $_from = $this->_var['property']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'property_entity');if (count($_from)):
    foreach ($_from AS $this->_var['property_entity']):
?>
						      <li class="item-content">
						        <div class="item-inner">
						          <div class="item-title label"><?php echo $this->_var['property_entity']['name']; ?></div>
						          <?php echo $this->_var['property_entity']['value']; ?>
						        </div>
						      </li>
						      <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
						      <?php endif; ?>
						      <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
						    </ul>
						  </div>
				      </div>
				      <div id="tab3" class="tab">
				        <div class="list-block media-list">
					      <ul>
					        <li>
					          <div class="item-content">
					            <div class="item-media"><img width="80" src="http://gqianniu.alicdn.com/bao/uploaded/i4//tfscom/i3/TB10LfcHFXXXXXKXpXXXXXXXXXX_!!0-item_pic.jpg_250x250q60.jpg"></div>
					            <div class="item-inner">
					              <div class="item-title-row">
					                <div class="item-title">猫小贱</div>
					                <div class="item-after">2016-09-12</div>
					              </div>
					              <div class="item-text">好吃，包装也漂亮好吃，包装也漂亮好吃，包装也漂亮好吃，包装也漂亮好吃，包装也漂亮好吃，包装也漂亮好吃，包装也漂亮</div>
					            </div>
					          </div>
					        </li>
					
					        <li>
					          <div class="item-content">
					            <div class="item-media"><img width="80" src="http://gqianniu.alicdn.com/bao/uploaded/i4//tfscom/i3/TB10LfcHFXXXXXKXpXXXXXXXXXX_!!0-item_pic.jpg_250x250q60.jpg"></div>
					            <div class="item-inner">
					              <div class="item-title-row">
					                <div class="item-title">猫小贱</div>
					                <div class="item-after">2016-09-12</div>
					              </div>
					              <div class="item-text">好吃，包装也漂亮</div>
					            </div>
					          </div>
					        </li>
					        <li>
					          <div class="item-content">
					            <div class="item-media"><img width="80" src="http://gqianniu.alicdn.com/bao/uploaded/i4//tfscom/i3/TB10LfcHFXXXXXKXpXXXXXXXXXX_!!0-item_pic.jpg_250x250q60.jpg"></div>
					            <div class="item-inner">
					              <div class="item-title-row">
					                <div class="item-title">猫小贱</div>
					                <div class="item-after">2016-09-12</div>
					              </div>
					              <div class="item-text">好吃，包装也漂亮</div>
					            </div>
					          </div>
					        </li>
					      </ul>
					    </div>
				      </div>
				    </div>
				  </div>
			</div>
		</div>

		<div class="panel-overlay"></div>
		<?php echo $this->fetch('library/user_profile.lbi'); ?>
		<?php echo $this->fetch('library/search_panel.lbi'); ?>
		<?php echo $this->fetch('library/supplier_info.lbi'); ?>
		<?php echo $this->fetch('library/subscribe.lbi'); ?>
		
		
		<a class="supplier_head open-popup" href="#" data-popup=".popup-supplier">
			<img src="<?php echo $this->_var['site_url']; ?><?php echo $this->_var['supplier']['head_img']; ?>"/>
		</a>
	</div>
	<?php echo $this->fetch('library/include_footer.lbi'); ?>
	<script src="<?php echo $this->_var['ectouch_themes']; ?>/js/goods.js"></script>
</body>
</html>
