jQuery( function($){
	$('.field.type-toggle .input input').each( function(e){
		var input = $(this); 
		var field = input.parents( '.field.type-toggle' );
		var section = field.parents( '.section, .metabox' ); 
		var is_subfield = field.parents( '.field' ).size() > 0 ; 
		
		var data_to_show = input.data('show'); 
		var data_to_hide = input.data('hide');
		var in_table_based_layout = input.parents('.table-layout.section').size() > 0 ;
		function parse_data( data, parent_key ){
			var fields = $();
			if ( typeof data === 'object' ){
				for( key in data ){
					var field_to_add ;
					if ( typeof data[ key ] == 'object' ){
						var fields_to_add = parse_data( data[ key ], key ) ; 
					} else {
						if ( parent_key ){
							var fields_to_add = section.find( '.field_slug-'+parent_key+' .subfield_slug-' + data[key]  ); 
						} else {
							if ( is_subfield ){
								var fields_to_add = field.parents( '.group' ).find( '.subfield_slug-'+data[key] ); 
							} else {
								var fields_to_add = section.find( '.field_slug-'+data[key] ); 						
							}
						}
					}
					fields = fields.add( fields_to_add );
				}
			} else if ( data ) {
				var field_to_add ;
			
				if ( is_subfield ){
					var fields_to_add = field.parents( '.group' ).find( '.subfield_slug-'+data ); 
				} else {
					console.log( 'adding .field_slug-'+data ); 
					var fields_to_add = section.find( '.field_slug-'+data ); 						
				}	
				fields = fields.add( fields_to_add );
						
			}
			return fields ;
		}	
		
		var fields_to_show = parse_data( data_to_show ) ;
		var fields_to_hide = parse_data( data_to_hide ) ;
		function show_fields( fields, animate ){
			if ( animate ) {
				if ( in_table_based_layout ){
					fields.fadeIn('fast');
				} else {
					fields.slideDown('fast'); 			
				}
			} else {
				fields.show()
			}
		}
		function hide_fields( fields , animate ){
			if ( animate ) {
				if ( in_table_based_layout ){
					fields.fadeOut('fast');
				} else {
					fields.slideUp('fast'); 			
				}
			} else {
				fields.hide()
			}	
		}
		function toggle_fields( show, animate ){
			if ( show ){			
				show_fields( fields_to_show, animate ) ;
				hide_fields( fields_to_hide, animate ) ;			
							
			} else {
				show_fields( fields_to_hide, animate ) ;
				hide_fields( fields_to_show, animate ) ;
			}
		}
		if ( fields_to_show.size() > 0 || fields_to_hide.size() > 0 ){
			var prev_state = input.is(':checked' ); 
			var changed = false; 
			input.on({
				click : function(){		
					input.focus();
				}, 
				focus: function(){
					var current_state = input.is(':checked' ); 
					var prev_state = current_state ;
				 	if ( current_state ){
					 	toggle_fields( true, true ) ;
					} else {
					 	toggle_fields( false, true ) ;				
					}
				},
				blur: function(){	
					setTimeout( function(){
						var current_state = input.is(':checked' );
					 	toggle_fields( current_state, true ) ;
					}, 200 );

				}
			});	
			
		 	if ( input.is( ':checked' ) ){
			 	toggle_fields( true, true ) ;
			} else {
			 	toggle_fields( false, true ) ;				
			}
			
		}

	});
});