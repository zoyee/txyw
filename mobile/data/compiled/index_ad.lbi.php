<div class="swiper-container" data-space-between='10' data-autoplay="3000">
	<div class="swiper-wrapper">
		<?php $_from = $this->_var['wap_index_ad']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'ad');$this->_foreach['wap_index_ad'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['wap_index_ad']['total'] > 0):
    foreach ($_from AS $this->_var['ad']):
        $this->_foreach['wap_index_ad']['iteration']++;
?>
		<div class="swiper-slide">
			<a href="<?php echo $this->_var['ad']['url']; ?>">
			<img src="<?php echo $this->_var['ad']['ad_code']; ?>" alt="" style='width: 100%'>
			</a>
		</div>
		<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
	</div>
	<div class="swiper-pagination"></div>
</div>
