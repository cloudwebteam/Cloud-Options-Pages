<?php 
abstract class Cloud_Forms {
	// path information
	public static $prefix = Cloud_prefix ;
	public static $ABS ;
	public static $dir ;
	// singleton (get method in child classes)
	protected static $instance; 
	// the master array with the defaults for form
	protected $defaults;
	// the data the user provides
	protected static $passed_in = array() ;
	// the data after it is merged with defaults;
	protected $spec;
	// the list of scripts to enqueue
	protected static $registered_scripts ;
	protected static $scripts; 
	// the list of styles to enqueue
	protected static $registered_styles ;
	protected static $styles; 
	
	protected function __construct(){
		self::$dir = Cloud_dir . '/' . __CLASS__ ;
		self::$ABS = Cloud_ABS . '/' . __CLASS__ ;
		$this->loader = Cloud_Loader::get_instance();		
				
		// loads folder with this class's name	
		$this->load_directories( array( 'Field', 'Layout' ) ); 	
		// load global scripts and styles	
		$this->load_global_scripts(); 
		$this->load_global_styles();

		// get defaults array from defaults.php
		$this->defaults = $this->set_defaults();	
		
		$this->spec = $this->merge_with_defaults();
		
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
		$this->loader->load_directory( $folder_path );
	}
	protected function load_directories( $folders = array() ){
		foreach( $folders as $directory_name ){
			$this->load_directory( $directory_name );
		}
	}

	/***====================================================================================================================================
			HANDLE LOADING OF SCRIPTS AND STYLES
		==================================================================================================================================== ***/
	protected function load_global_scripts(){
		self::register_script( 'jquery', 'http://code.jquery.com/jquery-1.9.1.min.js' ); 
		self::register_script( 'bootstrap', self::get_folder_url() .'/__inc/bootstrap/js/bootstrap.min.js', array( 'jquery' ) );  
		// full jQuery UI
		self::register_script( 'jquery-ui-core', self::get_folder_url() . '/__inc/jquery-ui-1.10.1.custom/development-bundle/ui/minified/jquery.ui.core.min.js', array('jquery') );
		self::register_script( 'jquery-effects-core', self::get_folder_url() . '/__inc/jquery-ui-1.10.1.custom/development-bundle/ui/minified/jquery.ui.effect.min.js', array('jquery') );
		self::register_script( 'jquery-effects-blind', self::get_folder_url() . '/__inc/jquery-ui-1.10.1.custom/development-bundle/ui/minified/jquery.ui.effect-blind.min.js', array('jquery-effects-core') );
		self::register_script( 'jquery-effects-bounce', self::get_folder_url() . '/__inc/jquery-ui-1.10.1.custom/development-bundle/ui/minified/jquery.ui.effect-bounce.min.js', array('jquery-effects-core') );
		self::register_script( 'jquery-effects-clip', self::get_folder_url() . '/__inc/jquery-ui-1.10.1.custom/development-bundle/ui/minified/jquery.ui.effect-clip.min.js', array('jquery-effects-core') );
		self::register_script( 'jquery-effects-drop', self::get_folder_url() . '/__inc/jquery-ui-1.10.1.custom/development-bundle/ui/minified/jquery.ui.effect-drop.min.js', array('jquery-effects-core') );
		self::register_script( 'jquery-effects-explode', self::get_folder_url() . '/__inc/jquery-ui-1.10.1.custom/development-bundle/ui/minified/jquery.ui.effect-explode.min.js', array('jquery-effects-core') );
		self::register_script( 'jquery-effects-fade', self::get_folder_url() . '/__inc/jquery-ui-1.10.1.custom/development-bundle/ui/minified/jquery.ui.effect-fade.min.js', array('jquery-effects-core') );
		self::register_script( 'jquery-effects-fold', self::get_folder_url() . '/__inc/jquery-ui-1.10.1.custom/development-bundle/ui/minified/jquery.ui.effect-fold.min.js', array('jquery-effects-core') );
		self::register_script( 'jquery-effects-highlight', self::get_folder_url() . '/__inc/jquery-ui-1.10.1.custom/development-bundle/ui/minified/jquery.ui.effect-highlight.min.js', array('jquery-effects-core') );
		self::register_script( 'jquery-effects-pulsate', self::get_folder_url() . '/__inc/jquery-ui-1.10.1.custom/development-bundle/ui/minified/jquery.ui.effect-pulsate.min.js', array('jquery-effects-core') );
		self::register_script( 'jquery-effects-scale', self::get_folder_url() . '/__inc/jquery-ui-1.10.1.custom/development-bundle/ui/minified/jquery.ui.effect-scale.min.js', array('jquery-effects-core') );
		self::register_script( 'jquery-effects-shake', self::get_folder_url() . '/__inc/jquery-ui-1.10.1.custom/development-bundle/ui/minified/jquery.ui.effect-shake.min.js', array('jquery-effects-core') );
		self::register_script( 'jquery-effects-slide', self::get_folder_url() . '/__inc/jquery-ui-1.10.1.custom/development-bundle/ui/minified/jquery.ui.effect-slide.min.js', array('jquery-effects-core') );
		self::register_script( 'jquery-effects-transfer', self::get_folder_url() . '/__inc/jquery-ui-1.10.1.custom/development-bundle/ui/minified/jquery.ui.effect-transfer.min.js', array('jquery-effects-core') );
	
		self::register_script( 'jquery-ui-accordion', self::get_folder_url() . '/__inc/jquery-ui-1.10.1.custom/development-bundle/ui/minified/jquery.ui.accordion.min.js', array('jquery-ui-core', 'jquery-ui-widget') );
		self::register_script( 'jquery-ui-autocomplete', self::get_folder_url() . '/__inc/jquery-ui-1.10.1.custom/development-bundle/ui/minified/jquery.ui.autocomplete.min.js', array('jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-position', 'jquery-ui-menu') );
		self::register_script( 'jquery-ui-button', self::get_folder_url() . '/__inc/jquery-ui-1.10.1.custom/development-bundle/ui/minified/jquery.ui.button.min.js', array('jquery-ui-core', 'jquery-ui-widget') );
		self::register_script( 'jquery-ui-datepicker', self::get_folder_url() . '/__inc/jquery-ui-1.10.1.custom/development-bundle/ui/minified/jquery.ui.datepicker.min.js', array('jquery-ui-core') );
		self::register_script( 'jquery-ui-dialog', self::get_folder_url() . '/__inc/jquery-ui-1.10.1.custom/development-bundle/ui/minified/jquery.ui.dialog.min.js', array('jquery-ui-resizable', 'jquery-ui-draggable', 'jquery-ui-button', 'jquery-ui-position') );
		self::register_script( 'jquery-ui-draggable', self::get_folder_url() . '/__inc/jquery-ui-1.10.1.custom/development-bundle/ui/minified/jquery.ui.draggable.min.js', array('jquery-ui-core', 'jquery-ui-mouse') );
		self::register_script( 'jquery-ui-droppable', self::get_folder_url() . '/__inc/jquery-ui-1.10.1.custom/development-bundle/ui/minified/jquery.ui.droppable.min.js', array('jquery-ui-draggable') );
		self::register_script( 'jquery-ui-menu', self::get_folder_url() . '/__inc/jquery-ui-1.10.1.custom/development-bundle/ui/minified/jquery.ui.menu.min.js', array( 'jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-position' ) );
		self::register_script( 'jquery-ui-mouse', self::get_folder_url() . '/__inc/jquery-ui-1.10.1.custom/development-bundle/ui/minified/jquery.ui.mouse.min.js', array('jquery-ui-widget') );
		self::register_script( 'jquery-ui-position', self::get_folder_url() . '/__inc/jquery-ui-1.10.1.custom/development-bundle/ui/minified/jquery.ui.position.min.js', array('jquery') );
		self::register_script( 'jquery-ui-progressbar', self::get_folder_url() . '/__inc/jquery-ui-1.10.1.custom/development-bundle/ui/minified/jquery.ui.progressbar.min.js', array('jquery-ui-widget') );
		self::register_script( 'jquery-ui-resizable', self::get_folder_url() . '/__inc/jquery-ui-1.10.1.custom/development-bundle/ui/minified/jquery.ui.resizable.min.js', array('jquery-ui-core', 'jquery-ui-mouse') );
		self::register_script( 'jquery-ui-selectable', self::get_folder_url() . '/__inc/jquery-ui-1.10.1.custom/development-bundle/ui/minified/jquery.ui.selectable.min.js', array('jquery-ui-core', 'jquery-ui-mouse') );
		self::register_script( 'jquery-ui-slider', self::get_folder_url() . '/__inc/jquery-ui-1.10.1.custom/development-bundle/ui/minified/jquery.ui.slider.min.js', array('jquery-ui-core', 'jquery-ui-mouse') );
		self::register_script( 'jquery-ui-sortable', self::get_folder_url() . '/__inc/jquery-ui-1.10.1.custom/development-bundle/ui/minified/jquery.ui.sortable.min.js', array('jquery-ui-core', 'jquery-ui-mouse') );
		self::register_script( 'jquery-ui-spinner', self::get_folder_url() . '/__inc/jquery-ui-1.10.1.custom/development-bundle/ui/minified/jquery.ui.spinner.min.js', array( 'jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-button' ) );
		self::register_script( 'jquery-ui-tabs', self::get_folder_url() . '/__inc/jquery-ui-1.10.1.custom/development-bundle/ui/minified/jquery.ui.tabs.min.js', array('jquery-ui-core', 'jquery-ui-widget') );
		self::register_script( 'jquery-ui-tooltip', self::get_folder_url() . '/__inc/jquery-ui-1.10.1.custom/development-bundle/ui/minified/jquery.ui.tooltip.min.js', array( 'jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-position' ) );
		self::register_script( 'jquery-ui-widget', self::get_folder_url() . '/__inc/jquery-ui-1.10.1.custom/development-bundle/ui/minified/jquery.ui.widget.min.js', array('jquery') );
		
		self::register_script( 'jquery-ui-timepicker-addon', self::get_folder_url() . '/__inc/jquery-ui-timepicker-addon/jquery-ui-timepicker-addon.js', array( 'jquery', 'bootstrap') );
		self::register_script( 'bootstrap-timepicker', self::get_folder_url() . '/__inc/bootstrap_timepicker/bootstrap-timepicker.min.js', array( 'jquery') ); 
		
		self::enqueue_script( 'jquery' ); 
		self::enqueue_script( 'bootstrap' );
		self::enqueue_script( __CLASS__, self::get_folder_url() .'/_js/Cloud_Forms.js', array( 'jquery', 'bootstrap' ) ); 
		self::enqueue_script( 'Cloud_Field', self::get_folder_url() .'/_js/Cloud_Field.js', array( 'jquery' ) ); 		
	}
	protected function load_global_styles(){	
		self::register_style( 'reset' , self::get_folder_url() .'/_css/reset.css' );
		self::register_style( 'bootstrap', self::get_folder_url() .'/__inc/bootstrap/css/bootstrap.min.css' ); 
		self::register_style( 'bootstrap-responsive', self::get_folder_url() .'/__inc/bootstrap/css/bootstrap-responsive.min.css', array( 'bootstrap' ) ); 
		
		self::register_style( 'bootstrap-timepicker', self::get_folder_url(). '/__inc/bootstrap_timepicker/bootstrap-timepicker.min.css', array( 'bootstrap') );

		self::enqueue_style( 'reset' ); 		
		self::enqueue_style( __CLASS__ .'-global', self::get_folder_url() .'/_css/Cloud_Forms_Global.css' ); 		
		self::enqueue_style( __CLASS__, self::get_folder_url() .'/_css/Cloud_Forms.css' ); 
	}
	public static function enqueue_script( $handle, $path = false , $dependencies = false ){
		if ( $path ){
			$item_to_enqueue = array(
				'handle' => $handle, 
				'path' 		=> $path,
				'dependencies' => $dependencies
			);	
		} else {
			if ( isset( self::$registered_scripts[ $handle ] ) ){
				$item_to_enqueue = self::$registered_scripts[ $handle ] ;
			}
		}
		if( isset( $item_to_enqueue ) ){
			self::$scripts[ $handle ] = $item_to_enqueue ;
		} 
	}
	public static function enqueue_style( $handle, $path = false, $dependencies = false ){
		if ( $path ){
			$item_to_enqueue = array(
				'handle' => $handle, 
				'path' 		=> $path,
				'dependencies' => $dependencies
			);	
		} else {
			if ( isset( self::$registered_styles[ $handle ] ) ){
				$item_to_enqueue = self::$registered_styles[ $handle ] ;
			}
		}
		if( isset( $item_to_enqueue ) ){
			self::$styles[ $handle ] = $item_to_enqueue ;
		} 
	}
	public static function register_script( $handle, $path, $dependencies = false ){
		$item_to_enqueue = array(
			'handle' => $handle, 
			'path' 		=> $path,
			'dependencies' => $dependencies
		);	
		
		self::$registered_scripts[ $handle ] = $item_to_enqueue ;	
	}
	public static function register_style( $handle, $path, $dependencies = false ){
		$item_to_enqueue = array(
			'handle' => $handle, 
			'path' 		=> $path,
			'dependencies' => $dependencies
		);	
		self::$registered_styles[ $handle ] = $item_to_enqueue ;
	}
	protected function sort_array_by_dependencies( $array_to_sort ){
		$sorted_array = array();
		while ( sizeof( $array_to_sort ) > 0 ){ 
			foreach( $array_to_sort as $handle => $item ){
				$all_dependencies_present = true; 
		
				if ( is_array( $item['dependencies'] ) && sizeof( $item['dependencies'] ) > 0 ){
					foreach( $item['dependencies'] as $dependency_handle ){
						if ( ! isset( $sorted_array[$dependency_handle] ) ){
							$all_dependencies_present = false ;
							break;
						}
					}
				}
				if ( $all_dependencies_present ){
					$sorted_array[ $handle ] = $item ;
					unset( $array_to_sort[ $handle ] );					
				}
			}
		}
		return $sorted_array ;
	}
	protected function filter_out_styles_without_needed_dependencies( &$item, $key, &$array ){
		if ( is_array( $item['dependencies'] ) ){
			foreach( $item['dependencies'] as $dependency ){
				if ( ! isset( $array[ $dependency ] ) ){
					if ( isset( self::$registered_styles[ $dependency ] ) ){
						$array[ $dependency ] = self::$registered_styles[ $dependency ] ;
					} else {
						unset( $array[ $key ] );
						break;
					}
				} 
			}
		}
	}		
	protected function filter_out_scripts_without_needed_dependencies( &$item, $key, &$array ){

		if ( is_array( $item['dependencies'] ) ){
			foreach( $item['dependencies'] as $dependency ){
				$dependency_found = false;
				if ( ! isset( $array[ $dependency ] ) ){				
					if ( isset( self::$registered_scripts[ $dependency ] ) ){
						$array[ $dependency ] = self::$registered_scripts[ $dependency ] ;
					} else {
						unset( $array[ $key ] );
						break;						
					}
				} 
			}
		}
	}		
	/***====================================================================================================================================
			HANDLE DEFAULTS
		==================================================================================================================================== ***/
	private function set_defaults(){
		// declared in defaults.php	
		global $cloud_form_defaults; 
		return $cloud_form_defaults;
	}			
	protected function merge_with_defaults(){
		// implemented by children ( note, can use 'finish_merge_with_defaults' to finish it out )
	}
	protected function finish_merge_with_defaults( $type, $section = array(), $subpage = array(), $top_level_page = array() ){
		// $type is in case we ever need multiple types of merges. Right now, the only type is section/metabox (same thing )
		
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
			$_field['section_slug'] = $section_slug ; 
			$_field['subpage_slug'] = $section[ 'subpage_slug' ];
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
					if ( isset ( $section['defaults']['fields'][$field_slug] ) ) {
						$set_value = $section['defaults']['fields'][$field_slug];
					} else if ( isset ( $subpage['defaults']['fields'][$field_slug] ) ) {
						$set_value = $subpage['defaults']['fields'][$field_slug];
					} else if ( isset ( $top_level_page['defaults']['fields'][$field_slug] ) ) {
						$set_value = $top_level_page['defaults']['fields'][$field_slug];
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
					$_subfield['subpage_slug'] = $section[ 'subpage_slug' ];					
					$_subfield['section_slug'] = $section_slug ; 
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
			
		==================================================================================================================================== ***/
	
	public static function get_folder_url(){
		return self::$dir ; 	
	}	
	public static function get_include_path(){
		return self::$ABS ; 	
	}
}

