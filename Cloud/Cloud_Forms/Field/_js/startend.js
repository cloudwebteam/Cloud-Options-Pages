CloudField.on( 'init', function( $context ){
	var selector = '.field.type-startend'; 
	var $fields = $( selector, $context ).add( $context.filter( selector ) ); 
	
	$fields.each( function(){
		var field = $(this) ;	
		
		var picker_type = field.find( '.selector' ).data('field_type' ); 
		
		var datepickers = field.find( '.datepicker' ) ; 
		var timepickers = field.find( '.timepicker' ) ; 
		
		function convert_to_saved_value( input, combined_timestamp ){

			if ( combined_timestamp ){
				var is_start = input.hasClass('.start' ); 
				if ( is_start ){	
					var timestamp_input = field.find( '.start .timestamp' );	
					var timeFieldData = field.find( '.timepicker.start' ).data('timepicker' ); 										
					var dateFieldData = field.find( '.datepicker.start' ).data('datepicker') ; 
				} else {
					var timestamp_input = field.find( '.end .timestamp' );				
					var timeFieldData = field.find( '.timepicker.end' ).data('timepicker' ); 
					var dateFieldData = field.find( '.datepicker.end' ).data('datepicker') ;
				}		
				var year = dateFieldData.selectedYear ; 
				var month = dateFieldData.selectedMonth ; 					
				var day = dateFieldData.selectedDay ; 
				var hour = timeFieldData.hour ; 
				var minute = timeFieldData.minute ; 
				var date_object = new Date( year, month, day, hour, minute ) ; 
				var timestamp = date_object.getTime() /1000 ;
				var value_to_save = {
					'date' : timestamp
				} ;
			
				timestamp_input.val( JSON.stringify( value_to_save ) ) ;	
			} else {
				if ( is_start ){	
					var timestamp_input = field.find( '.start .timestamp' );	
				} else {
					var timestamp_input = field.find( '.end .timestamp' );				
				}			
				timestamp_input.val( input.val() );
			}
		}
		datepickers.each( function(){
			var input = $(this) ;
			var is_start = input.hasClass( 'start' );		
			var dateFormat = input.data('dateformat'); 	
		
			if( input.val() ){
				var startingValue = new Date( input.val()*1000 ) ;
			} else {
				var startingValue = false ;
			}
			var end = field.find( 'input.datepicker.end' ); 
			var timestamp_input = input.siblings( '.timestamp' );		
			var onClose = function( time, instance ){
				if ( time ){
					var year = instance.selectedYear;
					var month = instance.selectedMonth; 
					var day = instance.selectedDay; 
					var date_object = new Date( year, month, day ) ; 
					
					var timestamp = date_object.getTime() /1000 ;
					var value_to_save = {
						'date' : timestamp
					} ;
					
					timestamp_input.val( JSON.stringify( value_to_save ) ) ;			
					if ( is_start ){
						console.log( input.datepicker( 'getDate' ) );
						end.datepicker('option', 'minDate', input.datepicker( 'getDate' ) );
					
						if ( end.val() == '' ){
							end.datepicker('setDate', input.datepicker( 'getDate' ) );
						}
					}
				} else {
					timestamp_input.val( '' ) ;			
				}
			};
			input.datepicker({
				dateFormat : dateFormat,
				onClose : onClose
			}); 	
			if ( startingValue ){
				input.datepicker('setDate', startingValue )  ;
			}				
		}); 
			
		timepickers.each( function( e ){
			var input = $(this);
			var is_start = input.hasClass( 'start' );			

			var end = field.find( '.timepicker.end' );
			if ( picker_type !== 'time' ){
				var onChange = function( e ){
					convert_to_saved_value( input, true )
					if ( is_start ){
						if ( end.val() ){
							end.datepicker('option', 'minDate', input.datepicker( 'getDate' ) ) ;
						} else {
							end.datepicker('setDate', input.datepicker( 'getDate' ) );
						}
						convert_to_saved_value( end, true );
					}
				}				
			} else {
				var onChange = function(){
					convert_to_saved_value( input, false ) ;
				} ;
			}
			$(this).timepicker({
				showInputs : false			
			}).on( 'changeTime.timepicker' , onChange ) ; 
		}); 	

				
	}); 	

});