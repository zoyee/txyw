<!DOCTYPE html>
<html>

<?php echo $this->fetch('head.htm'); ?>
<script type="text/javascript">

</script>
<body class="gray-bg">

    <div class="wrapper wrapper-content animated fadeInUp">
        <div class="row">
            <div class="col-sm-12">

                <div class="ibox">
                    <div class="ibox-title">
                        <h5>所有管理员</h5>
                        <div class="ibox-tools">
                            <a href="privilege.php?act=add" class="btn btn-primary btn-xs" target="tab"><i class="fa fa-plus-square"></i> 创建管理员</a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        

                        <div class="project-list">
							<!-- 全查的表格数据，前台分页请添加dataTable样式 -->
                            <table class="table table-hover dataTable">
                            	<thead>
                            		<tr>
                            			<th sort-by="user_name">用户名</th>
                            			<th class="visible-lg" sort-by="email">邮箱</th>
                            			<th class="visible-lg" sort-by="add_time">时间</th>
                            			<th></th>
                            		</tr>
                            	</thead>
                                <tbody>
                                	<?php $_from = $this->_var['admin_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'list');if (count($_from)):
    foreach ($_from AS $this->_var['list']):
?>
                                    <tr>
                                        <td class="project-status">
                                            <a><?php echo $this->_var['list']['user_name']; ?></a>
                                        </td>
                                        <td class="project-title visible-lg">
                                            <?php echo $this->_var['list']['email']; ?>
                                        </td>
                                        <td class="project-completion visible-lg">
                                           	 创建时间：<?php echo $this->_var['list']['add_time']; ?><br/>
                                           	 最后登录：<?php echo empty($this->_var['list']['last_login']) ? 'N/A' : $this->_var['list']['last_login']; ?>
                                        </td>
                                        <td class="project-actions">
                                            <a href="privilege.php?act=allot&id=<?php echo $this->_var['list']['user_id']; ?>&user=<?php echo $this->_var['list']['user_name']; ?>" class="btn btn-success btn-sm" target="tab"><i class="fa fa-cogs"></i> 权限 </a>
                                            <a href="admin_logs.php?act=list&id=<?php echo $this->_var['list']['user_id']; ?>" class="btn btn-info btn-sm"  target="tab"><i class="fa fa-calendar-check-o"></i> 日志 </a>
                                            <a href="privilege.php?act=edit&id=<?php echo $this->_var['list']['user_id']; ?>" class="btn btn-primary btn-sm" target="tab"><i class="fa fa-edit"></i> 编辑 </a>
                                            <a href="privilege.php?act=remove&id=<?php echo $this->_var['list']['user_id']; ?>" class="btn btn-warning btn-sm btn-del"><i class="fa fa-remove"></i> 删除 </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>