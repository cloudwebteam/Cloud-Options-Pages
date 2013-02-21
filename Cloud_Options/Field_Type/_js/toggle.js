jQuery( function($){
	$('.field.type-toggle .input input').each( function(e){
		var input = $(this); 
		var data_to_show = input.data('show'); 
		var data_to_hide = input.data('hide');
		var in_table_based_layout = input.parents('.standard.section').size() > 0 ;
		
		function parse_data( data, parent_key ){
			var fields = $();
			for( key in data ){
				var field_to_add ;
				if ( typeof data[ key ] == 'object' ){
					var fields_to_add = parse_data( data[ key ], key ) ; 
				} else {
					if ( parent_key ){
						var fields_to_add = $( '.field_slug-'+parent_key+' .field_slug-' + data[key]  ); 
					} else {
						var fields_to_add = $( '.field_slug-'+data[key] ); 
					}
				}
				fields = fields.add( fields_to_add );
			}
			return fields ;
		}	
		
		var fields_to_show = parse_data( data_to_show ) ;
		var fields_to_hide = parse_data( data_to_hide ) ;
		
		function show_fields( fields ){
			if ( in_table_based_layout ){
				fields.fadeIn('fast');
			} else {
				fields.slideDown('fast'); 			
			}
		}
		function hide_fields( fields ){
			if ( in_table_based_layout ){
				fields.fadeOut('fast');
			} else {
				fields.slideUp('fast'); 
			}
		}
		function toggle_fields( ){
			if ( input.is( ':checked' ) ){
				show_fields( fields_to_show ) ;
				hide_fields( fields_to_hide );				
			} else {
				show_fields( fields_to_hide ) ;
				hide_fields( fields_to_show );					
			}
		}		
		
		input.click( function(){
			toggle_fields( ) ;
		});
		toggle_fields( );
	});
});