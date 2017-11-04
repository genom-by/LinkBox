<?php
namespace lbx;

include_once 'cgi/auth.inc.php';

include_once 'cgi/utils.inc.php';
include_once 'cgi/dbObjects.class.php';
include_once 'cgi/HTMLroutines.class.php';
?>
<form class="well form-inline lbox-thinform" method="post" action="cgi/post.routines.php" name="lbx_form_addlink" id="lbx_form_addlink" target='formresult'>
<!--select folder list FuelUX-->
<div class='lbxForm_firstLine'>
	<span class='btn_folder_span'><div class="btn-group selectlist" data-resize="aut_" data-initialize="selectlist" id="lbox-SelectFolderList">
	<button class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" type="button" title="Folder">
	<span class="selected-label"></span>
	<span class="selected-value"></span>
	<span class="caret"></span>
	<span class="sr-only">Toggle Dropdown</span>
	</button>
	<?=HTML::getSelectULList('folderGroupped');?>
	<input class="hidden hidden-field" name="link_Folder" readonly="readonly" aria-hidden="true" type="text" value='-1'/>
	</div></span>
	<span><input type="text" name="link_to_save" id="link_to_save" class="form-control input-sm" placeholder="Link to save"></span>
</div>
<div class='lbxForm_secondLine'>	
<!-- / select folder list FuelUX-->
<!-- tags panel
<button class="btn btn-default btn-xs" title="Tags" type="button" data-target="#toggleTagsPanel" data-toggle="collapse" aria-expanded="false">Tags <span class="caret"></span></button>
-->
<div id="toggleTagsPanel" class="collapse in" aria-expanded="true" style="">
<!-- pillBox tags items-->
<div class="fuelux">
<div id="myPillbox1" data-initialize="pillbox" class="pillbox pills-editable">
<ul class="clearfix pill-group">
	<li class="pillbox-input-wrap btn-group lbox-pill-addmore">
	<a class="pillbox-more">and <span class="pillbox-more-count"></span> more...</a>
	<input class="form-control dropdown-toggle pillbox-add-item" placeholder="add tag(s) with comma" type="text">
	<button type="button" class="dropdown-toggle sr-only">
		<span class="caret"></span>
		<span class="sr-only">Toggle Dropdown</span>
	</button>
	<ul class="suggest dropdown-menu" role="menu" data-toggle="dropdown" data-flip="auto"></ul>
	</li>
</ul>
</div>
<input type="hidden" name="lbx_tagsSelected" id="lbx_tagsSelected" value=""/>
<script>
$('#myPillbox1').pillbox({acceptKeyCodes: [13,188,190]});
</script>
</div> <!--fuelux wrapper-->
</div> <!-- / tag-->
</div><!--second line-->
<div class='lbxForm_thirdLine'>
	<span class='span_inp_desc'><input type="text" name="link_description" id="link_description" class="form-control input-sm" placeholder="Name / Description"></span>
	<span class='span_btn_add'><button type="submit" id="btn_addLink" class="btn btn-info"><span class="glyphicon glyphicon-star"></span><span id="btn_addLinkCaption"> Add link </span></button></span>
</div><!-- / third line-->
	<div class="clearfix"></div>
	<div id="lbx_formErrors" class="alert alert-danger" hidden="true">
	<a class="close" href="#" onclick="$('#lbx_formErrors').prop('hidden', true);">x</a>
	<p></p>
	</div>
	<input type="hidden" name="id" value="-2">
	<input type="hidden" name="action" value="create">
	<input type="hidden" name="createType" value="linkMP">
	</form>
	<!--
		<ul class="dropdown-menu" role="menu">
		<li data-value="0" class="disabled"><a href="#">Select folder </a></li>
		<li class="divider"></li>
		<li data-value="1"><a href="#">Pictures</a></li>
		<li data-value="2"><a href="#">Documents</a></li>
		<li data-value="3"><a href="#">Music</a></li>
		<li data-value="4"><a href="#"><span class="glyphicon glyphicon-film"></span> Videos</a></li>
	</ul>
	-->