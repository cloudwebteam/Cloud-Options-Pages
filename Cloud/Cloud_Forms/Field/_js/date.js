jQuery( function($){
	$('.field.type-date .datepicker').each( function(){
		var dateFormat = $(this).data('dateformat'); 	
		var minDate = $(this).data( 'mindate' ); 
		var maxDate = $(this).data( 'maxdate' ); 
			
		var input = $(this) ;
		if ( input.hasClass( 'saves-json') && input.val() ){
			var startingValue = new Date( input.val()*1000 ) ;
		} else if( input.val() ){
			var startingValue = new Date( input.val() ) ;
		} else {
			var startingValue = '' ;
		}
		
		input.datepicker({
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
					input.siblings( '.timestamp' ).val( JSON.stringify( value_to_save ) ) ;
				} else {
					input.siblings( '.timestamp' ).val( '' ) ;
				}	
			}			
		}); 
		if ( typeof minDate !== 'undefined' ){
			input.datepicker('option', 'minDate', minDate )  ;
		} 
		if ( typeof maxDate !== 'undefined' ){		
			input.datepicker('option', 'maxDate', maxDate )  ;
		}
		input.datepicker('setDate',startingValue )  ;
		
	}); 
});