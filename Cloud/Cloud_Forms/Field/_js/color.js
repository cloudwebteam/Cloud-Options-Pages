CloudField.on( 'init', function( $context ){
	// miniColors
	$(".color-picker-input", $context ).minicolors({
		letterCase: 'uppercase',
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