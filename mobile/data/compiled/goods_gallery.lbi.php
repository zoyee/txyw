<div class="swiper-container" data-space-between='10' data-autoplay="3000">
	<div class="swiper-wrapper">
		<?php $_from = $this->_var['pictures']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'picture');$this->_foreach['name'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['name']['total'] > 0):
    foreach ($_from AS $this->_var['picture']):
        $this->_foreach['name']['iteration']++;
?>
		<div class="swiper-slide">
			<img src="<?php if ($this->_var['picture']['img_url']): ?><?php echo $this->_var['site_url']; ?><?php echo $this->_var['picture']['img_url']; ?><?php else: ?><?php echo $this->_var['site_url']; ?><?php echo $this->_var['picture']['thumb_url']; ?><?php endif; ?>" alt="" style='width: 100%'>
		</div>
		<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
	</div>
	<!-- <div class="swiper-pagination"></div> -->
</div>