<?php 
//include Cloud_Theme__DIR . '/__inc/geek_caller_rejection.php';
class Cloud_Options_Pages  {
	public static function init( $callable = array( ) )
	{
		$class_instance  = self::get_instance();
		if( is_callable( $callable ) && method_exists( $callable[ 0 ] , $callable[ 1 ]) )
		{
			call_user_func_array( $callable, array( $class_instance ) ) ;
		}
	}
	
	/**
	 * Constructor. Calls methods necessary to create the type.
	 *
	 * @internal 	This class <em>could</em> be included in the plugin as a simple function/action pair.
	 * 				However, the design calls for future functionality that will be convenient to class-wrap.
	 */
	private function __construct( )
	{	
		$this->prefix = Cloud_PREFIX;
		
		// load everything in /Cloud_Options_Pages
		$this->load_theme_classes(); 

		//load all files within specific folders within /Cloud_Options_Pages/
		if ( class_exists('Field_Type') ){
			$this->load_theme_classes('Field_Type');
			$this->load_theme_classes('Layout'); 
		}
		
		// setup 'user_enabled_overrides' and 'user_defaults' (mostly for color pickers)
		$this->setup_enabled_and_default_values(); 
		
		// get defaults array from defaults.php
		$this->set_defaults();
		
		// create self::$options_page_array by merging defaults with user array
		$this->create_options_page_array(); 		

		$this->initialize_options(); 
		
		//create options pages
		add_action('admin_menu', array( $this, 'create_options_pages' ) );
		//enqueue necessary css/js
		add_action('admin_enqueue_scripts', array($this, 'load_styles_and_scripts') );
		
		//add any customizations to MCE editor
		MCE_Plugins::init(); 

	}
	/**
	 * setting up singleton
	 */
	private static $instance;	
	public static function get_instance(){
		if ( self::$instance  == null ){
			$className = __CLASS__;
			self::$instance = new $className();
		}
		return self::$instance; 
	}
	
	
	private $options 		= array();
	protected $user_defaults 	= array();
	protected $user_enabled_overrides = array();
	private $field_types 	= array();
	
	public static $options_name  	= '' ;		
	
	public static $options_pages 	= array();
	
	
	
	/**
	 * Register the options so that WP knows about them and can handle saving
	 */
	private function initialize_options(){
		self::$options_name = $this->prefix . 'options';
		foreach ( self::$options_pages as $top_level_slug => $top_level_page ) {
		
			foreach ($top_level_page['subpages'] as $subpage_slug => $subpage) {
 
				$this->options[ $top_level_slug ][$subpage_slug] = get_option( $subpage_slug ); 
				
			}
		}
		add_action( 'admin_init', array( $this, 'register_settings' ) ); 
	}

	public function register_settings(){
	
		$option_names = array(); 
		foreach ( self::$options_pages as $top_level_slug => $top_level_page ){
			foreach ( $top_level_page['subpages'] as $subpage_slug => $subpage ){
				register_setting( $subpage_slug , $subpage_slug );
			}
		}
		foreach ($option_names as $subpage_slug){
			foreach ($subpage['sections'] as $section_slug => $section){
			}	
		}
	}
	
	/**
	 * Methods allowing access to saved options
	 * one field-specific
	 * one allowing more general and more specific results to be returned (page, section, field, group, subfield)
	 */
	public function get_option( $top_page_slug = null, $page_slug = null , $section_slug = null , $field_slug = null ){
		if ( !isset( $this->options[$top_page_slug] ) || 
			 !isset( $this->options[$top_page_slug][$page_slug] ) || 
			 !isset( $this->options[$top_page_slug][$page_slug][$section_slug] ) ||
			 !isset( $this->options[$top_page_slug][$page_slug][$section_slug][$field_slug] ) 
		){	

			return false;
		}
		$option = $this->options[$top_page_slug][ $page_slug ][ $section_slug ][ $field_slug ];	

		return $option;
	}
	public function get_options( $page_slug = null , $section_slug = null , $field_slug = null , $group_number = null, $subfield_slug = null ){		

		foreach( $this->options as $top_level_slug => $options ){
			foreach( $options as $subpage_slug => $options ){
				if ( $subpage_slug === $page_slug ){
					$top_page_slug = $top_level_slug ;
					break;
				}
			}
		}			
		// ha ha, overkill...but it might be useful to be able to grab individual group values
		if (  isset( $this->options[$top_page_slug][$page_slug][$section_slug][$field_slug][$group_number][$subfield_slug] ) && is_array( $this->options[$top_page_slug][$page_slug][$section_slug][$field_slug][$group_number] ) ) {
			return $this->options[$top_page_slug][$page_slug][$section_slug][$field_slug][$group_number][$subfield_slug];
		} else if ( is_int( $group_number ) && isset( $this->options[$top_page_slug][$page_slug][$section_slug][$field_slug][$group_number] ) ) {
			return $this->options[$top_page_slug][$page_slug][$section_slug][$field_slug][$group_number] ;
			
		} else if ( isset( $this->options[$top_page_slug][$page_slug][$section_slug][$field_slug] ) ) {
			// check if the current page has settable defaults before going to all the hassle of checking the field
			if ( self::$options_pages[$top_page_slug]['subpages'][$page_slug]['_has_settable_defaults'] ){
				if ( $this->is_enabled( $top_page_slug, $page_slug, $section_slug, $field_slug ) ){
					return $this->options[$top_page_slug][$page_slug][$section_slug][$field_slug] ;
				} else {
					return $this->user_defaults[$top_page_slug][$page_slug][$section_slug][$field_slug] ;
				}
			} else {
				return $this->options[$top_page_slug][$page_slug][$section_slug][$field_slug] ;
			}
		} else if ( isset( $this->options[$top_page_slug][$page_slug][$section_slug] ) ) {
			return $this->options[$top_page_slug][$page_slug][$section_slug] ; 
		} else if ( isset( $this->options[$top_page_slug][$page_slug] ) ){ 
			return $this->options[$top_page_slug][$page_slug] ; 
		} else if ( isset( $this->options[$top_page_slug] ) ) {
			return $this->options[$top_page_slug]; 
		} else {
			return false;
		}
	}	
	/**
	 * Methods for distilling information from the user array ( like compiling parent page, parent section, parent field, etc )
	 */
	public function get_options_array_info($subpage_slug = null, $section_slug = null, $field_slug = null ){

		if ($subpage_slug ){
			foreach (self::$options_pages as $slug => $top_level ){
				if ( isset( $top_level['subpages'][$subpage_slug] ) ){
					$top_level_slug = $slug;
					break;
				}
			}
			if ( $section_slug && $field_slug ){			
				return self::$options_pages[$top_level_slug]['subpages'][$subpage_slug]['sections'][$section_slug]['fields'][$field_slug]; 
			} else if ( $section_slug ){
				return self::$options_pages[$top_level_slug]['subpages'][$subpage_slug]['sections'][$section_slug];
			} else {
				return self::$options_pages[$top_level_slug]['subpages'][$subpage_slug];
			}
		} else {
			return self::$options_pages[$top_level_slug];
		}
	}
	public function get_options_section_info(){
		$top_level 	= $this->options_creation_tracker['top_level'];
		$subpage 	= $this->options_creation_tracker['subpage'];
		$section 	= $this->options_creation_tracker['section'];
		
		$options_array = self::$options_pages;			
		
		return $options_array[ $top_level ][ $subpage ][ $section ]; 
	}
	public function get_options_field_info(){
		$top_level 	= $this->options_creation_tracker['top_level'];
		$subpage 	= $this->options_creation_tracker['subpage'];
		$section 	= $this->options_creation_tracker['section'];
		$field 		= $this->options_creation_tracker['field'];	
		
		$options_array = self::$options_pages;		
		return $options_array[ $top_level ][ $subpage ][ $section ][ $field ]; 
	}
	private function setup_enabled_and_default_values(){
		$this->user_defaults = get_option( $this->prefix . 'user_defaults' );

		add_action('wp_ajax_set_values_as_defaults', array( __CLASS__, 'set_values_as_defaults' ) );
		add_action('wp_ajax_set_values_from_defaults', array( __CLASS__, 'set_values_from_defaults' ) );			
	}
	public function get_option_default(  $top_page_slug = null, $page_slug = null , $section_slug = null , $field_slug = null , $group_number = null, $subfield_slug = null ){
		if ( isset( $this->user_defaults[$top_page_slug][$page_slug][$section_slug][$field_slug] ) ) {
			return $this->user_defaults[$top_page_slug][$page_slug][$section_slug][$field_slug] ; 
		} else {
			return false;
		}
	}
	public function is_enabled( $top_page_slug = null, $page_slug = null , $section_slug = null , $field_slug = null ){

		if ( isset( self::$options_pages[$top_page_slug]['subpages'][$page_slug]['sections'][$section_slug]['fields'][$field_slug]['settable_defaults'] ) && self::$options_pages[$top_page_slug]['subpages'][$page_slug]['sections'][$section_slug]['fields'][$field_slug]['settable_defaults'] ){ 
			if( isset( $this->options[$top_page_slug][$page_slug]['enabled'][$section_slug][$field_slug] ) && $this->options[$top_page_slug][$page_slug]['enabled'][$section_slug][$field_slug] ){
				return true;
			} else {
				return false;
			}
		} 
		return true;
	}
	public function set_values_as_defaults(){
		$Options_Pages = Cloud_Options_Pages::get_instance();
		$success= array();
		foreach ($_POST['inputs'] as $input){
			$input_name_parts = preg_split( '/[\[\]]{1,2}/', $input['name'] );
			$subpage_slug = $input_name_parts[0];
			$section_slug = $input_name_parts[1];
			$field_slug = $input_name_parts[2];			
			$Options_Pages->user_defaults[$subpage_slug][$subpage_slug][$section_slug][$field_slug] = $input['value'];
			$success[ $input['name'] ] = $input['value'];  

		}
		update_option( $Options_Pages->prefix .'user_defaults', $Options_Pages->user_defaults) ;
		echo json_encode($success) ;
	
		die;
	}
	public function set_values_from_defaults(){
		$Options_Pages = Cloud_Options_Pages::get_instance();
		
		foreach ($_POST['inputs'] as $input){
			$input_name_parts = preg_split( '/[\[\]]{1,2}/', $input['name'] );
			$subpage_slug = $input_name_parts[0];
			$section_slug = $input_name_parts[1];
			$field_slug = $input_name_parts[2];
			$user_default_value = $Options_Pages->user_defaults[$subpage_slug][$subpage_slug][$section_slug][$field_slug] ;	
		
			$Options_Pages->options[$subpage_slug][$subpage_slug][$section_slug][$field_slug] = $user_default_value;
	
			$success[ $input['name'] ] = $user_default_value;  
		}
		update_option( $subpage_slug, $Options_Pages->options[$subpage_slug][$subpage_slug]) ;

		echo json_encode($success) ;
				
		die;
	}	
	
	
	/**
	 * Load all necessary styles and scripts, if the current page is an option page generated by this framework
	 * prevents unnecessary loading
	 */	
	public function load_styles_and_scripts( $hook ){
		$subpages = array();
		foreach (self::$options_pages as $top_level_page){
			foreach ($top_level_page['subpages'] as $subpage_slug => $subpage ){
				$subpages[] = $subpage_slug; 
			}
		}
		$current_subpage = isset( $_GET['page']  ) ? $_GET['page'] : ''; 
		
		wp_register_style('options_pages_global', Cloud_Theme__DIR . '/'.basename(dirname(__FILE__)).'/'.__CLASS__.'/_css/Options_Pages_Global.css' );
		wp_enqueue_style('options_pages_global' );
		if ( in_array( $current_subpage, $subpages ) ){ 
			// STYLES
			wp_register_style('Bootstrap Responsive', Cloud_Theme__DIR . '/'.basename(dirname(__FILE__)).'/'.__CLASS__.'/_css/bootstrap/css/bootstrap-responsive.css');
			wp_register_style('Bootstrap',  Cloud_Theme__DIR . '/'.basename(dirname(__FILE__)).'/'.__CLASS__.'/_css/bootstrap/css/bootstrap.css');
	
			wp_register_style('Options_Pages', Cloud_Theme__DIR . '/'.basename(dirname(__FILE__)).'/'.__CLASS__.'/_css/Options_Pages.css', array( 'thickbox' ) );
			wp_register_style('Field_Type', Cloud_Theme__DIR . '/'.basename(dirname(__FILE__)).'/'.__CLASS__.'/_css/Field_Type.css' );
	
			wp_enqueue_style('Bootstrap Responsive'); 				
			wp_enqueue_style('Bootstrap'); 	
			wp_enqueue_style('Field_Type');			
			wp_enqueue_style('Options_Pages');
			
			// SCRIPTS
			wp_register_script('Bootstrap',  Cloud_Theme__DIR . '/'.basename(dirname(__FILE__)).'/'.__CLASS__.'/_css/bootstrap/js/bootstrap.min.js'); 
			wp_register_script('scrollTo', Cloud_Theme__DIR . '/__inc/js/jquery.scrollTo-1.4.3.1-min.js'); 
			wp_register_script('Options_Pages', Cloud_Theme__DIR . '/'.basename(dirname(__FILE__)).'/'.__CLASS__.'/_js/Options_Pages.js', array( 'thickbox', 'media-upload', 'jquery-ui-core', 'jquery-ui-sortable' ) ); 
			wp_register_script('Field_Type', Cloud_Theme__DIR . '/'.basename(dirname(__FILE__)).'/'.__CLASS__.'/_js/Field_Type.js' ) ;
			wp_enqueue_script('Bootstrap');
			wp_enqueue_script('scrollTo');
			wp_enqueue_script('Field_Type');			
			wp_enqueue_script('Options_Pages');		
			wp_localize_script('Options_Pages', 'wp_vars', array(
				'ajax_url' 	=> admin_url( 'admin-ajax.php' ),
				'is_options_page' => isset( $_GET['page'] ) && $_GET['page'] 
			));					
		}
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

		$absolute_path = untrailingslashit( $absolute_path );

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
/*	*****************************************************************************************************************
	FUNCTIONS FOR CREATING OPTIONS PAGES
	***************************************************************************************************************** */
	/**
	 * The arrays that get merged into the master array, which is then used to create the pages. 
	 */
	
	//from defaults.php, set in set_defaults()
	public static $defaults = array(); 

	//whatever the user passes in with set_options_pages()
	private static $user_array = array();
	
	//the combined of all defaults ( including user defaults ) and user array
	private static $options_page_array = array();
	
	public static function add_options_pages( $user_array ){
		foreach ( $user_array as $key => $array ){
			self::$user_array[$key] = $array;
		}
	}		
	private function set_defaults(){
		global $options_pages_defaults; 
		self::$defaults = $options_pages_defaults;
	}
	private function create_options_page_array(){
		$defaults = self::$defaults; 
		$user_array = self::$user_array; 
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
						if ( !isset( $type ) || !class_exists( Field_Type::get_class_name( $type ) ) ) { 						
							$type = Field_Type::$default_type ; 
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
								if ( !isset( $subfield_type ) || !class_exists( Field_Type::get_class_name( $subfield_type ) ) ) { 						
									$subfield_type = Field_Type::$default_type ; 
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
						// toggle section/page if a field has the property settable_defaults = true 
						// allows for page/section reset defaults controls to be generated
						if ( $key === 'settable_defaults' && isset( $_field[$key] ) && $_field[$key] == true && !$field['cloneable'] ){
							$_section['_has_settable_defaults'] = $_section['_has_settable_defaults'] ? $_section['_has_settable_defaults'] + 1 : 1 ;
							$_subpage['_has_settable_defaults'] = $_subpage['_has_settable_defaults'] ? $_subpage['_has_settable_defaults'] + 1 : 1 ;;
							if ( $saved_default = $this->get_option_default( $top_level_slug, $subpage_slug, $section_slug, $field_slug ) ) {
								$_field['default'] =  $saved_default ; 

							}
						}					
					}
				}
			}		
		}
		self::$options_pages = $_master;
	}
	public function create_options_pages(){
		$options_page_array =& self::$options_pages; 
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
		$page_title 	= $info['subpages'][$slug]['title']; // listed as a subpage of itself...makes a strange sort of sense (look at the flyout menus!)
		$menu_title 	= $info['subpages'][$slug]['menu_title']; 
		$capability 	= $info['subpages'][$slug]['capability'] ? $info['subpages'][$slug]['capability'] : 'create_users'; 
		$menu_slug 		= $slug; 
		$callback 		= $this->get_page_layout_function(  $info['subpages'][$slug]['layout'] ); 
		$icon_url 		= $info['image'];
		$position		= $info['priority']; 

		add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $callback, $icon_url, $position );
		
		return $callback[1]; // return layout function name 
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
		$id 			= $slug;
		$page_slug		= $page_slug;
		$section_slug 	= $section_slug;
		$title 			= $info['title']; 
		$callback 		= $this->get_field_layout_function( $info, $page_slug, $section_slug, $slug, $section_layout_function ); 
		$args 			= array (
			'info'		=> $info,
			'top_level' => $top_level_slug,
			'subpage' 	=> $page_slug, 
			'section' 	=> $section_slug, 
			'field'	  	=> $slug
		);	

		add_settings_field( $id, $title, $callback, $page_slug, $section_slug, $args );
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
	private function get_page_layout_function( $layout = null ){
	
		$layout_function = Page_Layout::get_layout_function($layout, 'Page_Layout'); 
		return array('Page_Layout', $layout_function );
	}
	private function get_section_layout_function( $layout = null , $page_layout_function ) {
		$layout_function = Section_Layout::get_layout_function($layout, 'Section_Layout' , $page_layout_function ); 
		return array('Section_Layout', $layout_function );
	}
	private function get_field_layout_function( $info, $page_slug, $section_slug, $slug , $section_layout_function ) {
		$type 	= $info['type'];
		$field_type = Field_Type::get_class_name( $type );
		
		// strange, I know, but this is necessary to get the scripts added early enough. At this point, we KNOW they want to field. 
		add_action( 'admin_enqueue_scripts', array( $field_type, 'enqueue_field_scripts_and_styles' ) ); 		
		return array( $field_type, 'create_field' ) ;
	}	
	public static function get_include_path(){
		// Cloud-Theme / cloud    /    core              
		return Cloud_Theme__DIR .'/'. basename( dirname(__FILE__) ); 	
	}
	
} // End Class	
add_action( 'init' , array( 'Cloud_Options_Pages' , 'get_instance' ) ) ;


/**
 * PUBLIC FUNCTIONS FOR CONVENIENT ACCESS
 */
function get_theme_options( $subpage_id = null, $section_id = null, $field_id = null , $group_number = null, $subfield_id = null ){
	$Options_Pages = Cloud_Options_Pages::get_instance();
	return $Options_Pages->get_options( $subpage_id, $section_id, $field_id, $group_number, $subfield_id );
}
add_shortcode( 'info' , 'shortcode_theme_get_info');
function shortcode_theme_get_info( $atts ){

	extract(shortcode_atts( array(
		'p' => '',
		's' => '', 
		'f' => ''
	), $atts ));
	return nl2br( get_theme_options( $p, $s, $f ) );
	
}