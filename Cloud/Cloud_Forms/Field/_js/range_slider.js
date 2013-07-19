CloudField.on( 'init', function( $, $context ){
	var selector = '.field.type-range_slider'; 
	var $fields = $( selector, $context ).add( $context.filter( selector ) ); 
	$fields.each( function(){
		var $slider = $(this).find( '.range-slider' ); 
		var $min_input = $(this).find( '.range-slider-min' ); 
		var $max_input = $(this).find( '.range-slider-max' ); 		
		
		var min = $(this).data( 'min' ) ; 
		var max = $(this).data( 'max' ) ; 		
		var step = $(this).data('step' );
		
		if ( typeof( min ) == 'undefined' ){
			min = 0; 
		}
		if ( typeof( max ) == 'undefined' ){
			max = 100; 
		}		 
		if ( typeof( step ) == 'undefined' ){
			step = 1; 
		}	
		$slider.slider({
			range : true, 
			max : max, 
			min : min, 
			step : step, 
			change : function( e, ui ){				
				if ( $( ui.handle ).index() == 1 ){
					$min_input.val( ui.value ) ;
				} else {
					$max_input.val( ui.value ) ; 
				}
			}
		}); 			 		
	});
});