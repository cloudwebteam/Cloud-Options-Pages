<?php class WP_Cloud_Field_Atts {
	public static function get( $spec ){
		$top_level_slug = $spec['top_level_slug'] ; 
		$subpage_slug = $spec['subpage_slug'];
		$section_slug = $spec['section_slug'];
		$field_slug = $spec['field_slug']; 

		$input_id = $subpage_slug . '_' . $section_slug . '_' . $field_slug ;			
		$name = $subpage_slug . '['.$section_slug.']['.$field_slug.']' ;
		 
		$value = self::get_value( $top_level_slug, $subpage_slug, $section_slug, $field_slug );

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
		
		return $info;	
	}
	protected static function get_value( $top_level_slug, $subpage_slug, $section_slug, $field_slug ){
		$Forms = Cloud_Forms_WP::get_instance(); 
		return $Forms->get_saved_data( $top_level_slug, $subpage_slug, $section_slug, $field_slug ); 
	}		
}