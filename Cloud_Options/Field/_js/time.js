jQuery( function($){
	$('.field.type-time .timepicker').each( function(){
		var timeFormat = $(this).data('timeformat'); 
		$(this).timepicker({
			timeFormat : timeFormat,
			hourGrid : 4, 
			minuteGrid : 15,
			stepMinute : 5, 
			showTimezone : false
		}); 
	}); 
});