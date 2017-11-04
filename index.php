<?php
namespace lbx;

include_once 'cgi/auth.inc.php';

include_once 'cgi/utils.inc.php';
include_once 'cgi/dbObjects.class.php';
include_once 'cgi/HTMLroutines.class.php';

include_once 'cgi/indexpage.routines.php';

\LinkBox\Logger::log('Start linkbox app');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
	<?=HTML::favicon();?>

	<title>LinkBox main page</title>
	<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="css/bootstrap-theme.min.css">
	
	<link rel="stylesheet" type="text/css" href="css/FuelUX/fuelux.css">
	<link rel="stylesheet" type="text/css" href="css/FuelUX/fuelux-docs.css">

	<link rel="stylesheet" type="text/css" href="css/typeahead.css">
	<link rel="stylesheet" type="text/css" href="css/offcanvas.css">
	<link rel="stylesheet" type="text/css" href="css/linktable.css">
	<link rel="stylesheet" type="text/css" href="css/linkbox.css">
	
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript" src="js/bootstrap.min.js"></script>
	<script type="text/javascript" src="js/typeahead.bundle.js"></script>
	
	<script type="text/javascript" src="js/FuelX/selectlist.js"></script>
	<script type="text/javascript" src="js/FuelX/dropdown-autoflip.js"></script>
	<script type="text/javascript" src="js/FuelX/pillbox.js"></script>
	
	<script type="text/javascript" src="js/linkbox.js"></script>
<style>

</style>
<script>
$(document).ready(function () {
  $('[data-toggle="offcanvas"]').click(function () {
    $('.row-offcanvas').toggleClass('active');
  });
  
	$('#lbox-SelectFolderList').on('changed.fu.selectlist', function (event,data) {
	//console.log(data.value);
	$('input[name="link_Folder"]').val(data.value);
	});

	$('#myPillbox1').on('added.fu.pillbox', function pillboxAddedNew(evt, item) {
	$('.btn.btn-default.pill').addClass('btn-xs');	
	});	

/*	
			$( "#lbx_form_addlink" ).submit(lbxAddLink_onSubmit( event ) 
			//alert( "Handler for .submit() called." );
			//event.preventDefault();
			);
*/

});
</script>
</head>
<body>
<? include 'tmplt/topmenu.inc.php';?>
<div class="container-fluid">

  <div class="row row-offcanvas row-offcanvas-right">

	<div class="col-sm-9">
	  <p class="pull-right visible-xs">
		<button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle nav</button>
	  </p>

	<div class="row">
	<div class="col-xs-2 lbx_folders_list_col"><!--folders list-->
<? include 'tmplt/ui.foldersmenu.inc.php';?>
	</div><!--/.col-xs-6.col-lg-4-->
	<div class="col-xs-10 lbx_main_col"><!--main table-->
<!-- -------- FORM ADD LINK ----------------------------------------------------------------->		
<? include 'tmplt/ui.form_addlink.inc.php';?>
<? include 'tmplt/errorBlock.inc.php';?>
<!--/ ------- FORM ADD LINK END ------------------------------------------------------------->
<!-- -------- LINKS TABLE begin ------------------------------------------------------------->		
<? include 'tmplt/ui.table_links.inc.php';?>
<!--/ ------- LINKS TABLE end --------------------------------------------------------------->	
	</div><!--/.col-xs-6.col-lg-4-->
	</div><!--/row-->
	</div><!--/.col-xs-12.col-sm-9-->
	<div class="col-sm-3 sidebar-offcanvas" id="sidebar">
<!-- -------------------------------------------------------------- TAGS menu begin  ---------------------------------------------------------------->	
<? include 'tmplt/ui.tagsmenu.inc.php';?>
<!--/ -------------------------------------------------------------- TAGS menu end  ----------------------------------------------------------------->	
	</div><!--/.sidebar-offcanvas-->
  </div><!--/row-->

  <hr>
<!-- -------------------------------------------------------------- FOOTER menu begin  ---------------------------------------------------------------->	
<? include 'tmplt/ui.footer.inc.php';?>
<!--/ -------------------------------------------------------------- FOOTER menu end  ----------------------------------------------------------------->	
</div><!--/.container-->
	<div class='formresult'><iframe name='formresult' width='0' height='0'></iframe></div>
<div class="container">
<div class="row">
<div class="col-sm-12">

</div>
</div>
</div>
<br/>
<div class="container">
<div class="row">
<div class="col-sm-12">

</div>
</div>
</div>

</body>
</html>