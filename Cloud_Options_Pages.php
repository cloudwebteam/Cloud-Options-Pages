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
		
		// get defaults array from defaults.php
		$this->set_defaults();
		// create self::$options_page_array by merging defaults with user array
		$this->create_options_page_array(); 		

		$this->initialize_options(); 
				
		//create options pages
		add_action('admin_menu', array( $this, 'create_options_pages' ) );
		//enqueue necessary css/js
		add_action('admin_enqueue_scripts', array($this, 'load_styles_and_scripts') );
		
		//add buttons to MCE editor
		$this->add_editor_list(); 

	}
	
	private static $instance;	
	public static function get_instance(){
		if ( self::$instance  == null ){
			$className = __CLASS__;
			self::$instance = new $className();
		}
		return self::$instance; 
	}
	
	public static $options_name  	= '' ;	
	
	private $options 		= array();

	private $field_types 	= array();
	
	private static $options_pages 	= array();
	
	//from defaults.php, set in set_defaults()
	public static $defaults = array(); 

	//whatever the user passes in with set_options_pages()
	private static $user_array = array();
	
	//the combined of all defaults ( including user defaults ) and user array
	private static $options_page_array = array();
	
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
	public function load_styles_and_scripts( $hook ){
		$subpages = array();
		foreach (self::$options_pages as $top_level_page){
			foreach ($top_level_page['subpages'] as $subpage_slug => $subpage ){
				$subpages[] = $subpage_slug; 
			}
		}
		$current_subpage = isset( $_GET['page']  ) ? $_GET['page'] : ''; 

		if ( in_array( $current_subpage, $subpages ) ){ 
			// STYLES
			wp_register_style('Bootstrap Responsive', Cloud_Theme__DIR . '/__inc/bootstrap/css/bootstrap-responsive.min.css');
			wp_register_style('Bootstrap', Cloud_Theme__DIR . '/__inc/bootstrap/css/bootstrap.min.css');
	
			wp_register_style('Options_Pages', Cloud_Theme__DIR . '/'.basename(dirname(__FILE__)).'/'.__CLASS__.'/_css/Options_Pages.css', array( 'thickbox' ) );
	
			wp_enqueue_style('Bootstrap Responsive'); 				
			wp_enqueue_style('Bootstrap'); 	
			wp_enqueue_style('Options_Pages');
			
			// SCRIPTS
			wp_register_script('Bootstrap', Cloud_Theme__DIR . '/__inc/bootstrap/js/bootstrap.min.js'); 
			wp_register_script('scrollTo', Cloud_Theme__DIR . '/__inc/js/jquery.scrollTo-1.4.3.1-min.js'); 
			wp_register_script('Options_Pages', Cloud_Theme__DIR . '/'.basename(dirname(__FILE__)).'/'.__CLASS__.'/_js/Options_Pages.js', array( 'thickbox', 'media-upload' ) ); 
	
			wp_enqueue_script('Bootstrap');
			wp_enqueue_script('scrollTo');		
			wp_enqueue_script('Options_Pages');
			wp_localize_script('Options_Pages', 'wp_vars', array(
				'ajax_url' 	=> admin_url( 'admin-ajax.php' ),
				'is_options_page' => isset( $_GET['page'] ) && $_GET['page'] 
			));					
		}
	}
	private function set_defaults(){
		global $options_pages_defaults; 
		self::$defaults = $options_pages_defaults;
	}
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
	public function get_options( $top_page_slug = null, $page_slug = null , $section_slug = null , $field_slug = null , $group_number = null, $subfield_slug = null ){
		// ha ha, overkill...but it might be useful to be able to grab individual group values
		if ( isset( $this->options[$top_page_slug][$page_slug][$section_slug][$field_slug][$group_number][$subfield_slug] ) ) {
			return $this->options[$top_page_slug][$page_slug][$section_slug][$field_slug][$group_number][$subfield_slug];
		} else if ( is_int( $group_number ) && isset( $this->options[$top_page_slug][$page_slug][$section_slug][$field_slug][$group_number] ) ) {
			return $this->options[$top_page_slug][$page_slug][$section_slug][$field_slug][$group_number] ;
		} else if ( isset( $this->options[$top_page_slug][$page_slug][$section_slug][$field_slug] ) ) {
			return $this->options[$top_page_slug][$page_slug][$section_slug][$field_slug] ;
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
			return self:: $options_pages[$top_level_slug];
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

						foreach ( $defaults['fields'] as $key => $default_value ) {
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
							$_field[$key] = $set_value ;
						}						
					
					}
				}
			}		
		}
		self::$options_pages = $_master;
	}
	public static function set_options_pages( $user_array ){
		self::$user_array = $user_array;
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
			$sections[ $section['id'] ]['html'] = call_user_func($section['callback'], $section);
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
			call_user_func($field['callback'], $field['args'] );
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
		$field_type = class_exists( $type ) ? $type : Field_Type::$default_type;
		
		// strange, I know, but this is necessary to get the scripts added early enough. At this point, we KNOW they want to field. 
		add_action( 'admin_enqueue_scripts', array( $field_type, 'enqueue_field_scripts_and_styles' ) ); 		
		return array( $field_type, 'create_field' ) ;
	}	
	
	private function add_editor_list(){
		// Don't bother doing this stuff if the current user lacks permissions
		if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') ) {
			return;
		}
	   // Add only in Rich Editor mode
		if ( get_user_option('rich_editing') == 'true') {
			add_filter("mce_external_plugins", array( $this, "add_options_list_to_mce" ) );
			add_filter('mce_buttons', array ( $this, 'register_editor_mce_buttons' ) );
		}	
		add_action( 'wp_ajax_mce_get_options_list', array( $this, 'mce_get_options_list') ); 
	
	}
	public function mce_get_options_list(){
		$array = array( 'a','b','c'); 
		if ( $array ){
			echo json_encode($array);
		} else { 
			echo 0;
		}
	}
	public function add_options_list_to_mce( $plugin_array ){
		$plugin_array['options_list'] = self::get_include_path() .'/'.__CLASS__. '/_js/mce_plugins/options_list.js';
		return $plugin_array; 
	
	}
	public function register_editor_mce_buttons($buttons){
		array_push($buttons, "separator", "options_list");
		return $buttons;
	}
	public static function get_mce_options_list_info(){
		$shortcodes = array(); 
		foreach ( self::$options_pages as $top_level ){
			foreach ( $top_level['subpages'] as $subpage_slug => $subpage ){
				$shortcodes[$subpage_slug] = array();
				$shortcodes[$subpage_slug]['title'] = $subpage['title'];
				$shortcodes[$subpage_slug]['sections'] = array();
				foreach ( $subpage['sections'] as $section_slug => $section ){	
					$shortcodes[$subpage_slug]['sections'][$section_slug] = array();
					$shortcodes[$subpage_slug]['sections'][$section_slug]['title'] = $section['title'];
					foreach ( $section['fields'] as $field_slug => $field ){
						if ($field['editor_list'] === true ){
							$shortcodes[$subpage_slug]['sections'][$section_slug]['fields'][$field_slug] = array(
								'field_title' => $field['title'],
								'section_title' => $section['title'],
								'subpage_title' => $subpage['title'],
								'shortcode'		=> '[option p="'.$subpage_slug.'" s="'.$section_slug.'" f="'.$field_slug.'" ]' 
							);
						}
					}
					if (!isset( $shortcodes[$subpage_slug]['sections'][$section_slug]['fields']	)) {
						unset( $shortcodes[$subpage_slug]['sections'][$section_slug]);
					}
					
				}
				if (!isset( $shortcodes[$subpage_slug]['sections']	)) {
					unset( $shortcodes[$subpage_slug]);
				}				
			}
		}
		return $shortcodes;
	}
	public static function get_include_path(){
		// Cloud-Theme / cloud    /    core              
		return Cloud_Theme__DIR .'/'. basename( dirname(__FILE__) ); 	
	}
} // End Class	
add_action( 'init' , array( 'Cloud_Options_Pages' , 'get_instance' ) ) ;


function standard_field(){ 
	echo 'field here';
}


