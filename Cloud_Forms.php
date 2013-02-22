<?php 
//include Cloud_Theme__DIR . '/__inc/geek_caller_rejection.php';
class Cloud_Forms  {
	public static $dir ; 
	public static $ABS ;
	// setting up singleton
	private static $instance;
	// allows us to premake code and optionally enable it. This stores the enabled file names to be inclued
	protected static $modules = array() ; 
	// the master list of defaults, from defaults.php, set in set_defaults()
	public static $defaults = array() ; 
	// the list of scripts needed for the given fields
	public static $scripts = array() ; 	
	// the list of styles needed for the given fields
	public static $styles = array(
		array( 'handle' => 'jquery', 'url' => 'http://code.jquery.com/jquery-1.9.1.min.js', 'dependencies' => array(
			
		)),
		array( 'handle' => 'c', 'url' => 'http://code.jquery.com/fun.min.js', 'dependencies' => array(
			'b', 'a'
		)),
		array( 'handle' => 'b', 'url' => 'http://code.jquery.com/fun.min.js', 'dependencies' => array(
			'a'
		)),
		array( 'handle' => 'a', 'url' => 'http://code.jquery.com/fun.min.js', 'dependencies' => array(
			'jquery'
		)),
		array( 'handle' => 'd', 'url' => 'http://code.jquery.com/fun.min.js', 'dependencies' => array(
			'a','b','c'
		)),
		array( 'handle' => 'f', 'url' => 'http://code.jquery.com/fun.min.js', 'dependencies' => array(
			'a','b','c','e','d'
		)),
		array( 'handle' => 'e', 'url' => 'http://code.jquery.com/fun.min.js', 'dependencies' => array(
			'd','b','c','a'
		)),
		
		
		
		
	) ;
	
	/**
	 * Constructor. Calls methods necessary to create the type.
	 *
	 * @internal 	This class <em>could</em> be included in the plugin as a simple function/action pair.
	 * 				However, the design calls for future functionality that will be convenient to class-wrap.
	 */
	public static function get_instance(){
		if ( ! self::$instance  ){
			self::$instance = new self();
		}
		return self::$instance; 
	}	 
	private function __construct( $site_url = '' )
	{	
		self::$dir = $site_url ;
		self::$ABS = dirname( __FILE__ ) .  '/'. __CLASS__ ;
		$this->prefix = 'Cloud_' ;
		
		// load everything in /Cloud_Options
		$this->load_theme_classes(); 
		$this->load_global_styles_and_scripts() ;
		
		array_walk( self::$styles, array( $this, 'filter_out_items_without_needed_dependencies'), &self::$styles );
		usort( self::$styles, array( $this, 'sort_array_by_dependencies') );
/*
		//load all files within specific folders within /Cloud_Options/
		$this->load_theme_classes('Field');
		$this->load_theme_classes('Layout'); 
		
		// get defaults array from defaults.php
		$this->set_defaults();
		
		// includes chunks of code we may or may not want to execute
		// see self::enable_module
		$this->init_modules(); 
		 
		//create options pages
		$Options_Pages = Cloud_Options_Pages::init( self::$user_pages ) ; 
		$Options_Metaboxes = Cloud_Metaboxes::init( self::$user_metaboxes ) ;

		//add any customizations to MCE editor
		MCE_Plugins::init(); 
*/


	}


/*	*****************************************************************************************************************
	Class Loading
	***************************************************************************************************************** */
	/**
	 * Loads theme functionality classes appropriate for the current view-state.
	 *
	 * Classes are loaded from the following folders:
	 *   * CLASS_NAME/types     - Classes defining field types.
	 *
	 * @internal To instantiate these classes, use the action hook <kbd>cloud_init</kbd> in the class file.
	 * @since 0.9.0
	 */
	private function load_theme_classes( $subfolder = null )
	{
		$base_path = dirname(__FILE__).'/'.( get_class($this) );
		$load_folder = $subfolder ?  $base_path . '/' . $subfolder : $base_path;
		$load_list = array( ) ;
		$load_list = array_merge( $load_list , self::glob_php( $load_folder  ) );
		foreach ( $load_list as $file )
		{
			include $file;
		}
		//$this->add_enabled_classes_to_init($load_list); 

		// firing the cloud_init action triggers initialization of the classes if they subscript to the event.
		//do_action( __CLASS__.'_init', array( 'cloud' , 'register_class' ) ) ;
	}

	/**
	 * Returns an array of all PHP files in the specified directory path.
	 *
	 * Shamelessly borrowed from the Automattic Jetpack plug-in
	 * and re-purposed for my nefarious schemes.
	 *
	 * @static
	 * @param string $absolute_path The absolute path of the directory to search.
	 * @return array Array of absolute paths to the PHP files.
	 * @since 0.8.0
	 */
	private static function glob_php( $absolute_path )
	{

		$absolute_path = self::$ABS;

		$files = array( ) ;
		if ( !$dir = @opendir( $absolute_path ) )
		{
			return $files;
		}
		while ( false !== $file = readdir( $dir ) )
		{
			if ( '.' == substr( $file, 0, 1 ) || '.php' != substr( $file, -4 ) )
			{
				continue;
			}

			$file = "$absolute_path/$file";

			if ( !is_file( $file ) )
			{
				continue;
			}

			$files[] = $file;
		}

		closedir( $dir );

		return $files;
	}
	private function add_enabled_classes_to_init( $load_list ){

		foreach ($load_list as $file){
		
			// the file name MUST be the class name
			$class_name = basename($file, '.php'); 
			
			// If the class is enabled, then subscribe to the theme initialization event (create an instance on init)
			add_action( $class.'_init' , array( $class_name , 'init' ) , 1 ,  1 ) ;
			
		}
	}	
	
	/***====================================================================================================================================
			LOAD STYLES AND SCRIPTS
		==================================================================================================================================== ***/
	public function enqueue_script( $handle, $path ){
	
	}
	public function enqueue_style( $handle, $path, $dependencies = false ){
		$item_to_enqueue = array(
			'handle' => $handle, 
			'path' 		=> $path,
			'dependencies' => $dependencies
		);	
		self::$styles[ ] = $item_to_enqueue ;
	}
	protected function sort_array_by_dependencies( $a, $b ){
		if ( is_array( $a['dependencies'] ) ){
			if ( in_array($b['handle'], $a['dependencies']) ){
				return true;
			}
		}
		return 0;
	}
	protected function filter_out_items_without_needed_dependencies( &$item, $key, &$array ){

		if ( is_array( $item['dependencies'] ) ){
			foreach( $item['dependencies'] as $dependency ){
				$dependency_found = false;
				foreach( $array as $array_item ){
					if ($array_item['handle'] == $dependency){
						$dependency_found = true; 
						break;
					}
				}
				if ( ! $dependency_found ){
					unset( $array[ $key ] );
					break;
				} 
			}
		}
	}	
	public function load_global_styles_and_scripts( ){
		$this->enqueue_style( 'Options_global', self::get_folder_url().'/_css/Options_Global.css', array( 'fun' ) );
	}
	public function load_styles_and_scripts(){
		// GENERAL STYLES
		wp_register_style('Bootstrap Responsive', self::get_folder_url().'/_css/bootstrap/css/bootstrap-responsive.css');
		wp_register_style('Bootstrap',  self::get_folder_url().'/_css/bootstrap/css/bootstrap.css');

		wp_register_style('Options', self::get_folder_url().'/_css/Options.css', array( 'thickbox' ) );

		wp_enqueue_style('Bootstrap Responsive'); 				
		wp_enqueue_style('Bootstrap'); 	
		wp_enqueue_style('Options');
		
		// GENERAL SCRIPTS
		wp_register_script('Bootstrap',  self::get_folder_url().'/_css/bootstrap/js/bootstrap.min.js'); 
		wp_register_script('scrollTo', self::get_folder_url().'/__inc/js/jquery.scrollTo-1.4.3.1-min.js'); 
		wp_register_script('Options', self::get_folder_url().'/_js/Options.js', array( 'thickbox', 'media-upload', 'jquery-ui-core', 'jquery-ui-sortable' ) ); 
		wp_register_script('Cloud_Field', self::get_folder_url().'/_js/Cloud_Field.js' ) ;
		wp_enqueue_script('Bootstrap');
		wp_enqueue_script('scrollTo');
		wp_enqueue_script('Cloud_Field');			
		wp_enqueue_script('Options');		
		wp_localize_script('Options', 'wp_vars', array(
			'ajax_url' 	=> admin_url( 'admin-ajax.php' ),
			'is_options_page' => isset( $_GET['page'] ) && $_GET['page'] , 
		));	
	}
	private function set_defaults(){
		global $options_defaults; 
		self::$defaults = $options_defaults;
	}				
/***====================================================================================================================================
		MODULES SET UP
	==================================================================================================================================== ***/
	public static function enable_module( $file_name = '' ){
		if ( $file_name && file_exists( dirname( __FILE__ ) .  '/'. __CLASS__ .'/modules/'.$file_name .'.php' ) ){
			self::$modules[] = dirname( __FILE__ ) .  '/'. __CLASS__ .'/modules/'.$file_name .'.php' ;
		}
	}
	protected function init_modules(){
		if ( sizeof( self::$modules ) > 0 ){
			foreach( self::$modules as $module ){
				include $module ; 
			}
		}
	}
	// Public method for adding options pages
	public static function add_pages( $options_array ){
		foreach ( $options_array as $key => $array ){
			self::$user_pages[$key] = $array;
		}	
	}	
	
/***====================================================================================================================================
		FUNCTIONS FOR ADDING METABOXES
	==================================================================================================================================== ***/		
	//whatever the user passes in with add_metaboxes()
	public static function add_metaboxes( $add_to, $user_array, $context = '' , $priority = '' ){
		self::$user_metaboxes[] = array(
			'add_to' => $add_to,
			'user_array' => $user_array, 
			'context' => $context,
			'priority' => $priority
		);
	}

/***====================================================================================================================================
		FUNCTIONS FOR BOTH THE METABOXES AND THE OPTIONS 
	==================================================================================================================================== ***/

	public static function merge_with_defaults( $type, $section = array(), $subpage = array(), $top_level_page = array() ){
		// $type is in case we ever need multiple types of merges. Right now, the only type is section/metabox (smae thing )
		
		$defaults = self::$defaults; 

		foreach ( $defaults['sections'] as $key => $default_value ) {
			if ( isset( $section[$key] ) ){
				$set_value = $section[$key] ;
			} else {
				if ( isset ( $subpage['defaults']['sections'][$key] ) ) {
					$set_value = $subpage['defaults']['sections'][$key];
				} else if ( isset ( $top_level_page['defaults']['sections'][$key] ) ) {
					$set_value = $top_level_page['defaults']['sections'][$key];
				} else {
					$set_value = $default_value;
				}
			}
			$_section[$key]	= $set_value;
		}				
	
		foreach ( $section['fields'] as $field_slug => $field ){

			$_section['fields'][$field_slug] = array();  
			$_field =& $_section['fields'][$field_slug]; 
			$type = '';
			// establish type ( if it is specificied by user anywhere and is a valid type , else default )						
			if ( isset( $field['type'] ) ){
				$type = $field['type'];  							
			} else {
				if ( isset ( $section['defaults']['fields']['type'] ) ) {
					$type = $section['defaults']['fields']['type'];
				} else if ( isset ( $subpage['defaults']['fields']['type'] ) ) {
					$type = $subpage['defaults']['fields']['type'];
				} else if ( isset ( $top_level_page['defaults']['fields']['type'] ) ) {
					$type = $top_level_page['defaults']['fields']['type'];
				}
			}
			// valid type?						
			if ( !isset( $type ) || !class_exists( Cloud_Field::get_class_name( $type ) ) ) { 						
				$type = Cloud_Field::$default_type ; 
			}
			// set type
			$_field['type'] = $type ;
 
			// go through defaults for that type
			if ( isset(  $defaults['fields'][$type] ) ) {
				$field_defaults =  $defaults['fields'][$type] ; 
			} else {
				$field_defaults = $defaults['fields']['general'] ;
			}
			foreach ( $field_defaults as $key => $default_value ) { 
				if ( isset( $field[$key] ) ){
					$set_value = $field[$key];  
				} else {
					if ( isset ( $section['defaults']['fields'][$key] ) ) {
						$set_value = $section['defaults']['fields'][$key];
					} else if ( isset ( $subpage['defaults']['fields'][$key] ) ) {
						$set_value = $subpage['defaults']['fields'][$key];
					} else if ( isset ( $top_level_page['defaults']['fields'][$key] ) ) {
						$set_value = $top_level_page['defaults']['fields'][$key];
					} else {
						$set_value = $default_value; 
					}
					
				}

				// if something has already set the master value (like for the default (below!) )
				if ( !isset( $_field[$key] ) ){							
					$_field[$key] = $set_value ;
				}


			}
			// only if group 
			if ( isset( $_field['subfields'] ) ){
				foreach ( $field['subfields'] as $subfield_slug => $subfield ){
					$_field['subfields'][$subfield_slug]= array();  
					$_subfield =& $_field['subfields'][$subfield_slug]; 
					
					
					// establish type ( if it is specificied by user anywhere and is a valid type , else default )						
					$subfield_type = '' ;
					if ( isset( $subfield['type'] ) ){
						$subfield_type = $subfield['type'];  							
					} else {
						if ( isset ( $section['defaults']['fields']['type'] ) ) {
							$subfield_type = $section['defaults']['fields']['type'];
						} else if ( isset ( $subpage['defaults']['fields']['type'] ) ) {
							$subfield_type = $subpage['defaults']['fields']['type'];
						} else if ( isset ( $top_level_page['defaults']['fields']['type'] ) ) {
							$subfield_type = $top_level_page['defaults']['fields']['type'];
						}
					}
					// valid type?						
					if ( !isset( $subfield_type ) || !class_exists( Cloud_Field::get_class_name( $subfield_type ) ) ) { 						
						$subfield_type = Cloud_Field::$default_type ; 
					}
					
					// set type
					$_subfield['type'] = $subfield_type ;
					// go through defaults for that type
					if ( isset(  $defaults['fields'][$subfield_type] ) ) {
						$subfield_defaults =  $defaults['fields'][$subfield_type] ; 
					} else {
						$subfield_defaults = $defaults['fields']['general'] ;
					}									
					foreach ( $subfield_defaults as $key => $subfield_default_value ) {
						if ( isset( $subfield[$key] ) ){
							$set_value = $subfield[$key];  
						} else {
							if ( isset ( $field['defaults']['subfields'][$key] ) ) {
								$set_value = $field['defaults']['subfields'][$key];
							} else if ( isset ( $section['defaults']['subfields'][$key] ) ) {
								$set_value = $section['defaults']['subfields'][$key];
							} else if ( isset ( $subpage['defaults']['subfields'][$key] ) ) {
								$set_value = $subpage['defaults']['subfields'][$key];
							} else if ( isset ( $top_level_page['defaults']['subfields'][$key] ) ) {
								$set_value = $top_level_page['defaults']['subfields'][$key];
							} else {
								$set_value = $subfield_default_value; 
							}
						}
						if ( $key === 'settable_defaults' && isset( $_subfield[$key] ) && $_subfield[$key] == true ){
							$_section['_has_settable_defaults'] = $_section['_has_settable_defaults'] ? $_section['_has_settable_defaults'] + 1 : 1 ;
							$_subpage['_has_settable_defaults'] = $_subpage['_has_settable_defaults'] ? $_subpage['_has_settable_defaults'] + 1 : 1 ;
						}										
						$_subfield[$key] = $set_value ;
					}
				}
			}

					
		}	
		return $_section ;

	}

	public static function get_settings_sections($page) {
		global $wp_settings_sections, $wp_settings_fields;		
		if ( !isset($wp_settings_sections) || !isset($wp_settings_sections[$page]) ){
		    return;
		}
		$sections = array(); 
		foreach ( (array) $wp_settings_sections[$page] as $section ) {
			$sections[ $section['id'] ]['info'] = $section;
			$sections[ $section['id'] ]['html'] = call_user_func( $section['callback'], $section );
		}
		return $sections;
	}
	public static function do_settings_fields( $page, $section ) {
		global $wp_settings_fields;
		
		if ( !isset($wp_settings_fields) || !isset($wp_settings_fields[$page]) || !isset($wp_settings_fields[$page][$section['id'] ]) ){
			return;
		}

		foreach ( (array) $wp_settings_fields[$page][$section['id']] as $field ) {
			$field['args']['parent_section_layout'] = $section['callback'][1] ;
			call_user_func( $field['callback'], $field['args'] );
		}
	}	

	public function get_field_layout_function( $info, $page_slug = '', $section_slug = '', $slug = '', $section_layout_function = '' ) {
		$type 	= $info['type'];

		$field_type = Cloud_Field::get_class_name( $type );
		// strange, I know, but this is necessary to get the scripts added early enough. At this point, we KNOW they want to field. 
		add_action( 'admin_enqueue_scripts', array( $field_type, 'enqueue_field_scripts_and_styles' ) ); 		
		return array( $field_type, 'create_field' ) ;
	}	
	public static function get_folder_URL(){
		return self::$dir .'/'. basename( __FILE__, '.php' )  ; 	
	}	
	public static function get_include_path(){
		return self::$ABS .'/'. basename( __FILE__, '.php' ) ; 	
	}
	
} // End Class	
$Forms = Cloud_Forms::get_instance() ;
