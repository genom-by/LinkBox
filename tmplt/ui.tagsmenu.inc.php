<?php
namespace lbx;

include_once 'cgi/auth.inc.php';

include_once 'cgi/utils.inc.php';
include_once 'cgi/dbObjects.class.php';
include_once 'cgi/HTMLroutines.class.php';

//echo $_SERVER['PHP_SELF']; //echo $_SERVER['SCRIPT_FILENAME']; //echo $_SERVER['SCRIPT_NAME'];
//$current_page = substr($_SERVER['PHP_SELF'], strrpos($_SERVER['PHP_SELF'], '/') );
//echo $current_page; 
//$tags = Tag::getTagsAndCounts();
//$HTMLtagsNode = '';?>
<div class='clearfix twoColumns btn_n_input tagSearcgDiv'>
<input class="form-control input-sm" placeholder="Search tag(s)" name="tagsearch" id="tagsearch" type="text" onkeypress="inp_tagsearch_onkeypress(event)">
<button class="btntagsearch" name="tagsearchbtn" id="tagsearchbtn" onClick='btn_tagSearchClick(event);'><span class="glyphicon glyphicon-search"></span></button>
</div>
<script type="text/javascript" src="js/tagsearch.js"></script>
<div id='lbx_tagslikeres' class="btn-group" data-toggle="buttons"></div>
<?

?>
<div class="btn-group" data-toggle="buttons">
<?//echo HTML::tagBlock($tags);?>
</div>