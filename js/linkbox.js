

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
console.log('here2');
console.log( $('#myPillbox1').pillbox('items') );
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
function markEmptyControls(controls, add=true) {
		controls.forEach( function(item, i, arr) {
		//console.log(item);
		//item.parentNode.className += " emptyControl";}
			if(add){
				$(item.parentNode).addClass("emptyControl");
			}else{
				$(item.parentNode).removeClass("emptyControl");
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
	
	var folderObject = $('#lbox-SelectFolderList').selectlist('selectedItem');
	//console.log("link: %s ; name: %s ; folder # %d: %s",link, linkName, folderObject.value, folderObject.text);
	mandatoryControls.push(link);
	mandatoryControls.push(linkName);
	mandatoryControls.push($('#lbox-SelectFolderList')[0]);
	
	if(link.value.trim()===""){emptyControls.push(link)}
	if(linkName.value.trim()===""){emptyControls.push(linkName)}
	if(folderObject.value==0){emptyControls.push( $('#lbox-SelectFolderList')[0] )}
	
	//clean controls
	markEmptyControls(mandatoryControls, false);
	
	//are there empty fields?
	if(emptyControls.count() > 0){
		markEmptyControls(emptyControls);
		$('#lbx_formErrors p').html( '<strong>Error:</strong> fill the form properly!');
		return false
	}else{
		$('#lbx_formErrors p').html( 'No errors.');
		return true;
	}
}
//fill hidden input with tags strings
//
function fillTagsSelected(){

	var tagsObjects =  $('#myPillbox1').pillbox('items') ;
	//console.info("selected tags: %d", tagsObjects.count());
	
	var form = document.forms.lbx_form_addlink;
	var tagsSelected = form.elements.lbx_tagsSelected;

	var tagsStr = [];
	var tagsJoinedString;
	tagsObjects.forEach(function(item, i, arr) {
		tagsStr.push(item["text"]);
	});

	tagsJoinedString = tagsStr.join("||");
	//console.warn(tagsJoinedString);
	tagsSelected.value = tagsJoinedString;
	
	//return false;
}

	var submitting = false;
	
$(function () {
	

	
	resetForm( );
	
	//adding link routine (submit event)
	//
	$( "#lbx_form_addlink" ).submit(function( event ){ 
		
		// prevent submit btn from doubleclicking
		if(submitting) {
			alert('The form is being submitted, please wait a moment...');
			$('#btn_addLink').prop( "disabled", true );
			//$('#btn_addLink').css('outline', '3px solid blue');
			//
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

			//prepare data and send by ajax
			var serializedForm = $( "#lbx_form_addlink" ).serialize();
			console.info("serialized data: { %s }", serializedForm);
			sample_post( serializedForm );
			event.preventDefault();			
			
			//toggleAddButton(false);
			
			return true;
		}else{
			console.log('fill the form properly!');
			$('#lbx_formErrors').prop( "hidden", false );
		}
		
		event.preventDefault();
		
	});
	// END / adding link routine (submit event)

			
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