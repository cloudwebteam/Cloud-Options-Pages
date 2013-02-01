jQuery( function($){
	$('.field.type-startend .datetimepicker').each( function(){
		var field = $(this).parents( '.field.type-startend' );	
		var is_start = $(this).hasClass( 'start' );
			
		var dateFormat = $(this).data('dateformat'); 	
		var timeFormat = $(this).data('timeformat'); 
		
		var is_start = $(this).hasClass( 'start' );
		var end = field.find( 'input.end' ); 
		var start = field.find( 'input.start' );		
		if ( is_start ){
			var onClose = function( dateText, inst ){
				end.datetimepicker('option', 'minDate', start.datetimepicker( 'getDate' ) );
				if (end.val() === '') {
					end.datetimepicker( 'setDate', dateText );
					
				}
			};
		} else {
			var minDate = start.val() ; 		
			var onClose = false ;
		}		
		$(this).datetimepicker({
			dateFormat : dateFormat,
			timeFormat : timeFormat,
			hourGrid : 4, 
			minuteGrid : 15,
			stepMinute : 5, 
			minDate : minDate,
			onClose : onClose
		}); 	
	}); 
	$('.field.type-startend .datepicker').each( function(){
		var field = $(this).parents( '.field.type-startend' );	
		var is_start = $(this).hasClass( 'start' );
			
		var dateFormat = $(this).data('dateformat'); 	
		var timeFormat = $(this).data('timeformat'); 
		
		var end = field.find( 'input.end' ); 
		var start = field.find( 'input.start' );	
			
		if ( is_start ){
			var minDate = '' ;
			var onClose = function( dateText, inst ){
				end.datepicker('option', 'minDate', start.datepicker( 'getDate' ) );
				if (end.val() === '') {
					end.datepicker( 'setDate', dateText );
				}
			};
		} else {
			var minDate = start.val() ; 
			var onClose = false ;
		}		
		$(this).datepicker({
			dateFormat : dateFormat,
			timeFormat : timeFormat,
			hourGrid : 4, 
			minuteGrid : 15,
			stepMinute : 5, 
			onClose : onClose,
			minDate : minDate
		}); 
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