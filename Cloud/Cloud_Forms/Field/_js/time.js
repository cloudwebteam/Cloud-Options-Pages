jQuery( function($){
	$('.field.type-time input.timepicker').each( function(){
		$(this).timepicker({
			showInputs : false
		}); 
	}); 
});