<?php class Cloud_Field_Atts {
	public static function get( $spec ){
		$form_slug = $spec['form_slug'];
		$section_slug = $spec['section_slug'];
		$field_slug = $spec['field_slug']; 
		
		if ( $section_slug ){
			$input_id = $form_slug . '_' . $section_slug . '_' . $field_slug ;			
			$name = $section_slug.'['.$field_slug.']' ;
		// if its a standAlone form
		} else {
			$input_id  = $form_slug . '_'.$field_slug ; 
			$name = $field_slug ;
		}
 
		$value = self::get_value( $field_slug, $section_slug, $form_slug );
		$cloneable =  isset( $spec['cloneable'] ) ? $spec['cloneable'] : false;
		$default_value =  isset( $spec['default'] ) ? $spec['default'] : ''; 
			
		// part of a group?
		if ( isset( $spec['subfield_slug'] ) && $spec['subfield_slug']){
			$subfield_slug = isset( $spec['subfield_slug'] ) ? $spec['subfield_slug'] : '' ; 
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
		$info['disabled'] = isset( $spec['disabled'] ) && $spec['disabled'] ? 'disabled' : '' ; 
		
		$info['layout'] = isset ($spec['layout'] ) ? $spec['layout'] : 'default';
		$info['width'] = isset( $spec['width'] ) ? $spec['width'] : 6; 
		$info['save_json'] = false ;
		$info['is_subfield'] =  isset( $spec['subfield_slug'] ) && $spec['subfield_slug'] ? true: false ;
		
		return $info;	
	}
	protected static function get_value( $field_slug, $section_slug = '' , $form_slug = '' ){
		if ( isset( $_REQUEST['form_id'] ) && $_REQUEST['form_id'] === $form_slug ){
			if ( $field_slug && $section_slug && $form_slug ){
				return isset( $_REQUEST[$section_slug][$field_slug] ) ? $_REQUEST[$section_slug][$field_slug] : false ; 	
			} else if ( $field_slug && $section_slug ){
				return isset( $_REQUEST[$section_slug][$field_slug] ) ? $_REQUEST[$section_slug][$field_slug] : false ; 	
			} else {
				return isset( $_REQUEST[$field_slug] ) ? $_REQUEST[$field_slug] : false ; 	
			}
		}
	}	
}