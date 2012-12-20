<?php 
class Cloud_Options_Pages {
	protected $prefix ;
	private static $instance ; 
	// what is the options name it stored under in options.php ?
	protected $options_name = '' ;		
	protected static $options_pages ;
	protected $user_defaults 	= array();
	// what are the values stored in the database?
	protected static $values ;
	// whatever the user passes in
	protected $user_array = array();
	// after merging with defaults
	public static $pages = array();
	
	public static function init( $user_pages ){

		if ( sizeof( $user_pages ) > 0 ){
			if ( ! self::$instance ){
				self::$instance = new self( $user_pages ); 
			}
			return self::$instance ; 
		} else {
			return false; 
		}
	}
	public static function get_instance(){
		return self::$instance; 
	}
	private function __construct( $user_pages = array() ){

		$this->user_array = $user_pages ;
		$this->prefix = 'Cloud_' ;
		$this->options_name = $this->prefix . 'options';

		// setup 'user_enabled_overrides' and 'user_defaults' (mostly for color pickers)
		$this->user_defaults = get_option( $this->prefix . 'user_defaults' );
		add_action('wp_ajax_set_values_as_defaults', array( __CLASS__, 'set_values_as_defaults' ) );
		add_action('wp_ajax_set_values_from_defaults', array( __CLASS__, 'set_values_from_defaults' ) );	
		
		self::$pages = $this->merge_with_defaults(); 		

		self::$values = self::get_values(); 		
		add_action( 'admin_init', array( $this, 'register_settings' ) ); 		
		add_action('admin_menu', array( $this, 'create_options_pages' ) );	
			add_action('admin_enqueue_scripts', array( $this, 'load_styles_and_scripts' ) );
		if ( isset( $_GET['page'] ) && in_array( $_GET['page'], self::$pages ) ){	
			add_action('admin_enqueue_scripts', array(Cloud_Options, 'load_styles_and_scripts') );	
		}
	}
	
	protected function merge_with_defaults(){
		$defaults = Cloud_Options::$defaults; 
		$user_array = $this->user_array; 
		$_master = array();
		// set it to be the user array merged with all the defaults
		foreach ( $user_array as $top_level_slug => $top_level_page ){
			$_master[$top_level_slug] = array();  
			$_top_level =& $_master[$top_level_slug]; 
			
			foreach ( $defaults['top_level'] as $key => $default_value ) {
				if ( isset( $top_level_page[$key] ) ){
					$set_value = $top_level_page[$key] ;
				} else {
					$set_value = $default_value ;
				}
				$_top_level[$key] = $set_value;
			}
			
			foreach ( $top_level_page['subpages'] as $subpage_slug => $subpage ){	
				$_top_level['subpages'][$subpage_slug] = array();  
				$_subpage =& $_top_level['subpages'][$subpage_slug];
				
				foreach ( $defaults['subpages'] as $key => $default_value ) {
					if ( isset( $subpage[$key] ) ){
						$set_value = $subpage[$key];
					} else {
						if ( isset ( $top_level_page['defaults']['subpages'][$key] ) ) {
							$set_value = $top_level_page['defaults']['subpages'][$key];
						} else {
							$set_value = $default_value;
						}					
					}
					$_subpage[$key] = $set_value;
				}						
				foreach ($subpage['sections'] as $section_slug => $section){
					$_subpage['sections'][$section_slug] = array();  
					$_section =& $_subpage['sections'][$section_slug];	
					$_section = Cloud_Options::merge_with_defaults( 'sections', $section, $subpage, $top_level_page ); 
					// toggle section/page if a field has the property settable_defaults = true 
					// allows for page/section reset defaults controls to be generated					
					foreach( $_section['fields'] as $field_slug =>  $field ){
						foreach ( $field as $key => $value ){
							if ( $key === 'settable_defaults' && isset( $field[$key] ) && $field[$key] == true && !$field['cloneable'] ){
								$_subpage['_has_settable_defaults'] = $_subpage['_has_settable_defaults'] ? $_subpage['_has_settable_defaults'] + 1 : 1 ;
								$_section['_has_settable_defaults'] = $_section['_has_settable_defaults'] ? $_subpage['_has_settable_defaults'] + 1 : 1 ;
								if ( $saved_default = self::get_option_default( $top_level_slug, $subpage_slug, $section_slug, $field_slug ) ) {
									$_section[ 'fields'][$field_slug]['default'] =  $saved_default ; 
								}
							}
						}
					}

				}
			}		
		}
		return $_master;
	}
	
	/**
	 * Register the options so that WP knows about them and can handle saving
	 */
	private static function get_values(){
		$values = array(); 
		foreach ( self::$pages as $top_level_slug => $top_level_page ) {
			foreach ($top_level_page['subpages'] as $subpage_slug => $subpage) {
				$values[ $top_level_slug ][$subpage_slug] = get_option( $subpage_slug ); 
			}
		}
		return $values ;
	}
	
	public function register_settings(){
	
		foreach ( self::$pages as $top_level_slug => $top_level_page ){
			foreach ( $top_level_page['subpages'] as $subpage_slug => $subpage ){
				register_setting( $subpage_slug , $subpage_slug );
			}
		}
	}
	public function load_styles_and_scripts(){
		// if it is an options page that is currently displayed, get all of the options scripts and styles. 
		if ( isset( $_GET['page'] ) ){
			foreach( self::$pages as $top_level ){
				foreach ( $top_level['subpages'] as $subpage_slug => $subpage ){
					if ( $subpage_slug == $_GET['page'] ){
						$Options = Cloud_Options::get_instance();
						$Options->load_styles_and_scripts() ;
						break; 
					}
				}
			}
		}
	}
	
	/**
	 *
	 * Create the options pages, integrating with WP's functions
	 *
	 */
	 
	 public function create_options_pages(){
		$options_page_array =& self::$pages; 
		foreach ( $options_page_array as $top_level_slug => $top_level_page ){
			$page_layout_function = $this->create_top_level_menu_page( $top_level_slug, $top_level_page ) ;
			$options_page_array[$top_level_slug]['subpages'][$top_level_slug]['callback'] = $page_layout_function; // save for later reference
			foreach ( $top_level_page['subpages'] as $subpage_slug => $subpage ){
				if( $subpage_slug !== $top_level_slug ) { // listed as a subpage of itself (necessarily)...make sure it isn't added to the menu twice 
					$page_layout_function = $this->create_subpage($top_level_slug, $subpage_slug, $subpage);
					$options_page_array[$top_level_slug]['subpages'][$subpage_slug]['callback'] = $page_layout_function; // save for later reference
				}			
				foreach ($subpage['sections'] as $section_slug => $section){
					
					$section_layout_function = $this->create_section( $subpage_slug, $section_slug, $section, $page_layout_function );
					$options_page_array[$top_level_slug]['subpages'][$subpage_slug]['sections'][$section_slug]['callback'] = $section_layout_function ; // save for later reference 

					foreach ( $section['fields'] as $field_slug => $field ){
						$this->create_field( $top_level_slug, $subpage_slug, $section_slug, $field_slug, $field, $section_layout_function);						
					}
				}
			}		
		}
	}	
	private function create_top_level_menu_page( $slug, $info ){
		// are you creating your own top level? Then this should be set. 
		if( isset( $info['subpages'][$slug] ) ){
			$page_title 	= $info['subpages'][$slug]['title']; // listed as a subpage of itself...makes a strange sort of sense (look at the flyout menus!)
			$menu_title 	= $info['subpages'][$slug]['menu_title']; 
			$capability 	= $info['subpages'][$slug]['capability'] ? $info['subpages'][$slug]['capability'] : 'create_users'; 
			$menu_slug 		= $slug; 
			$callback 		= $this->get_page_layout_function(  $info['subpages'][$slug]['layout'] ); 
			$icon_url 		= $info['image'];
			$position		= $info['priority']; 
			add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $callback, $icon_url, $position );
			
			return $callback[1]; // return layout function name 
		// otherwise, it assumes you are adding to an existing page
		} else {
			return false;
		}
	}
	private function create_subpage( $top_level_slug, $slug, $info ) {
		$page_title 	= $info['title']; 
		$menu_title 	= $info['menu_title']; 
		$capability 	= isset( $info['capability'] ) ? $info['capability'] : 'create_users'; 
		$menu_slug 		= $slug; 
		$callback 		= $this->get_page_layout_function( $info['layout'] ); 
		add_submenu_page( $top_level_slug, $page_title, $menu_title, $capability, $menu_slug, $callback );
		
		return $callback[1];	
	}
	private function create_section( $page_slug, $slug, $info, $page_layout_function ) {
		$id 			= $slug;
		$title 			= $info['title']; 
		$callback 		= $this->get_section_layout_function( $info['layout'], $page_layout_function ); 
		add_settings_section( $id, $title, $callback, $page_slug );		
		
		return $callback[1]; // return layout function name
	}
	private function create_field( $top_level_slug, $page_slug, $section_slug, $slug, $info, $section_layout_function ) {
		$Options = Cloud_Options::get_instance();
		$id 			= $slug;
		$page_slug		= $page_slug;
		$section_slug 	= $section_slug;
		$title 			= $info['title']; 
		$callback 		= $Options->get_field_layout_function( $info, $page_slug, $section_slug, $slug, $section_layout_function ); 
		$args 			= array (
			'info'		=> $info,
			'top_level' => $top_level_slug,
			'subpage' 	=> $page_slug, 
			'section' 	=> $section_slug, 
			'context' 	=> 'options-page',
			'field'	  	=> $slug
		);	
		add_settings_field( $id, $title, $callback, $page_slug, $section_slug, $args );
	}	
	private function get_page_layout_function( $layout = null ){
	
		$layout_function = Page_Layout::get_layout_function($layout, 'Page_Layout'); 
		return array('Page_Layout', $layout_function );
	}
	private function get_section_layout_function( $layout = null , $page_layout_function = '' ) {
		$layout_function = Section_Layout::get_layout_function($layout, 'Section_Layout' , $page_layout_function ); 
		return array('Section_Layout', $layout_function );
	}		 
	 
	/**
	 *
	 * FUNCTIONS FOR RETRIEVING VARIOUS OPTIONS VALUES
	 *
	 */
	 // Retrieve one specific option value
	public function get_option( $top_page_slug = null, $page_slug = null , $section_slug = null , $field_slug = null ){
		if ( !isset( self::$values[$top_page_slug] ) || 
			 !isset( self::$values[$top_page_slug][$page_slug] ) || 
			 !isset( self::$values[$top_page_slug][$page_slug][$section_slug] ) ||
			 !isset( self::$values[$top_page_slug][$page_slug][$section_slug][$field_slug] ) 
		){	

			return false;
		}
		$option = self::$values[$top_page_slug][ $page_slug ][ $section_slug ][ $field_slug ];	

		return $option;
	}
	// Retrieve whatever level of specificity is desired: page, section, field, group, subfield
	public function get_options( $page_slug = null , $section_slug = null , $field_slug = null , $group_number = null, $subfield_slug = null ){		
		if ( ! self::$values ) self::$values = self::get_values() ;
		foreach( self::$values as $page_slug => $options ){
			foreach( $options as $subpage_slug => $options ){
				if ( $subpage_slug === $page_slug ){
					$top_page_slug = $page_slug ;
					break 2;
				}
			}
		}	

		// ha ha, overkill...but it might be useful to be able to grab individual group values
		if (  isset( self::$values[$top_page_slug][$page_slug][$section_slug][$field_slug][$group_number][$subfield_slug] ) && is_array( self::$values[$top_page_slug][$page_slug][$section_slug][$field_slug][$group_number] ) ) {
			return self::$values[$top_page_slug][$page_slug][$section_slug][$field_slug][$group_number][$subfield_slug];
		} else if ( is_int( $group_number ) && isset( self::$values[$top_page_slug][$page_slug][$section_slug][$field_slug][$group_number] ) ) {
			
			return self::$values[$top_page_slug][$page_slug][$section_slug][$field_slug][$group_number] ;
			
		} else if ( isset( self::$values[$top_page_slug][$page_slug][$section_slug][$field_slug] ) ) {
			
			// check if the current page has settable defaults before going to all the hassle of checking the field
			if ( self::$pages[$top_page_slug]['subpages'][$page_slug]['_has_settable_defaults'] ){
				if ( $this->is_option_enabled( $top_page_slug, $page_slug, $section_slug, $field_slug ) ){
					return self::$values[$top_page_slug][$page_slug][$section_slug][$field_slug] ;
				} else {
					return $this->user_defaults[$top_page_slug][$page_slug][$section_slug][$field_slug] ;
				}
			} else {
				return self::$values[$top_page_slug][$page_slug][$section_slug][$field_slug] ;
			}
		} else if ( isset( self::$values[$top_page_slug][$page_slug][$section_slug] ) ) {
			return self::$values[$top_page_slug][$page_slug][$section_slug] ; 
		} else if ( isset( self::$values[$top_page_slug][$page_slug] ) ){ 
			return self::$values[$top_page_slug][$page_slug] ; 
		} else if ( isset( self::$values[$top_page_slug] ) ) {
			return self::$values[$top_page_slug]; 
		} else {
			return false;
		}
	}	
	
	/**
	 * Methods for distilling information from the user array ( like compiling parent page, parent section, parent field, etc )
	 */
	public function get_options_array_info($subpage_slug = null, $section_slug = null, $field_slug = null ){
		if ($subpage_slug ){
			foreach (self::$pages as $slug => $top_level ){
				if ( isset( $top_level['subpages'][$subpage_slug] ) ){
					$top_level_slug = $slug;
					break;
				}
			}
			if ( $section_slug && $field_slug ){			
				return self::$pages[$top_level_slug]['subpages'][$subpage_slug]['sections'][$section_slug]['fields'][$field_slug]; 
			} else if ( $section_slug ){
				return self::$pages[$top_level_slug]['subpages'][$subpage_slug]['sections'][$section_slug];
			} else {
				return self::$pages[$top_level_slug]['subpages'][$subpage_slug];
			}
		} else {
			return self::$pages[$top_level_slug];
		}
	}
	public function get_options_section_info(){
		$top_level 	= $this->options_creation_tracker['top_level'];
		$subpage 	= $this->options_creation_tracker['subpage'];
		$section 	= $this->options_creation_tracker['section'];
		
		$options_array = self::$pages;			
		
		return $options_array[ $top_level ][ $subpage ][ $section ]; 
	}
	public function get_options_field_info(){
		$top_level 	= $this->options_creation_tracker['top_level'];
		$subpage 	= $this->options_creation_tracker['subpage'];
		$section 	= $this->options_creation_tracker['section'];
		$field 		= $this->options_creation_tracker['field'];	
		
		$options_array = self::$pages;		
		return $options_array[ $top_level ][ $subpage ][ $section ][ $field ]; 
	}

	public function get_option_default(  $top_page_slug = null, $page_slug = null , $section_slug = null , $field_slug = null , $group_number = null, $subfield_slug = null ){
/*
		if ( isset( $this->user_defaults[$top_page_slug][$page_slug][$section_slug][$field_slug] ) ) {
			return $this->user_defaults[$top_page_slug][$page_slug][$section_slug][$field_slug] ; 
		} else {
			return false;
		}
*/
	}
	public function is_option_enabled( $top_page_slug = null, $page_slug = null , $section_slug = null , $field_slug = null ){
		if ( isset( self::$pages[$top_page_slug]['subpages'][$page_slug]['sections'][$section_slug]['fields'][$field_slug]['settable_defaults'] ) && self::$pages[$top_page_slug]['subpages'][$page_slug]['sections'][$section_slug]['fields'][$field_slug]['settable_defaults'] ){ 
			if( isset( self::$values[$top_page_slug][$page_slug]['enabled'][$section_slug][$field_slug] ) && self::$values[$top_page_slug][$page_slug]['enabled'][$section_slug][$field_slug] ){
				return true;
			} else {
				return false;
			}
		} 
		return true;
	}
	 
	/**
	 *
	 * AJAX FUNCTIONS
	 *
	 */
	public function set_values_as_defaults(){
		$Options = Cloud_Options::get_instance();
		$success= array();
		foreach ($_POST['inputs'] as $input){
			$input_name_parts = preg_split( '/[\[\]]{1,2}/', $input['name'] );
			$subpage_slug = $input_name_parts[0];
			$section_slug = $input_name_parts[1];
			$field_slug = $input_name_parts[2];			
			$Options->user_defaults[$subpage_slug][$subpage_slug][$section_slug][$field_slug] = $input['value'];
			$success[ $input['name'] ] = $input['value'];  

		}
		update_option( $Options->prefix .'user_defaults', $Options->user_defaults) ;
		echo json_encode($success) ;
	
		die;
	}
	public function set_values_from_defaults(){
		$Options = Cloud_Options::get_instance();
		
		foreach ($_POST['inputs'] as $input){
			$input_name_parts = preg_split( '/[\[\]]{1,2}/', $input['name'] );
			$subpage_slug = $input_name_parts[0];
			$section_slug = $input_name_parts[1];
			$field_slug = $input_name_parts[2];
			$user_default_value = $Options->user_defaults[$subpage_slug][$subpage_slug][$section_slug][$field_slug] ;	
		
			$Options->options[$subpage_slug][$subpage_slug][$section_slug][$field_slug] = $user_default_value;
	
			$success[ $input['name'] ] = $user_default_value;  
		}
		update_option( $subpage_slug, $Options->options[$subpage_slug][$subpage_slug]) ;

		echo json_encode($success) ;
				
		die;
	}		
	
}