<?php
//session_start();
//echo $_SERVER['PHP_SELF']; //echo $_SERVER['SCRIPT_FILENAME']; //echo $_SERVER['SCRIPT_NAME'];
$current_page = substr($_SERVER['PHP_SELF'], strrpos($_SERVER['PHP_SELF'], '/') );
echo $current_page; 
?>
  <footer>
	<p>&copy; Company 2014</p>
  </footer>