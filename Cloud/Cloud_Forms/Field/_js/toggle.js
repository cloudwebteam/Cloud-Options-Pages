CloudField.on( 'init', function( $context ){
	var selector = '.field.type-toggle'; 
	var $fields = $( selector, $context ).add( $context.filter( selector ) ); 
	
	$fields.each( function(e){
		var $field = $(this); 
		var $section = $field.parents( '.section, .metabox' ); 
		var is_subfield = $field.parents( '.field' ).size() > 0 ; 
		var in_table_based_layout = $section.hasClass('.table-layout') ;
				
		var $inputs = $(this).find( 'input' ); 
		var $select = $(this).find( 'select' ); 
		var $prev_item = false; 		
		
		function parse_data( data, parent_key ){
			var $fields = $();
			if ( typeof data === 'object' ){
				for( key in data ){
					var $field_to_add ;
					if ( typeof data[ key ] == 'object' ){
						var $fields_to_add = parse_data( data[ key ], key ) ; 
					} else {
						if ( parent_key ){
							var $fields_to_add = $section.find( '.field_slug-'+parent_key+' .subfield_slug-' + data[key]  ); 
						} else {
							if ( is_subfield ){
								var $fields_to_add = $field.parents( '.group' ).find( '.subfield_slug-'+data[key] ); 
							} else {
								var $fields_to_add = $section.find( '.field_slug-'+data[key] ); 						
							}
						}
					}
					$fields = $fields.add( $fields_to_add );
				}
			} else if ( data ) {
				var $field_to_add ;
			
				if ( is_subfield ){
					var $fields_to_add = $field.parents( '.group' ).find( '.subfield_slug-'+data ); 
				} else {
					var $fields_to_add = $section.find( '.field_slug-'+data ); 						
				}	
				$fields = $fields.add( $fields_to_add );
						
			}
			return $fields ;
		}		
		function show_fields( $fields, animate ){
			if ( animate ) {
				if ( in_table_based_layout ){
					$fields.fadeIn('fast');
				} else {
					$fields.slideDown('fast'); 			
				}
			} else {
				$fields.show()
			}
		}
		function hide_fields( $fields , animate ){		
			if ( animate ) {
				if ( in_table_based_layout ){
					$fields.fadeOut('fast');
				} else {
					$fields.slideUp('fast'); 			
				}
			} else {
				$fields.hide()
			}	
		}	
		function toggle_fields( $toggle_item, animate ){
		
			var data_to_show = $toggle_item.data('show'); 
			var data_to_hide = $toggle_item.data('hide');

			var $fields_to_show = parse_data( data_to_show ) ;
			var $fields_to_hide = parse_data( data_to_hide ) ;
			
			if ( $prev_item ){
				deactivate_item( $prev_item, data_to_show, data_to_hide, animate ); 
			}					
			if ( $fields_to_show.size() > 0 ){
			 	show_fields( $fields_to_show, animate ) ;
			}
			if ( $fields_to_hide.size() > 0 ){			
			 	hide_fields( $fields_to_hide, animate ) ;
			}	
			$prev_item = $toggle_item ; 				
		}		
		function deactivate_item( $prev_item, new_to_show, new_to_hide, animate ){
			if ( ! $prev_item ){
				return ; 
			}
			var prev_to_show = $prev_item.data('show'); 
			var prev_to_hide = $prev_item.data('hide');	
			var fields_to_hide = prev_to_show; 
			var fields_to_show = prev_to_hide ; 
			if ( new_to_show && prev_to_show ){
				fields_to_hide = [] ; 
				for( i in prev_to_show ){
					var found_index = new_to_show.indexOf( prev_to_show[i] ); 
					if( found_index == -1 ){
						fields_to_hide.push( prev_to_show[ i ] ); 
					}
				}
			} 
			if ( new_to_hide && prev_to_hide ){
				fields_to_show = [] ; 
			
				for( i in prev_to_hide ){
					var found_index = new_to_hide.indexOf( prev_to_hide[i] ); 
					if( found_index == -1 ){
						fields_to_show.push( prev_to_hide[ i ] ); 
					}
				}
				
			}	

			var $fields_to_show = parse_data( fields_to_show ) ;
			var $fields_to_hide = parse_data( fields_to_hide ) ;

			if ( $fields_to_show.size() > 0 ){
			 	show_fields( $fields_to_show, animate ) ;
			}
			if ( $fields_to_hide.size() > 0 ){
			 	hide_fields( $fields_to_hide, animate ) ;
			}				
		}
		// radio or checkboxes
		if ( $inputs.size() > 0 ){

			$inputs.on( 'change', function(){
				var $selected = $inputs.filter(':checked' ); 
				if ( $selected.size() > 0 ){
					toggle_fields( $selected , true ); 
				} else {
					deactivate_item( $(this), false, false, true); 
				}
			}); 
				$inputs.each( function(){
					if ( $(this).is(':checked') ){
						toggle_fields( $(this) , true ); 
					} else {	
						deactivate_item( $(this), false, false, false); 
					}
				}); 
			$inputs.filter( ':checked' ).each( function(){
				toggle_fields( $(this) , false ); 			

			}); 
		// dropdown
		} else if ( $select.size() > 0 ){
			$select.on( 'change', function(){
				var $selected = $select.find('option:selected' ); 
				toggle_fields( $selected , true ); 
			}); 
			$select.find( 'option' ).each( function(){
				if ( $(this).is( ':not(:selected)' ) ){
					toggle_fields( $(this) , false ); 			
				}
			}) ; 
			$select.find( 'option:selected' ).each( function(){
				toggle_fields( $(this) , false ); 			
			})
			
		
		}
		

	});
});