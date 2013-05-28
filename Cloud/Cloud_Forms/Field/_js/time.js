jQuery( function($){
	$('.field.type-time').each( function(){
		var $field = $(this); 
		
		$field.find( 'input.timepicker' ).timepicker({
			showInputs : true 		
		}); 
		var $dropdown = $field.find( '.dropdown-menu' ); 
		$dropdown.find( 'input' ).attr('name', '' ); 
		
	}); 
});