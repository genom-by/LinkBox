<?php
namespace lbx;

//==========================================
// HTML routines and snippets classes
// ver 1.0
// © genom_by
// last updated 30 sep 2016
//==========================================

use PDO;
use PDOException;
use LinkBox;
use LinkBox\Logger as LiLogger;
use LinkBox\Utils as Utils;

//include_once 'settings.inc.php';
include_once 'utils.inc.php';
include_once 'settings.inc.php';
include_once 'database.class.php';
include_once 'linkHandler.class.php';

Interface iHTML{
	function createNode();
	function deleteNode();
	function appendNode();
}


class HTML{
	
	public static function getSelectItems($table, $selected_id = -1){
	
	$htmlList = '';	
	$htmlListFirstEntry = "<option value='-1' selected class='sel_first'>Select..</option>";	
	
	switch ($table){
		case 'folder':
		case 'parentfolder':
		{
			if($table=='folder'){
				$list = Folder::getAll() ; //'SELECT id_folder, folderName, id_user from folder';
			}elseif($table=='parentfolder'){
				$list = Folder::getAllParents() ; //'SELECT id_folder, folderName, id_user from folder';
			}
			if(false !== $list){
				$htmlItem = '';
				foreach($list as $item){
				if($item['id_folder'] == $selected_id){$is_selected=' selected ';}else{$is_selected='';}
					$htmlItem = "<option value='{$item['id_folder']}'{$is_selected}>{$item['folderName']}</option>";
					$htmlList = $htmlList.$htmlItem.PHP_EOL;
				}
			//return $htmlList;
			if($selected_id == -1) $htmlList = $htmlListFirstEntry.PHP_EOL.$htmlList;			
			}
			else $htmlList = "<option disabled value='-1'>No folders</option>";
		}
		break;
		case 'folderGroupped':{
			$list = Folder::getFoldersArray() ; //groupped array
			if(false !== $list AND count($list) > 0){
				$htmlItem = '';
				foreach($list as $folderSet){
					$fldNameParent = $folderSet['parentName'];
					$fldCountParent = $folderSet['folderCount'];
					$fldIDParent = $folderSet['parentID'];
					
					if( count($folderSet['subfolders']) > 0 ){
						//group header//
			$htmlList = $htmlList."<optgroup label='{$fldNameParent}'>";
						//group folder itself
			$htmlItem = "<option value='{$fldIDParent}'{$is_selected}>{$fldNameParent}</option>";
			$htmlList = $htmlList.$htmlItem.PHP_EOL;
			
			foreach($folderSet['subfolders'] as $subfolder){
			
				$folderName = $subfolder['folderName'];
				$folderCount = $subfolder['folderCount'];
				$folderID = $subfolder['id_folder'];
				$HTMLfldID = 'subfolder'.$folderID;
	if($item['id_folder'] == $selected_id){$is_selected=' selected ';}else{$is_selected='';}
	$htmlItem = "<option value='{$folderID}'{$is_selected}>{$folderName}</option>";
	$htmlList = $htmlList.$htmlItem.PHP_EOL;
				}
				$htmlList = $htmlList."</optgroup>";
		}else{
						//group header//
			$htmlList = $htmlList."<optgroup label='{$fldNameParent}'>";
						//group folder itself
			if($item['id_folder'] == $selected_id){$is_selected=' selected ';}else{$is_selected='';}
			$htmlItem = "<option value='{$fldIDParent}'{$is_selected}>{$fldNameParent}</option>";
			$htmlList = $htmlList.$htmlItem.PHP_EOL;
			$htmlList = $htmlList."</optgroup>";			
		}
				}
			if($selected_id == -1) $htmlList = $htmlListFirstEntry.PHP_EOL.$htmlList;			
			}
			else $htmlList = "<option disabled value='-1'>No folders</option>";
		}
		break;

		case 'tag':{
			$list = Tag::getAll() ;
			if(false !== $list){
				$htmlItem = '';
				foreach($list as $item){
//LiLogger::log("item inside selecttableid_station: {$item['id_station']} and name {$item['name']} and selected_id is {$selected_id}");				
				if($item['id_tag'] === $selected_id){
		$htmlItem = "<option value='{$item['id_tag']}' selected>{$item['tagName']}</option>";
				}else{
		$htmlItem = "<option value='{$item['id_tag']}'>{$item['tagName']}</option>";				
				}	
					$htmlList = $htmlList.$htmlItem.PHP_EOL;
				}
			//return $htmlList;
				if($selected_id == -1) $htmlList = $htmlListFirstEntry.PHP_EOL.$htmlList;			
			}
			else $htmlList = "<option disabled value='-1'>No tags</option>";
		}
		break;
		
		break;
		default:
			$htmlList = "<option disabled value='-1'>No data</option>";	
		}
	return $htmlList;	

	}
/* ==============================	select list bootstrapped
*/
	public static function getSelectULList($table, $selected_id = -1){
	
	$htmlList = '';
	$htmlListFirstEntry = "<li data-value='-1' class='disabled'><a href='#'>Select folder </a></li><li class='divider'></li>";	
	
	switch ($table){
		case 'folder':
		case 'parentfolder':
		{
			if($table=='folder'){
				$list = Folder::getAll() ; //'SELECT id_folder, folderName, id_user from folder';
			}elseif($table=='parentfolder'){
				$list = Folder::getAllParents() ; //'SELECT id_folder, folderName, id_user from folder';
			}		
			if(false !== $list){
				$htmlItem = '';
				foreach($list as $item){
				if($item['id_folder'] == $selected_id){$is_selected=' selected ';}else{$is_selected='';}
				$htmlItem = "<li data-value='{$item['id_folder']}'><a href='#'>{$item['folderName']}</a></li>";
					$htmlList = $htmlList.$htmlItem.PHP_EOL;
				}
			if($selected_id == -1) $htmlList = $htmlListFirstEntry.PHP_EOL.$htmlList;			
			}
			else $htmlList = "<li data-value='-1' class='disabled'><a href='#'>No folders</a></li>";
		}
		$htmlList = "<ul class='dropdown-menu' role='menu'>".$htmlList."</ul>";
		break;
		case 'folderGroupped':{
			$list = Folder::getFoldersArray() ; //groupped array
			if(false !== $list AND count($list) > 0){
				$htmlItem = '';
				foreach($list as $folderSet){
					$fldNameParent = $folderSet['parentName'];
					$fldCountParent = $folderSet['folderCount'];
					$fldIDParent = $folderSet['parentID'];
					
					if( count($folderSet['subfolders']) > 0 ){
						//group header//
			$htmlItem = "<li data-value='{$fldIDParent}' class='ulheader'><a href='#'>{$fldNameParent}</a></li>";
			$htmlList = $htmlList.$htmlItem.PHP_EOL;
			
			foreach($folderSet['subfolders'] as $subfolder){
			
				$folderName = $subfolder['folderName'];
				$folderCount = $subfolder['folderCount'];
				$folderID = $subfolder['id_folder'];
				$HTMLfldID = 'subfolder'.$folderID;
	$htmlItem = "<li data-value='{$folderID}'><a href='#'>{$folderName}</a></li>";
	$htmlList = $htmlList.$htmlItem.PHP_EOL;
				}
				$htmlList = $htmlList."<li class='divider'></li>";
		}else{
			$htmlItem = "<li data-value='{$fldIDParent}' class='ulheader'><a href='#'>{$fldNameParent}</a></li>";
			$htmlList = $htmlList.$htmlItem.PHP_EOL;
			$htmlList = $htmlList."<li class='divider'></li>";
		}
				}
			if($selected_id == -1) $htmlList = $htmlListFirstEntry.PHP_EOL.$htmlList;			
			}
			else $htmlList = "<li data-value='-1' class='disabled'><a href='#'>No folders</a></li>";
		}
		$htmlList = "<ul class='dropdown-menu' role='menu'>".$htmlList."</ul>";		
		break;

		case 'tag':{
			$list = Tag::getAll() ;
			if(false !== $list){
				$htmlItem = '';
				foreach($list as $item){			
					if($item['id_tag'] === $selected_id){
		$htmlItem = "<li data-value='{$item['id_tag']}' selected><a href='#'>{$item['tagName']}</a></li>";		
					}else{
		$htmlItem = "<li data-value='{$item['id_tag']}'><a href='#'>{$item['tagName']}</a></li>";
					}	
					$htmlList = $htmlList.$htmlItem.PHP_EOL;
				}
				if($selected_id == -1) $htmlList = $htmlListFirstEntry.PHP_EOL.$htmlList;			
			}
			else $htmlList = "<li data-value='-1' class='disabled'><a href='#'>No tags</a></li>";
		}
		$htmlList = "<ul class='dropdown-menu' role='menu'>".$htmlList."</ul>";
		break;
		
		break;
		default:
			$htmlList = "<option disabled value='-1'>No data</option>";	
		}
	return $htmlList;	

	}
/* ==============================
*								getTableItems
* ============================== */	
	public static function getTableItems($table, $id=null, $parentOnly=false, $offset=0){
		
	$htmlTable = '';	
	
	switch ($table){
		case 'link':{
			$list = Link::getAll() ; //'SELECT id_link, id_folder, url, id_user, created, lastVisited, isShared, title from link'
			//$folders = Folder::getAll() ; //'SELECT id_folder, folderName, id_user from folder'
			$folders = Folder::getFoldersNames() ; //'SELECT id_folder, folderName, id_user from folder'
			if(false !== $list){
				$htmlItem = '';
				foreach($list as $item){
			//$btnDel = self::createDELbutton($item['id_itin'], 'btnDelItin_onClick');
			$btnDel = self::createDELTablebutton('link', $item['id_link']);		
					$id_link = $item['id_link'];
					$id_folder = $item['id_folder'];
						//$sel_folder = self::getSelectItems('folder',$item['id_folder']); 	//$html_folder = "<select>{$sel_folder}</select>";
						$folderName = $folders[$id_folder];	//test
					$url = LinkHandler::wrapUrl($item['url']);
					$title = $item['title'];	//$id_user = $item['id_user'];
					$created = date("M j, Y", $item['created'] );
					$lastVisited = date("M j, Y", $item['lastVisited'] );
					$isShared = $item['isShared'];

					$fvsrc = LinkHandler::getFaviconHref($url);
					$favicon = "<img src='{$fvsrc}' class='simpleFav'></img>";
$htmlItem = "<tr id='link_id_{$id_link}'><td>{$favicon}</td><td><a class='simpleUrl' href='{$url}' target='_blank' rel='noopener noreferrer' title='{$url}'>{$title}</a></td><td>{$title}</td>"."<td>{$folderName}</td><td>{$created}</td><td>{$lastVisited}</td><td>{$btnDel}</td></tr>";

					$htmlTable = $htmlTable.$htmlItem.PHP_EOL;
				}
				$selF1 = self::getSelectItems('folder');
		$filterSelect = "<select id='filter_link_folder' onChange='filter_link_folder_onChange(filter_link_folder,this.value);'>{$selF1}</select>";
		$htmlheader = "<thead><tr><th>-</th><th>URL</th><th>Name</th><th>Folder {$filterSelect}</th><th>Created</th><th>Visited</th><th>del</th></tr></thead>";				
			//return $htmlList;
			$htmlTable = $htmlheader.$htmlTable;
			}else{$htmlTable = "no data";}			
		}
		break;
		case 'linkMainPage':{
		
			if($id==null OR $id=='all'){
				$list = Link::fetchLinks(0, $offset);
				//$list = Link::getAll() ; //'SELECT id_link, id_folder, url, id_user, created, lastVisited, isShared, title from link'
			}else{
				if($parentOnly){	// get links only for parent folder, not subfolders too
					$list = Link::fetchLinks($id, $offset);
				}else{
					$subfolders = Folder::getSubFoldersNames($id);
					if(count($subfolders) > 0){
						$subfoldersIDs = array_keys($subfolders);
						$subfoldersIDs[] = $id;
						$list = Link::fetchLinks($subfoldersIDs, $offset);
					}else{
						$list = Link::fetchLinks($id, $offset);
					}				
				}	
			}
						
			if(false !== $list){
				$htmlItem = '';
				$rows = self::pileLinkRows($list);

			$htmlTable = $htmlTable.$rows;
		$htmlheader = "<thead><tr><th>-</th><th>URL name</th><th>Created</th><th>del</th></tr></thead>";
		// pager
		$pager = self::pagerBlock($table, $id, $parentOnly, $offset, count($list));
		$pagerRow = "<tr><td colspan='4'>{$pager}</td></tr>";
			$htmlTable = $htmlheader.$htmlTable;
			$htmlTable = $htmlTable.$pagerRow;
			//$htmlTable = $htmlTable1.'test';var_dump($htmlTable);die();
			}else{$htmlTable = "no data";}
			
		}
		break;
		case 'linkTagsFiltered':{
		
			$list = Link::fetchTaggedLinks($id, $offset);

			if(false !== $list){
				$htmlItem = '';
				$rows = self::pileLinkRows($list);
		if(false === $rows){
			$htmlTable = "no data";
			break;
		}
			$htmlTable = $htmlTable.$rows;
		$htmlheader = "<thead><tr><th>-</th><th>URL name</th><th>Created</th><th>del</th></tr></thead>";
		// pager
		$pager = self::pagerBlock($table, $id, $parentOnly, $offset);
		$pagerRow = "<tr><td colspan='4'>{$pager}</td></tr>";
			$htmlTable = $htmlheader.$htmlTable;
			$htmlTable = $htmlTable.$pagerRow;
			//$htmlTable = $htmlTable1.'test';var_dump($htmlTable);die();
			}else{$htmlTable = "no data";}
			
		}
		break;
		case 'linkSearchMain':{
		
			$list = Link::searchLinks($id, $offset);

			if(false !== $list){
				$htmlItem = '';
				$rows = self::pileLinkRows($list);
		if(false === $rows){
			$htmlTable = "no data";
			break;
		}
			$htmlTable = $htmlTable.$rows;
		$htmlheader = "<thead><tr><th>-</th><th>URL name</th><th>Created</th><th>del</th></tr></thead>";
		// pager
		$pager = self::pagerBlock($table, $id, $parentOnly, $offset);
		$pagerRow = "<tr><td colspan='4'>{$pager}</td></tr>";
			$htmlTable = $htmlheader.$htmlTable;
			$htmlTable = $htmlTable.$pagerRow;
			//$htmlTable = $htmlTable1.'test';var_dump($htmlTable);die();
			}else{$htmlTable = "no data";}
			
		}
		break;
		case 'tag':{
			$list = Tag::getAll() ;
			if(false !== $list){
				$htmlItem = '';
				foreach($list as $item){
$btnDel = self::createDELTablebutton('tag', $item['id_tag']);	
$btnEd = self::createEDTablebutton('tag', $item['id_tag']);	
$btnBlock = self::createBlockOfButtons('tag', $item['id_tag']);	
$btnSav = '<span>save</span>';	
$htmlItem = "<tr id='tag_id_{$item['id_tag']}'><td class='rowtxt' orm='tagName'>{$item['tagName']}</td><td class='btnBlock'>{$btnBlock}</td></tr>";
					$htmlTable = $htmlTable.$htmlItem.PHP_EOL;
				}
			//return $htmlList;
			}else{$htmlTable = "no data";}			
		}
		break;
		case 'folder':{
			$list = Folder::getAll() ;
			if(false !== $list){
				$htmlItem = '';
				foreach($list as $item){

$btnDel = self::createDELTablebutton('folder', $item['id_folder']);	
$btnEd = self::createEDTablebutton('folder', $item['id_folder']);	
$btnBlock = self::createBlockOfButtons('folder', $item['id_folder']);	
$btnSav = '<span>save</span>';	
$htmlItem = "<tr id='folder_id_{$item['id_folder']}'><td class='rowtxt' orm='folderName'>{$item['folderName']}</td><td class='btnBlock'>{$btnBlock}</td></tr>";
					$htmlTable = $htmlTable.$htmlItem.PHP_EOL;
				}
			//return $htmlList;
			}else{$htmlTable = "no data";}			
		}
		break;
		case 'folderGroupEdit':{
			$htmlTable = '';
			$list = Folder::getFoldersArray() ; //groupped array
			if(false !== $list AND count($list) > 0){
				$htmlItem = '';
				foreach($list as $folderSet){
					$fldNameParent = $folderSet['parentName'];
					$fldCountParent = $folderSet['folderCount'];
					$fldIDParent = $folderSet['parentID'];
					
					if( count($folderSet['subfolders']) > 0 ){	//there are subfolres
						//group header//
			$htmlItem = "<tr class='rowheader'><th>{$folderSet['parentName']}</th><th>Parent Folder</th><th class='btnBlock'></th></tr>";
			$htmlTable = $htmlTable.$htmlItem.PHP_EOL;

			$btnBlock = self::createBlockOfButtons('folder', $folderSet['parentID']);	
			$htmlItem = "<tr id='folder_id_{$folderSet['parentID']}'><td class='rowtxt' orm='folderName'>{$folderSet['parentName']}</td><td></td><td class='btnBlock'>{$btnBlock}</td></tr>";
			
			$htmlTable = $htmlTable.$htmlItem.PHP_EOL;
			
			foreach($folderSet['subfolders'] as $subfolder){
			
				$folderName = $subfolder['folderName'];
				$folderCount = $subfolder['folderCount'];
				$folderID = $subfolder['id_folder'];
				
				$selParentFld = self::getSelectItems('parentfolder', $folderSet['parentID']);
		$filterSelect = "<select id='sel_parent_fld_".$subfolder['id_folder']."' orm='id_parentFolder' disabled>{$selParentFld}</select>";
				
			$btnBlock = self::createBlockOfButtons('folder', $subfolder['id_folder']);	
			$htmlItem = "<tr id='folder_id_{$subfolder['id_folder']}'><td class='rowtxt' orm='folderName'>{$subfolder['folderName']}</td><td class='rowsel'>{$filterSelect}</td><td class='btnBlock'>{$btnBlock}</td></tr>";
	$htmlTable = $htmlTable.$htmlItem.PHP_EOL;
				}
				
		}else{
								//group header//
			$htmlItem = "<tr class='rowheader'><th>{$folderSet['parentName']}</th><th>Parent Folder</th><th class='btnBlock'></th></tr>";
			$htmlTable = $htmlTable.$htmlItem.PHP_EOL;

			$btnBlock = self::createBlockOfButtons('folder', $folderSet['parentID']);	
			$htmlItem = "<tr id='folder_id_{$folderSet['parentID']}'><td class='rowtxt' orm='folderName'>{$folderSet['parentName']}</td><td></td><td class='btnBlock'>{$btnBlock}</td></tr>";
			
			$htmlTable = $htmlTable.$htmlItem.PHP_EOL;
			
		}
				}
			}else{$htmlTable = "no data";}			
		}
		break;
		
		break;
		default:
			$htmlTable = "no such table";	
	}
	return $htmlTable;
	}
	
	/* create table rows for Links array
	*/
	public static function pileLinkRows($linksArray){
		$rw = '';
		
		$folders = Folder::getFoldersNames() ; //'SELECT id_folder, folderName, id_user from folder'
		$settShowFavicon = Settings::Val('showFawIcons');
		if( $settShowFavicon == 'n' ){$showFvIc = false;}else{$showFvIc = true;}
		
		foreach($linksArray as $item){
		
		$btnDel = self::createDELTablebutton('link', $item['id_link']);		
				$id_link = $item['id_link'];
			$id_folder = $item['id_folder'];					//$sel_folder = self::getSelectItems('folder',$item['id_folder']);
					//$html_folder = "<select>{$sel_folder}</select>";						
			$folderName = $folders[$id_folder];	//test
				$url = LinkHandler::wrapUrl($item['url']);
				$title = $item['title'];
				$id_user = $item['id_user'];
				$created = date("j/M/y", $item['created'] );
				$lastVisited = date("M j, Y", $item['lastVisited'] );
				$isShared = $item['isShared'];
//"<a class='icon_delete' href='javascript:manageLink(`{$k}`, `delete`);' alt='x' title='Delete'></a>"

		$tags = Tag::getLinkTags($id_link,'csv');
				$btnBlock = "<span class='row-buttons'>".
	"<a class='icon_delete' href='javascript:mainLinkDelete(\"link\", {$id_link});' alt='x' title='Delete'></a>".
	"<a class='icon_edit' href='javascript:mainLinkEdit(\"link\", {$id_link});' alt='e' title='Edit'></a>".
	"<a class='icon_sharelbx' href='javascript:manageLink(`{$k}`, `share`);' alt='s' title='Share'></a>".
	"</span>";

				if($showFvIc){
					$fvsrc = LinkHandler::getFaviconHref($url);					
					$favicon = "<img src='{$fvsrc}' class='simpleFav'></img>";}else{
					$fvsrc = LinkHandler::getNoFaviconHref();
					$favicon = "<img src='{$fvsrc}' class='simpleFav'></img>";
				}
				$datablock = "data-attr-fldID='{$id_folder}' data-attr-fld-name='{$folderName}' data-attr-tags='{$tags}'";
	//htmlentities 
	$title = htmlentities($title,ENT_QUOTES,'UTF-8');
//LiLogger::log("title: {$title} -- {$title2}");
	
$htmlItem = "<tr id='link_id_{$id_link}' class='lbox-linkrow'><td class='favtd'>{$favicon}</td>".
"<td><a class='simpleUrl' href='{$url}' target='_blank' rel='noopener noreferrer' title='{$url}' {$datablock}>{$title}</a></td>".
"<td class='datetime' title='last visited: {$lastVisited}'>{$created}</td><td class='btns'>{$btnBlock}</td></tr>";

				$rw = $rw.$htmlItem.PHP_EOL;
			}
		return $rw;
	}
	
	/* create link node with favicon
	*/
	public static function favicon(){
		$fv = "<link rel='shortcut icon' href='favicon.ico' type='image/x-icon' />";
		return $fv;
	}
	/* create tag node block
	*/
	public static function tagBlock($tags, $showLinkBtn = true){
		$HTMLtagsNode = '';
		if( ! empty($tags) ){
			foreach($tags as $tag){
				$tagName = $tag['tagName'];
				$tagID = $tag['tagID'];
				$tagCount = $tag['tagCount'];
			$entry = "<input type='checkbox' name='tagsFiltered'> {$tagName} <span class='badge'>{$tagCount}</span>";
				$HTMLtagsNode = $HTMLtagsNode."<label class='btn btn-xs btn-info' data-tagID='{$tagID}'>{$entry}</label>";
			}
			if($showLinkBtn){
				$divLinkFilter = "<div class='clearfix'></div><div><a id='ttTagAlert' href='#' data-toggle='tooltip' data-placement='left' title='Select some tags'></a><button class='btn_taglinkFilter' type='button' onClick='btn_taglinkFilterClick();' >Show links</button></div>";
				//$divPopOver = "<button type='button' class='btn btn-warning'   title='Select some tags' ></button>";
				$HTMLtagsNode = $HTMLtagsNode.$divLinkFilter;
			}
				
			}else{
				$HTMLtagsNode = "<label class='btn btn-info'> No tags yet</label>";
			}
		return $HTMLtagsNode;
	}
		
	/* create pager block
	*/
	public static function pagerBlock($table, $id, $parentOnly, $offset, $countRows = -1){
		
		$linksPerPage = Settings::Val('pagerLimit');	//offset == $offset		//LiLogger::log("got val {$linksCount}");
		if($linksPerPage === false){$linksPerPage = 0;}
		
		$nextOff = $offset + $linksPerPage;
		$prevOf = $offset - $linksPerPage;
		if( $prevOf < 0 ){ $prevOf = 0; }
		if( empty($offset) ){ 
			//first page - no previous link
			$litag_prev = "<li class='disabled'><a data-value='p_noclick' >X&nbsp; Previous</a></li>";
		}else{
			$litag_prev = "<li><a href='#' data-value='l_prev' data-offset='{$prevOf}'>&lt;&nbsp; Previous</a></li>";		
		}
		
		$linksCount = Settings::Val('pagerLimit');	
		if($linksCount === false){$linksCount = 1;}

		if( $countRows < $linksCount  ){ 
			//last page
			$litag_next = "<li class='disabled'><a data-value='p_noclick' >Next&nbsp;X</a></li>";		
		}else{
			//not last page or not defined
			$litag_next = "<li><a href='#' data-value='l_next' data-offset='{$nextOff}'>Next&nbsp;&gt;</a></li>";			
		}
		
		$block = "<ul class='pager' data-tblname='{$table}' data-fldid='{$id}' data-paronly='{$parentOnly}' onClick='pagerClicked(event)'>".
		"{$litag_prev}".
		"<li><a href='#' data-value='l_all' data-offset='0'>All</a></li>".
		"{$litag_next}";
		return $block;
	}
	
	
	/* create html button for deleting table row
	*   input: +id, ?js-procedure
	*/
	public static function createDELbutton($id, $js_routine='alert("no js-routine to delete");'){
		if(empty($id)) return "<span>xDELx</span>";
		return "<button type='button' class='btn_del' onclick='{$js_routine}({$id})'>del</button>";
	}
	/* create html button wih common function btnDelFromTable for deleting table row
	*   input: table, id
	*/
	public static function createDELTablebutton($table='no_table_provided',$id=0){
		if(empty($id)) return "<span>xDELx</span>";
		return "<button type='button' class='btn_del' onclick='btnDelFromTable(\"{$table}\",{$id});'>del</button>";
	}
	/* create html button wih common function btnEditlFromTable for deleting table row
	*   input: table, id
	*/
	public static function createEdTablebutton($table='no_table_provided',$id=0){
		if(empty($id)) return "<span>xEdx</span>";
		return "<button type='button' class='btn_edit' onclick='btnEditTableItem(\"{$table}\",{$id});'>edit</button>";
	}
	/* create html block of buttons for edit, save, cancel, delete  - row
	*   input: table, id
	*/
	public static function createBlockOfButtons($table='no_table_provided',$id=0){
		if(empty($id)) return "<span>xBlocKx</span>";
		
		$edSpan = "<span class='btnEditBl'>".self::createEdTablebutton($table,$id)."</span>";
		
	$btnDel = self::createDELTablebutton($table,$id);
	$btnSave = "<button type='button' class='btn_save' onclick='btnSaveTableItem(\"{$table}\",{$id});'>save</button>";
	$btnCancel = "<button type='button' class='btn_cancel' onclick='btnCancelTableItem(\"{$table}\",{$id});'>cancel</button>";
		$dscSpan = "<span class='btnDSCBl hided'>{$btnDel}{$btnSave}{$btnCancel}</span>";
		return $edSpan.$dscSpan;
	}

	public static function normalizeWays2JSON($ways){
		if (empty($ways)) return false;
	
		$json_arr = array();
		$js_arr_string = "";
		
		foreach($ways as $pit){
		
			//$js_arr_string = $js_arr_string.'{name:'.$pit['name'] ;
			$js_arr_string = $js_arr_string.'{name:"'.$pit['name'].'"' ;
				
				foreach($pit as $key=>$val){
					if($key !== 'name'){
						foreach($val as $stat_shrtname => $stat_time){
							$js_arr_string = $js_arr_string.", {$stat_shrtname}:{$stat_time}";
						}
					}
				}
				$js_arr_string = $js_arr_string.'},';
		}
		$js_arr_string = rtrim($js_arr_string, ",");
//echo $js_arr_string;		
//var cars = [{name:a47c_Зел_07:20, name:a47c_Зел_07:20, 0:Array, 1:Array, 2:Array},{name:a47c_ака_7:46, name:a47c_ака_7:46, 0:Array, 1:Array, 2:Array},{name:t46_Кол_7:04, name:t46_Кол_7:04, 0:Array, 1:Array, 2:Array}]		
		$js_string = "var cars = [".$js_arr_string."]";
		
		return $js_string;
	}
	

	//returns LI entries
	public static function getTopMenuItems(){
	 //class="active"
	 	$lis = '';
		$pages = App::getTopMenuPagesArr();
		foreach($pages as $pg=>$title){
			$link = App::link($pg);
			$class = '';
			if( App::currentPage()== $pg ){
				$class = " class='active' ";
			}
			$lis = $lis."<li {$class}><a href='{$link}'>{$title}</a></li>";
		}
		/*$lis = $lis."<li><a href='{App::link('dataset')}'>Dataset</a></li>";
		$lis = $lis."<li><a href='{App::link('profile')}'>Profile</a></li>";
		$lis = $lis."<li><a href='{App::link('howto')}'>How to use</a></li>";
*/
		if(App::currentPage() == 'chart'){
			$sel = HTML::getSelectItems('sequences');
$lis = $lis."<li><a><select name='sequencesSelectUP' id='sequencesSelectUP'>{$sel}</select>";
$lis = $lis."<button onClick='redrawUP();'>Show</button></a></li>";
		}
		return $lis;
	}
	
} //HTML class

?>