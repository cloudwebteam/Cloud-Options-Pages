jQuery( function($){
	var all_inputs = $('.field.type-toggle .input');
	$('.field.type-toggle .input').each( function(e){
		var section ;
		var input_container = $(this) ;
		var in_table_based_layout = input_container.parents('.standard.section').size() > 0 ;
		var data_to_show ; 
		var data_to_hide ;
		
		var fields_to_show ;
		var fields_to_hide ;
				
		
		function parse_data( data, parent_key ){
			var fields = $();
			if ( typeof data !== 'undefined' ){
				for( key in data ){
					var field_to_add ;
					if ( typeof data[ key ] == 'object' ){
						var fields_to_add = parse_data( data[ key ], key ) ; 
					} else {
						if ( parent_key ){
							var fields_to_add = section.find( '.field_slug-'+parent_key+' .field_slug-' + data[key]  ); 
						} else {
							var fields_to_add = section.find( '.field_slug-'+data[key] ); 
						}
					}
					fields = fields.add( fields_to_add );
				}
			}
			return fields ;
		}			
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
		function toggle_fields( input ){
			var sibling_inputs = input.parent('label').siblings('label').find( 'input' );
		
			data_to_show = input.data('show'); 
			data_to_hide = input.data('hide');
			
			fields_to_show = parse_data( data_to_show ) ;
			fields_to_hide = parse_data( data_to_hide ) ;
		
			if ( input.is( ':checked' ) ){
				if ( fields_to_show.size() > 0 ){
					show_fields( fields_to_show ) ;
				}
				if( fields_to_hide.size() > 0 ){
					hide_fields( fields_to_hide );				
				}
				sibling_inputs.change(); 				
			} else {
				if ( fields_to_hide.size() > 0 ){		
					show_fields( fields_to_hide ) ;
				} 
				if ( fields_to_show.size() > 0 ){
					hide_fields( fields_to_show );					
				}
			}
		}
				
		if ( input_container.find( 'select' ).size() > 0 ){
			input_container.find( 'select' ).change( function(){
				if ( $(this).parents('.group').size() > 0 ){
					section = $(this).parents('.group'); 
				} else {
					section = $(this).parents('.section'); 
				}				
				var options = $(this).find( 'option' ); 
				options.each( function(){
					var input = $(this); 
					
					toggle_fields( input ) ;
				}); 
			});
			input_container.find( 'select' ).change(); 
		} else {
			input_container.find( 'input' ).each( function(){
				var input = $(this); 
				input.change( function( ){
					if ( $(this).parents('.group').size() > 0 ){
						section = $(this).parents('.group'); 
					} else {
						section = $(this).parents('.section'); 
					}				
					toggle_fields( $(this) ) ;
				});
			});
			input_container.find( 'input' ).change(); 
		}

	});
});