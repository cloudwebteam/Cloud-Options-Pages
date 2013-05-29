<?php 
abstract class Cloud_Forms {
	// path information
	public static $prefix = Cloud_prefix ;
	public static $ABS ;
	public static $dir ;
	
	
	protected static $loader ; 
	// singleton (get method in child classes)
	protected static $instance; 
	// the master array with the defaults for form
	protected $defaults;
	// the data the user provides
	protected $passed_in = array() ;
	// the data after it is merged with defaults;
	protected $spec;
	// the list of scripts to enqueue
	protected static $registered_scripts ;
	protected static $scripts; 
	// the list of styles to enqueue
	protected static $registered_styles ;
	protected static $styles; 
	
	protected $directories_to_load = array( 'Layout', 'Field' ); 
	protected $validation_enabled = true ;
	
	protected function __construct(){

		self::$loader = Cloud_Loader::get_instance();		
		self::$dir = self::$loader->dir() . '/' . __CLASS__ ;
		self::$ABS = self::$loader->ABS() . '/' . __CLASS__ ;
		// loads folder with this class's name	
		$this->load_directories( ); 	
		// load global scripts and styles	
		$this->load_global_scripts(); 
		$this->load_global_styles();
		
		$this->set_local_javascript_vars(); 
		// get defaults array from defaults.php
		$this->defaults = $this->set_defaults();	
		$this->init();		

	}
	public static function get_instance(){
		if ( ! self::$instance ){
			self::$instance = new self();
		} 
		return self::$instance; 
	}
	protected function init(){
		
	}
		
	/***====================================================================================================================================
			HANDLE AUTO-LOADING OF FILES
		==================================================================================================================================== ***/
	protected function load_directory( $directory_name = '' ){
		$folder_path = $directory_name ? __CLASS__ . '/'.$directory_name : __CLASS__ ;	
		self::$loader->load_directory( $folder_path );		
	}
	protected function load_directories( $folders = array() ){
		foreach( $this->directories_to_load as $directory_name ){
			$this->load_directory( $directory_name );
		}
	}

	/***====================================================================================================================================
			HANDLE LOADING OF SCRIPTS AND STYLES
		==================================================================================================================================== ***/
	protected function load_global_scripts(){		
		self::register_script( 'jquery-ui-timepicker-addon', self::get_folder_url() . '/__inc/jquery-ui-timepicker-addon/jquery-ui-timepicker-addon.js', array( 'jquery', 'bootstrap') );
		self::register_script( 'bootstrap-timepicker', self::get_folder_url() . '/__inc/bootstrap_timepicker/bootstrap-timepicker.min.js', array( 'jquery') ); 
		
		self::enqueue_script( 'jquery' ); 
		self::enqueue_script( 'Cloud_Forms', self::get_folder_url() .'/_js/Cloud_Forms.js', array( 'jquery', 'scrollTo' ) ); 
		self::enqueue_script( 'Cloud_Field', self::get_folder_url() .'/_js/Cloud_Field.js', array( 'jquery' ) ); 		
	}
	protected function load_global_styles(){	
		self::enqueue_style( 'reset' ); 		
		self::enqueue_style( __CLASS__ .'-global', self::get_folder_url() .'/_css/Cloud_Forms_Global.css' ); 		
		self::enqueue_style( __CLASS__, self::get_folder_url() .'/_css/Cloud_Forms.css' ); 
	}
	protected function set_local_javascript_vars(){
		$this->global_js_vars = false; 
	}
	public static function enqueue_script( $handle, $path = false , $dependencies = false ){
		self::$loader->enqueue_script( $handle, $path, $dependencies ); 
	}
	public static function enqueue_style( $handle, $path = false, $dependencies = false ){
		self::$loader->enqueue_style( $handle, $path, $dependencies ); 
	}
	public static function register_script( $handle, $path, $dependencies = false ){
		self::$loader->register_script( $handle, $path, $dependencies ); 
	}
	public static function register_style( $handle, $path, $dependencies = false ){
		self::$loader->register_style( $handle, $path, $dependencies ); 
	}
	
	public function print_styles(){
		self::$loader->print_styles(); 
	}
	public function print_scripts(){ 
		self::$loader->print_scripts(); 
	}		
	/***====================================================================================================================================
			HANDLE DEFAULTS
		==================================================================================================================================== ***/
	protected function set_defaults(){
		// declared in defaults.php	
		global $cloud_form_defaults; 
		return $cloud_form_defaults;
	}			
	protected function merge_with_defaults( $form_slug, $form ){
		// implemented by children ( note, can use 'finish_merge_with_defaults' to finish it out )
	}
	protected function finish_merge_with_defaults( $section = array(), $subpage = array(), $top_level_page = array() ){
		
		$defaults = $this->defaults; 

		foreach ( $defaults['sections'] as $section_slug => $default_value ) {
			if ( isset( $section[$section_slug] ) ){
				$set_value = $section[$section_slug] ;
			} else {
				if ( isset ( $subpage['defaults']['sections'][$section_slug] ) ) {
					$set_value = $subpage['defaults']['sections'][$section_slug];

				} else if ( isset ( $top_level_page['defaults']['sections'][$section_slug] ) ) {
					$set_value = $top_level_page['defaults']['sections'][$section_slug];
				} else {
					$set_value = $default_value;
				}
			}
			$_section[$section_slug]	= $set_value;
		}				

		foreach ( $section['fields'] as $field_slug => $field ){
			$_section['fields'][$field_slug] = array();  
			$_field =& $_section['fields'][$field_slug];
			if ( isset( $section['metabox_slug'] ) ){
				$_field['metabox_slug'] = $section['metabox_slug'] ; 
			} else {
				$_field['top_level_slug'] = isset( $section[ 'top_level_slug' ] ) ? $section['top_level_slug'] : false ; 			
				$_field['form_slug'] = isset( $section[ 'form_slug' ] ) ? $section['form_slug'] : false ; 
				$_field['subpage_slug'] = isset( $section[ 'subpage_slug' ] ) ? $section['subpage_slug'] : false ; 
				$_field['section_slug'] = isset( $section[ 'section_slug' ] ) ? $section['section_slug'] : false ; 
			}
			$_field['field_slug'] = $field_slug; 

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
			foreach ( $field_defaults as $att_slug => $default_value ) {
 				if ( isset( $field[$att_slug] ) ){
					$set_value = $field[$att_slug];  
				} else {
					if ( isset ( $section['defaults']['fields'][$att_slug] ) ) {
						$set_value = $section['defaults']['fields'][$att_slug];
					} else if ( isset ( $subpage['defaults']['fields'][$att_slug] ) ) {
						$set_value = $subpage['defaults']['fields'][$att_slug];
					} else if ( isset ( $top_level_page['defaults']['fields'][$att_slug] ) ) {
						$set_value = $top_level_page['defaults']['fields'][$att_slug];
					} else {
						$set_value = $default_value; 
					}
					
				}

				// if something has already set the master value (like for the default (below!) )
				if ( !isset( $_field[$att_slug] ) ){							
					$_field[$att_slug] = $set_value ;
				}


			}
			// only if group 
			if ( isset( $_field['subfields'] ) ){
				foreach ( $field['subfields'] as $subfield_slug => $subfield ){
					$_field['subfields'][$subfield_slug]= array();  
					$_subfield =& $_field['subfields'][$subfield_slug]; 
					if ( isset( $section['metabox_slug'] ) ){
						$_subfield['metabox_slug'] = $section['metabox_slug'] ; 
					} else {
						$_subfield['top_level_slug'] = isset( $section[ 'top_level_slug' ] ) ? $section['top_level_slug'] : false ; 			
						$_subfield['form_slug'] = isset( $section[ 'form_slug' ] ) ? $section['form_slug'] : false ; 
						$_subfield['subpage_slug'] = isset( $section[ 'subpage_slug' ] ) ? $section['subpage_slug'] : false ; 
						$_subfield['section_slug'] = isset( $section[ 'section_slug' ] ) ? $section['section_slug'] : false ; 
					}
					$_subfield['field_slug'] = $field_slug ;		
					$_subfield['subfield_slug'] = $subfield_slug ;

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
	/***====================================================================================================================================
			FORM VALIDATION
		==================================================================================================================================== ***/

	
	// all this does is add a 'validation_error' item to the field specs of those fields that failed to validate, and return a general success or failure message	
	protected function validate_form( $form_slug ){
		$form_spec = $this->spec[ $form_slug ] ; 
		if ( $this->validation_enabled && isset( $_POST['form_id'] ) && $_POST['form_id'] == $form_slug ){
			$validation_results = Validator::validate( $_POST, $form_spec )  ;			
			$this->has_validation_errors = $validation_results['success'] ? false : true ;
    		if ( $this->has_validation_errors ){

    			if ( isset( $this->spec[ $form_slug][ 'sections' ] ) ){
    				$this->spec[ $form_slug ][ 'sections' ] = $validation_results['updated_form_spec']; 
    				
					$this->spec[ $form_slug ][ 'validation_error' ] = true; 		
					$error_found = false; 
					function check_for_error( $item, $key, &$error_found){
						if ( isset( $item['validation_error'] ) ){
							$error_found = true; 
						}
					}			
					foreach( $this->spec[ $form_slug ]['sections' ]	as &$section ){
						array_walk_recursive( $section, 'check_for_error', &$error_found );
					}
					if ( $error_found ){
						$section['validation_error'] = true; 
					}
    			} else {
    				$this->spec[ $form_slug ][ 'fields' ]  = $validation_results['updated_form_spec']; 				
	    			$this->spec[ $form_slug ][ 'validation_error' ] = true; 
                }
		    } else {
                if ( $this->spec[ $form_slug ][ 'success_function'] ){
                    call_user_func_array( $this->spec[ $form_slug ][ 'success_function'], array( $validation_results['to_save'] ) ); 
		        }
                $this->spec[ $form_slug ]['validation_error'] = false;    	    
		    }
		}		
	
	}
	public function ajax_validate_form(){
		$form_slug = $_POST['ajax_form_id']; 
		$submission_data = $_POST['ajax_form_data'] ;
		parse_str( $submission_data, $submission_data ) ;

		$Forms = Cloud_Forms_StandAlone::get_instance(); 
		if ( isset( $Forms->spec[ $form_slug ] ) ){
			$validation_results = Validator::validate( $submission_data, $Forms->spec[ $form_slug ] );  
			$response = $validation_results ; 
		} else {
			$response = array( 
				'error' => 'Not a valid form name' 
			); 
		}
		echo json_encode( $response );
		die; 
	}	
	/***====================================================================================================================================
			PUBLIC FUNCTIONS
		==================================================================================================================================== ***/
	public function get_spec( $form_slug ){
		return !empty( $this->spec[$form_slug] ) ? $this->spec[ $form_slug ] : false ;
	}
	
	/***====================================================================================================================================
			
		==================================================================================================================================== ***/
	
	public static function get_folder_url(){
		return self::$dir ; 	
	}	
	public static function get_include_path(){
		return self::$ABS ; 	
	}
}

