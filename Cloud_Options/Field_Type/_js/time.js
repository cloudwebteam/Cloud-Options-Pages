jQuery( function($){
	$('.field.type-time .timepicker').timepicker({
		timeFormat : 'hh:mm tt',
		hourGrid : 4, 
		minuteGrid : 15,
		stepMinute : 5
	}); 
});