jQuery( function($){

	$('.field.type-startend .datepicker').each( function(){
		var field = $(this).parents( '.field.type-startend' );	
		var is_start = $(this).hasClass( 'start' );
			
		var dateFormat = $(this).data('dateformat'); 	
		var timeFormat = $(this).data('timeformat'); 
		
		var end = field.find( 'input.end' ); 
		var start = field.find( 'input.start' );	

		var input = $(this) ;
		if( input.val() ){
			var startingValue = new Date( input.val()*1000 ) ;
		} else {
			var startingValue = false ;
		}
					
		var onClose = function( time, instance ){
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
				if ( is_start ){
					end.datepicker('option', 'minDate', start.datepicker( 'getDate' ) );
				}
			} else {
				input.siblings( '.timestamp' ).val( '' ) ;			
			}
		};

		input.datepicker({
			dateFormat : dateFormat,
			timeFormat : timeFormat,
			onClose : onClose
		}); 
		if ( startingValue ){
			input.datepicker('setDate', startingValue )  ;
		}
				
	}); 	
	$('.field.type-startend .timepicker').each( function(){
		var field = $(this).parents( '.field.type-startend' );	
		var is_start = $(this).hasClass( 'start' );			

		var timeFormat = $(this).data('timeformat'); 
		
		var is_start = $(this).hasClass( 'start' );
		if ( is_start ){
			var end = field.find( 'input.end' ); 
			var start = field.find( 'input.start' );
			var onChange = function( e ){
				if (end.val() == ''  ) {
					end.timepicker( 'setTime', e.time.value );
				}
			};
		} else {
			var onChange = false ;
		}
		$(this).timepicker().on( 'changeTime.timepicker', onChange ) ; 
	}); 	
});