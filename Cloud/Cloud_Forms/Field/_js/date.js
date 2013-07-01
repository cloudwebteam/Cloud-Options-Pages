CloudField.on( 'init', function( $context ){
	var selector = '.field.type-date .datepicker'; 
	var $inputs = $( selector, $context ).add( $context.filter( selector ) ); 
	
	$inputs.each( function(){
	
		if ( $(this).parents('.to-clone').size() == 0 ){
			
			var $input = $(this) ;
	
			var dateFormat = $(this).data('dateformat'); 	
			var minDate = $(this).data( 'mindate' ); 
			var maxDate = $(this).data( 'maxdate' ); 
				
	
			if ( $input.hasClass( 'saves-json') && input.val() ){
				var startingValue = new Date( input.val()*1000 ) ;
			} else if( $input.val() ){
				var startingValue = new Date( input.val() ) ;
			} else {
				var startingValue = '' ;
			}
			$input.datepicker({
				dateFormat : dateFormat,
				minDate : minDate,
				maxDate : maxDate,
				onClose : function( time, instance ){
					if ( time ){			
						var year = instance.selectedYear;
						var month = instance.selectedMonth; 
						var day = instance.selectedDay; 
						var date_object = new Date( year, month, day ) ; 
						
						var timestamp = date_object.getTime() /1000 ;
						var value_to_save = {
							'datetime' : timestamp
						} ;
						$input.siblings( '.timestamp' ).val( JSON.stringify( value_to_save ) ) ;
					} else {
						$input.siblings( '.timestamp' ).val( '' ) ;
					}	
					$input.blur();
				}			
			}); 
			if ( typeof minDate !== 'undefined' ){
				$input.datepicker('option', 'minDate', minDate )  ;
			} 
			if ( typeof maxDate !== 'undefined' ){		
				$input.datepicker('option', 'maxDate', maxDate )  ;
			}
			$input.datepicker('setDate',startingValue )  ;
		}	
	}); 
});