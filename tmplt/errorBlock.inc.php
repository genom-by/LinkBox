	<?if($actionStatus == 'error'){?>
	<div id="obus_formErrors" class='alert alert-danger' >
	<a class="close" href="#" onclick="$('#obus_formErrors').prop('hidden', true);">x</a>
	<p id="obus_register_baloon"><?=$message?></p>
	</div>	
	<?}elseif($actionStatus == 'success'){?>
	<div id="obus_formErrors" class='alert alert-success' >
	<a class="close" href="#" onclick="$('#obus_formErrors').prop('hidden', true);">x</a>
	<p id="obus_register_baloon"><?=$message?></p>
	</div>	
	<?}?>