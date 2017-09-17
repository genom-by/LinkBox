// test post ajax function
//
function sample_post( formData ){
	
	$.post('cgi/lbox-test.php',
		{ obus_form_addlink: formData },
		
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
		$('#btn_register').prop( "disabled", true);
		//$('#btn_addLinkCaption').html(' Adding..');
		$('#btn_register').prop( "value", 'Submitting..');
		submitting = true;
	}else{
		$('#btn_register').prop( "disabled", false );
		//$('#btn_addLinkCaption').html(" Add link");
		$('#btn_register').prop( "value", 'Submit');
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
	//$("#inputAgree").checked=false;
	$("#inputAgree").prop( "checked", false );
	$("#btn_register").prop( "disabled", true );
}
// checks if values fit for sending
//
function checkFormBeforeAdding() {

	var mandatoryControls = [];
	var emptyControls = [];
	
	var form = document.forms.obus_registerForm;
	var link = form.elements.link_to_save;
	var linkName = form.elements.link_description;
	
	//console.log("link: %s ; name: %s ; folder # %d: %s",link, linkName, folderObject.value, folderObject.text);
	mandatoryControls.push(link);
	mandatoryControls.push(linkName);
	
	if(link.value.trim()===""){emptyControls.push(link)}
	if(linkName.value.trim()===""){emptyControls.push(linkName)}
	
	//clean controls
	markEmptyControls(mandatoryControls, false);
	
	//are there empty fields?
	if(emptyControls.count() > 0){
		markEmptyControls(emptyControls);
		$('#obus_formErrors p').html( '<strong>Error:</strong> fill the form properly!');
		return false;
	}else{
		$('#obus_formErrors p').html( 'No errors.');
		return true;
	}
}

	var submitting = false;
	
$(function () {
	
	resetForm( );
	
	//adding ckeckbox routine (checked event)
	//
	$( "#inputAgree" ).change(function() {
		var $input = $( this );
		if($input.is( ":checked" )){
			$("#btn_register").prop( "disabled", false );
		}else{
			$("#btn_register").prop( "disabled", true );
		}
	});//.change()

		//adding link routine (submit event)
	//
$( "#obus_registerForm" ).submit(function( event ){ 

    if (! $("#obus_registerForm").valid()) {
        console.log('not valid');
		event.preventDefault();
		//$("#obus_registerForm").validate();
		return;
    }
console.log ('here');	
		// prevent submit btn from doubleclicking
		if(submitting) {
			alert('The form is being submitted, please wait a moment...');
			$('#btn_addLink').prop( "disabled", true );
			//$('#btn_addLink').css('outline', '3px solid blue');
			//
			//event.preventDefault();
			return false;
		}
	
	
		//chech if filled properly...
		if(checkFormBeforeAdding()) {
			console.log('submitting');
			$('#obus_formErrors').prop( "hidden", true );
			
			toggleAddButton(true);

			//prepare data and send by ajax
			var serializedForm = $( "#obus_form_addlink" ).serialize();
			console.info("serialized data: { %s }", serializedForm);
			sample_post( serializedForm );
			event.preventDefault();			
			
			//toggleAddButton(false);
			
			return true;
		}else{
			console.log('fill the form properly!');
			$('#obus_formErrors').prop( "hidden", false );
		}
		
		event.preventDefault();
		
});
// END / submit routine (submit event)

//========= validate ============================== val =================	

$('#obus_registerForm').validate({
rules: {
  
 userName: {
	minlength: 3,
	required: true,
	remote: {
		url: "cgi/ajax.check_username.php?val_type=name",
		type: 'POST',
		delay: 2000     // Send Ajax request every 2 seconds
		}
  },
  
  userEmail: {
	required: true,
	email: true,
	remote: {
		url: "cgi/ajax.check_username.php?val_type=email",
		type: 'POST',
		delay: 2000     // Send Ajax request every 2 seconds
		}
  },  
  
  inputPWD: {
		required: true,
		minlength: 4
	},
	inputPWD2: {
		required: true,
		minlength: 4,
		equalTo: "#inputPWD"
	},

  inputAgree: "required"
  
},
	messages:{
		userName: {
			required: "provide correct name", 
			//remote: jQuery.validator.format("{0} is already in use")
			remote: "This username is already taken! Try another."
		},
		userEmail: {
			required: "provide correct email", 
			//remote: jQuery.validator.format("{0} is already in use")
			remote: "This Email is already taken! Try another."
		}
	},
  /*glyphicon-warning-sign*/	
	highlight: function(element) {
		/*$(element).closest('.control-group').removeClass('success').addClass('error');
		$(element).closest('.form-group').removeClass('has-success').addClass('has-error');
		*/
		//console.log(element);
			//$(element).closest('.form-group').removeClass('has-success has-feedback').addClass('has-error has-feedback');
			//$(element).closest('.form-group').find('span.val_msg').remove();
		$(element).closest('.form-group').removeClass('has-success').addClass('has-error');
					
        $(element).closest('.form-group').find('span.val_msg').removeClass('glyphicon-ok').addClass("glyphicon-remove");
        //$(element).closest('.form-group').find('span.val_msg').removeClass('has-success').addClass("has-error glyphicon-remove");
            //$(element).append('<span class="glyphicon glyphicon-remove form-control-feedback"></span>'); -- glyphicon-ok
	},
	success: function(element) {
		/*element
		/*.text('OK!').addClass('valid')*/
		/*.closest('.control-group').removeClass('error').addClass('success');
		$(element).closest('.form-group').removeClass('has-error').addClass('has-success');
		console.log(element);
		*/
		$(element).closest('.form-group').removeClass('has-error').addClass('has-success');
		
		$(element).closest('.form-group').find('span.val_msg').removeClass('glyphicon-remove').addClass('glyphicon-ok');
		
		$(element).remove();
		//$(element).closest('.form-group').find('label.error').remove(); -- the same!
		
		//$(element).closest('.form-group').find('span.val_msg').removeClass('has-error glyphicon-ok').addClass('has-success glyphicon-ok');
        //$(element).closest('.form-group').find('span.glyphicon').remove();
        //$(element).closest('.form-group').append('<span class="glyphicon glyphicon-ok form-control-feedback"></span>');

	},
	remote: function(){console.log('here2');},
    //errorElement: 'label',
	errorPlacement: function(error, element) {
		//console.log(error[0].innerHTML);
		//console.log( $(element).closest('.form-group').find('label.error') );
		if(error[0].innerHTML != ""){
			error.insertAfter($(element).closest('.form-group').find('span.val_msg') );
			}else{
			$(element).closest('.form-group').find('label.error').remove();
			}
		}
});
//========= validate ============================== val =================	
			
});

function clearValidation(formElement){
 //Internal $.validator is exposed through $(form).validate()
 var validator = $(formElement).validate();
 //Iterate through named elements inside of the form, and mark them as error free
 $('[name]',formElement).each(function(){
   validator.successList.push(this);//mark as error free
   validator.showErrors();//remove error messages if present
 });
 validator.resetForm();//remove error class on name elements and clear history
 validator.reset();//remove all error and success data
}

//used
function refreshValidations(){
	var myForm = document.getElementById("obus_registerForm");
	var validator = myForm.validate();
	validator.resetForm();
	//clearValidation(myForm);
}