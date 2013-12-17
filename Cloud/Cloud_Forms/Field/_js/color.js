CloudField.on( 'init', function( $, $context ){
	var selector = '.field.type-color .color-picker-input'; 
	var $inputs = $( selector, $context ).add( $context.filter( selector ) ); 	
	// miniColors
	$inputs.each( function(){
		if ( $(this).parents('.to-clone').size() > 0 ){
			return;
		}
		$(this).minicolors({
			letterCase: 'uppercase',
			position: 'top left'
		});

	});

	/*
$('.option_enabler.color').click( function(){
		var field_container = $(this).parents('.field').first();

		field_container.find('.color-toggle').toggle('fast');
	}); 
	
    $('.default-swab').click( function(){
		var color_container = $(this).parents('.option');    
		if ( typeof $(this).css('backgroundColor') !== 'undefined' ){
	    	color_container.find('.color-picker-input:visible').miniColors('value',  helpers.rgb2hex($(this).css('backgroundColor')) ); 
	    }
    }); 
*/
});