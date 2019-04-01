<nav class="navbar navbar-inverse navbar-fixed-top">
	<div class="container-fluid">
		<div class="navbar-header">
			<a class="navbar-brand" href="<?php echo base_url(); ?>" style="font-size:20px;color:white;"><?php echo HILTON_NAME; ?>管理后台</a>
		</div>
		<div id="navbar" class="navbar-collapse collapse">
			<ul class="nav navbar-nav navbar-right">
				<li><a>欢迎你，<?php echo $real_name; ?></a></li>
				<li><a href="<?php echo base_url('changepwd'); ?>">修改密码</a></li>
				<li><a href="<?php echo base_url('logout'); ?>">退出</a></li>
			</ul>
		</div>
	</div>
</nav>