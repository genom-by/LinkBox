function getSelectedText(elementId) {
    var elt = document.getElementById(elementId);

    if (elt.selectedIndex == -1)
        return null;

    return elt.options[elt.selectedIndex].text;
}

/*
function itinerarySelect_onChange(){
	$('#submitNewPitstop').prop('disabled', false);	
}*/
function btn_showtrans_onClick(){
	var transblock = document.getElementById("folderblock");
	//if(transblock)
}
function onTestPOSToperation(){

}
function btnDelFromTable(table_, id_entry){
	console.info("delete from table: "+table_+" entry id:"+id_entry);
	action_ = 'delete';
	console.info('data to send:', {action:action_ , id:id_entry, table:table_});
	
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
/* save edited row
*/
function btnSaveTableItem(table_, id_entry){
	action_ = 'objectUpdate';
	//console.info('data to send:', {action:action_ , id:id_entry, table:table_});
var rowid = table_ + '_id_' + id_entry;
var cssFtxt = '#' + rowid + ' input';
	var els = $(cssFtxt);
	//console.log(els);
var objArray = {};
	//toggleEditControls(rowid, true,id_entry);
	
	$.each($(els),function(ind,el){

var orm = el.getAttribute("orm")+'2';

	objArray[orm]=el.value;
				//newInput = mountNewInput($(el));
                //$(el).html("");
                //$(el).append(newInput);
		//console.log(objArray['name']);
//console.info('el:', {ele:el, valuu:el.value});		
	})
	if(table_ == 'folder'){
		var selectEl = $('#sel_parent_fld_'+id_entry);
		//var els = $(cssFtxt);

//console.info('select:', {selectEl_:$(selectEl), valuu:$(selectEl).value});
$.each($(selectEl),function(ind,el){
		orm = 'id_parentFolder2';
		objArray[orm]=el.value;
//console.info('el:', {ele:el, valuu:el.value});
})
		
	}
	
	data_ = JSON.stringify(objArray);
console.info('data to send:', {data:data_});
	$.post(
		"cgi/post.routines.php",
		{action:action_ ,id:id_entry, table:table_, data: data_},
		function(data){
		console.log("post returned: "+data.result);
		alert(data.result);
		if (data.result == 'ok' ){
			var domID_td = '#'+table_+'_id_'+id_entry+' td.rowtxt';
			var els2 = $(domID_td);
				$.each($(els2),function(ind,el3){
					$(el3).html( objArray[el3.getAttribute("orm")+'2'] );
				})
			//$(domID_td).html( objArray['name2'] );
			toggleEditControls(rowid, false, id_entry);
		}else{
			console.log('error message: ',data.message);
			alert(data.message);
		}
		}
		,"json"
	);	
}
/* edit entry
*/
function btnEditTableItem(table_, id_entry){
	//console.info("edit into table: "+table_+" entry id:"+id_entry);
	action_ = 'edit';
	console.info('data to send:', {action:action_ , id:id_entry, table:table_});
var rowid = table_ + '_id_' + id_entry;
var cssFtxt = '#' + rowid + ' td.rowtxt';
	var els = $(cssFtxt);
	//console.log(els);

	toggleEditControls(rowid, true, id_entry);
	
	$.each($(els),function(ind,el){
	//console.log(ind, el);
				newInput = mountNewInput($(el));
                $(el).html("");
                $(el).append(newInput);
	})
	var selectEl = '#sel_parent_fld_'+id_entry;
//console.log('el: '+selectEl);	
	//$( selectEl ).prop( "disabled", true ); //Disable
	$( selectEl ).prop( "disabled", false ); //Enable
	
}
/* cancel editing entry
*/
function btnCancelTableItem(table_, id_entry){
	
	action_ = 'cancel';
	//console.info('data to send:', {action:action_ , id:id_entry, table:table_});
var rowid = table_ + '_id_' + id_entry;
var cssFtxt = '#' + rowid + ' td.rowtxt input';
	var els = $(cssFtxt);

	toggleEditControls(rowid, false, id_entry);
	
	$.each($(els),function(ind,el){
		var oldVal = el.getAttribute("valInit");
                $(el).parent().html(oldVal);
                //$(el).append(newInput);
	})
}

// sample edit grid
function mountNewInput(cell) {
	
	var element = document.createElement("input");
	//get string in attribute ref
	/*var attrsString = $(cell).attr("ref");
	if(attrsString != null){
		//split attributes
		var attrsArray = attrsString.split(",");

		var currentObj;
		for(n=0; n < attrsArray.length; n++){
			//separate name of attribute and value attribute
			currentObj = attrsArray[n].split(":");
			$(element).attr($.trim(currentObj[0]), $.trim(currentObj[1]));
		}
	}else{
		indexCell = $(cell).parent().children().index($(cell));
		element.setAttribute("name", "column_"+indexCell);
		element.setAttribute("type", "text");
	}*/
	var DB_column_name = $(cell).attr("orm");
	
	element.setAttribute("orm", DB_column_name);
	element.setAttribute("value", $(cell).text());
	element.setAttribute("valInit", $(cell).text());
	//element.setAttribute("style", "width:" + $(cell).width() + "px");
	element.setAttribute("size", "8");
	$(element).addClass("edit_from_te");
	return element;
}

// oleg_
function toggleEditControls(rowid, to_dsc,id_entry) {
/*var rowid = table + '_id_' + id;
var cssFtxt = '#' + rowid + ' td.txt';*/
var cssFed = '#' + rowid + ' .btnEditBl';
var cssFdsc = '#' + rowid + ' .btnDSCBl';
//console.log(cssFdsc, cssFed);
	var selectEl = '#sel_parent_fld_'+id_entry;
//console.log('el: '+selectEl);	
	//$( selectEl ).prop( "disabled", true ); //Disable
	//$( selectEl ).prop( "disabled", false ); //Enable
	
	if (true === to_dsc) {
//console.log('to_dsc:'+to_dsc);	
		$(cssFed).hide();
		$(cssFdsc).show();
		$( selectEl ).prop( "disabled", false ); //Enable		
        //$(".addnewbtn").hide();
	} else {
		$(cssFed).show();
		$(cssFdsc).hide();
		$( selectEl ).prop( "disabled", true ); //Disable
        //$(".addnewbtn").show();
	}	
}
/*	delete pitstops for selected itinerary
*/
function btn_del_pits_stats_onClick(){
	
	if (! confirm('Are you sure to delete all stations for itinerary?') ) {return;}
	//console.info("delete from table: "+table_+" entry id:"+id_entry);
	var id_entry = $('#itinerarySelectEdit').val();
	console.info('data to send:', {id:id_entry, table:'itin_pitstops_delete'});
	
	$.post(
		"post.routines.php",
		{action:'delete', id:id_entry, table:'itin_pitstops_delete'},
		function(data){
		console.log("post returned: "+data.result);
		alert(data.result);
		if (data.result == 'ok' ){
			var domID = '#pitEditContent';
			$(domID).html( "" );
		}else{
			console.log('error message: ',data.message);
		}
		}
		,"json"
	);	
}

function selPitSeqEdit_onChange(action, itin_id){
	console.log("selected id"+itin_id+" and action is "+action);
	if(action=='pitstops'){
	table_='pits_PitEdit_table';
	divID='#pitEditContent';
	}else if(action=='pitstopsView'){
	table_='pits_PitView_table';
	divID='#pitViewContent';
	}else if(action=='sequencesEdit'){
	table_='seq_SeqEdit_table';
	divID='#seqEditContent';
	}else if(action=='sequencesView'){
	table_='seq_SeqView_table';
	divID='#seqViewContent';
	}else{table:'no_table'; return false;}

	$.post(
		"post.routines.php",
		{action: 'pageUpdate', id:itin_id, table:table_},
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

//SERIALIZE
function serialize(_obj)
{
   // Let Gecko browsers do this the easy way
   if (typeof _obj.toSource !== 'undefined' && typeof _obj.callee === 'undefined')
   {
      return _obj.toSource();
   }
   // Other browsers must do it the hard way
   switch (typeof _obj)
   {
      // numbers, booleans, and functions are trivial:
      // just return the object itself since its default .toString()
      // gives us exactly what we want
      case 'number':
      case 'boolean':
      case 'function':
         return _obj;
         break;

      // for JSON format, strings need to be wrapped in quotes
      case 'string':
         return '\'' + _obj + '\'';
         break;

      case 'object':
         var str;
         if (_obj.constructor === Array || typeof _obj.callee !== 'undefined')
         {
            str = '[';
            var i, len = _obj.length;
            for (i = 0; i < len-1; i++) { str += serialize(_obj[i]) + ','; }
            str += serialize(_obj[i]) + ']';
         }
         else
         {
            str = '{';
            var key;
            for (key in _obj) { str += key + ':' + serialize(_obj[key]) + ','; }
            str = str.replace(/\,$/, '') + '}';
         }
         return str;
         break;

      default:
         return 'UNKNOWN';
         break;
   }
}
//SERIALIZE
$(function(){
	$('form[name=formLinks]').submit(function(event){
		console.info('onSubmit link simple');
		var name = $('#linkName').val();
		var url = $('#linkLink').val();
		var folder = $('#linkFolder').val();
		var tag = $('#linkTag').val();
		console.info('data: ' + name + ' ' + url + ' ' + folder + ' ' + tag);
		var tmpstr = name+url;
		if( (tmpstr == '') || (folder == -1) ){
			event.preventDefault();
			return false;
		}
		console.log('sending..');
	});
})