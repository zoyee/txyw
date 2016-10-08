<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="Generator" content="ECSHOP v2.7.3" />
<meta property="qc:admins" content="11513552070745662101446375" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="<?php echo $this->_var['keywords']; ?>" />
<meta name="description" content="<?php echo $this->_var['description']; ?>" />
<meta itemprop="name" content="<?php echo $this->_var['page_title']; ?>"/> 
<meta itemprop="description" content="这是要分享的内容植物素生活" />


<title><?php echo $this->_var['page_title']; ?></title>



<link rel="shortcut icon" href="favicon.ico" />
<link rel="icon" href="animated_favicon.gif" type="image/gif" />
<link href="<?php echo $this->_var['ecs_css_path']; ?>" rel="stylesheet" type="text/css" />
<link rel="alternate" type="application/rss+xml" title="RSS|<?php echo $this->_var['page_title']; ?>" href="<?php echo $this->_var['feed_url']; ?>" />

<?php echo $this->smarty_insert_scripts(array('files'=>'common.js,index.js')); ?>

</head>
<body>
<?php echo $this->fetch('library/page_header_index.lbi'); ?>
<div class="block">
  <div class="content">
    <div class="contentBody">
      <div class="mainBody"> <?php echo $this->fetch('library/category_tree_index.lbi'); ?>
        <div class="right_banner"> <?php echo $this->fetch('library/index_ad.lbi'); ?>
         <?php echo $this->fetch('library/index_ad_r.lbi'); ?> </div>
        <div class="right_banner"> <?php echo $this->fetch('library/index_ad_b.lbi'); ?> </div>
      </div>
     
      <div class="mainBody"> <?php echo $this->fetch('library/group_buy.lbi'); ?>
        <div class="title_list">
          <div class="title_txt"> <img src="themes/miqinew/images/title_txt02.jpg" border="0"> </div>
        </div>
        <?php echo $this->fetch('library/recommend_hot.lbi'); ?>
        <div class="title_body ">
          <dl class="hot_list02">
            <?php echo $this->fetch('library/recommend_promotion.lbi'); ?>
             <?php echo $this->fetch('library/recommend_new.lbi'); ?>
              <?php echo $this->fetch('library/recommend_best.lbi'); ?>
          </dl>
        </div>
      </div>
      <div class="blank"></div>
      

      <div class="blank" style="height:0"></div>
      

      <div class="blank" style="height:0"></div>
      

      <div class="blank" style="height:0"></div>
      <Div class="title_more03"> 
 </Div>
      <div class="blank" style="height:0"></div>
      

      <div class="blank" style="height:0"></div>
      
<?php echo $this->fetch('library/top10.lbi'); ?>


      
    </div>
  </div>
</div>
<div class="blank5"></div>
<div class="footer">
<div class="footerBody">
<Div class="block">
<?php echo $this->fetch('library/page_footer.lbi'); ?> 
</Div>
</div>
</div>
<script type="text/javascript">

<?php $_from = $this->_var['lang']['compare_js']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
?>
<?php if ($this->_var['key'] != 'button_compare'): ?>
var <?php echo $this->_var['key']; ?> = "<?php echo $this->_var['item']; ?>";
<?php else: ?>
var button_compare = '';
<?php endif; ?>
<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
var compare_no_goods = "<?php echo $this->_var['lang']['compare_no_goods']; ?>";
var btn_buy = "<?php echo $this->_var['lang']['btn_buy']; ?>";
var is_cancel = "<?php echo $this->_var['lang']['is_cancel']; ?>";
var select_spe = "<?php echo $this->_var['lang']['select_spe']; ?>";
var url = "<?php echo $this->_var['url']; ?>";
</script>

<script type="text/javascript" >
var jiathis_config={
	siteNum:5,
	sm:"cqq,qzone,weixin,tqq,tsina",
	url:window.document.location.href+"?u=<?php echo $this->_var['user_id']; ?>",
	pic:window.location.origin+"/themes/miqinew/images/logo.gif",
	showClose:false,
	shortUrl:false,
	hideMore:false
}
</script>
<script type="text/javascript" src="http://v3.jiathis.com/code_mini/jiathis_r.js?btn=r.gif&move=1" charset="utf-8"></script>

</body>
</html>
