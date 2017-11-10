<?php
namespace lbx;

include_once 'cgi/auth.inc.php';

include_once 'cgi/utils.inc.php';
include_once 'cgi/dbObjects.class.php';
include_once 'cgi/HTMLroutines.class.php';

//$folders = Folder::getParentFoldersNames();
$folders = Folder::getFoldersArray();
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
		$entryContentA = "<a href='#{$HTMLfldID}' class='list-group-item' onClick='menuFolderSelected(\"subfolder\",{$folderID});'>{$folderName} {$entryCount}</a>";
		$entryContentB = "<div class='FLDmenuItem'>{$entryContentA}</div>";
		$entryContent = $entryContent.$entryContentB;
		$entryContent = $entryContent.PHP_EOL;
		//$entryContent = $entryContent."<a>{$folderName} <span class='badge'>{$folderCount}</span></a>";				
			}
			$panelBodyClass='panel-body-menu';
		}else{
			$entryContent = "No subfolders";	
			$panelBodyClass='panel-body';
		}
		
		$entryHead = "<a data-toggle='collapse' data-parent='#accordion' href='#{$HTMLentryID}' onClick='menuFolderSelected(\"parent\",{$fldIDParent}); ' ondblclick='menuFolderSelected(\"parentOnly\",{$fldIDParent}); ' class='aHeaderMenu'> {$fldNameParent} <span class='badge'>{$fldCountParent}</span></a>";
				
		//$eHTMLheading = "<div class='panel-heading'><h4 class='panel-title'>{$entryHead}</h4></div>";
		$eHTMLheading = "<div class='panel-heading fldHeading'><h4 class='panel-title'><div class='FLDmenuItem'>{$entryHead}</div></h4></div>";
		//$eHTMLheading = "<div class='FLDmenuItem'>{$eHTMLheadingA}</div>";
		$eHTMLContent = "<div id='{$HTMLentryID}' class='panel-collapse collapse'><div class='{$panelBodyClass}'>{$entryContent}</div></div>";
		$HTMLfoldersAccord = $HTMLfoldersAccord."<div class='panel panel-default'>{$eHTMLheading}{$eHTMLContent}</div>";
		$HTMLfoldersAccord = $HTMLfoldersAccord.PHP_EOL;
		
		$panelID++;
	}
}else{
$HTMLfoldersAccord = "<div class='panel panel-default'> No folders yet</div>";
}
	$totalCount = Folder::$totalLinksCount;
		$eAllentryHead = "<a class='aHeaderMenu' data-toggle='collapse' data-parent='#accordion' href='#' onClick='menuFolderSelected(\"parent\",\"all\");'>Show all <span class='badge'>{$totalCount}</span></a>";
		$eShowAllmenuItem = "<div class='panel-heading fldHeading'><h4 class='panel-title'><div class='FLDmenuItem'>{$eAllentryHead}</div></h4></div>";
		$eHTMLallContent = "<div class='panel panel-default'>{$eShowAllmenuItem}</div>";
		$eHTMLallContent = $eHTMLallContent.PHP_EOL;
		
		$HTMLfoldersAccord = $eHTMLallContent.$HTMLfoldersAccord;
//		<a href="#" class="list-group-item active">
	//		<span class="glyphicon glyphicon-camera"></span> Pictures</a>
	/*
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
*/
?>
<div id="folders_listA">
    <div id="accordion" class="panel-group">
 <?=$HTMLfoldersAccord;?>
    </div>
</div>