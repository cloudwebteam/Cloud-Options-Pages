<?php 
abstract class Cloud_Forms {
	// path information
	public static $prefix = Cloud_prefix ;
	public static $ABS ;
	public static $dir ;
	// singleton (get method in child classes)
	protected static $instance; 
	// what directories, in addition to the one with this class name, would you like to load?
	protected $directories_to_load = array('Field');	
	// the master array with the defaults for form
	protected $defaults;
	// the data the user provides
	protected $passed_in;
	// the data after it is merged with defaults;
	protected $spec;
	// the list of scripts to enqueue
	protected $scripts; 
	// the list of styles to enqueue
	protected $styles; 
	
	protected function __construct(){
		self::$dir = Cloud_dir . '/' . __CLASS__ ;
		self::$ABS = Cloud_ABS . '/' . __CLASS__ ;
		$this->loader = Cloud_Loader::get_instance();		
				
		// loads folder with this class's name	
		$this->load_directories(); 	
		// load global scripts and styles	
		$this->load_global_scripts(); 
		$this->load_global_styles();
		
		// get defaults array from defaults.php
		$this->set_defaults();		
	}
	protected function init(){}
		
	/***====================================================================================================================================
			HANDLE AUTO-LOADING OF FILES
		==================================================================================================================================== ***/
	protected function load_directory(){
		$this->loader->load_directory( __CLASS__ );
	}
	protected function load_directories(){
		$this->load_directory();
		foreach( $this->directories_to_load as $directory_name ){
			$this->loader->load_directory( $directory_name );
		}
	}

	/***====================================================================================================================================
			HANDLE LOADING OF SCRIPTS AND STYLES
		==================================================================================================================================== ***/
	protected function load_global_scripts(){
		// use $this->enqueue_script();
	}
	protected function load_global_styles(){	
		// use $this->enqueue_style();	
	}
	protected function enqueue_script( $handle, $path, $dependencies = false ){
		$item_to_enqueue = array(
			'handle' => $handle, 
			'path' 		=> $path,
			'dependencies' => $dependencies
		);	
		$this->scripts[] = $item_to_enqueue ;	
	}
	protected function enqueue_style( $handle, $path, $dependencies = false ){
		$item_to_enqueue = array(
			'handle' => $handle, 
			'path' 		=> $path,
			'dependencies' => $dependencies
		);	
		$this->styles[] = $item_to_enqueue ;
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
	/***====================================================================================================================================
			HANDLE DEFAULTS
		==================================================================================================================================== ***/
	private function set_defaults(){
		// declared in defaults.php	
		global $form_defaults; 
		$this->defaults = $form_defaults;
	}			
	protected static function merge_with_defaults( $type, $section = array(), $subpage = array(), $top_level_page = array() ){
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
	

}

