jQuery( function($){

	
	$('.field.type-startend .datetimepicker').each( function(){
		var field = $(this).parents( '.field.type-startend' );	
		var is_start = $(this).hasClass( 'start' );
		var end = field.find( 'input.end' ); 
		var start = field.find( 'input.start' );		
			
		var dateFormat = $(this).data('dateformat'); 	
		var timeFormat = $(this).data('timeformat'); 

		var input = $(this) ;
		var startingValue = new Date( input.val()*1000 ) ;	
		
		var onClose = function( time, instance ){
			if ( time ){
				var year = instance.selectedYear;
				var month = instance.selectedMonth; 
				var day = instance.selectedDay; 
				var hour = instance.settings.timepicker.hour ; 
				var minute = instance.settings.timepicker.minute ; 
				var date_object = new Date( year, month, day, hour, minute ) ; 
				
				var timestamp = date_object.getTime() /1000 ;
				var value_to_save = {
					'datetime' : timestamp
				} ;
				
				input.siblings( '.timestamp' ).val( JSON.stringify( value_to_save ) ) ;			
				if ( is_start ){
					end.datetimepicker('option', 'minDate', start.datetimepicker( 'getDate' ) );
				}
			} else {
				input.siblings( '.timestamp' ).val( '' ) ;			
			}
		};

		input.datetimepicker({
			dateFormat : dateFormat ,
			timeFormat : timeFormat,
			hourGrid : 4, 
			minuteGrid : 15,
			stepMinute : 5,
			onClose : onClose
		})
		input.datetimepicker('setDate',startingValue )  ;

	});	
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
			var onClose = function( timeText, inst ){
				if (end.val() === '') {
					end.val( timeText );
				}
			};
		} else {
			var onClose = false ;
		}
		$(this).timepicker({
			timeFormat : timeFormat,
			hourGrid : 4, 
			minuteGrid : 15,
			stepMinute : 5, 
			showTimezone : false,
			onClose: onClose 
		}); 
	}); 	
});