<?php
namespace lbx;

include_once 'cgi/auth.inc.php';

include_once 'cgi/utils.inc.php';
include_once 'cgi/dbObjects.class.php';
include_once 'cgi/HTMLroutines.class.php';

//$folders = Folder::getParentFoldersNames();
$folders = Folder::getFoldersArray();

$standardFolders = array("<span class='glyphicon glyphicon-camera'></span> Pictures",
						"<span class='glyphicon glyphicon-file'></span> Documents",
						"<span class='glyphicon glyphicon-music'></span>",
						"<span class='glyphicon glyphicon-film'></span> Videos");
$HTMLfoldersNode = '';
if(false !== $folders AND count($folders) > 0){

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
// ---
$HTMLfoldersAccord = '';
$entryContent = '';
if(false !== $folders AND count($folders) > 0){
$panelID = 1;
	foreach($folders as $folderSet){
		$HTMLentryID = 'collapse'.$panelID;

		$fldNameParent = $folderSet['parentName'];
		$fldCountParent = $folderSet['folderCount'];
		$fldIDParent = $folderSet['parentID'];

		$entryHead = "<a data-toggle='collapse' data-parent='#accordion' href='#{$HTMLentryID}' onClick='menuFolderSelected(`parent`,{$fldIDParent});'> {$fldNameParent}</a> <span class='badge'>{$fldCountParent}</span>";
		//$entryCount = "<span class='glyphicon'></span> {$fldNameParent} <span class='badge'>{$folderCount}</span>";
		
		if( count($folderSet['subfolders']) > 0 ){
			$entryContent = '';
			foreach($folderSet['subfolders'] as $subfolder){
				
				$folderName = $subfolder['folderName'];
				$folderCount = $subfolder['folderCount'];
				$folderID = $subfolder['id_folder'];
				$HTMLfldID = 'subfolder'.$folderID;
		$entry = "<span class='glyphicon'></span> {$folderName}";
		$entryCount = "<span class='badge'>{$folderCount}</span>";
		//$HTMLfoldersNode = $HTMLfoldersNode."<a href='#' class='list-group-item'>{$entry}</a>";
		$entryContent = $entryContent."<a href='#{$HTMLfldID}' class='list-group-item' onClick='menuFolderSelected(`subfolder`,{$folderID});'>{$folderName} {$entryCount}</a>";
		//$entryContent = $entryContent."<a>{$folderName} <span class='badge'>{$folderCount}</span></a>";				
			}
			$panelBodyClass='panel-body-menu';
		}else{
			$entryContent = "No subfolders";	
			$panelBodyClass='panel-body';
		}
		
		$eHTMLheading = "<div class='panel-heading'><h4 class='panel-title'>{$entryHead}</h4></div>";
		$eHTMLContent = "<div id='{$HTMLentryID}' class='panel-collapse collapse'><div class='{$panelBodyClass}'>{$entryContent}</div></div>";
		$HTMLfoldersAccord = $HTMLfoldersAccord."<div class='panel panel-default'>{$eHTMLheading}{$eHTMLContent}</div>";
		$panelID++;
	}
}else{
$HTMLfoldersAccord = "<div class='panel panel-default'> No folders yet</div>";
}
//		<a href="#" class="list-group-item active">
	//		<span class="glyphicon glyphicon-camera"></span> Pictures</a>
?>
<style>
.panel-body-menu{
	padding:0;
}
</style>
<div id="folders_list">
	<div class="list-group lbox-folders">
<?=$HTMLfoldersNode1;?>
	</div>
</div>
<div id="folders_listA">
    <div id="accordion" class="panel-group">
 <?=$HTMLfoldersAccord;?>
    </div>
</div>