<?php
namespace lbx;

include_once 'cgi/auth.inc.php';

include_once 'cgi/utils.inc.php';
include_once 'cgi/dbObjects.class.php';
include_once 'cgi/HTMLroutines.class.php';

?>
<script type="text/javascript" src="js/linksearch.js"></script>
<div id="table-header">
<span class="lbox-orderheader">Order by: </span>
	<ul class="lbox-orderby">
	<li><a href="#">name</a></li><li><a href="#">date</a></li><li><a href="#">last visited</a></li>
	</ul>
<span class="lbox-searchheader">
<input class="form-control" placeholder="Search" name="table-search" id="inp-tblsearch" type="text" onkeypress="inp_linksearch_onkeypress(event)">
</span>
<button name="search" id="btn-tablesearch" onClick='btn_linkSearchClick(event);'><span class="glyphicon glyphicon-search"></span></button>
</div>
<table class="table table-striped table-condensed" id="lbx_LinksTable">
<?=HTML::getTableItems('linkMainPage','all');?>
</table>