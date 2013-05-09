<?php 
class Cloud_Forms_WP extends Cloud_Forms {
	protected $forms = array(); 
	protected $directories_to_load = array('Field', 'WP', 'Layout');	
	protected $wp_saved ; 
	// singleton get method
	public static function get_instance(){
		if ( !self::$instance ){
			self::$instance = new self(); 
		}
		return self::$instance; 
	}
	protected function init(){
		add_action( 'init', array( $this, 'wp_init' ) ); 	
	}
	public function wp_init(){
		if ( $this->spec ){
			$this->wp_saved = $this->get_WP_saved_data(); 
			add_action( 'admin_menu', array( $this, 'construct_forms' ) ); 
			add_action( 'admin_init', array( $this, 'register_form_settings') ); 
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts_and_styles' ) ); 
		}		
	}	
	/***====================================================================================================================================
			CREATING SPEC
		==================================================================================================================================== ***/
	protected function merge_with_defaults( $top_level_slug, $top_level_page ){
		$defaults = $this->defaults; 
		$_top_level = array() ;
		foreach( $defaults['top_level'] as $option_key => $default_value ){
			if ( isset( $top_level_page[$option_key] ) ){
				$set_value = $top_level_page[$option_key];
			} else {
				$set_value = $default_value;
			}
			$_top_level[$option_key] = $set_value;		
		}
		foreach( $top_level_page['subpages'] as $subpage_slug => $subpage ){
			$_top_level['subpages'][$subpage_slug] = array();  
			$_subpage =& $_top_level['subpages'][$subpage_slug];			
			foreach ( $defaults['subpages'] as $option_key => $default_value ) {

			
				if ( isset( $subpage[ $option_key ] ) ){
					$set_value = $subpage[$option_key];
				} else {
					if ( isset ( $top_level_page['defaults']['subpages'][$option_key] ) ) {
						$set_value = $top_level_page['defaults']['subpages'][$option_key];
					} else {
						$set_value = $default_value;
					}					
				}
				$_subpage[$option_key] = $set_value;
			}		

			foreach ( $subpage['sections'] as $section_slug => $section){
				$_subpage['sections'][$section_slug] = array();  
				$_section =& $_subpage['sections'][$section_slug];	
				$section['top_level_slug'] = $top_level_slug ; 
				$section['subpage_slug'] = $subpage_slug;
				$section['section_slug'] = $section_slug;
				$_section = $this->finish_merge_with_defaults( $section, $subpage ); 
			}
				
		}
		return $_top_level ; 
	}
	protected function get_WP_saved_data(){
		$data = array(); 
		foreach( $this->spec as $top_level_slug => $top_level_page ){
			foreach( $top_level_page['subpages'] as $subpage_slug => $subpage ){
				$data[ $top_level_slug ][ $subpage_slug ] = get_option( $subpage_slug ); 
			}
		}

		return $data ; 

	}
	/***====================================================================================================================================
			LOAD SCRIPTS AND STYLES
		==================================================================================================================================== ***/
	protected function set_local_javascript_vars(){
		$this->global_js_vars = array( 
			'ajax_url' => self::$dir . '/ajax/standAlone.php',
			'cloud_url' => self::$dir
		); 
	}
	public function enqueue_scripts_and_styles(){
		foreach( self::$registered_scripts as $script ){
			wp_register_script( $script['handle'], $script['path'], $script['dependencies'] ); 
		}
		foreach( self::$scripts as $script ){
			wp_enqueue_script( $script['handle'], $script['path'], $script['dependencies'] ); 
		}
		foreach( self::$registered_styles as $style ){
			wp_register_style( $style['handle'], $style['path'], $style['dependencies'] ); 
		}
		foreach( self::$styles as $style ){
			if ($style['handle'] !== 'reset' ){
				wp_enqueue_style( $style['handle'], $style['path'], $style['dependencies'] ); 
			}
		}
		wp_localize_script( 'Cloud_Forms', 'cloud', $this->global_js_vars ); 
	}



	public function construct_forms(){
		$forms = array();
		foreach( $this->spec as $top_level_slug => $top_level_page ){
			if ( isset( $top_level_page['subpages'][$top_level_slug] ) ){
				$same_name_subpage = $top_level_page['subpages'][$top_level_slug]; 
				$page_title = $same_name_subpage['title'] ; 
				$menu_title = $same_name_subpage['menu_title'] ? $same_name_subpage['menu_title'] : $page_title ;  
				$capability = $same_name_subpage['capability'] ; 
				$menu_slug = $top_level_slug ; 
				$icon_url = $top_level_page['image'] ; 
				$position = $top_level_page['priority'] ; 
				$function = array( 'Layout_WP_Page' , Layout_WP_Page::get_layout_function( $same_name_subpage[ 'layout' ] ) ) ;
				add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );

			} else {
				echo 'Cloud Notice: You have not added a subpage with the top level slug <br />' ; 
			}
			unset( $top_level_page['subpages'][$top_level_slug] ); 
			foreach( $top_level_page['subpages'] as $subpage_slug => $subpage ){
				$page_title = $subpage['title'] ; 
				$menu_title = $subpage['menu_title'] ? $subpage['menu_title'] : $page_title ;  
				$capability = $subpage['capability'] ; 
				$menu_slug = $top_level_slug . '.' .$subpage_slug ; 
				$icon_url = $top_level_page['image'] ; 
				$position = $top_level_page['priority'] ; 

				$function = array( 'Layout_WP_Page' , Layout_WP_Page::get_layout_function( $subpage[ 'layout' ] ) ) ; 

				add_submenu_page( $top_level_slug, $page_title, $menu_title, $capability, $menu_slug, $function );

			}
		}
		return $forms; 
	}
	public function register_form_settings(){
		foreach( $this->spec as $top_level_slug => $top_level_page ){
			foreach( $top_level_page['subpages'] as $subpage_slug => $subpage ){
				register_setting( $subpage_slug, $subpage_slug ); 
			}
		}
		return true; 	
	}

	/***====================================================================================================================================
			HANDLE DEFAULTS
		==================================================================================================================================== ***/
	protected function set_defaults(){
		// declared in wp/wp-defaults.php	
		global $cloud_form_defaults ; 		
		global $cloud_form_defaults_wp; 
		$combined_defaults = array(); 
		foreach( $cloud_form_defaults_wp as $key => $wp_defaults ){
			if ( $key !== 'fields' ){
				$defaults = isset( $cloud_form_defaults[ $key ] ) ? $cloud_form_defaults[ $key ] : array() ;			
				$combined_defaults[ $key ] = array_merge( $defaults, $wp_defaults ); 
			} else {
				foreach( $wp_defaults as $field_type => $wp_field_defaults ){
					$field_defaults = isset( $cloud_form_defaults[ $key ][ $field_type ] ) ? $cloud_form_defaults[ $key ][ $field_type ] : array() ;			
					$combined_defaults[ $key ][ $field_type ] = array_merge( $field_defaults, $wp_field_defaults ); 			
				}
			}
		}
		return $combined_defaults;
	}	
	
	/***====================================================================================================================================
			PUBLIC FUNCTIONS
		==================================================================================================================================== ***/


	public function register( $arg1, $arg2 = false ){
		if ( is_array( $arg1 ) ){
			foreach( $arg1 as $top_page_slug => $top_level_page ){
				$this->passed_in[ $top_page_slug ] = $top_level_page;
				$this->spec[ $top_page_slug ] = $this->merge_with_defaults( $top_page_slug, $top_level_page );
			}
		} else {
			$top_page_slug = $arg1; 
			$top_level_page = $arg2; 
			$this->passed_in[ $top_page_slug ] = $top_level_page;
			$this->spec[ $top_page_slug ] = $this->merge_with_defaults( $top_page_slug, $top_level_page );		
		}
	}	
	public function display( $form_slug ){
		if ( isset( $this->forms[ $form_slug ] ) ){
			echo $this->forms[ $form_slug ] ;
		} else { ?>
			<div class="cloud cloud-form form-not-found">Form "<?php echo $form_slug; ?>" has not been registered</div>
		<?php }
	}
	public function get_spec( $top_slug, $sub_slug = false ){
		if ( $sub_slug ){
			return !empty( $this->spec[$top_slug]['subpages'][$sub_slug] ) ? $this->spec[ $top_slug ]['subpages'][$sub_slug] : false ;
		} else {
			return !empty( $this->spec[$top_slug] ) ? $this->spec[ $top_slug ] : false ;
		}
	}
	public function get_saved_data( $top_level_slug = false, $subpage_slug = false, $section_slug = false, $field_slug = false, $clone_number = false, $subfield_slug = false ){
		$value = false;
 
		$array_values = $this->wp_saved ;
		if ( $top_level_slug === false ){
			$value = $array_values ; 
		} else {
			$array_values = isset( $this->wp_saved[ $top_level_slug ] ) ? $this->wp_saved[ $top_level_slug ] : false ;
			if ( $array_values ){
				if ( $subpage_slug === false ){
					$value = $array_values ; 
				} else {
					$array_values = isset( $this->wp_saved[ $top_level_slug ][ $subpage_slug ] ) ? $this->wp_saved[ $top_level_slug ][ $subpage_slug ] : false ;
					if ( $array_values ){
						if ( $section_slug === false ){
							$value = $array_values ; 
						} else {
							$array_values = isset( $this->wp_saved[ $top_level_slug ][ $subpage_slug ][ $section_slug ] ) ? $this->wp_saved[ $top_level_slug ][ $subpage_slug ][ $section_slug ] : false ;
							if ( $array_values ){
								if ( $field_slug === false ){
									$value = $array_values ; 
								} else {
									$array_values = isset( $this->wp_saved[ $top_level_slug ][ $subpage_slug ][ $section_slug ][ $field_slug ] ) ? $this->wp_saved[ $top_level_slug ][ $subpage_slug ][ $section_slug ][ $field_slug ] : false ;
									if ( $array_values ){
										if ( $clone_number === false ){
											$value = $array_values ; 
										} else {
											$array_values = isset( $this->wp_saved[ $top_level_slug ][ $subpage_slug ][ $section_slug ][ $field_slug ][ $clone_number ] ) ? $this->wp_saved[ $top_level_slug ][ $subpage_slug ][ $section_slug ][ $field_slug ][ $clone_number ] : false ;
											if ( $array_values ){
												if ( $subfield_slug === false ){
													$value = $array_values ; 
												} else {
													$array_values = isset( $this->wp_saved[ $top_level_slug ][ $subpage_slug ][ $section_slug ][ $field_slug ][ $clone_number ][ $subfield_slug ] ) ? $this->wp_saved[ $top_level_slug ][ $subpage_slug ][ $section_slug ][ $field_slug ][ $clone_number ][ $subfield_slug ] : false ;
													if ( $array_values ){
														$value = $array_values ; 
													}
												}
											}										
										}
									}
								}
							}						
						}
					}
				}
			}
		}
		return $value ; 
	}	
	
	
	
	public static function get_folder_url(){
		return self::$dir .'/'. basename( __FILE__, '.php' )  ; 	
	}	
	public static function get_include_path(){
		return self::$ABS .'/'. basename( __FILE__, '.php' ) ; 	
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
}