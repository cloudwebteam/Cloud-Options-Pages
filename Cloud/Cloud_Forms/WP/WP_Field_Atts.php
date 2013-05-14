<?php class WP_Cloud_Field_Atts {
	public static function get( $spec ){
		$is_metabox = isset( $spec['metabox_slug'] ); 
		if ( $is_metabox ){
			$metabox_slug = $spec['metabox_slug'] ; 
			$field_slug = $spec['field_slug']; 			
			
			$input_id = $metabox_slug . '_' . $field_slug ;			
			$name = $metabox_slug . '['.$field_slug.']' ;
			$value = self::get_metabox_value( $_GET['post'], $metabox_slug, $field_slug );
		 
			$to_retrieve = array( $_GET['post'], '"'.$metabox_slug.'"', '"'.$field_slug.'"' ); 			
		} else {
			$top_level_slug = $spec['top_level_slug'] ; 
			$subpage_slug = $spec['subpage_slug'];
			$section_slug = $spec['section_slug'];
			$field_slug = $spec['field_slug']; 
			
			$input_id = $subpage_slug . '_' . $section_slug . '_' . $field_slug ;			
			$name = $subpage_slug . '['.$section_slug.']['.$field_slug.']' ;
			$value = self::get_page_value( $subpage_slug, $section_slug, $field_slug );
		 
			$to_retrieve = array( '"'.$subpage_slug .'"', '"'.$section_slug.'"', '"'.$field_slug.'"' ); 				
		}

		$cloneable =  isset( $spec['cloneable'] ) ? $spec['cloneable'] : false;
		$default_value =  isset( $spec['default'] ) ? $spec['default'] : ''; 
			

		// part of a group?
		if ( isset( $spec['subfield_slug'] ) && $spec['subfield_slug']){
			$subfield_slug = isset( $spec['subfield_slug'] ) ; 
			$group_number = isset( $spec['group_number'] ) ? $spec['group_number'] : 0 ;		
			$value = $value && isset( $value[$group_number][$subfield_slug] ) ? $value[$group_number][$subfield_slug] : ''; 
			$name = $name . '['.$group_number.']['.$subfield_slug.']'; 	
			$input_id = $input_id . '_' . $subfield_slug . '-' .$group_number ;				

			$cloneable = false;
			$to_retrieve[] = '"'.$group_number.'"' ;
			$to_retrieve[] = '"'.$subfield_slug.'"' ; 			
		}

		$info = array(); 		
		$info['title'] = $spec['title'];
		$info['cloneable'] = $cloneable ;
	
		$info['clone_controls'] = isset( $spec['clone_controls'] ) ? $spec['clone_controls'] : true; 
		$info['sort'] = isset( $spec['sort'] ) ? $spec['sort'] : true; 
	
		$info['name'] = $name; 
		$info['description'] = $spec['description'] ;
		$info['id']   = Cloud_prefix . $input_id;
		$info['value'] = $value !== false && $value !== null ? $value : $default_value;
		$info['default'] = $default_value; 
		
		$info['layout'] = isset ($spec['layout'] ) ? $spec['layout'] : 'default';
		$info['width'] = isset( $spec['width'] ) ? $spec['width'] : 6; 
		$info['save_json'] = false ;
		$info['is_subfield'] =  isset( $spec['subfield_slug'] ) && $spec['subfield_slug'] ? true: false ;
		$info['in_metabox'] = $is_metabox ; 
		
		$info['to_retrieve'] = $is_metabox ? 'get_metabox_options( '.implode(', ',$to_retrieve ) .'); ' : 'get_theme_options('.implode( ', ', $to_retrieve ).');'; 
		return $info;	
	}
	protected static function get_page_value( $subpage_slug, $section_slug, $field_slug ){
		$Forms = Cloud_Forms_WP::get_instance(); 

		return $Forms->get_page_data( $subpage_slug, $section_slug, $field_slug ); 
	}		
	protected static function get_metabox_value( $post_id, $metabox_slug, $field_slug ){
		$Forms = Cloud_Forms_WP::get_instance(); 
	
		return $Forms->get_metabox_data( $post_id, $metabox_slug, $field_slug ); 
	}
}