jQuery( function($){
	$('.field.type-datetime .datetimepicker').each( function(){
		var dateFormat = $(this).data('dateformat'); 	
		var timeFormat = $(this).data('timeformat'); 
		var input = $(this) ;
		var startingValue = new Date( input.val()*1000 ) ;

		input.datetimepicker({
			dateFormat : dateFormat ,
			timeFormat : timeFormat,
			hourGrid : 4, 
			minuteGrid : 15,
			stepMinute : 5,
			onClose : function( time, instance ){
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
			}	
		})
		input.datetimepicker('setDate',startingValue )  ;
		 
	}); 

});