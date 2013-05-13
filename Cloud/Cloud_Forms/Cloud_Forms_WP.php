<?php 
class Cloud_Forms_WP extends Cloud_Forms {
	protected $forms = array(); 
	protected $directories_to_load = array('Field', 'WP', 'Layout');	
	protected $wp_saved ; 
	protected $passed_in_pages = array() ; 
	protected $pages = array() ; 
	protected $passed_in_metaboxes = array() ; 
	protected $metaboxes = array() ; 
	protected $valid_metaboxes = array(); 
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
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts_and_styles' ) ); 	
		
		// options pages
		$this->wp_saved = $this->get_WP_saved_data(); 
		add_action( 'admin_menu', array( $this, 'construct_pages' ) ); 
		add_action( 'admin_init', array( $this, 'register_form_settings') ); 
		
		//metaboxes 
		add_action( 'add_meta_boxes', array( $this, 'construct_metaboxes' ) ); 
		add_action( 'save_post', array( $this, 'save_metaboxes' ) );
		
		Cloud_Field::$is_WP = true; 
		
	}	
	/***====================================================================================================================================
			CREATING SPEC
		==================================================================================================================================== ***/
	protected function merge_page_with_defaults( $top_level_slug, $top_level_page ){
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
	protected function merge_metabox_with_defaults( $metabox_slug, $metabox ){
		$defaults = $this->defaults; 
		$section = $metabox ; //metabox has same structure as a section
		$section['metabox_slug'] = $metabox_slug;

		// merge in the defaults' 'forms' array, and unset the sections (since obviously this form has no sections)
		$_metabox = array(); 
		foreach ( $defaults['sections'] as $option_key => $default_value ) {
			if ( isset( $section[$option_key] ) ){
				$set_value = $section[$option_key] ;
			} else {
				$set_value = $default_value;
			}
			$_metabox[$option_key] = $set_value;
		}			
		$_metabox = array_merge( $_metabox, $this->finish_merge_with_defaults( $section ) ); 
		return $_metabox ; 
	}	
	protected function get_WP_saved_data(){
		$data = array(); 
		foreach( $this->pages as $top_level_slug => $top_level_page ){
			foreach( $top_level_page['subpages'] as $subpage_slug => $subpage ){
				$data[ $subpage_slug ] = get_option( $subpage_slug ); 
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



	public function construct_pages(){

		$forms = array();
		foreach( $this->pages as $top_level_slug => $top_level_page ){
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
	public function construct_metaboxes(){
		foreach( $this->valid_metaboxes as $metabox_slug => $metabox ){

			$id = $metabox_slug ; 
			$title = $metabox['title'];
			$callback =  array( 'Layout_WP_Metabox', Layout_WP_Metabox::get_layout_function( $metabox[ 'layout' ] ) );
			$post_type = '' ; 
			$context = $metabox['context']  ; 
			$priority = $metabox['priority'] ; 
			$callback_args = array( 'metabox_slug' => $metabox_slug, 'spec' => $metabox ); 
			
			add_meta_box( $id, $title, $callback, $post_type, $context, $priority, $callback_args );
		}
	}
	public function save_metaboxes( $post_id ){
		  // First we need to check if the current user is authorised to do this action. 
		  if ( 'page' == $_POST['post_type'] ) {
			  if ( ! current_user_can( 'edit_page', $post_id ) )
			  	return;
		  } else {
			  if ( ! current_user_can( 'edit_post', $post_id ) )
			  	return;
		  }	
		// Now can save the value to the database
		if ( !wp_is_post_revision( $post_id ) ){
			$Forms = Cloud_Forms_WP::get_instance();
			foreach ( $Forms->metaboxes as $metabox_id => $metabox ){
				if ( isset( $_POST[ $metabox_id ] ) ){
					if ( is_array( $_POST[ $metabox_id ] ) ){
						if ( is_array_empty( $_POST[ $metabox_id ] ) ){
							delete_post_meta( $post_id, $metabox_id, false ); 
						} else {
							update_post_meta( $post_id, $metabox_id, $_POST[ $metabox_id ] ); 						
						}
					}
				} 
			}
		}
		return $post_id ;			  
	}
	public function register_form_settings(){
		foreach( $this->pages as $top_level_slug => $top_level_page ){
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


	public function add_pages( $arg1, $arg2 = false ){
		if ( is_array( $arg1 ) ){
			foreach( $arg1 as $top_page_slug => $top_level_page ){
				$this->passed_in[ $top_page_slug ] = $top_level_page;
				$this->pages[ $top_page_slug ] = $this->merge_page_with_defaults( $top_page_slug, $top_level_page );
			}
		} else {
			$top_page_slug = $arg1; 
			$top_level_page = $arg2; 
			$this->passed_in_pages[ $top_page_slug ] = $top_level_page;
			$this->pages[ $top_page_slug ] = $this->merge_page_with_defaults( $top_page_slug, $top_level_page );		
		}
	}	
	public function add_metaboxes( $add_to, $metaboxes, $context = 'normal', $priority = 'low' ){
		foreach( $metaboxes as $metabox_slug => $metabox ){
			$this->passed_in_metaboxes[ $metabox_slug ] = $metabox;
			
			if ( empty( $metabox['priority'] ) ){
				$metabox['priority'] = $priority; 
			}
			if ( empty( $metabox['context'] ) ){
				$metabox['context'] = $context; 
			}
			$metabox_spec = $this->merge_metabox_with_defaults( $metabox_slug, $metabox );
			$this->metaboxes[ $metabox_slug ] = $metabox_spec ; 
			if ( $this->is_valid_on_current_page( $add_to ) ){
				$this->valid_metaboxes[ $metabox_slug ] = $metabox_spec ; 
			}
		}
	}
	public function is_valid_on_current_page( $add_to ){
		if ( is_string( $add_to ) ){
			// slug or title provided
			if ( $post = get_page_by_title( $add_to ) ){
				$posts = array( $post ); 
			} else {
				$registered_post_types = get_post_types( array(), 'objects' );
				foreach( $registered_post_types as $slug => $post_type ){
					$post_types[] = $slug; 
				}
				$posts = get_posts( array(
					'name' => $add_to, 
					'post_type' => $post_types
				) );
			}
		} else if ( is_numeric( $add_to ) ){
			// ID provided
			$post = get_post( $add_to );
			$posts = array( $post ); 
		} else if ( is_array( $add_to ) ){ 
			// check if its just a declaration of post_types
			if ( sizeof( $add_to ) > 0 && isset( $add_to['post_type'] ) ) {
				if ( isset( $_GET['post_type'] ) ){
					//creating new post of this post type? 
					if ( stripos( $_SERVER['REQUEST_URI'] , 'post-new' ) !== false ){
						if ( is_array( $add_to['post_type'] ) && in_array($_GET['post_type'], $add_to['post_type'] ) ){
							return true;
						} else if ($_GET['post_type'] === $add_to['post_type'] ){
							return true; 	
						}
					}
				} else {
					if ( stripos( $_SERVER['REQUEST_URI'] , 'post-new' ) !== false ){
						if ( $add_to['post_type'] == 'post' ){
							return true;
						} else if ( is_array( $add_to['post_type'] ) && in_array('post', $add_to['post_type'] ) ){
							return true;
						}
					}
				}
			} 
			$add_to['numberposts'] = -1; 
			$posts = get_posts( $add_to );
			
		}
		
		if ( is_array( $posts ) ){
			$valid_post_IDs = array();
			foreach( $posts as $valid_post ){
				$valid_post_IDs[] = $valid_post->ID ;
			}
			if ( isset( $_GET['post'] )){
				$current_post = get_post( $_GET['post'] );
				if ( in_array( $_GET['post'], $valid_post_IDs ) || in_array( $current_post->ID, $valid_post_IDs ) ){
					return true; 
				}
			} else if ( isset( $_POST['post_ID'] ) ){
				$current_post = get_post( $_POST['post_ID'] );
				if ( in_array( $current_post->ID, $valid_post_IDs ) || in_array( $current_post->ID, $valid_post_IDs ) ){
					return true; 
				}			
			} else {
				return false;
			}
		} else {
			return false;
		}	
	}
	public function display( $form_slug ){
		if ( isset( $this->forms[ $form_slug ] ) ){
			echo $this->forms[ $form_slug ] ;
		} else { ?>
			<div class="cloud cloud-form form-not-found">Form "<?php echo $form_slug; ?>" has not been registered</div>
		<?php }
	}
	public function get_page_spec( $top_slug, $sub_slug = false ){
		if ( $sub_slug ){
			return !empty( $this->pages[$top_slug]['subpages'][$sub_slug] ) ? $this->pages[ $top_slug ]['subpages'][$sub_slug] : false ;
		} else {
			return !empty( $this->pages[$top_slug] ) ? $this->pages[ $top_slug ] : false ;
		}
	}
	public function get_metabox_spec( $metabox_slug){
		return !empty( $this->metaboxes[$metabox_slug] ) ? $this->metaboxes[ $metabox_slug ] : false ;
	}	
	
	public function get_page_data( $top_level_slug = false, $subpage_slug = false, $section_slug = false, $field_slug = false, $clone_number = false, $subfield_slug = false ){
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
	public function get_metabox_data( $post_id, $metabox_slug, $field_slug = false, $clone_number = false, $subfield_slug = false ){
		$value = false; 
		$array_values = get_post_meta( $post_id, $metabox_slug, true );

		
		if ( $array_values ){
			if ( $field_slug === false ){
				$value = $array_values ; 
			} else {
				$array_values = isset( $array_values[ $field_slug ] ) ? $array_values[ $field_slug ] : false ;
				if ( $array_values ){
					if ( $clone_number === false ){
						$value = $array_values ; 
					} else {
						$array_values = isset( $array_values[ $field_slug ][ $clone_number ] ) ? $array_values[ $field_slug ][ $clone_number ] : false ;
						if ( $array_values ){
							if ( $subfield_slug === false ){
								$value = $array_values ; 
							} else {
								$array_values = isset( $array_values[ $field_slug ][ $clone_number ][ $subfield_slug ] ) ? $array_values[ $field_slug ][ $clone_number ][ $subfield_slug ] : false ;
								if ( $array_values ){
									$value = $array_values ; 
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

}

/***====================================================================================================================================
		GLOBAL FUNCTIONS
	==================================================================================================================================== ***/
function get_theme_options( $subpage_slug = false, $section_slug = false, $field_slug = false, $clone_number = false, $subfield_slug = false ){
	$Forms = Cloud_Forms_WP::get_instance(); 
	return $Forms->get_page_data( $subpage_slug, $section_slug, $field_slug, $clone_number, $subfield_slug ); 
}
function get_metabox_options( $post_id, $metabox_slug = false , $field_slug = false, $group_number = false, $subfield_slug = false ){
	if ( ! is_numeric( $post_id ) ){
		global $post; 
		// shift parameters if post_id not provided
		$subfield_slug = $group_number;
		$group_number = $field_slug ;
		$field_slug = $metabox_slug ; 
		$metabox_slug = $post_id; 
		$post_id = $post->ID; 
	} else {
		$post_id = intval( $post_id );
	}	
	$Forms = Cloud_Forms_WP::get_instance(); 

	return $Forms->get_metabox_data( $post_id, $metabox_slug, $field_slug, $group_number, $subfield_slug ); 
	
	

}
function is_array_empty($InputVariable){
   $Result = true;

   if (is_array($InputVariable) && count($InputVariable) > 0) {
      foreach ($InputVariable as $Value)
      {
         $Result = $Result && is_array_empty($Value);
      }
   } else {
      $Result = empty($InputVariable);
   }

   return $Result;
}
