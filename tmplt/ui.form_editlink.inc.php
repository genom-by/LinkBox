<?php
namespace lbx;

include_once 'cgi/auth.inc.php';

include_once 'cgi/utils.inc.php';
include_once 'cgi/dbObjects.class.php';
include_once 'cgi/settings.class.php';
include_once 'cgi/HTMLroutines.class.php';

include_once 'cgi/indexpage.routines.php';
?>
<div id='form_to_clone' class='hidden'>
<form class="well form-inline lbox-thinform" method="post" name="lbx_form_editlink" id="lbx_form_editlink">
<!--select folder list FuelUX-->
<div class='lbxForm_firstLine'>
<div class='clearfix'>
<div class='btneditparent'>
	<span class='fld_edit'><label>Current folder: </label>
		<span class='currentFLD'></span>
	</span>
	<span class='fld_edit'>
	<label for="linkFolder">Move to folder</label>
	<select name="linkFolder" id="linkFolder">
	<?=HTML::getSelectItems('folderGroupped');?>
	</select>
	</span>
	<button type="button" id="btn_editLink_cancel" class="btn btn-info" onClick='callCancelEditLink();'><span id="btn_cancelLinkCaption_edit">Cancel</span></button>
	<button type="button" id="btn_addLink_edit" class="btn btn-warning" onClick='callPostEditLink();'><span class="glyphicon glyphicon-edit"></span><span id="btn_addLinkCaption_edit"> Save link </span></button>
</div>
</div>
<div class='clearfix twoColumns'>
	<label for="link_to_edit">Link url</label><input type="text" name="link_to_edit" id="link_to_edit" class="form-control input-sm" placeholder="Link to save" autocomplete="off" autocorrect="off" autofocus/>
</div>
</div>
<div class='lbxForm_secondLine'>	
<!-- / select folder list FuelUX-->
<!-- tags panel
<button class="btn btn-default btn-xs" title="Tags" type="button" data-target="#toggleTagsPanel" data-toggle="collapse" aria-expanded="false">Tags <span class="caret"></span></button>
-->
<div id="toggleTagsPanel2" class="collapse in" aria-expanded="true" style="">
<!-- pillBox tags items-->
<!-- //TODO Settings::TagsStyle() == 'simple' / 'pillbox' -->
<?if (Settings::HTMLStyle('tagsInputStyle')=='pillbox'){?>
<div class="fuelux">
<div id="myPillbox2" data-initialize="pillbox" class="pillbox pills-editable">
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
<input type="hidden" name="lbx_tagsSelected_edit" id="lbx_tagsSelected_edit" value=""/>
<script>
$('#myPillbox2').pillbox({acceptKeyCodes: [13,188,190]});
</script>
</div> <!--fuelux wrapper-->
<?}elseif(Settings::HTMLStyle('tagsInputStyle')=='simple'){?>

<div class='clearfix twoColumns'>
<label for="tagsSimple">Tags </label>		
<input name="lbx_tagsSelected_edit" id="lbx_tagsSelected_edit" type="text" autocomplete="off" placeholder="add tag(s) with comma" class="form-control tags-simple input-sm" disabled/>
</div>
<?}?>
</div> <!-- / tag-->
</div><!--second line-->
<div class='lbxForm_thirdLine2'>
<div class='clearfix twoColumns'>
	<label for="link_description_edit">Name</label><input type="text" name="link_description_edit" id="link_description_edit" class="form-control input-sm" placeholder="Name / Description" autocomplete="off" autocorrect="off" autocorrect="off"/>
</div>
</div><!-- / third line-->
	<div class="clearfix"></div>
	<div id="lbx_formErrors_edit" class="alert alert-danger" hidden="true">
	<a class="close" href="#" onclick="$('#lbx_formErrors_edit').hide();">x</a>
	<p></p>
	</div>
	<input type="hidden" name="link_id_edit" id="link_id_edit" value="-2">
	<input type="hidden" name="fld_id_edit" id="fld_id_edit" value="-2">
	<input type="hidden" name="action" value="editLinkMP">
	<input type="hidden" name="createType" value="linkMP">
	</form>
	<input type="hidden" name="editMPformOpeningStatus" id="editMPformOpeningStatus" value="closed">	
</div>