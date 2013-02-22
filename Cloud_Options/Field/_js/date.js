jQuery( function($){
	$('.field.type-date .datepicker').each( function(){
		var dateFormat = $(this).data('dateformat'); 	
		
		var input = $(this) ;
		var startingValue = new Date( input.val()*1000 ) ;
		
		input.datepicker({
			dateFormat : dateFormat,
			minDate : new Date( 2012, 11, 26 ),
			maxDate : new Date( 2012, 11, 30 ),
			onClose : function( time, instance ){
				var year = instance.selectedYear;
				var month = instance.selectedMonth; 
				var day = instance.selectedDay; 
				var date_object = new Date( year, month, day ) ; 
				
				var timestamp = date_object.getTime() /1000 ;
				var value_to_save = {
					'datetime' : timestamp
				} ;
				input.siblings( '.timestamp' ).val( JSON.stringify( value_to_save ) ) ;
			}			
		}); 
		input.datepicker('setDate',startingValue )  ;
		
	}); 
});