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
		case 'folder':{
			$list = Folder::getAll() ; //'SELECT id_folder, folderName, id_user from folder';
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
		case 'parentfolder':{
			$list = Folder::getAllParents() ; //'SELECT id_folder, folderName, id_user from folder';
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
		case 'pitstopType':{
			$list = PitType::getAll() ;
			if(false !== $list){
				$htmlItem = '';
				foreach($list as $item){
				if($selected_id == -1){
					If($item['type'] == 'trans')
						$htmlItem = "<option selected='selected' value='{$item['id_pittype']}'>{$item['type']}</option>";
						else
						$htmlItem = "<option value='{$item['id_pittype']}'>{$item['type']}</option>";
				}else{
					If($item['id_pittype'] == $selected_id)
						$htmlItem = "<option selected='selected' value='{$item['id_pittype']}'>{$item['type']}</option>";
						else
						$htmlItem = "<option value='{$item['id_pittype']}'>{$item['type']}</option>";					
					}
					$htmlList = $htmlList.$htmlItem.PHP_EOL;
				}
			//return $htmlList;
				if($selected_id == -1) $htmlList = $htmlListFirstEntry.PHP_EOL.$htmlList;			
			}
			else $htmlList = "<option disabled value='-1'>No Stations</option>";
		}
		break;
		case 'itinerary':{
			$list = Itinerary::getAll() ;
			if(false !== $list){
				$htmlItem = '';
				foreach($list as $item){
					$htmlItem = "<option value='{$item['id_itin']}'>{$item['name']}</option>";
					$htmlList = $htmlList.$htmlItem.PHP_EOL;
				}
			//return $htmlList;
				if($selected_id == -1) $htmlList = $htmlListFirstEntry.PHP_EOL.$htmlList;			
			}
			else $htmlList = "<option disabled value='-1'>No Stations</option>";
		}
		break;
		case 'destination':{
			$list = Destination::getAll() ;
			if(false !== $list){
				$htmlItem = '';
				foreach($list as $item){
//LiLogger::log("id_dest: {$item['id_dest']} and name {$item['name']} and selected_id is {$selected_id}");
					if($item['id_dest'] == $selected_id){
			$htmlItem = "<option selected value='{$item['id_dest']}'>{$item['name']}</option>";		
					}else{
			$htmlItem = "<option value='{$item['id_dest']}'>{$item['name']}</option>";		
					}
					//$htmlItem = "<option value='{$item['id_dest']}'>{$item['name']}</option>";
					$htmlList = $htmlList.$htmlItem.PHP_EOL;
				}
			//return $htmlList;
			if($selected_id == -1){
				$htmlList = $htmlListFirstEntry.PHP_EOL.$htmlList;
				}
			}
			else $htmlList = "<option disabled value='-1'>No Stations</option>";
		}
		break;
		case 'sequences':{
			$list = Sequence::getAll() ;
			if(false !== $list){
				$htmlItem = '';
				foreach($list as $item){
					$htmlItem = "<option value='{$item['id_seq']}'>{$item['name']}</option>";
					$htmlList = $htmlList.$htmlItem.PHP_EOL;
				}
			//return $htmlList;
				if($selected_id == -1) $htmlList = $htmlListFirstEntry.PHP_EOL.$htmlList;
			}
			else $htmlList = "<option disabled value='-1'>No Sequences</option>";
		}
		break;
		default:
			$htmlList = "<option disabled value='-1'>No data</option>";	
		}
	return $htmlList;	

	}
/* ==============================
*								getTableItems
* ============================== */	
	public static function getTableItems($table, $id=null){
		
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
						$sel_folder = self::getSelectItems('folder',$item['id_folder']);
						$html_folder = "<select>{$sel_folder}</select>";
						$folderName = $folders[$id_folder];	//test
					$url = LinkHandler::wrapUrl($item['url']);
					$title = $item['title'];
					$id_user = $item['id_user'];
					$created = date("M j, Y", $item['created'] );
					$lastVisited = date("M j, Y", $item['lastVisited'] );
					$isShared = $item['isShared'];
					//$time = LinkBox\Utils::Int2HHmm($item['start_time']);
					//$destin = self::getSelectItems('destination',$item['destination']);
					//$destinSelect = "<select>{$destin}</select>";
					$fvsrc = LinkHandler::getFaviconHref($url);
					$favicon = "<img src='{$fvsrc}' class='simpleFav'></img>";
$htmlItem = "<tr id='link_id_{$id_link}'><td>{$favicon}</td><td><a class='simpleUrl' href='{$url}' target='_blank' title='{$url}'>{$title}</a></td><td>{$title}</td>"."<td>{$folderName}</td><td>{$created}</td><td>{$lastVisited}</td><td>{$btnDel}</td></tr>";

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
			if($id==null){
				$list = Link::getAll() ; //'SELECT id_link, id_folder, url, id_user, created, lastVisited, isShared, title from link'
			}else{
				//$list = Link::getAllWhere("WHERE id_folder={$id}") ;	//TODO - to include subfolders in parent set too
				$subfolders = Folder::getSubFoldersNames($id);
				if(count($subfolders) > 0){
					$subfoldersIDs = array_keys($subfolders);
					$subids = implode(",", $subfoldersIDs);
					$list = Link::getAllWhere("WHERE id_folder IN ( {$id}, {$subids} )") ;
				}else{
					$list = Link::getAllWhere("WHERE id_folder={$id}") ;
				}
			}
						
			$folders = Folder::getFoldersNames() ; //'SELECT id_folder, folderName, id_user from folder'
			if(false !== $list){
				$htmlItem = '';
				foreach($list as $item){
			
			$btnDel = self::createDELTablebutton('link', $item['id_link']);		
					$id_link = $item['id_link'];
					//$id_folder = $item['id_folder'];
						//$sel_folder = self::getSelectItems('folder',$item['id_folder']);
						//$html_folder = "<select>{$sel_folder}</select>";
						//$folderName = $folders[$id_folder];	//test
					$url = LinkHandler::wrapUrl($item['url']);
					$title = $item['title'];
					$id_user = $item['id_user'];
					$created = date("j/M/y", $item['created'] );
					$lastVisited = date("M j, Y", $item['lastVisited'] );
					$isShared = $item['isShared'];
					
					$btnBlock = "<span class='row-buttons'>".
		"<a class='icon_delete' href='javascript:manageLink(`{$k}`, `delete`);' alt='x' title='Delete'></a>".
		"<a class='icon_edit' href='javascript:manageLink(`{$k}`, `edit`);' alt='e' title='Edit'></a>".
		"<a class='icon_sharelbx' href='javascript:manageLink(`{$k}`, `share`);' alt='s' title='Share'></a>".
		"</span>";
					$fvsrc = LinkHandler::getFaviconHref($url);
					$favicon = "<img src='{$fvsrc}' class='simpleFav'></img>";
$htmlItem = "<tr id='link_id_{$id_link}' class='lbox-linkrow'><td>{$favicon}</td><td><a class='simpleUrl' href='{$url}' target='_blank' title='{$url}'>{$title}</a></td>"."<td class='datetime' title='last visited: {$lastVisited}'>{$created}</td><td class='btns'>{$btnBlock}</td></tr>";

					$htmlTable = $htmlTable.$htmlItem.PHP_EOL;
				}
				//$selF1 = self::getSelectItems('folder');
		//$filterSelect = "<select id='filter_link_folder' onChange='filter_link_folder_onChange(filter_link_folder,this.value);'>{$selF1}</select>";
		$htmlheader = "<thead><tr><th>-</th><th>URL name</th><th>Created</th><th>del</th></tr></thead>";				
			//return $htmlList;
			$htmlTable = $htmlheader.$htmlTable;
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
		case 'stations':{
			$list = Station::getAll() ;
			if(false !== $list){
				$htmlItem = '';
				foreach($list as $item){
					$btnDel = self::createDELTablebutton('station', $item['id_station']);
	$btnEd = self::createEDTablebutton('station', $item['id_station']);	
	$btnBlock = self::createBlockOfButtons('station', $item['id_station']);				
//$htmlItem = "<tr id='station_id_{$item['id_station']}'><td>{$item['name']}</td><td>{$item['shortName']}</td><td>{$btnDel}</td></tr>";
$htmlItem = "<tr id='station_id_{$item['id_station']}'><td class='rowtxt' orm='name'>{$item['name']}</td><td class='rowtxt' orm='shortName'>{$item['shortName']}</td><td class='btnBlock'>{$btnBlock}</td></tr>";
					$htmlTable = $htmlTable.$htmlItem.PHP_EOL;
				}
			//return $htmlList;
			}else{$htmlTable = "no data";}			
		}
		break;
		case 'sequences':{
			$list = Sequence::getAll() ;
			if(false !== $list){
				$htmlItem = '';
				foreach($list as $item){
				$btnDel = self::createDELTablebutton('sequences', $item['id_seq']);
$htmlItem = "<tr id='sequences_id_{$item['id_seq']}'><td>{$item['name']}</td><td>{$item['destName']}</td><td>{$btnDel}</td></tr>";
					$htmlTable = $htmlTable.$htmlItem.PHP_EOL;
				}
			//return $htmlList;
			}else{$htmlTable = "no data";}			
		}
		break;
		default:
			$htmlTable = "no such table";	
	}
	return $htmlTable;
	}
	
	/* create html table rows for new pitstops
	*/
	public static function getPitStopsTable($type = "new"){
		
	$htmlTable = '';	
	$totalstops = 0;
	
	$list = Station::getAll() ;
	if(false !== $list){
		$htmlItem = '';
		
		$html_selectorStations = self::getSelectItems('station');
		$html_selectorPitTypes = self::getSelectItems('pitstopType');
		
		foreach($list as $item){
			$totalstops++;
			if($type == 'new'){
				/*$row_selStation = "<select name='station' id='stationSel".$item['id_station']."'>".$html_selectorStations."</select>";//self::getSelectItems('station')
				$row_Time = "<input type='text' autocomplete='off' name='stationTime' id='stationTime".$item['id_station']."' size='10'/>";
				$row_selpitType = "<select name='pitType' id='pitType".$item['id_station']."'>".$html_selectorPitTypes."</select>";
				$btn_delRow = "<button type='button' class='tbl_pitnew_row_del' onclick='btn_delPitstopNewRow({$totalstops})'>X</button>";
				*/$row_selStation = "<select name='station".$item['id_station']."' id='stationSel".$item['id_station']."'>".$html_selectorStations."</select>";//self::getSelectItems('station')
				$row_Time = "<input type='text' autocomplete='off' name='stationTime".$item['id_station']."' id='stationTime".$item['id_station']."' size='10' tabindex='{$totalstops}'/>";
				$row_selpitType = "<select name='pitType".$item['id_station']."' id='pitType".$item['id_station']."'>".$html_selectorPitTypes."</select>";
				$btn_delRow = "<button type='button' class='tbl_pitnew_row_del' onclick='btn_delPitstopNewRow({$totalstops})'>X</button>";
			
			}else if($type == 'edit'){
				//refactored
			}
			$htmlItem = "<tr class='trpitnew' id='tbl_pitnew_row_{$totalstops}' data-id='{$totalstops}'><td>{$row_selStation}</td><td>{$row_Time}</td><td>{$row_selpitType}</td><td>{$btn_delRow}</td></tr>";

			$htmlTable = $htmlTable.$htmlItem.PHP_EOL;
		}
		$cloneID = -1;
		$row_selStation = "<select name='station' id='stationSel{$cloneID}'>".$html_selectorStations."</select>";//self::getSelectItems('station')
		$row_Time = "<input type='text' autocomplete='off' name='stationTime' id='stationTime{$cloneID}' size='10' tabindex='{$cloneID}'/>";
		$row_selpitType = "<select name='pitType' id='pitType{$cloneID}'>".$html_selectorPitTypes."</select>";
		$btn_delRow = "<button type='button' class='tbl_pitnew_row_del'>X</button>";
		$htmlItemToClone = "<tr class='trpitnewcloneable' id='tbl_pitnew_row_clone' data-id='-1'><td>{$row_selStation}</td><td>{$row_Time}</td><td>{$row_selpitType}</td><td>{$btn_delRow}</td></tr>";
		
	//return $htmlList;
	}else{$htmlTable = "no data";}			
	
	$htmlheader = "<thead><tr><th>Station</th><th>Time(HH:mm)</th><th>Stat.Type</th></tr>";
	$htmlBtnAddRow = "<tr><td colspan='4'><button class='btn_new_tablerow' type='button' onclick='btn_addPitstopNewRow()'>Add new row</button></td></tr></thead>";
	
	$htmlInputsTotal_Last = "<input name='totalstops' value='{$totalstops}' type='hidden'>".PHP_EOL."<input name='laststopID' value='{$totalstops}' type='hidden'>";
	
	return "<table class='pitstops_new'>".$htmlheader.$htmlTable.$htmlBtnAddRow.$htmlItemToClone."</table>".$htmlInputsTotal_Last;
	}	
	
	/* create html table rows for new pitstops - cycle FOR
	*/
	public static function getPitStopsTable2($type = "new", $lines=3){
		
	$htmlTable = '';	
	$totalstops = 0;
	
	$list = Station::getAll() ;
	if(false !== $list){
		$htmlItem = '';
		
		$html_selectorStations = self::getSelectItems('station');
		$html_selectorPitTypes = self::getSelectItems('pitstopType');
		
		for($row = 1; $row <= $lines; $row++){
			$totalstops++;
			if($type == 'new'){
				$row_selStation = "<select name='station{$row}' id='stationSel{$row}'>".$html_selectorStations."</select>";//self::getSelectItems('station')
				$row_Time = "<input type='text' autocomplete='off' name='stationTime{$row}' id='stationTime{$row}' size='10' tabindex='{$row}'/>";
				$row_selpitType = "<select name='pitType{$row}' id='pitType{$row}'>".$html_selectorPitTypes."</select>";
				$btn_delRow = "<button type='button' class='tbl_pitnew_row_del' onclick='btn_delPitstopNewRow({$row})'>X</button>";
			
			}else if($type == 'edit'){
				//refactored
			}
			$htmlItem = "<tr class='trpitnew' id='tbl_pitnew_row_{$row}' data-id='{$row}'><td>{$row_selStation}</td><td>{$row_Time}</td><td>{$row_selpitType}</td><td>{$btn_delRow}</td></tr>";

			$htmlTable = $htmlTable.$htmlItem.PHP_EOL;
		}
		$cloneID = -1;
		$row_selStation = "<select name='station'>".$html_selectorStations."</select>";//self::getSelectItems('station')
		$row_Time = "<input type='text' autocomplete='off' name='stationTime' size='10' tabindex='{$cloneID}'/>";
		$row_selpitType = "<select name='pitType' >".$html_selectorPitTypes."</select>";
		$btn_delRow = "<button type='button' class='tbl_pitnew_row_del'>X</button>";
		$htmlItemToClone = "<tr class='trpitnewcloneable' id='tbl_pitnew_row_clone' data-id='-1'><td>{$row_selStation}</td><td>{$row_Time}</td><td>{$row_selpitType}</td><td>{$btn_delRow}</td></tr>";
		
	//return $htmlList;
	}else{$htmlTable = "no data";}			
	
	$htmlheader = "<thead><tr><th>Station</th><th>Time(HH:mm)</th><th>Stat.Type</th></tr></thead>";
	$htmlBtnAddRow = "<tr><td colspan='3'><button class='btn_new_tablerow' type='button' onclick='btn_addPitstopNewRow()'>Add new row</button></td></tr>";
	
	$htmlInputsTotal_Last = "<input name='totalstops' value='{$totalstops}' type='hidden'>".PHP_EOL."<input name='laststopID' value='{$totalstops}' type='hidden'>";
	
	return "<table class='pitstops_new'>".$htmlheader.$htmlTable.$htmlBtnAddRow.$htmlItemToClone."</table>".$htmlInputsTotal_Last;
	}	
	
	/* create html table rows for editing pitstops
	*/
	public static function getPitStopsEditRows($itin_id = -1){
		
	$htmlTable = '';	
	$totalstops = 0;
	$maxstopID = -1;

	$list = Way::getAllWhere("WHERE id_itin = {$itin_id}") ;
	if(false !== $list){
		$htmlItem = '';
		foreach($list as $item){
			$totalstops++;
			if($maxstopID < $item['id_pitstop']){$maxstopID = $item['id_pitstop'];}
	$row_selStation = "<select name='stationED".$item['id_station']."' id='stationSelED".$item['id_station']."'>".self::getSelectItems('station', $item['id_station'])."</select>";//self::getSelectItems('station')
	$row_Time = "<input type='text' autocomplete='off' name='stationTimeED".$item['id_station']."' id='stationTimeED".$item['id_station']."' size='10' value='".Utils::Int2HHmm($item['time'])."'/>";
	$row_selpitType = "<select name='pitTypeED".$item['id_station']."' id='pitTypeED".$item['id_station']."'>".self::getSelectItems('pitstopType', $item['id_pittype'])."</select>";			

	$row = $item['id_pitstop'];
	$btn_delRow = "<button type='button' class='tbl_pitnew_row_del' onclick='btn_delPitstopEDITRow({$row})'>X</button>";	
	
	$htmlItem = "<tr class='trpitedit' id='tbl_pitedit_row_{$row}' data-id='{$row}'><td>{$row_selStation}</td><td>{$row_Time}</td><td>{$row_selpitType}</td><td>{$btn_delRow}</td></tr>";
	/*	
			$btnDel = self::createDELTablebutton('pitstop', $item['id_pitstop']);
			$row_Time = LinkBox\Utils::Int2HHmm($item['time']);	
	$htmlItem = "<tr id='pitstop_id_{$item['id_pitstop']}'><td>{$item['itinName']}</td><td>{$item['statName']}</td><td>{$row_Time}</td><td>{$btnDel}</td></tr>";
		*/		
			$htmlTable = $htmlTable.$htmlItem.PHP_EOL;
		}
		
$html_selectorStations = self::getSelectItems('station');
$html_selectorPitTypes = self::getSelectItems('pitstopType');
		
//TODO REFACTOR by creating separate function
$cloneID = -1;
$row_selStation = "<select name='stationED'>".$html_selectorStations."</select>";//self::getSelectItems('station')
$row_Time = "<input type='text' autocomplete='off' name='stationTimeED' size='10' tabindex='{$cloneID}'/>";
$row_selpitType = "<select name='pitTypeED' >".$html_selectorPitTypes."</select>";
$btn_delRow = "<button type='button' class='tbl_pitnew_row_del'>X</button>";
$htmlItemToClone = "<tr class='trpitnewcloneableEDIT' id='tbl_pitedit_row_clone' data-id='-1'><td>{$row_selStation}</td><td>{$row_Time}</td><td>{$row_selpitType}</td><td>{$btn_delRow}</td></tr>";
//TODO END

$htmlheader = "<thead><tr><th>Station</th><th>Time(HH:mm)</th><th>Stat.Type</th></tr></thead>";
$htmlBtnAddRow = "<tr><td colspan='3'><button class='btn_new_tablerow' type='button' onclick='btn_addPitstopNewRowEDIT()'>Add new row</button></td></tr>";

$htmlInputsTotal_Last = "<input name='totalstopsEDIT' value='{$totalstops}' type='hidden'>".PHP_EOL."<input name='laststopIDEDIT' value='{$maxstopID}' type='hidden'>";

return "<table class='pitstops_edit'>".$htmlheader.$htmlTable.$htmlBtnAddRow.$htmlItemToClone."</table>".$htmlInputsTotal_Last;
	
	//return $htmlList;
	}else{$htmlTable = "no data";}			
	
	/*$htmlheader = "<tr><th>Itinerary</th><th>Stat. Name</th><th>Time(HH:mm)</th><th>Del.</th></tr>";
	return $htmlheader.$htmlTable;*/
	}	
	/* ## restored ## ============================= pitstops table
	/* create html table rows for editing pitstops
	*/
	public static function getPitStopsViewRows($itin_id = -1){
		
	$htmlTable = '';	
	$totalstops = 0;

	$list = Way::getAllWhere("WHERE id_itin = {$itin_id}") ;
	if(false !== $list){
		$htmlItem = '';
		foreach($list as $item){
			$totalstops++;

			$btnDel = self::createDELTablebutton('pitstop', $item['id_pitstop']);
			$row_Time = LinkBox\Utils::Int2HHmm($item['time']);	
			$htmlItem = "<tr id='pitstop_id_{$item['id_pitstop']}'><td>{$item['itinName']}</td><td>{$item['statName']}</td><td>{$row_Time}</td><td>{$btnDel}</td></tr>";
				
			$htmlTable = $htmlTable.$htmlItem.PHP_EOL;
		}
	//return $htmlList;
	}else{$htmlTable = "no data";}			
	
	$htmlheader = "<tr><th>Itinerary</th><th>Stat. Name</th><th>Time(HH:mm)</th><th>Del.</th></tr>";
	return $htmlheader.$htmlTable;
	}	

	## restored ## ===========================================*/
	
	/* =======================================================================================================================================
	create html table rows for viewing sequences
	=======================================================================================================================================*/
	public static function getSeqViewRows($seq_id = -1){
		
	$htmlTable = '';	
	$totalstops = 0;

	$list = sequencesStations::getSeqStationsBySequenceID($seq_id);
	if(false !== $list){
		$htmlItem = '';
		foreach($list as $item){
			$totalstops++;

			$btnDel = self::createDELTablebutton('seq_stations', $item['id_ss']);

			$htmlItem = "<tr id='seq_stations_id_{$item['id_ss']}'><td>{$item['orderal']}</td><td>{$item['shortName']}</td><td>{$item['statName']}</td><td>{$btnDel}</td></tr>";
				
			$htmlTable = $htmlTable.$htmlItem.PHP_EOL;
		}

	}else{$htmlTable = "no data";}			
	
	$htmlheader = "<tr><th>orderal</th><th>shrtN</th><th>Name</th><th>Del.</th></tr>";
	return $htmlheader.$htmlTable;
	}
	
	/* =======================================================================================================================================
	create html table rows for editing sequences
	=======================================================================================================================================*/
	public static function getSeqEditRows($seq_id = -1){
		
	$htmlTable = '';	
	$totalstops = 0;
	$maxstopID = -1;
/*SELECT id_ss, seq_stations.id_station, orderal, station.shortName, station.name AS statName FROM seq_stations LEFT JOIN station ON seq_stations.id_station = station.id_station ORDER BY orderal*/
	$list = sequencesStations::getSeqStationsBySequenceID($seq_id);
	
	if(false !== $list){
		$htmlItem = '';
		foreach($list as $item){
			$totalstops++;
			if($maxstopID < $item['id_ss']){$maxstopID = $item['id_ss'];}
			$row_orderal = "".$item['orderal']."";
	$row_selStation = "<select name='stationSSED".$totalstops."' id='stationSelSSED".$totalstops."'>".self::getSelectItems('station', $item['id_station'])."</select>";
	$row_selpitType = "<select name='seqTypeED".$totalstops."' id='seqTypeED".$totalstops."'>".self::getSelectItems('pitstopType', $item['id_pitstoptype'])."</select>";			
	$hiddenOrd = "<input name='orderalED".$totalstops."' id='orderalED".$totalstops."' size='0' value=".$item['orderal']." hidden type='text'>";

	$row = $item['id_ss'];
	$btn_delRow = "<button type='button' class='tbl_seqSS_row_del' onclick='btn_delSequenceEDITRow({$row})'>X</button>";	
	
	$htmlItem = "<tr class='trseqedit' id='tbl_seqedit_row_{$row}' data-id='{$row}'><td class='tdorder'>{$row_orderal}</td><td class='hid_inp'>{$hiddenOrd}</td><td>{$row_selStation}</td><td>{$row_selpitType}</td><td>{$btn_delRow}</td></tr>";
		
			$htmlTable = $htmlTable.$htmlItem.PHP_EOL;
		}
		
$html_selectorStations = self::getSelectItems('station');
$html_selectorPitTypes = self::getSelectItems('pitstopType');
		
//TODO REFACTOR by creating separate function
$cloneID = -1;
$row_orderal = "0";
$row_selStation = "<select name='station'>".$html_selectorStations."</select>";//self::getSelectItems('station')
$row_selpitType = "<select name='seqType' >".$html_selectorPitTypes."</select>";
$btn_delRow = "<button type='button' class='tbl_seqnew_row_del'>X</button>";
$hiddenOrd = "<input name='orderalED' id='orderalED' size='0' value={$cloneID} hidden type='text'>";
$htmlItemToClone = "<tr class='trseqnewcloneableEDIT' id='tbl_seqedit_row_clone' data-id='-1'><td class='tdorder'>{$row_orderal}</td><td class='hid_inp'>{$hiddenOrd}</td><td>{$row_selStation}</td><td>{$row_selpitType}</td><td>{$btn_delRow}</td></tr>";
//TODO END

$htmlheader = "<thead><tr><th>Orderal</th><th>Station</th><th>Stat.Type</th></tr></thead>";
$htmlBtnAddRow = "<tr><td colspan='3'><button class='btn_new_tablerow' type='button' onclick='btn_addSequenceNewRowEDIT()'>Add new row</button></td></tr>";

$htmlInputsTotal_Last = "<input name='totalseqEDIT' value='{$totalstops}' type='hidden'>".PHP_EOL."<input name='lastseqIDEDIT' value='{$maxstopID}' type='hidden'>";

return "<table class='sequences_edit'>".$htmlheader.$htmlTable.$htmlBtnAddRow.$htmlItemToClone."</table>".$htmlInputsTotal_Last;
	
	//return $htmlList;
	}else{$htmlTable = "no data";
		return "<table class='sequences_edit'>".$htmlTable."</table>";
	}
	
	}
	/*=======================================================================================================================================
	*/
	public static function getSequencesTable(){
		
	$htmlTable = '';	
	$totalstops = 0;
	
	$list = Station::getAll() ;
	if(false !== $list){
		$htmlItem = '';
		foreach($list as $item){
			$totalstops++;
			$row_selStation = "<select name='station".$item['id_station']."' id='stationSel".$item['id_station']."'>".self::getSelectItems('station')."</select>";//self::getSelectItems('station')
			$row_orderal = "<input type='text' hidden name='orderal".$item['id_station']."' id='orderal".$item['id_station']."' size='0' value='{$totalstops}'/>";
			$row_selpitType = "<select name='pitType".$item['id_station']."' id='pitType".$item['id_station']."'>".self::getSelectItems('pitstopType')."</select>";
			
			$htmlItem = "<tr><td class='order'>{$totalstops}</td><td>{$row_orderal}</td><td>{$row_selStation}</td><td>{$row_selpitType}</td></tr>";
			$htmlTable = $htmlTable.$htmlItem.PHP_EOL;
		}
	//return $htmlList;
	}else{$htmlTable = "no data";}			
	
	$htmlheader = "<tr><th>Orderal</th><th>Station</th></tr>";
	return "<table>".$htmlheader.$htmlTable."</table>".PHP_EOL."<input name='totalstops' value='{$totalstops}' type='hidden'>";
	}
	
	/*======================================================================================================================================
	*/
	public static function getSequencesTable2($lines=3){
		
	$htmlTable = '';	
	$totalstops = 0;
	
	$list = Station::getAll() ;
	if(false !== $list){
		$html_selectorStations = self::getSelectItems('station');
		$html_selectorPitTypes = self::getSelectItems('pitstopType');
		
		for($row = 1; $row <= $lines; $row++){
			
			$htmlItem = '';		
			$totalstops++;
			$row_selStation = "<select name='station{$row}' id='stationSel{$row}'>{$html_selectorStations}</select>";//self::getSelectItems('station')
			$row_orderal = "<input type='text' hidden name='orderal{$row}' id='orderal{$row}' size='0' value='{$totalstops}'/>";
			$row_selpitType = "<select name='pitType{$row}' id='pitType{$row}'>{$html_selectorPitTypes}</select>";
			$btn_delRow = "<button type='button' class='tbl_pitnew_row_del' onclick='btn_delSequenceNewRow({$row})'>X</button>";
							
			$htmlItem = "<tr class='trseqnew' id='tbl_seqnew_row_{$row}' data-id='{$row}'><td class='order'>{$totalstops}</td><td class='hid_inp'>{$row_orderal}</td><td>{$row_selStation}</td><td>{$row_selpitType}</td><td>{$btn_delRow}</td></tr>";
			$htmlTable = $htmlTable.$htmlItem.PHP_EOL;			
		}	
	}else{$htmlTable = "no data";}	
	
	$cloneID = -1;
	$row_selStation = "<select name='station'>{$html_selectorStations}</select>";
	$row_orderal = "<input type='text' size='0' hidden value='{$cloneID}'/>";	
	$row_selpitType = "<select name='pitType' >{$html_selectorPitTypes}</select>";
	$btn_delRow = "<button type='button' class='tbl_seqnew_row_del'>X</button>";
	
	$htmlItemToClone = "<tr class='trseqnewcloneable' id='tbl_seqnew_row_clone' data-id='-1'><td class='order'>-1</td><td class='hid_inp'>{$row_orderal}</td><td>{$row_selStation}</td><td>{$row_selpitType}</td><td>{$btn_delRow}</td></tr>";
	
	$htmlBtnAddRow = "<tr><td colspan='3'><button class='btn_new_tablerow' type='button' onclick='btn_addSequenceNewRow()'>Add new row</button></td></tr>";
	
	$htmlInputsTotal_Last = "<input name='totalsequences' value='{$totalstops}' type='hidden'>".PHP_EOL."<input name='lastseqID' value='{$totalstops}' type='hidden'>";
	
	$htmlheader = "<thead><tr><th>##</th><th>Station</th><th>Stat.Type</th></tr></thead>";
	
	return "<table class='sequences_new'>".$htmlheader.$htmlTable.$htmlBtnAddRow.$htmlItemToClone."</table>".$htmlInputsTotal_Last;
	}
	
	public static function timeTableTest($startHour = 6, $endHour = 10){
			
			$htmlTable="";
			
		for($row = $startHour ; $row <= $endHour; $row++){
			$minHTML = "";
			$rowHTML = "";
			for($min = 0; $min < 60; $min++){
				$minHTML = $minHTML."<td>&nbsp;<sup>".str_pad($min,2,'0',STR_PAD_LEFT)."</sup></td>";
				//$minHTML = $minHTML."<td><sub>{$row}</sub>".str_pad($min,2,'0',STR_PAD_LEFT)."</td>";
				//$minHTML = $minHTML."<td>{$row}<sup>".str_pad($min,2,'0',STR_PAD_LEFT)."</sup></td>";
			}
			
			$rowHTML = "<tr><td>{$row}</td>{$minHTML}</tr>";
			$htmlTable = $htmlTable.PHP_EOL.$rowHTML;
		}
		
		return "<table>".$htmlTable."</table>";
	}

/*
getAll()
array(49) { [0]=> array(14) { ["id_pitstop"]=> string(2) "10" [0]=> string(2) "10" ["id_station"]=> string(1) "1" [1]=> string(1) "1" ["shortName"]=> string(4) "zel0" [2]=> string(4) "zel0" ["statName"]=> string(21) "Зелёный луг" [3]=> string(21) "Зелёный луг" ["id_itinerary"]=> string(1) "1" [4]=> string(1) "1" ["itinName"]=> string(16) "t46_Кол_07:04" [5]=> string(16) "t46_Кол_07:04" ["time"]=> string(3) "417" [6]=> string(3) "417" } [1]=> array(14) { ["id_pitstop"]=> string(2) "11" [0]=> string(2) "11" ["id_station"]=> string(1) "2" [1]=> string(1) "2" ["shortName"]=> string(4) "kol1" [2]=> string(4) "kol1" ["statName"]=> string(16) "Кольцова" [3]=> string(16) "Кольцова" ["id_itinerary"]=> string(1) "1" [4]=> string(1) "1" ["itinName"]=> string(16) "t46_Кол_07:04" [5]=> string(16) "t46_Кол_07:04" ["time"]=> string(3) "424" [6]=> string(3) "424" } [2]=> array(14) 

getPitstopsByItinerary
array(7) { ["id_pitstop"]=> string(2) "10" ["id_station"]=> string(1) "1" ["shortName"]=> string(4) "zel0" ["statName"]=> string(21) "Зелёный луг" ["id_itinerary"]=> string(1) "1" ["itinName"]=> string(16) "t46_Кол_07:04" ["time"]=> string(3) "417" } 
*/	
	public static function getPitstops($itir = 'all'){
		$htmlTable = '';	
		$totalstops = 0;
		
		if ($itir == 'all'){
			$list = Way::getAll() ;	
		}else if($itir > 0){
			//$list = Way::getPitstopsByItinerary($itir) ;		//var_dump($list);	- garbage here
			$list = Way::getAllWhere("WHERE id_itin = {$itin_id}") ; ### REFACTORED ###
		}
			
		if(false !== $list){
		$htmlItem = '';
		foreach($list as $item){
			$totalstops++;
			$btnDel = self::createDELTablebutton('pitstop', $item['id_pitstop']);
			$row_Time = LinkBox\Utils::Int2HHmm($item['time']);	
			$htmlItem = "<tr id='pitstop_id_{$item['id_pitstop']}'><td>{$item['itinName']}</td><td>{$item['statName']}</td><td>{$row_Time}</td><td>{$btnDel}</td></tr>";
			$htmlTable = $htmlTable.$htmlItem.PHP_EOL;
		}
	//return $htmlList;
	}else{$htmlTable = "no data";}	
				
		return $htmlTable;
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
	/*
	// get nested aray
	// gives json array {var:val,...}
	array(3) {
  [1]=>
  array(4) {
    ["itin_name"]=>
    string(17) "a47c_Зел_07:20"
    [0]=>
    array(1) {
      [1]=>
      string(1) "5"
    }
    [1]=>
    array(1) {
      [2]=>
      string(1) "5"
    }
    [2]=>
    array(1) {
      [3]=> -- stat_id
      string(1) "5"
    }
  }
  to
  var cars = [
{name:"chevrolet chevelle malibu", mpg:18, cyl:8, dsp:307, hp:130, lbs:3504, acc:12, year:70, origin:1},
{name : 'kol1',  value : kol1 },  	{name : 'nem2',  value : nem2 },  	{name : 'mas3',  value : mas3 }, 	{name : 'akd4',   value : akd4  }, 
{name : 'spu5',  value : spu5 }, 	{name : 'kaz6',  value : kaz6 }, 	{name : 'tra7', value : tra7}
	*/
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
	
	/* return such array:
	[{         
		name: 'Tokyo',
		data: [7.0, 6.9, 9.5, 14.5, 18.4, 21.5, 25.2, 26.5, 23.3, 18.3, 13.9, 9.6]
	}, {
		name: 'London',
		data: [3.9, 4.2, 5.7, 8.5, 11.9, 15.2, 17.0, 16.6, 14.2, 10.3, 6.6, 4.8, 3.3, 4.4]
	}]
	source: Way::getPitstopsByItinerary()
	array(9) {
  [1]=>
  array(5) {
    ["name"]=>
    string(16) "t46_Кол_07:04"
    [0]=>
    array(1) {
      ["zel0"]=>
      string(3) "417"
    }
    [1]=>
    array(1) {
      ["kol1"]=>
      string(3) "424"
    }
    [2]=>
    array(1) {
      ["nem2"]=>
      string(3) "446"
    }
    [3]=>
    array(1) {
      ["mas3"]=>
      string(3) "452"
    }
  }
	*/

	/* input
array(49) {
  [0]=>  array(7) {
    ["id_pitstop"]=>    string(2) "10"
    ["id_station"]=>    string(1) "1"
    ["shortName"]=>    string(4) "zel0"
    ["statName"]=>    string(21) "Зелёный луг"
    ["id_itinerary"]=>    string(1) "1"
    ["itinName"]=>    string(16) "t46_Кол_07:04"
    ["time"]=>    string(3) "417"
  }
  [1]=>  array(7) {
	*/
//if no data - return string 'null' for not breaking js-formatting
	public static function arrayLineChart($ways, $sequence_id=-1, $delimiter=','){
		//if (empty($ways)) return "[no data1]";	//var_dump($ways);die();
		if (empty($ways)) return "null";	//var_dump($ways);die();
		if (empty($sequence_id)) return "null";
		if ( $sequence_id == -1) return "null";

	$seq_stations = sequencesStations::getSeqStatNamesBySequenceID($sequence_id);
	if ($seq_stations === false) { LiLogger::log("HTML::arrayLineChart error: no sequence stations obtained: ".sequencesStations::$errormsg); return "null";	}
	//$name2index = array ('zel0'=>0, 'kol1'=>1, 'nem2'=>2, 'mas3'=>3, 'akd4'=>4, 'spu5'=>5, 'kaz6'=>6, 'tra7'=>7);
	
	$name2index = array(); ###REFACTORED###
	$seq_orderal=0;
	foreach($seq_stations as $short){
		$name2index[$short] = $seq_orderal;
		$seq_orderal++;
	}	//var_dump($name2index);	
	
	$linechartXaxis = array(); //of 8 entries
		$json_arr = array();
		$js_arr_string = "";
		
		foreach($ways as $pit){
			
			$linechartXaxis = array_fill(0,8,'null'); // var_dump($linechartXaxis ); - array(8) { [0]=>   int(0)		
			$lineArName = "name:'".$pit['name']."'";
			//$lineArData = "data:[";
				
				foreach($pit as $key=>$val){
					if($key !== 'name'){
						foreach($val as $stat_shrtname => $stat_time){
						if(array_key_exists($stat_shrtname,$name2index)){
							$linechartXaxis[$name2index[$stat_shrtname]] = $stat_time;
						}					
						//$js_arr_string = $js_arr_string.", {$stat_shrtname}:{$stat_time}";
						}
					}
				}
			$lineArData = implode(",",$linechartXaxis);	
			$line_arr_string = "{".$lineArName.",data:[".$lineArData."]}{$delimiter}".PHP_EOL;
			$js_arr_string = $js_arr_string.$line_arr_string;
		}
		$js_arr_string = rtrim($js_arr_string, PHP_EOL);
		$js_arr_string = rtrim($js_arr_string, $delimiter);
		
		$js_string = "[".$js_arr_string."]";
		
//{name:'a73_Нем_07:32',433,440,452,458,461,465,480]}[{name:'a73_Нем_07:32',433,440,452,458,461,465,480]}]		
		return $js_string;	
	}
	/* ============================================= arrayLineChart 2 MODIFIED not fixed 8 ========================================
	*/
	public static function arrayLineChartFlex($ways, $sequence_id=-1, $delimiter=','){
		//if (empty($ways)) return "[no data1]";	//var_dump($ways);die();
		if (empty($ways)) return "null";	//var_dump($ways);die();
		if (empty($sequence_id)) return "null";
		if ( $sequence_id == -1) return "null";

	$seq_stations = sequencesStations::getSeqStatNamesBySequenceID($sequence_id);
	if ($seq_stations === false) { LiLogger::log("HTML::arrayLineChart error: no sequence stations obtained: ".sequencesStations::$errormsg); return "null";	}
	//$name2index = array ('zel0'=>0, 'kol1'=>1, 'nem2'=>2, 'mas3'=>3, 'akd4'=>4, 'spu5'=>5, 'kaz6'=>6, 'tra7'=>7);
	
	$statcount = count($seq_stations);
	$name2index = array(); ###REFACTORED###
	$seq_orderal=0;
	foreach($seq_stations as $short){
		$name2index[$short] = $seq_orderal;
		$seq_orderal++;
	}	//var_dump($name2index);	
	
	$linechartXaxis = array(); //of 8 entries -- $statcount
		$json_arr = array();
		$js_arr_string = "";
		
		foreach($ways as $pit){
			
			$linechartXaxis = array_fill(0,$statcount,'null'); // var_dump($linechartXaxis ); - array(8) { [0]=>   int(0)		
			$lineArName = "name:'".$pit['name']."'";
			//$lineArData = "data:[";
				
				foreach($pit as $key=>$val){
					if($key !== 'name'){
						foreach($val as $stat_shrtname => $stat_time){
						if(array_key_exists($stat_shrtname,$name2index)){
							$linechartXaxis[$name2index[$stat_shrtname]] = $stat_time;
						}					
						//$js_arr_string = $js_arr_string.", {$stat_shrtname}:{$stat_time}";
						}
					}
				}
			$lineArData = implode(",",$linechartXaxis);	
			$line_arr_string = "{".$lineArName.",data:[".$lineArData."]}{$delimiter}".PHP_EOL;
			$js_arr_string = $js_arr_string.$line_arr_string;
		}
		$js_arr_string = rtrim($js_arr_string, PHP_EOL);
		$js_arr_string = rtrim($js_arr_string, $delimiter);
		
		$js_string = "[".$js_arr_string."]";
		
//{name:'a73_Нем_07:32',433,440,452,458,461,465,480]}[{name:'a73_Нем_07:32',433,440,452,458,461,465,480]}]		
		return $js_string;	
	}
	
	public static function arrayLineChartCategories($seqs){
		if (empty($seqs)) return false;
//echo'<pre>'; 	var_dump($seqs);	echo'</pre>';
		$arQuoted = array();
		
		foreach($seqs as $ss){
			$arQuoted[] = "'{$ss}'";
		}
		$lineArData = implode(",",$arQuoted);	
		$line_arr_string = $lineArData;
		//$line_arr_string = "[".$lineArData."]".PHP_EOL;
		//$line_arr_string = "{".$lineArData."}".PHP_EOL;
		
	return $line_arr_string; 
 //return "'zel0', 'kol1', 'nem2', 'mas3', 'akd4', 'spu5', 'kaz6', 'tra7'";
 
	}
	//returns LI entries
	public static function getTopMenuItems(){
	 //class="active"
	 	$lis = '';
		$pages = array('customize'=>'Customize', 'profile'=>'Profile', 'howto'=>'How to use');
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