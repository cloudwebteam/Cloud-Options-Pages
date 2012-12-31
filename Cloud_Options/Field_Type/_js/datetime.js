jQuery( function($){
	$('.field.type-datetime .datepicker').each( function(){
		var dateFormat = $(this).data('dateformat'); 	
		var timeFormat = $(this).data('timeformat'); 
		$(this).datetimepicker({
			dateFormat : dateFormat,
			timeFormat : timeFormat,
			hourGrid : 4, 
			minuteGrid : 15,
			stepMinute : 5, 
			minDate : new Date( 2012, 11, 26 ),
			maxDate : new Date( 2012, 11, 30 ),		
		}); 
	}); 

});