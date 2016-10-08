		<div class="panel panel-left panel-reveal panel-uprofile">
			<img class="bg" src="themes/sui/images/up_bg.jpg"/>
		  <div class="content-block">
		  	<div class="list-block media-list">
				<div class="item-content user_profile">
					<div class="item-media">
						<img src="<?php echo $this->_var['login_user']['headimgurl']; ?>">
					</div>
					<div class="item-inner">
						<div class="item-title-row">
							<div class="item-title"><?php echo $this->_var['login_user']['nickname']; ?></div>
							<div class="item-after">
								<a class="open-popup" data-panel=".popup-subscribe"><i class="icon icon-code"></i></a>
							</div>
						</div>
						<div class="item-subtitle"><?php echo $this->_var['login_user']['province']; ?><?php if ($this->_var['login_user']['city']): ?>-<?php echo $this->_var['login_user']['city']; ?><?php endif; ?></div>
					</div>
				</div>
			</div>
	      	<div class="list-block">
			    <ul>
			      <li class="item-content">
			        <div class="item-media"><i class="icon icon-card"></i></div>
			        <div class="item-inner">
			          <div class="item-title">
			          	<a class="close-panel" href="user.php?act=profile" >我的信息</a>
			          </div>
			        </div>
			      </li>
			      <li class="item-content">
			        <div class="item-media"><i class="icon icon-menu"></i></div>
			        <div class="item-inner">
			          <div class="item-title">我的订单</div>
			        </div>
			      </li>
			      <li class="item-content">
			        <div class="item-media"><i class="icon icon-star"></i></div>
			        <div class="item-inner">
			          <div class="item-title">我的收藏</div>
			        </div>
			      </li>
			      <li class="item-content">
			        <div class="item-media"><i class="icon icon-me"></i></div>
			        <div class="item-inner">
			          <div class="item-title">收货地址</div>
			        </div>
			      </li>
			      <li class="item-content">
			        <div class="item-media"><i class="icon icon-edit"></i></div>
			        <div class="item-inner">
			          <div class="item-title">我的评论</div>
			        </div>
			      </li>
			    </ul>
			  </div>
  
		    
		    <p><a href="#" class="button button-dark">夜间模式</a></p>
		  </div>
		</div>