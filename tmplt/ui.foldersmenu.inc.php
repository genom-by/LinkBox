<?php
namespace lbx;

include_once 'cgi/auth.inc.php';

include_once 'cgi/utils.inc.php';
include_once 'cgi/dbObjects.class.php';
include_once 'cgi/HTMLroutines.class.php';

$folders = Folder::getFoldersAndCounts();

$standardFolders = array("<span class='glyphicon glyphicon-camera'></span> Pictures",
						"<span class='glyphicon glyphicon-file'></span> Documents",
						"<span class='glyphicon glyphicon-music'></span>",
						"<span class='glyphicon glyphicon-film'></span> Videos");
$HTMLfoldersNode = '';
if(false !== $folders){

	foreach($folders as $folder){
		$folderName = $folder['folderName'];
		$folderCount = $folder['folderCount'];
		$entry = "<span class='glyphicon'></span> {$folderName}";
		$entryCount = "<span class='glyphicon'></span> {$folderName} <span class='badge'>{$folderCount}</span>";
		$HTMLfoldersNode = $HTMLfoldersNode."<a href='#' class='list-group-item'>{$entry}</a>";
	}
}else{
$HTMLfoldersNode = "<label class='btn btn-info'> No folders yet</label>";
}
//		<a href="#" class="list-group-item active">
	//		<span class="glyphicon glyphicon-camera"></span> Pictures</a>
?>
<div id="folders_list">
	<div class="list-group lbox-folders">
<?=$HTMLfoldersNode;?>
	</div>
</div>