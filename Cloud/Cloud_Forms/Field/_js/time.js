CloudField.on( 'init', function( $, $context ){
	var selector = '.field.type-time'; 
	var $fields = $( selector, $context ).add( $context.filter( selector ) ); 
	$fields.each( function(){
		var $field = $(this); 
		
		$field.find( 'input.timepicker' ).timepicker({
			showInputs : true 		
		}); 
		var $dropdown = $field.find( '.dropdown-menu' ); 
		$dropdown.find( 'input' ).attr('name', '' ); 
		
	}); 
});