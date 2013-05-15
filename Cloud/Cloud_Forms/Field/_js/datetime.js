jQuery( function($){

	$('.field.type-datetime').each( function(){
		var dateFormat = $(this).data('dateformat'); 	
		var timeFormat = $(this).data('timeformat'); 
		var $field = $(this) ;
		var $datepicker = $field.find( '.datepicker' ); 
		var $timepicker = $field.find( '.timepicker' ); 
	
		var startingDate = new Date( $datepicker.val()*1000 ) ;	
		var $datetime = $field.find( '.datetime' ); 
		function set_combined_datetime(){
			$datetime.val( $datepicker.val() + ' ' + $timepicker.val() ) ;
		}		
		$datepicker.datepicker({
			dateFormat : dateFormat,
			onClose : function( time, instance ){
				if ( time ){			
					var year = instance.selectedYear;
					var month = instance.selectedMonth; 
					var day = instance.selectedDay; 
					var date_object = new Date( year, month, day ) ; 
					
					set_combined_datetime(); 
				} else {
					$datepicker.siblings( '.timestamp' ).val( '' ) ;
				}				
			}	
		});
		console.log( 'setup timepicker' ); 
		$timepicker.timepicker({
			showInputs : true, 
			defaultTime: false, 
			
		}).on( 'hide.timepicker' , function(e){
			set_combined_datetime();
		}) ;		
		var $dropdown = $field.find( '.dropdown-menu' ); 		
		$dropdown.find( 'input' ).attr('name', '' ); 
		
		if ( $datetime.val() ){ 
			var dateValue = new Date( $datetime.val() ); 
			$datepicker.datepicker('setDate', dateValue )  ;
			$timepicker.timepicker( 'setTime', dateValue.toLocaleTimeString() );
		}
	
		 
	}); 

});