jQuery( function($){
	$('.field.type-date .datepicker').each( function(){
		var dateFormat = $(this).data('dateformat'); 	
		
		$(this).datepicker({
			dateFormat : dateFormat,
			minDate : new Date( 2012, 11, 26 ),
			maxDate : new Date( 2012, 11, 30 ),		
		}); 
	}); 
});