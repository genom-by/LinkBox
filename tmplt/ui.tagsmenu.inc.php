<?php
namespace lbx;

include_once 'cgi/auth.inc.php';

include_once 'cgi/utils.inc.php';
include_once 'cgi/dbObjects.class.php';
include_once 'cgi/HTMLroutines.class.php';

//echo $_SERVER['PHP_SELF']; //echo $_SERVER['SCRIPT_FILENAME']; //echo $_SERVER['SCRIPT_NAME'];
$current_page = substr($_SERVER['PHP_SELF'], strrpos($_SERVER['PHP_SELF'], '/') );
//echo $current_page; 
$tags = Tag::getTagsAndCounts();
$HTMLtagsNode = '';
if(false !== $tags){

	foreach($tags as $tag){
		$tagName = $tag['tagName'];
		$tagCount = $tag['tagCount'];
		$entry = "<input type='checkbox' name='options'> {$tagName} <span class='badge'>{$tagCount}</span>";
		$HTMLtagsNode = $HTMLtagsNode."<label class='btn btn-info'>{$entry}</label>";
	}
}else{
$HTMLtagsNode = "<label class='btn btn-info'> No tags yet</label>";
}
?>
<div class="btn-group" data-toggle="buttons">
<?=$HTMLtagsNode;?>
</div>