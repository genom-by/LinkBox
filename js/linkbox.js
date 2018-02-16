

function getTitle(externalUrl){
  var proxyurl = "http://localhost/tt/atwebpages/LinkBox_bs/cgi/getPageTitle.php?url=" + externalUrl;
  $.ajax({
    url: proxyurl,
    async: true,
    success: function(response) {
      alert(response);
    },   
    error: function(e) {
      alert("error! " + e);
    }
  });
}
//getTitle('ya.ru');

function lbxAddLink_onSubmit(){
//console.log('here2');
//console.log( $('#myPillbox1').pillbox('items') );
event.preventDefault();
//return false;
}

//append added link to table on page
//
/*				<tr class="lbox-linkrow">
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
*/
//
function appendAddedLink(data){
	
	if(data.status != 'success'){return false;}
	
	var tblRow = document.createElement('tr');
		tblRow.className = "lbox-linkrow";
		//tblRow.setAttribute("class", "fadingRow");
		tblRow.setAttribute("class", "lbox-linkrow fadingRow");
	
	var tblTD = document.createElement('td');
		tblTD.setAttribute("class", "faviconCol");
		
	var tblIMG = document.createElement('img');
		tblIMG.setAttribute("src", "http://belpost.by/favicon.ico");	
		tblIMG.innerHTML = "";
	tblTD.appendChild(tblIMG);		
	tblRow.appendChild(tblTD);
	
		tblTD = document.createElement('td');
		tblTD.innerHTML = data.date;
	tblRow.appendChild(tblTD);
		
		tblTD = document.createElement('td');
	var tblA = document.createElement('a');
		tblA.setAttribute("href", data.link);
		tblA.setAttribute("title", data.link);
		tblA.innerHTML = data.linkName;
	tblTD.appendChild(tblA);
	tblRow.appendChild(tblTD);
		
		tblTD = document.createElement('td');
	var tblSpan = document.createElement('span');
		tblSpan.setAttribute("class", "row-buttons");	
		tblA = document.createElement('a');
		tblA.setAttribute("class", "icon_delete");
		tblA.setAttribute("href", "javascript:manageLink('{$k}', 'delete');");
		tblA.setAttribute("alt", "x");
		tblA.setAttribute("title", "Delete");
		tblA.innerHTML = "";
	tblSpan.appendChild(tblA);	
	
		tblA = document.createElement('a');
		tblA.setAttribute("class", "icon_edit");
		tblA.setAttribute("href", "javascript:manageLink('{$k}', 'edit');");
		tblA.setAttribute("alt", "e");
		tblA.setAttribute("title", "Edit");
		tblA.innerHTML = "";
	tblSpan.appendChild(tblA);	
	
		tblA = document.createElement('a');
		tblA.setAttribute("class", "icon_sharelbx");
		tblA.setAttribute("href", "javascript:manageLink('{$k}', 'share');");
		tblA.setAttribute("alt", "s");
		tblA.setAttribute("title", "Share");
		tblA.innerHTML = "";
	tblSpan.appendChild(tblA);
	tblTD.appendChild(tblSpan);
	tblRow.appendChild(tblTD);
	
		tblTD = document.createElement('td');
	var tblInput = document.createElement('input');
		tblInput.setAttribute("type", "hidden");	
		tblInput.setAttribute("name", "link_id");
		tblInput.setAttribute("value", data.linkID);
		tblInput.innerHTML = "";	
	tblTD.appendChild(tblInput);
	tblRow.appendChild(tblTD);

	
	//$("#lbx_LinksTable tbody")[0].appendChild(tblRow);
	//$("#lbx_LinksTable tbody")[0].insertBefore(tblRow);
	//$("#lbx_LinksTable tbody")[0].prepend(tblRow);
	$("#lbx_LinksTable tbody").prepend(tblRow);
	//$(tblRow).show( "slow" );
	//$(tblRow).slideDown( "slow" );
	$(tblRow).fadeIn( "slow" , function(){$(tblRow).removeClass('fadingRow');});
	
}

// test post ajax function
//
function sample_post( formData ){
	
	$.post('cgi/lbox-test.php',
		{ lbx_form_addlink: formData },
		
		function (data) {
			console.log("returned:" + data);
			// if returned 'sucsess', add new link to DOM
			if(data.status == 'success'){
				console.log("Success!. Result = " + data.status); 
				appendAddedLink(data);
				toggleAddButton(false);
				return true;
			}else{
			
				return true
			}
			//$('body').html(data);
		},
		"json"
	);
}

Array.prototype.count = function () {
	var counter = 0; // Initializing main counter
	for(i in this) // Looping through elements
		if(typeof this[i] != "undefined") // If empty it's undefined
			counter++; // Counting not empty elements
	return counter-1; // Excepting own function
}

//============================== validating emphasising ==============
function showError(container, errorMessage) {
      container.className = 'error';
      var msgElem = document.createElement('span');
      msgElem.className = "error-message";
      msgElem.innerHTML = errorMessage;
      container.appendChild(msgElem);
    }

    function resetError(container) {
      container.className = '';
      if (container.lastChild.className == "error-message") {
        container.removeChild(container.lastChild);
      }
    }

    function validate(form) {
      var elems = form.elements;

      resetError(elems.from.parentNode);
      if (!elems.from.value) {
        showError(elems.from.parentNode, ' Укажите от кого.');
      }
	}
//=================

// toggle add button
//
function toggleAddButton(sending) {
	if(sending){
		$('#btn_addLink').prop( "disabled", true);
		$('#btn_addLinkCaption').html(' Adding..');
		submitting = true;
	}else{
		$('#btn_addLink').prop( "disabled", false );
		$('#btn_addLinkCaption').html(" Add link");
		submitting = false;
	}
}
// set form's emty controls to red border
//
function markEmptyControls(controls, add) {

		//default value for preES2005
		add = typeof add !== 'undefined' ? add : true;
		
		controls.forEach( function(item, i, arr) {
		//console.log(item);
		//console.log(item.tagName);
			if(add){
				//if(item.tagName.toLowerCase() == 'input'){
					$(item).addClass("emptyControl");
				//}
				//console.log('item name: '+item.tagName.toLowerCase());				
			}else{
				$(item).removeClass("emptyControl");
				//$(item.parentNode).removeClass("bg-danger");
			}
		});
}
// set form's controls to default
//
function resetForm() {
	toggleAddButton(false);
}
// checks if values fit for sending
//
function checkFormBeforeAdding() {

	var mandatoryControls = [];
	var emptyControls = [];
	
	var form = document.forms.lbx_form_addlink;
	var link = form.elements.link_to_save;
	var linkName = form.elements.link_description;
	var folderIdInput = $("input[name='link_Folder']");
	
	//var folderObject = $('#lbox-SelectFolderList').selectlist('selectedItem');
	//console.log("link: %s ; name: %s ; folder # %d: %s",link, linkName, folderObject.value, folderObject.text);
	mandatoryControls.push(link);
	mandatoryControls.push(linkName);
	mandatoryControls.push($('#lbox-SelectFolderList')[0]);
	
	if(link.value.trim()===""){emptyControls.push(link)}
	if(linkName.value.trim()===""){emptyControls.push(linkName)}
	//if(folderObject.value==0){emptyControls.push( $('#lbox-SelectFolderList')[0] )}
	//console.log('v:'+$(folderIdInput).val().trim() );
	if($(folderIdInput).val()=="-1"){emptyControls.push( $('#lbox-SelectFolderList') )}
	
	//clean controls
	markEmptyControls(mandatoryControls, false);
console.log('emptyControls.count:'+emptyControls.count());	
	//are there empty fields?
	if(emptyControls.count() > 0){
		markEmptyControls(emptyControls);
		$('#lbx_formErrors p').html( '<strong>Error:</strong> fill the form properly!');
		$('#lbx_formErrors').show();
		return false;
	}else{
		$('#lbx_formErrors p').html( 'No errors.');
		markEmptyControls(emptyControls, false);		
		return true;
	}
}
//fill hidden input with tags strings
//
function fillTagsSelected(){

	if( typeof( $('#myPillbox1').html() ) == 'undefined' ){
		return;
	}
	var tagsObjects =  $('#myPillbox1').pillbox('items') ;

	//console.info("selected tags: %d", tagsObjects.count());
	
	var form = document.forms.lbx_form_addlink;
	var tagsSelected = form.elements.lbx_tagsSelected;

	var tagsStr = [];
	var tagsJoinedString;
	tagsObjects.forEach(function(item, i, arr) {
		tagsStr.push(item["text"]);
	});

	tagsJoinedString = tagsStr.join(",");
	//console.warn(tagsJoinedString);
	tagsSelected.value = tagsJoinedString;
	
	//return false;
}
//
//
function menuFolderSelected(folderType, folderID){

	divID='#lbx_LinksTable';
	id_ = folderID;	
	dblSent = false;
	subSent = false;
console.log('folderType:'+folderType + ' folderID:'+folderID);
	var tid1;
	if(folderType=='parent'){
		tid1 = setTimeout(function(){
			// onClick event call;
			table_ = 'link_folder';	
			if( ( ! dblSent) && ( ! subSent ) ){
			_post({action: 'pageUpdate', id:id_, table:table_});
			}
		},1700);	

	}else if(folderType=='parentOnly'){
		table_ = 'link_folder_parOnly';

		clearTimeout(tid1);	//always undefined

		_post({action: 'pageUpdate', id:id_, table:table_});		
		dblSent = true;
	}else if(folderType=='subfolder'){
		table_ = 'link_folder';

		clearTimeout(tid1);	//always undefined

		_post({action: 'pageUpdate', id:id_, table:table_});		
		subSent = true;
	}else{
		table_ = 'link_folder';
		
		_post({action: 'pageUpdate', id:id_, table:table_});		
	}
	
		function _post(data_){
			//console.info('data to send:');
			//console.table(data_);
			$('#currentFolderID').val(folderID);	//global
			$('#currentFolderType').val(folderType);	//global
			
console.log('folderType:'+folderType + ' folderID:'+folderID);		
			$.post(
			"cgi/post.routines.php",
			data_,
			function(data){
				console.log("post returned: "+data.result);
			if (data.result == 'ok' ){
				//console.log(data.payload);
				$(divID).html(data.payload);
			}else{
				console.log('error message: ',data.message);
				$(divID).html(data.message);			
			}
			}
			,"json"
			);
		
		}
}
/*	=======  delete link from main table
*/
function mainLinkDelete(table_, id_entry){
	//console.info("delete from table: "+table_+" entry id:"+id_entry);
	action_ = 'delete';
	console.info('data to send:', {action:action_ , id:id_entry, table:table_});
	
	if (! confirm('Are you sure to delete this link?') ) {return;}
		
	$.post(
		"cgi/post.routines.php",
		{action:action_ ,id:id_entry, table:table_},
		function(data){
		console.log("post returned: "+data.result);
		//alert(data.result);
		if (data.result == 'ok' ){
			var domID = '#'+table_+'_id_'+id_entry;
			$(domID).toggle( "highlight" );
		}else{
			console.log('error message: ',data.message);
			alert(data.message);
		}
		}
		,"json"
	);	
}
/*	=======  edit link from main table
*/
function mainLinkEdit(table_, id_entry){

if( $('#editMPformOpeningStatus').val() != 'closed' ){
	console.info('editing form is opened now');
	// UI change
	callCancelEditLink();
	return;
}

	var rowID = '#'+table_+'_id_'+id_entry;		

	var tA = $(rowID).find('a');
	var s_url = $(tA).attr('href');
	var s_title = $(tA).attr('title');
	var s_name = $(tA).html();
	var s_fldname = $(tA).attr('data-attr-fld-name');
	var s_fldID = $(tA).attr('data-attr-fldid');
	var s_tags = $(tA).attr('data-attr-tags');
	
	var form = $('#form_to_clone').clone();
	$(form).removeClass('hidden');

	var rowrow = "<tr id='editLinkFormRow'><td colspan='4'>"+$(form).html()+"</td></tr>"
	$(rowrow).insertAfter(rowID);
	
	$('#link_to_edit').val(s_url);
	$('#link_description_edit').val(s_name);
	$('#lbx_tagsSelected_edit').val(s_tags);
	$('#link_id_edit').val(id_entry);
	$('#fld_id_edit').val(s_fldID);
	$('.currentFLD').html(s_fldname);

	$('#editMPformOpeningStatus').val('opened');
}
/* cancel link EDIT*/
function callCancelEditLink(){
	$('#editMPformOpeningStatus').val('closed');
	$('#editLinkFormRow').remove();
}
/* call POST for editing link
*/
function callPostEditLink(){
	var action_ = 'objectUpdate';
	var id_ = $('#link_id_edit').val();
	var table_ = 'link';
		var s_url = $('#link_to_edit').val();
		//var s_title = $(tA).attr('title');
		var s_name = $('#link_description_edit').val();
		var s_fldIDinitial = $('#fld_id_edit').val();
		var s_fldID = $('#linkFolder').val();
		var s_tags = $('#lbx_tagsSelected_edit').val();
		var isShared = 0;
		
		if(s_fldID == -1){
			s_fldID = s_fldIDinitial;
		}
		
console.info('data to send:', 
	{"action":action_ , "id":id_, "table":table_, "name2":s_name, "url2":s_url, "id_folder2":s_fldID, "tags2":s_tags, "isShared2":isShared}
	);
	var dataArray = {"name2":s_name, "url2":s_url, "id_folder2":s_fldID, "tags2":s_tags, "isShared2":isShared};
			//$('#link_id_30').addClass('editedOKeffect');			
		$.post(
		"cgi/post.routines.php",
		{"action":action_ , "id":id_, "table":table_, "data":dataArray},
		function(data){
		console.log("post returned: "+data.result);
		//alert(data.result);
		
		var domID = '#'+table_+'_id_'+id_;	
		if (data.result == 'ok' ){
			lbxDomNodeBlink('ok',domID);
			callCancelEditLink();
			//reload table
			var folderID = $('#currentFolderID').val();	//global
			var folderType = $('#currentFolderType').val();	//global
			setTimeout(function(){
				menuFolderSelected(folderType, folderID);
			},2500);
		}else{
			console.log('error message: ',data.message);
			lbxDomNodeBlink('error',domID);		
			$('#lbx_formErrors_edit').show();
			$('#lbx_formErrors_edit p').html(data.message);
		}
		}
		,"json"
	);
	
}
function lbxDomNodeBlink(type, domID){
	if(type=='ok'){
		$('#lbx_formErrors_edit').hide();
		$(domID).removeClass("editedERReffect");
		$(domID).addClass("editedOKeffect");			
		setTimeout(function(){
			$(domID).removeClass("editedOKeffect");
		},4000);
		//$(domID).effect("pulsate"); jq UI
			for(i=0;i<2;i++) {	$(domID).fadeTo('slow', 0.5).fadeTo('slow', 1.0);	}
	}else if(type=='error'){
		$(domID).removeClass("editedOKeffect");			
		$(domID).addClass("editedERReffect");
		setTimeout(function(){
			$(domID).removeClass("editedERReffect");
		},4000);
			for(i=0;i<2;i++) {	$(domID).fadeTo('slow', 0.5).fadeTo('slow', 1.0);	}	
	}
}
/* inquire page title for url
*/
function inquirePageTitle(pageurl){
	//console.log('folderType:'+folderType + ' folderID:'+folderID);
	
	id_ = -2;
	table_ = pageurl;
	divID='#link_description';
	$.post(
		"cgi/post.routines.php",
		{action: 'inquire', id:id_, table:table_, question:'pageTitle'},
		function(data){
			console.log("post returned: "+data.result);
		if (data.result == 'ok' ){
			console.log(data.payload);
			$(divID).val(data.payload);
		}else{
			console.log('error message: ',data.message);
			//$(divID).html(data.message);			
		}
		}
		,"json"
	);
}
function pagerClicked(ev){
	
	//console.log(ev.target);
	
	var dest_ = $(ev.target).attr('data-value');	
	
	if( dest_ == 'p_noclick' ){ return;}
	
	var offset_ = $(ev.target).attr('data-offset');	
	var id_ = $(ev.target).closest('ul').attr('data-fldid');	
	var table_ = $(ev.target).closest('ul').attr('data-tblname');	
	var parOnly = $(ev.target).closest('ul').attr('data-paronly');	
	
	console.log('offset '+offset_);
	console.log('clicked at '+dest_);
	console.log('folder id '+id_);
	if( table_ == '' ){table_ = 'link_folder';}
	if( dest_ == 'l_all' ){offset_ = '-2';}	// no limit
	if( id_ == '0' ){id_ = 'all';}
// ---
		divID='#lbx_LinksTable';
		_post({action: 'pageUpdate', id:id_, table:table_, offset:offset_});		
		
		function _post(data_){
			//console.info('data to send:');
			//console.table(data_);
	//		$('#currentFolderID').val(folderID);	//global
	//		$('#currentFolderType').val(folderType);	//global
			
//console.log('folderType:'+folderType + ' folderID:'+folderID);		
			$.post(
			"cgi/post.routines.php",
			data_,
			function(data){
				console.log("post returned: "+data.result);
			if (data.result == 'ok' ){
				//console.log(data.payload);
				$(divID).html(data.payload);
			}else{
				console.log('error message: ',data.message);
				$(divID).html(data.message);			
			}
			}
			,"json"
			);
		
		}		
	
}

	var submitting = false;
	
$(function () {
	
	$(".FLDmenuItem").click(function(e){
		//console.log(e.target);		//var a = $(this).find('a');		//a.css( "color", "red" );		//a.click();		//console.log('a is:'+a);
		$(".FLDmenuItem").removeClass('FLDmenuActive').removeClass('FLDmenuActivePO');
		$(".fldHeading").removeClass('FLDmenuActivePO');
		$(this).addClass('FLDmenuActive');
		//$(e.target).addClass('FLDmenuActive');		
	});
	$(".fldHeading").dblclick(function(e){
		$(".FLDmenuItem").removeClass('FLDmenuActive').removeClass('FLDmenuActivePO');
		$(".fldHeading").removeClass('FLDmenuActivePO');
		$(this).addClass('FLDmenuActivePO');
	});
	
	resetForm( );
	
	//adding link routine (submit event)
	//
	$( "#lbx_form_addlink" ).submit(function( event ){ 
		
		// prevent submit btn from doubleclicking
		if(submitting) {
			alert('The form is being submitted, please wait a moment...');
			$('#btn_addLink').prop( "disabled", true );
			
			//event.preventDefault();
			return false;
		}
	
		//try to fill all fields with data
		fillTagsSelected();
	
		//chech if filled properly...
		if(checkFormBeforeAdding()) {
			console.log('submitting');
			$('#lbx_formErrors').prop( "hidden", true );
			
			toggleAddButton(true);

			setTimeout(function(){
				toggleAddButton(false);
			},5000);
			//
			
			return true;
		}else{
			console.log('fill the form properly!');
			$('#lbx_formErrors').prop( "hidden", false );
			event.preventDefault();
		}
		
		//event.preventDefault();
		
	});
	// END / adding link routine (submit event)
	
	$('#link_to_save').focusout(function () {
    //Your Code
	var url = this.value;
	if(url.length > 3){
		inquirePageTitle(url);
		console.log('inquired for ' + url);
	}
	});

	//folder button menu - set selected value to hidden input
	$('#lbox-SelectFolderList li[data-value]').click(function(eventObject){
		//console.log('clicked at '+eventObject.tagName);
		var fldid = $(this).attr('data-value');
		//console.log('id: ' + fldid);		
		$("input[name='link_Folder']").val(fldid);

	});
	
	//simple select list menu - set selected value to hidden input
	$('#linkFolderAddnew').on("change", function() {
		console.log('clicked at '+$(this).val());
		$("input[name='link_Folder']").val( $(this).val() );
		
		//var fldid = $(this).attr('data-value');
		//console.log('id: ' + fldid);		
		//$("input[name='link_Folder']").val(fldid);

	});
	
});
//#btn_addLink
//$('#myPillbox1').pillbox('items')
/*
http://htmlbook.ru/samhtml5/formy/otpravka-dannykh-formy
https://htmlweb.ru/html/forms.php
http://javascript.ru/forum/misc/11962-onsubmit-otpravka-formy.html
http://htmlbook.ru/html/attr/onsubmit
http://stackoverflow.com/questions/9441531/replace-onsubmit-with-jquery
http://api.jquery.com/submit/
*/