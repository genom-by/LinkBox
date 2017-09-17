<?php
//session_start();
//echo $_SERVER['PHP_SELF']; //echo $_SERVER['SCRIPT_FILENAME']; //echo $_SERVER['SCRIPT_NAME'];
$current_page = substr($_SERVER['PHP_SELF'], strrpos($_SERVER['PHP_SELF'], '/') );
echo $current_page; 
?>
<nav id="headerNav" role="navigation" class="navbar navbar-default navbar-fixed-top">
	<!-- Brand and toggle get grouped for better mobile display -->
	<div class="navbar-header">
		<button type="button" data-target="#navbarCollapse" data-toggle="collapse" class="navbar-toggle">
		<span class="sr-only">Toggle navigation</span>
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
		</button>
		<a href="/tt/atwebpages/LinkBox_bs/" class="navbar-brand">LinkBox2</a>
	</div>
	<!-- Collection of nav links and other content for toggling -->
	<div id="navbarCollapse" class="collapse navbar-collapse">
		<ul class="nav navbar-nav">
			<li class="active"><a href="#">Import</a></li>
			<li class="dropdown">
<a data-toggle="dropdown" class="dropdown-toggle" href="#">Settings <b class="caret"></b></a>
				<ul role="menu" class="dropdown-menu">
					<li><a href="#">Account</a></li>
					<li><a href="#">Export</a></li>
					<li><a href="#">Import</a></li>
					<li class="divider"></li>
					<li><a href="#">Trash</a></li>
				</ul>
			</li>
		</ul>
		<ul class="nav navbar-nav navbar-right">
			<li><a href="?action=logout">Logout</a></li>
		<?php if ($current_page == '/register.php'){?>
			<li><a href="auth.php">Login</a></li>
		<?php }else if ($current_page == '/index.php'){?>
			<li><a href="auth.php">Login</a></li>
			<li><a href="register.php">Register</a></li>
		<?php }?>
		</ul>
	</div>
</nav>