<?php
namespace lbx;

include_once 'cgi/auth.inc.php';
include_once 'cgi/app.class.php';

?>
<style>
.menu_or{
	margin-left:-20px;
	margin-right:-20px;
	font-style:italic;
}
div.brand_active a{
	background-color: #e7e7e7;
	height:60px;
}
</style>
<nav role="navigation" class="navbar navbar-default">
<!-- Brand and toggle get grouped for better mobile display -->
<div class="navbar-header">
<button type="button" data-target="#navbarCollapse" data-toggle="collapse" class="navbar-toggle">
		<span class="sr-only">Toggle navigation</span>
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
	</button>
	<div <?php if(App::currentPage()== 'linkbox') echo "class='brand_active'";?>><a href="<?=App::link('linkbox');?>" class="navbar-brand">Linkbox</a></div>
</div>
<!-- Collection of nav links and other content for toggling -->
<div id="navbarCollapse" class="collapse navbar-collapse">
	<ul class="nav navbar-nav">
		<?= HTML::getTopMenuItems();?>
	</ul>
	<ul class="nav navbar-nav navbar-right">
	<?if(Auth::notLogged()){?>
		<li><a href="<?=App::link('login'); ?>">Login</a></li>
		<li><a class='menu_or'>or</a></li>
		<li><a href="<?=App::link('register'); ?>">Register</a></li>
	<?}else {?>
		<li class="dropdown">
    <a data-toggle="dropdown" class="dropdown-toggle" href="#">Logged as <? echo $_SESSION['user_name'];?> <b class="caret"></b></a>
			<ul role="menu" class="dropdown-menu">
				<li><a href="#">Inbox</a></li>
				<li><a href="#">Drafts</a></li>
				<li><a href="#">Sent Items</a></li>
				<li class="divider"></li>
				<li><a href="<?= App::link('logout');?>">Logout</a></li>
			</ul>
		</li>
	<?}?>
	</ul>
</div>
</nav>