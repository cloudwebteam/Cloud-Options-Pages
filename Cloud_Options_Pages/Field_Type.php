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
		// enqueue js and css with the field's name
		add_action( 'admin_enqueue_scripts', array( $class_name, 'enqueue_stuff' ) );				
		
		// set type for reference if needed
		$this->type = $args['info']['type'] ;
		
		// setup basic field attributes
 		$this->info = self::get_field_info($args);
 		
 		// create standard label to be placed
		$this->label = $this->get_label( $this->info ); 
		
		// create standard description to be placed
		$this->description = $this->get_description( $this->info );
		
		// create attributes to be set on the field container
		$this->attributes = $this->get_attributes( $this->info );
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
		$layout = self::get_layout( $class_name, $this->info );
		
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
	public static function get_field_info( $args ){
		$Options_Page = Cloud_Options_Pages::get_instance(); 

		$info = array(); 
		
		$top_level_slug = $args['top_level'];		
		$page_slug = $args['subpage'];
		$section_slug = $args['section'];
		$field_slug = $args['field']; 	

		$subfield_slug = isset( $args['subfield'] ) ? $args['subfield'] : '' ; 

		
		$enabled = $Options_Page->is_enabled( $top_level_slug, $page_slug, $section_slug, $field_slug ); 
		$value = $Options_Page->get_option( $top_level_slug, $page_slug, $section_slug, $field_slug ); 
		$cloneable =  isset( $args['info']['cloneable'] ) ? $args['info']['cloneable'] : false;
		$user_enabled_override_name = $page_slug.'[enabled]['.$section_slug.']['.$field_slug.']';
		
		// part of a group?
		if ( $subfield_slug ){
			$group_number = isset( $args['group_number'] ) ? $args['group_number'] : 0 ;		
			$value = isset( $value[$group_number] ) ? $value[$group_number][$subfield_slug] : ''; 
			$name =  $page_slug.'['.$section_slug.']['.$field_slug.']['.$group_number.']['.$subfield_slug.']'; 	
			$to_retrieve = 	'get_theme_options( "'. $page_slug.'", "'. $section_slug . '" , "'. $field_slug.'" , ' . $group_number .' , "' .$subfield_slug .'" )';	
			$cloneable = false;
			$saved_default = $Options_Page->get_option_default( $top_level_slug, $page_slug, $section_slug, $field_slug, $group_number, $subfield_slug );		
			$default_value = $saved_default ? $saved_default : ( isset( $args['info']['default'] ) ? $args['info']['default'] : '' ); 
		// plain old field
		} else {		
			$default_value =  isset( $args['info']['default'] ) ? $args['info']['default'] : ''; 
			$name =  $page_slug.'['.$section_slug.']['.$field_slug.']'; 
			$to_retrieve = 'get_theme_options( "'. $page_slug.'", "'. $section_slug . '" , "'. $field_slug.'" )' ;			
		}
		$info['title'] = $args['info']['title'];
		$info['to_retrieve'] = 	$to_retrieve;				
		$info['cloneable'] = $cloneable ; 
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
		$info['id']   = $field_slug;
		$info['value'] = $value ? $value : $default_value;
		$info['default'] = $default_value; 
		$info['settable_defaults'] = isset( $args['info']['settable_defaults'] ) ? $args['info']['settable_defaults'] : false;
		
		$info['enabled'] = $enabled ;
		$info['enabled_name'] = $user_enabled_override_name; 		
		$info['parent_layout'] = $args['parent_section_layout'];
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
		$label = $to_use."<label for='".$field_info['prefix'] . $field_info['id'] . "' >" . $field_info['title'] ."</label>";
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
		$classes[] = 'type-'.$this->type ;
		$classes[] = $field_info['sort'] ? '' : 'no-sort'; 
		if ( $field_info['parent_layout'] === 'grid' ){
			$classes[] = isset( $field_info['width'] ) ? 'span' . $field_info['width'] : 'span6';
		}
		$classes[] = isset( $field_info['style'] ) ? $field_style['style'] :  '' ;
		
		$classes_html = 'class="'.implode(' ', $classes) .'"';
		
		return $classes_html ;
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
	protected static function get_layout( $class_name, $field_info ){

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
				if ( file_exists( dirname( __FILE__ ).'/'.basename( __FILE__, '.php' ) . '/_js/'.$subfield_type.'.js' ) ){
					wp_enqueue_script( $subfield_type, self::get_include_path(). '/_js/'.$subfield_type.'.js', array( 'jquery', 'Options_Pages' ), '');
				} 
				if ( file_exists( dirname( __FILE__ ).'/'.basename( __FILE__, '.php' ) . '/_css/'.$subfield_type.'.css' ) ){
					wp_enqueue_style( $subfield_type, self::get_include_path(). '/_css/'.$subfield_type.'.css', array( 'Options_Pages' ));
				}
			}
		}
	}
	public static function get_include_path(){
		// Cloud-Theme / cloud / core / 				/   Cloud_Options_Pages /   Field_Type           	
		return Cloud_Options_Pages::get_include_path().'/'. basename(__DIR__) . '/'. basename( __FILE__, '.php') ;
	} 	
}