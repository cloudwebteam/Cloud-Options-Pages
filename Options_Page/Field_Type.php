<?php 
class Field_Type {
	public static $default_type ;
	public static $default_layout ; 

	private static $default_value ;
	private $layout = 'default'; // fallback layout type
	
	private $layouts = array();
	
	private $name 	= 'DEFAULT_NAME';
	private $value 	= 'DEFAULT_HERE'; 
	
	public function standard(){
		echo 'this field needs a display function!';
	}
	public static function get_layout_function( $layout = null , $field_type = null , $section_layout_type ){
		self::$default_type = 'text'; // fallback field type
		self::$default_layout = 'standard'; // fallback layout type
		self::$default_value = 'default';
		
		// if they passed in an array, rather than a string, route it to the 'custom' function
		if ( is_array( $layout ) ){
			$layout = 'custom'; 
		}
		
		if ( $field_type && method_exists( $field_type , $layout) ){
			$chosen_layout = $layout;
		} else {
			$chosen_layout = self::$default_layout;
		}

		// handles when a parent's layout necessitates a certain fields layout
		switch ( $section_layout_type ){
		
			// make sure if the section is a table ('standard'), then these are table rows ('standard')
			case 'standard' : 
				$chosen_layout = 'standard';
				break;
		
		}

		return $chosen_layout;
		
	}
	public static function get_field_info( $args ){
		$Options_Page = Options_Page::get_instance(); 

		$info = array(); 
		
		$top_level_slug = $args['top_level'];		
		$page_slug = $args['subpage'];
		$section_slug = $args['section'];
		$field_slug = $args['field']; 	
		$name =  $page_slug.'['.$section_slug.']['.$field_slug.']'; 
		
		
		$default_value =  isset( $args['info']['default'] ) ? $args['info']['default'] : ''; 
		$value = $Options_Page->get_option( $top_level_slug, $page_slug, $section_slug, $field_slug ); 

		$info['title'] = $args['info']['title'];
		$info['to_retrieve'] = 'get_theme_options( "'. $page_slug.'", "'. $section_slug . '" , "'. $field_slug.'" )';
		$info['name'] = $name; 
		$info['description'] = isset( $args['info']['description'] ) ? $args['info']['description'] : null;
		$info['id']   = $field_slug;
		$info['value'] = $value ? $value : $default_value;
		$info['parent_layout'] = $args['parent_section_layout'];
		$info['layout'] = isset ($args['info']['layout'] ) ? $args['info']['layout'] : 'default';
		$info['prefix'] = $Options_Page->prefix; 		
		return $info;
	}
	protected static function get_label($field_info){
		$label = "<label for='".$field_info['prefix'] . $field_info['id'] . "' >" . $field_info['title'] . "<a data-to_use='".$field_info['to_retrieve']."' class='to_use'>Code<span class='copy'>".$field_info['to_retrieve']."</span></a></label>";
		return $label;
	}
	protected static function get_layout( $class_name, $field_info ){
	
		if ( isset( $field_info['layout'] ) && is_callable( $class_name, $field_info['layout'] ) ){
			if ( is_array( $field_info['layout'] ) ){
				$layout = 'custom';
			} else {
				$layout = $field_info['layout'];
			}
		} else {
			$layout = self::$default_layout; 
		}
		
		if ( $field_info['parent_layout'] === 'standard' ) {
			$layout = 'standard'; 
		}
		
		return $layout;
	}
}