<?php 
class Field_Type {
	public static $default_type = 'text' ;
	public static $default_layout ; 
	public static $class_prefix = 'Cloud_Field_' ; 
	protected static $default_value ;
	protected $layout = 'default'; // fallback layout type
	protected $layouts = array();
	
	protected $info ; 
	protected $label ; 
	protected $field ; 
	protected $attributes = array();
	
	protected function __construct( $class_name, $args){
		// where is the field being placed? Metabox? Options page? Somewhere else?
		$this->context = isset( $args['context'] ) ? $args['context'] : 'options-page' ; 
		
		// set type for reference if needed
		$this->type = $args['info']['type'] ;
		
		// setup basic field attributes
 		$this->info = $this->get_field_info($args);
 		
 		// create standard label to be placed
		$this->label = $this->get_label( $this->info ); 
		
		// create standard description to be placed
		$this->description = $this->get_description( $this->info );
		
		// create attributes to be set on the field container
		$this->attributes = 'class="' . implode(' ', $this->get_attributes( $this->info ) ).'"';
		
		if ( $this->info['cloneable'] ){
			// create field ( this method should be implemented by each field subclass )
			$this->make_cloneable( $args );
		} else {
			// create field ( this method should be implemented by each field subclass )
			$this->field = $this->get_field_html( $args ); 		
		}
		
		// get components needed to build the field ( optionally implemented )
		$this->get_field_components( $args );
		
		// figure out what layout to use
		$layout = $this->get_layout( $class_name, $this->info );
 		// echo the field using the appropriate layout function. 
		$this->$layout( $args ); 
	}
	public function standard(){
		echo 'this field needs a display function!';
	}
	public function enqueue_field_scripts_and_styles(){
		self::register_scripts_and_styles( get_called_class() );  // would be best, but only in 5.3... should fallback and not break
	
	}
	/****
		* standardizes handling of a field attributes
	****/
	public static function get_class_name( $type ){
		return self::$class_prefix . $type ;
	}
	public function get_field_info( $args ){
		$Options_Page = Cloud_Options_Pages::get_instance(); 
		$info = array(); 
		switch ($this->context ){
			case 'options-page' :
				$top_level_slug = $args['top_level'];		
				$page_slug = $args['subpage'];
				$section_slug = $args['section'];
				$field_slug = $args['field']; 
				$input_id = $section_slug . '_' . $field_slug ;				
				$enabled = $Options_Page->is_enabled( $top_level_slug, $page_slug, $section_slug, $field_slug ); 
				$user_enabled_override_name = $page_slug.'[enabled]['.$section_slug.']['.$field_slug.']';
				$value = $Options_Page->get_option( $top_level_slug, $page_slug, $section_slug, $field_slug ); 
				$name =  $page_slug.'['.$section_slug.']['.$field_slug.']'; 
				// only interior here, so it can be added to
				$to_retrieve = '"'. $page_slug.'", "'. $section_slug . '" , "'. $field_slug.'"' ;			
				$function_to_retrieve = 'get_theme_options' ;
				break; 
			case 'metabox' :
				global $post ;
			
				$metabox_slug = $args['metabox'];
				$field_slug = $args['field']; 
				$input_id = $metabox_slug . '_'. $field_slug ;
				$enabled = $Options_Page->is_metabox_option_enabled( $post->ID, $metabox_slug, $field_slug ); 
				$user_enabled_override_name =  $metabox_slug.'[enabled]['.$field_slug.']';
				$value = $Options_Page->get_metabox_option( $post->ID, $metabox_slug, $field_slug ); 
				$name =  $metabox_slug . '['.$field_slug.']'; 
				// only interior here, so it can be added to
				$to_retrieve = '"'. $post->ID.'", "'. $metabox_slug . '" , "'. $field_slug.'"' ;
				$function_to_retrieve = 'get_metabox_options' ;
				break ;
		}
		$cloneable =  isset( $args['info']['cloneable'] ) ? $args['info']['cloneable'] : false;
		$subfield_slug = isset( $args['subfield'] ) ? $args['subfield'] : '' ; 
		$default_value =  isset( $args['info']['default'] ) ? $args['info']['default'] : ''; 
			
		// part of a group?
		if ( $subfield_slug ){
			$group_number = isset( $args['group_number'] ) ? $args['group_number'] : 0 ;		
			$value = isset( $value[$group_number][$subfield_slug] ) ? $value[$group_number][$subfield_slug] : ''; 
			$name = $name . '['.$group_number.']['.$subfield_slug.']'; 	
			$to_retrieve = 	$function_to_retrieve .'( '. $to_retrieve .', '. $group_number .' , "' .$subfield_slug .'" ) ';	
			$cloneable = false;
			if ( $this->context == 'options-page' ){
				$enabled = true ; 
			} else if ( $this->context == 'metabox' ){
				$enabled = $Options_Page->is_metabox_option_enabled( $post->ID, $metabox_slug, $field_slug, $group_number, $subfield_slug ); 
			}
			$user_enabled_override_name = $user_enabled_override_name . '['.$group_number.']['.$subfield_slug.']' ;
			$default_value = isset( $saved_default ) ? $saved_default : ( isset( $args['info']['default'] ) ? $args['info']['default'] : '' ); 
		// plain old field
		} else {
			$to_retrieve = 	$function_to_retrieve .'( '. $to_retrieve .' ) ';			
		}
		
		$info['title'] = $args['info']['title'];
		$info['to_retrieve'] = 	$to_retrieve;				
		$info['cloneable'] = $cloneable ;
		
		// setup lock on various things we might want to lock down. 			 
		if ( $args['info']['_lock'] ){
			$info['clone_controls'] = false;
			$info['code_link'] = false;
			$info['sort'] = false;
		} else {
			$info['clone_controls'] = isset( $args['info']['clone_controls'] ) ? $args['info']['clone_controls'] : true; 
			$info['code_link'] = isset( $args['info']['code_link'] ) ? $args['info']['code_link'] : true; 
			$info['sort'] = isset( $args['info']['sort'] ) ? $args['info']['sort'] : true; 
		}
	
		$info['name'] = $name; 
		$info['description'] = isset( $args['info']['description'] ) ? $args['info']['description'] : null;
		$info['id']   = $input_id;
		$info['value'] = $value ? $value : $default_value;
		$info['default'] = $default_value; 
		$info['settable_defaults'] = isset( $args['info']['settable_defaults'] ) ? $args['info']['settable_defaults'] : false;
		
		$info['enabled'] = $enabled ;
		$info['enabled_name'] = $user_enabled_override_name; 		
		$info['parent_layout'] = isset( $args['parent_section_layout'] ) ? $args['parent_section_layout'] : 'standard' ;
		$info['layout'] = isset ($args['info']['layout'] ) ? $args['info']['layout'] : 'default';
		$info['prefix'] = $Options_Page->prefix; 		
		$info['fields'] = isset( $args['info']['fields'] ) ? $args['info']['fields'] : ''; 
		$info['width'] = isset( $args['info']['width'] ) ? $args['info']['width'] : 6; 
		$info['is_subfield'] = $subfield_slug !== '' ? true : false;

		return $info;
	}
	
	// each field needs to know how to create itself. This is where they do it. 
	protected function get_field_html($args){ echo 'this field needs to implement get_field_html()'; }
	
	// optional, allows each field to create its own necessary components
	protected function get_field_components( $args ){}
	
	// wraps the field/fields in html that makes it cloneable
	protected function make_cloneable( $args ){
		$Options_Page = Cloud_Options_Pages::get_instance(); 		
		$top_level_slug = $args['top_level'];		
		$page_slug = $args['subpage'];
		$section_slug = $args['section'];
		$field_slug = $args['field']; 	
		
		$name = $this->info['name']; 
		$value = $this->info['value'] ;
		$parent_to_retrieve = $this->info['to_retrieve']; 
		$this->saved_values = $Options_Page->get_option( $top_level_slug, $page_slug, $section_slug, $field_slug ); 
		$this->args = $args;		
		
		$this->info['settable_defaults'] = false ;


		$clones = array(); 
		if ( is_array( $this->saved_values ) ){

			foreach ( $this->saved_values as $clone_number => $clone_value ){
				$parent_to_retrieve = 	'get_theme_options( "'. $page_slug.'", "'. $section_slug . '" , "'. $field_slug.'" , ' . $clone_number .' )';	
				$clones[$clone_number] = $this->make_clone( $clone_number, $clone_value, $name, $parent_to_retrieve ); 
			} 
		} else {
			$clones[0] = $this->make_clone( 0, '', $name, $parent_to_retrieve); 
		}
		$output = '';
		$output .= '<ul class="cloneable cf">';
		foreach( $clones as $clone_number => $clone ){
			$output .= '<li class="clone cf">' ;
			$output .= '<div class="number">'.($clone_number+1).'</div>';
			$output .= $clone;
			if ( $this->info['clone_controls'] ){
				$output .= '<div class="add-remove"><a class="remove">-</a><a class="add">+</a></div>';
			}
			$output .= '</li>';
		}
		$output .= '</ul>';
		
		$this->field = $output;
		$this->info['to_retrieve'] = $parent_to_retrieve ; 
		 
	}
	protected function make_clone( $clone_number, $clone_value, $name, $to_retrieve  ){	
	
		$this->info['name'] = $name.'['.$clone_number.']';
		$this->info['value'] = $clone_value;	
		$this->info['to_retrieve'] = $to_retrieve ;
		$clone = $this->get_field_html( $this->args ); 

		return $clone . self::get_copy_to_use( $this->info );
	}
	protected function get_label($field_info){
		$to_use = self::get_copy_to_use( $field_info); 
		$label = $to_use."<label class='title' for='".$field_info['prefix'] . $field_info['id'] . "' >" . $field_info['title'] ."</label>";
		return $label;
	}
	protected static function get_copy_to_use( $field_info ){
		if ($field_info['code_link'] ){
			$to_use = "<span class='copy_to_use'><a rel='copy_to_use'>&#36;use;</a><span class='copy-container'><input class='copy' type='text' value='".$field_info['to_retrieve']."' /></span></span>";
		} else {
			$to_use = '';
		}
		return $to_use;
	}
	protected function get_description( $field_info ){
		$description = isset( $field_info['description']) && $field_info['description'] !== '' ? '<span class="description">'.$field_info['description'] . '</span>' : '';
		return $description;
	}
	protected function get_attributes( $field_info ){
		$classes = array(); 
		$classes[] = 'field' ;
		$classes[] = 'cf' ;
		$classes[] = 'type-'.$this->type ;
		$classes[] = $this->info['sort'] ? '' : 'no-sort'; 
		if ( $field_info['parent_layout'] === 'grid' ){
			$classes[] = isset( $field_info['width'] ) ? 'span' . $field_info['width'] : 'span6';
		}
		$classes[] = isset( $field_info['style'] ) ? $field_style['style'] :  '' ;
		
		return $classes ;
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
	protected function get_layout( $class_name, $field_info ){

		if ( isset( $field_info['layout'] ) ){
			if ( is_array( $field_info['layout'] ) ){
				$layout = 'custom';
			} else if ( is_callable( $class_name, $field_info['layout'] ) ){
				$layout = $field_info['layout'];
			} else {
				$layout = self::$default_layout; 		
			}
		} else {
			$layout = self::$default_layout; 
		}

		if ( $field_info['parent_layout'] === 'standard' ) {
			$layout = 'standard'; 
		}
		if ( $field_info['is_subfield'] ){
			$layout = 'custom' ;
		}
		if ( $this->context == 'metabox' ){
			if ( is_callable( array( $this, 'metabox' ) ) ){
				$layout = 'metabox' ;
			} else {
				$layout = 'custom' ;
			}
		}
		return $layout;
	}
	protected static function register_scripts_and_styles( $class_name, $subfield_types = null ){
		$field_type = substr( $class_name, strlen(Field_Type::$class_prefix) ); 
		if ( $field_type ){			
			if ( file_exists( dirname( __FILE__ ).'/'.basename( __FILE__, '.php' ) . '/_js/'.$field_type.'.js' ) ){
				wp_enqueue_script( $field_type, self::get_include_path(). '/_js/'.$field_type.'.js', array( 'jquery', 'Options_Pages' ), '');
			} 
			if ( file_exists( dirname( __FILE__ ).'/'.basename( __FILE__, '.php' ) . '/_css/'.$field_type.'.css' ) ){
				wp_enqueue_style( $field_type, self::get_include_path(). '/_css/'.$field_type.'.css', array( 'Options_Pages' ));
			}
		}
		if ( is_array( $subfield_types ) && sizeof( $subfield_types ) > 0 ){
			
		
			foreach ( $subfield_types as $subfield_type ){
				$subfield_class = Field_Type::get_class_name( $subfield_type ) ; 
				if ( class_exists( $subfield_class ) ){
					call_user_func( array( $subfield_class, 'enqueue_field_scripts_and_styles' ) ); 		
				}
			}			
		}
	}
	public static function get_include_path(){
		// Cloud-Theme / cloud / core / 				/   Cloud_Options_Pages /   Field_Type           	
		return Cloud_Options_Pages::get_include_path().'/'. basename(__DIR__) . '/'. basename( __FILE__, '.php') ;
	} 	
}