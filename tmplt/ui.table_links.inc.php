<?php
namespace lbx;

include_once 'cgi/auth.inc.php';

include_once 'cgi/utils.inc.php';
include_once 'cgi/dbObjects.class.php';
include_once 'cgi/HTMLroutines.class.php';

?>
<div id="table-header">
<span class="lbox-orderheader">Order by: </span>
	<ul class="lbox-orderby">
	<li><a href="#">name</a></li><li><a href="#">date</a></li><li><a href="#">last visited</a></li>
	</ul>
<span class="lbox-searchheader">
<input class="form-control" placeholder="Search" name="table-search" id="inp-tblsearch" type="text">
</span>
<button name="search" id="btn-tablesearch"><span class="glyphicon glyphicon-search"></span></button>
</div>
<table class="table table-striped table-condensed" id="lbx_LinksTable">
<!--
<tbody>
	<tr class="lbox-linkrow">
	<td class="faviconCol"><img src="http://belpost.by/favicon.ico" /></td>
	<td>{Date}</td>
	<td><a href="{Link}" title="{Link}">{Header}</a></td>
	<td>
		<span class="row-buttons">
		<a class="icon_delete" href="javascript:manageLink('{$k}', 'delete');" alt="x" title="Delete"></a>
		<a class="icon_edit" href="javascript:manageLink('{$k}', 'edit');" alt="e" title="Edit"></a>
		<a class="icon_sharelbx" href="javascript:manageLink('{$k}', 'share');" alt="s" title="Share"></a>
		</span>
	</td>
	<td><input type="hidden" name="link_id" value="{$k}"/></td>
	</tr>
	<tr class="lbox-linkrow">
	<td class="faviconCol"><img src="http://belpost.by/favicon.ico" /></td>				
	<td>{Date}</td>
	<td><a href="{Link}" title="{Link}">{Header}</a></td>
	<td>
		<span class="row-buttons">
		<a class="icon_delete" href="javascript:manageLink('{$k}', 'delete');" alt="x" title="Delete"></a>
		<a class="icon_edit" href="javascript:manageLink('{$k}', 'edit');" alt="e" title="Edit"></a>
		<a class="icon_sharelbx" href="javascript:manageLink('{$k}', 'share');" alt="s" title="Share"></a>
		</span>
	</td>
	<td><input type="hidden" name="link_id" value="{$k}"/></td>
	</tr>
	<tr class="lbox-linkrow">
	<td class="faviconCol"><img src="http://belpost.by/favicon.ico" /></td>				
	<td>{Date}</td>
	<td><a href="{Link}" title="{Link}">{Header}</a></td>
	<td>
		<span class="row-buttons">
		<a class="icon_delete" href="javascript:manageLink('{$k}', 'delete');" alt="x" title="Delete"></a>
		<a class="icon_edit" href="javascript:manageLink('{$k}', 'edit');" alt="e" title="Edit"></a>
		<a class="icon_sharelbx" href="javascript:manageLink('{$k}', 'share');" alt="s" title="Share"></a>
		</span>
	</td>
	<td><input type="hidden" name="link_id" value="{$k}"/></td>
	</tr>
	<tr class="lbox-linkrow">
	<td class="faviconCol"><img src="http://belpost.by/favicon.ico" /></td>				
	<td>{Date}</td>
	<td><a href="{Link}" title="{Link}">{Header}</a></td>
	<td>
		<span class="row-buttons">
		<a class="icon_delete" href="javascript:manageLink('{$k}', 'delete');" alt="x" title="Delete"></a>
		<a class="icon_edit" href="javascript:manageLink('{$k}', 'edit');" alt="e" title="Edit"></a>
		<a class="icon_sharelbx" href="javascript:manageLink('{$k}', 'share');" alt="s" title="Share"></a>
		</span>
	</td>
	<td><input type="hidden" name="link_id" value="{$k}"/></td>
	</tr>
</tbody>
-->
<?=HTML::getTableItems('linkMainPage');?>
</table>