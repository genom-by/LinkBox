function btn_tagSearchClick(ev){
	
	//console.log(ev.target);
	
	var stag = $('#tagsearch').val();
	//console.log(stag);
	
	if(stag.length <= 2){
		console.log('too short');
		return false;
	}
	
	var question_ = 'tagsearchlike';	
	var id_ = '-1';	
	var table_ = stag;	

	divID='#lbx_tagslikeres';
	
	_post({action: 'inquire', id:id_, table:table_, question:question_});		
	
	function _post(data_){

		//console.table(data_);
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


function inp_tagsearch_onkeypress(e){
	if(e.keyCode === 13){
		e.preventDefault(); // Ensure it is only this code that run
		
		btn_tagSearchClick(e);
		
		//alert("Enter was pressed was presses");
	}
}


function btn_taglinkFilterClick(){
	
console.log('btn_taglinkFilterClick');	
	var stagsIDs = new Array();
	var stagLbls = $('#lbx_tagslikeres label.active');
	console.log('lend:'+stagsIDs.length);	
	$.each(stagLbls, function(i,v){
		stagsIDs.push( $(v).attr('data-tagid') );
	});
	console.log('len:'+stagsIDs.length);	
	if(stagsIDs.length < 1){
		shakeTags();

		$('#ttTagAlert').tooltip("show");
		return false;
	}else{
		$('#ttTagAlert').tooltip("destroy");	
	}
	console.table(stagsIDs);
	var tagsString = stagsIDs.join();

	var action_ = 'pageUpdate';	
	var id_ = tagsString;	
	var table_ = 'linkTagsFiltered';	
	
	divID='#lbx_LinksTable';
	
	_post({action: action_, id:id_, table:table_});		
	
	function _post(data_){

		//console.table(data_);
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

// trembling effect
function shakeTags(){
    $('#lbx_tagslikeres').animate({
        'margin-left': '-=5px',
        'margin-right': '+=5px'
    }, 50, function() {
        $('#lbx_tagslikeres').animate({
            'margin-left': '+=5px',
            'margin-right': '-=5px'
        }, 50, function() {
            //and so on...
        });
    });
}