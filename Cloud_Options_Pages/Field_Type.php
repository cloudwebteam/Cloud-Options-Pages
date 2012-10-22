<?php 
class Field_Type {
	public static $default_type ;
	public static $default_layout ; 

	private static $default_value ;
	private $layout = 'default'; // fallback layout type
	
	private $layouts = array();
	
	private $name 	= 'DEFAULT_NAME';
	private $value 	= 'DEFAULT_HERE'; 
	
	protected function __construct( $class_name, $args){
		add_action( 'admin_enqueue_scripts', array( $class_name, 'enqueue_stuff' ) );				
 		$this->info = self::get_field_info($args);

		$this->label = self::get_label( $this->info ); 
		$this->description = self::get_description( $this->info );
		
		$layout = self::get_layout( $class_name, $this->info );

		$this->$layout( $args ); 
	}
	public function standard(){
		echo 'this field needs a display function!';
	}
	public function enqueue_field_scripts_and_styles(){
		self::register_scripts_and_styles( get_called_class() );  // would be best, but only in 5.3... should fallback and not break
	
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
		$Options_Page = Cloud_Options_Pages::get_instance(); 

		$info = array(); 
		
		$top_level_slug = $args['top_level'];		
		$page_slug = $args['subpage'];
		$section_slug = $args['section'];
		$field_slug = $args['field']; 	
		$subfield_slug = isset( $args['subfield'] ) ? $args['subfield'] : '' ; 

		
		$default_value =  isset( $args['info']['default'] ) ? $args['info']['default'] : ''; 
		$value = $Options_Page->get_option( $top_level_slug, $page_slug, $section_slug, $field_slug ); 
		
		// part of a group?
		if ( $subfield_slug ){
			$group_number = isset( $args['group_number'] ) ? $args['group_number'] : 0 ;		
			$value = $value[$group_number][$subfield_slug]; 
			$name =  $page_slug.'['.$section_slug.']['.$field_slug.']['.$group_number.']['.$subfield_slug.']'; 	
			$to_retrieve = 	'get_theme_options( "'. $page_slug.'", "'. $section_slug . '" , "'. $field_slug.'" , "' . $group_number .'" )';	
		// most fields aren't
		} else {
			$name =  $page_slug.'['.$section_slug.']['.$field_slug.']'; 
			$to_retrieve = 'get_theme_options( "'. $page_slug.'", "'. $section_slug . '" , "'. $field_slug.'" )' ;			
		}
		$info['title'] = $args['info']['title'];
		$info['to_retrieve'] = 	$to_retrieve;				
		
		$info['name'] = $name; 
		$info['description'] = isset( $args['info']['description'] ) ? $args['info']['description'] : null;
		$info['id']   = $field_slug;
		$info['value'] = $value ? $value : $default_value;
		$info['parent_layout'] = $args['parent_section_layout'];
		$info['layout'] = isset ($args['info']['layout'] ) ? $args['info']['layout'] : 'default';
		$info['prefix'] = $Options_Page->prefix; 		
		$info['fields'] = isset( $args['info']['fields'] ) ? $args['info']['fields'] : ''; 
		$info['is_subfield'] = $subfield_slug !== '' ? true : false;
		return $info;
	}
	protected static function get_label($field_info){
		$to_use = "<div class='copy_to_use'><a rel='copy_to_use'>Code</a><input class='copy' type='text' value='".$field_info['to_retrieve']."' /></div>";
		$label = "<label for='".$field_info['prefix'] . $field_info['id'] . "' >" . $field_info['title'] ."</label>".$to_use;
		return $label;
	}
	protected static function get_description( $field_info ){
		$description = isset( $field_info['description']) && $field_info['description'] !== '' ? '<span class="description">'.$field_info['description'] . '</span>' : '';
		return $description;
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
	protected static function register_scripts_and_styles( $class_name ){
		if ( $class_name ){			
			if ( file_exists( dirname( __FILE__ ).'/'.basename( __FILE__, '.php' ) . '/_js/'.$class_name.'.js' ) ){
				wp_enqueue_script( $class_name, self::get_include_path(). '/_js/'.$class_name.'.js', array( 'jquery', 'Options_Pages' ), '');
			} 
			if ( file_exists( dirname( __FILE__ ).'/'.basename( __FILE__, '.php' ) . '/_css/'.$class_name.'.css' ) ){
				wp_enqueue_style( $class_name, self::get_include_path(). '/_css/'.$class_name.'.css', array( 'Options_Pages' ));
			}
		}
	}
	public static function get_include_path(){
		// Cloud-Theme / cloud / core / 				/   Cloud_Options_Pages /   Field_Type           	
		return Cloud_Options_Pages::get_include_path().'/'. basename(__DIR__) . '/'. basename( __FILE__, '.php') ;
	} 	
}