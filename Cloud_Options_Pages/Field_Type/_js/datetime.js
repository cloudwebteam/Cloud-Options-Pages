jQuery( function($){
	$('.field.type-datetime .datepicker').datetimepicker({
		timeFormat : 'hh:mm tt',
		hourGrid : 4, 
		minuteGrid : 15,
		stepMinute : 5, 
		minDate : new Date( 2012, 11, 26 ),
		maxDate : new Date( 2012, 11, 30 ),		
	}); 
});