<?php 
class Cloud_Forms_WP extends Cloud_Forms {
	protected $directories_to_load = array('Field', 'WP', 'Layout');	
	protected $wp_saved ; 
	
	protected $passed_in_pages = array() ; 
	protected $pages = array() ; 
	
	protected $passed_in_metaboxes = array() ; 
	protected $metaboxes = array() ; 
	protected $valid_metaboxes = array(); 
	
	protected $passed_in_forms = array() ; 
	protected $forms = array() ; 

	protected $supports_to_remove = array();
	protected $supports_to_add = array(); 
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
		if ( is_admin() ){
			$this->enqueue_style( 'jquery-ui-lightness' ); 
		}
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts_and_styles' ) ); 	
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts_and_styles' ) ); 	
		
		// options pages
		$this->wp_saved = $this->get_WP_saved_data(); 
		add_action( 'admin_menu', array( $this, 'construct_pages' ) ); 
		add_action( 'admin_init', array( $this, 'register_form_settings') ); 
		
		//metaboxes 
		add_action( 'add_meta_boxes', array( $this, 'construct_metaboxes' ) ); 
		add_action( 'save_post', array( $this, 'save_metaboxes' ) );		
		
		//removing supports
		add_action( 'admin_init', array( $this, 'add_remove_supports') ); 
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
				$_section = $this->finish_merge_with_defaults( $section, $subpage, $top_level_page ); 
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
	protected function merge_form_with_defaults( $form_slug, $form ){
		$defaults = $this->defaults; 
		$_form = array() ;
		
		// if it has sub-sections, deal with it as a complex form
		if ( isset( $form['sections'] ) ){
			foreach ( $defaults['forms'] as $subpage_slug => $default_value ) {
				if ( isset( $form[$subpage_slug] ) ){
					$set_value = $form[$subpage_slug];
				} else {
					if ( isset ( $top_level_page['defaults']['forms'][$subpage_slug] ) ) {
						$set_value = $top_level_page['defaults']['forms'][$subpage_slug];
					} else {
						$set_value = $default_value;
					}					
				}
				$_form[$subpage_slug] = $set_value;
			}						
			foreach ( $form['sections'] as $section_slug => $section){
				$_form['sections'][$section_slug] = array();  
				$_section =& $_form['sections'][$section_slug];	
				$section['form_slug'] = $form_slug;
				$section['section_slug'] = $section_slug;
				$_section = $this->finish_merge_with_defaults( $section, $form ); 
			}
		
		// if it doesn't, just proceed with a simple form. 
		} else {		
			$form['form_slug'] = $form_slug ; 
			// merge in the defaults' 'forms' array, and unset the sections (since obviously this form has no sections)
			foreach ( $defaults['forms'] as $option_key => $default_value ) {
				if ( isset( $form[$option_key] ) ){
					$set_value = $form[$option_key] ;
				} else {
					$set_value = $default_value;
				}
				$_form[$option_key] = $set_value;
			}			
			$_form = array_merge( $_form, $this->finish_merge_with_defaults( $form ) ); 
			unset( $_form['sections'] ); 
		}		
		return $_form;		
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
			'cloud_ajax' => self::$dir . '/ajax/standAlone.php',
			'wp_ajax' => admin_url( 'admin-ajax.php'),
			'cloud_url' => self::$dir,
			
		); 
	}
	protected function get_needed_field_scripts_and_styles(){
		$url = parse_url( $_SERVER['REQUEST_URI'] ) ; 
		$script = basename( $url['path'] ); 
		
		//sets $query to array of parameters
		$query = array();
		if ( isset( $url['query'] ) ){
			parse_str( $url['query'], $query ); 
		}
		function run_field_enqueue_function( $item, $key ){
			if ( $key === 'type' ){
				$field_type = $item ; 
				$field_classname = Cloud_Field::get_class_name( $field_type );
				$field_classname::enqueue_scripts_and_styles( $field_type ) ;	// only this early to get them in Wordpress's queue early enough
			}
		}
		// if any valid metaboxes on page, obviously not an options page, and has no forms
		if ( $this->valid_metaboxes ){
			array_walk_recursive( $this->valid_metaboxes, 'run_field_enqueue_function' ); 
		// if its a valid options page, obviously no forms
		} else if ( $script === 'admin.php' ){ 		
			if ( isset( $query['page'] ) ){
				foreach( $this->pages as $top_level_slug => $page ){
					if ( $query['page'] == $top_level_slug ){
						array_walk_recursive( $page['subpages'][$top_level_slug], 'run_field_enqueue_function' ); 
						break;
					}
					foreach( $page['subpages'] as $subpage_slug => $subpage ){
						if ( $query['page'] === $top_level_slug . '.' . $subpage_slug ){
							array_walk_recursive( $subpage, 'run_field_enqueue_function' ); 
							break 2;						
						}
					}
				}
			}
		// otherwise, there's no telling when they'll display a form, so we gotta be ready (only add the form on pages you want it!)
		} else if ( $this->forms ) {
			array_walk_recursive( $this->forms, 'run_field_enqueue_function' ); 
		}

	}
	
	public function enqueue_scripts_and_styles(){
		$this->get_needed_field_scripts_and_styles(); 
		$registered_scripts = self::$loader->get_registered_scripts(); 
		foreach( $registered_scripts as $script ){
			wp_register_script( $script['handle'], $script['path'], $script['dependencies'] ); 
		}
		$enqueued_scripts = self::$loader->get_scripts(); 
		foreach( $enqueued_scripts as $script ){		
			wp_enqueue_script( $script['handle'], $script['path'], $script['dependencies'] ); 
		}
		
		$registered_styles = self::$loader->get_registered_styles(); 
		foreach( $registered_styles as $style ){
			wp_register_style( $style['handle'], $style['path'], $style['dependencies'] ); 
		}
		$enqueued_styles = self::$loader->get_styles(); 
		foreach( $enqueued_styles as $style ){
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
	public function form( $form_slug ){
		if ( isset( $this->forms[$form_slug] ) ){
			$this->validate_form( $form_slug ); 	
			$form_spec = $this->forms[ $form_slug ] ; 
			if ( isset( $form_spec['sections'] ) ){
				$layout = Layout_Form::get_layout_function( $form_spec['layout'] );
				$form_html = Layout_Form::$layout( $form_slug, $form_spec ); 
			} else {
				$form_html = Layout_Section::standAlone( $form_slug, $form_spec ); 
			}			
		} else {
			$form_html = 'No form "'.$form_slug.'" found' ; 		
		}
	
		echo $form_html; 		
	}
	public function display( $form_slug ){
		$this->form( $form_slug ); 
	}	
	public function save_metaboxes( $post_id ){

		// Now can save the value to the database
		if ( isset( $_POST['post_type'] ) && !wp_is_post_revision( $post_id ) ){
            // First we need to check if the current user is authorised to do this action. 
            if ( 'page' == $_POST['post_type'] ) {
                if ( ! current_user_can( 'edit_page', $post_id ) )
                    return;
            } else {
                if ( ! current_user_can( 'edit_post', $post_id ) )
                    return;
            }	
            
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
	public function add_forms( $arg1, $arg2 = false ){
		if ( is_array( $arg1 ) ){
			foreach( $arg1 as $form_slug => $form ){
				$this->passed_in_forms[ $form_slug ] = $form;
				$this->forms[ $form_slug ] = $this->merge_form_with_defaults( $form_slug, $form );
			}
		} else {
			$form_slug = $arg1; 
			$form = $arg2; 
			$this->passed_in_forms[ $form_slug ] = $arg2;
			$this->forms[ $form_slug ] = $this->merge_form_with_defaults( $form_slug, $form );
		}
	} 
	public function remove_supports( $from_what, $supports ){

		if ( $this->is_valid_on_current_page( $from_what ) ){
			if ( is_array( $supports )){
				foreach( $supports as $support){
					$this->supports_to_remove[] = $support ; 
					if ( in_array( $support, $this->supports_to_add ) ){
						$index = array_search( $support, $this->supports_to_add ); 
						unset( $this->supports_to_add[$index]);
					}					
				}
			} else {
				$support = $supports;				
				$this->supports_to_remove[] = $support ; 
				if ( in_array( $support, $this->supports_to_add ) ){
					$index = array_search( $support, $this->supports_to_add ); 
					unset( $this->supports_to_add[$index]);
				}					
			}
		}
	}
	public function add_supports( $from_what, $supports ){

		if ( $this->is_valid_on_current_page( $from_what ) ){
			if ( is_array( $supports )){
				foreach( $supports as $support){
					$this->supports_to_add[] = $support ; 
					if ( in_array( $support, $this->supports_to_remove ) ){
						$index = array_search( $support, $this->supports_to_remove ); 
						unset( $this->supports_to_remove[$index]);
					}

				}
			} else {
				$support = $supports;
				$this->supports_to_add[] = $support ; 
				if ( in_array( $support, $this->supports_to_remove ) ){
					$index = array_search( $support, $this->supports_to_remove ); 
					unset( $this->supports_to_remove[$index]);
				}				
			}
		}
	}	
	public function add_remove_supports(){

		if ( isset( $_GET['post'] ) ){
			$current_post = get_post( $_GET['post'] );
			$post_type = $current_post->post_type;
		} else if ( isset( $_GET['post_type'])){
			$post_type = $_GET['post_type']; 
		}
		if ( empty( $post_type ) ) return; 
		foreach( $this->supports_to_remove as $support ){
			remove_post_type_support( $current_post->post_type, $support );
		}
		foreach( $this->supports_to_add as $support ){
			add_post_type_support( $current_post->post_type, $support );
		}		
	}
	
	protected function is_valid_on_current_page( $add_to ){
		if ( is_string( $add_to ) ){
			// slug or title provided
			$registered_post_types = get_post_types( array(), 'objects' );
			$posts = array(); 
			foreach( $registered_post_types as $slug => $post_type ){
				if ( $post = get_page_by_title( $add_to, 'OBJECT', $post_type->name ) ){
					$posts[] = $post; 
				}
			}
			if ( sizeof( $posts ) == 0 ){
				$registered_post_types = get_post_types( array(), 'objects' );
				foreach( $registered_post_types as $slug => $post_type ){
					$post_types[] = $slug; 
				}
				$posts = get_posts( array(
					'name' => $add_to, 
					'post_type' => $post_types, 
					'post_status' => 'any'
				) );
			}
		} else if ( is_numeric( $add_to ) ){
			// ID provided
			$post = get_post( $add_to );
			$posts = array( $post ); 
		} else if ( is_array( $add_to ) ){ 
            // check if it includes a template parameter, and return false if the current page doesn't use that template
    		if ( ( isset( $add_to['template'] ) || isset( $add_to['exclude_template'] ) )  && isset( $_GET['post'] ) ) {
    		    $template_file = get_post_meta( $_GET['post'], '_wp_page_template', true );

                if ( isset( $add_to['template'] ) ){
	    		    if ( $template_file === $add_to['template'] || $template_file === $add_to['template'] . '.php' ){
	    		    	return true; 
	    		    }                	

		            $all_templates = wp_get_theme()->get_page_templates();                         
		            $template_name = isset( $all_templates[ $template_file ] ) ? $all_templates[ $template_file ] : false ;	    		    
                    if ( $template_name ){
                        if ( is_array( $add_to['template'])){
                            $allowed = false; 
                            foreach( $add_to['template'] as $allowed_template ){
                                if ( $template_name == $allowed_template ){
                                    $allowed = true; 
                                    break;
                                }
                            }
                            if ( ! $allowed ){
                                return false; 
                            } else {
                                unset( $add_to[ 'template'] ) ;                                
                            }
                        } else {
                            if ( $template_name !== $add_to['template'] ){
                                return false;
                            } else {
                                unset( $add_to[ 'template'] ) ;
                            }
                        }
                    } else {
                    	if ( ! $add_to['template'] || $add_to['template'] === 'default' ){
	                        return true;
	                    }
	                    return false; 
					}
                } else if ( isset( $add_to['exclude_template']) ){
	    		    if ( $template_file === $add_to['exclude_template'] || $template_file === $add_to['exclude_template'] . '.php' ){
	    		    	return false; 
	    		    }                	

		            $all_templates = wp_get_theme()->get_page_templates();                         
		            $template_name = isset( $all_templates[ $template_file ] ) ? $all_templates[ $template_file ] : false ;                	
                    if ( $template_name ){
                        if ( is_array( $add_to['exclude_template'])){
                            $allowed = false;                             
                            foreach( $add_to['exclude_template'] as $excluded_template ){
                                if (  $template_name == $excluded_template ){
                                    return false ;
                                }
                            }                        
                            unset( $add_to[ 'exclude_template'] ) ;                                                            
                        } else {
                            if ( $template_name == $add_to['exclude_template'] ){
                                return false;
                            } else {
                                unset( $add_to[ 'exclude_template'] ) ;
                            }
                        }                        
                    }
                }
            }
        
            
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
			$add_to['post_status'] = 'any';

            if ( ! isset( $add_to['post_type'])){
                $add_to['post_type'] = array( 'page') ;
            }
			$posts = get_posts( $add_to );
			
		}
		
		if ( is_array( $posts ) ){
			$valid_post_IDs = array();
			foreach( $posts as $valid_post ){
				if ( $valid_post ){
					$valid_post_IDs[] = $valid_post->ID ;
				}
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
	
	public function get_page_data( $subpage_slug = false, $section_slug = false, $field_slug = false, $clone_number = false, $subfield_slug = false ){
		$value = false;
 
		$array_values = $this->wp_saved ;
			
		if ( $array_values ){
			if ( $subpage_slug === false ){
				$value = $array_values ; 
			} else {
				$array_values = isset( $this->wp_saved[ $subpage_slug ] ) ? $this->wp_saved[ $subpage_slug ] : false ;
				if ( $array_values ){
					if ( $section_slug === false ){
						$value = $array_values ; 
					} else {
						$array_values = isset( $this->wp_saved[ $subpage_slug ][ $section_slug ] ) ? $this->wp_saved[ $subpage_slug ][ $section_slug ] : false ;
						if ( $array_values ){
							if ( $field_slug === false ){
								$value = $array_values ; 
							} else {
								$array_values = isset( $this->wp_saved[ $subpage_slug ][ $section_slug ][ $field_slug ] ) ? $this->wp_saved[ $subpage_slug ][ $section_slug ][ $field_slug ] : false ;
								if ( $array_values ){
									if ( $clone_number === false ){
										$value = $array_values ; 
									} else {
										$array_values = isset( $this->wp_saved[ $subpage_slug ][ $section_slug ][ $field_slug ][ $clone_number ] ) ? $this->wp_saved[ $subpage_slug ][ $section_slug ][ $field_slug ][ $clone_number ] : false ;
										if ( $array_values ){
											if ( $subfield_slug === false ){
												$value = $array_values ; 
											} else {
												$array_values = isset( $this->wp_saved[ $subpage_slug ][ $section_slug ][ $field_slug ][ $clone_number ][ $subfield_slug ] ) ? $this->wp_saved[ $subpage_slug ][ $section_slug ][ $field_slug ][ $clone_number ][ $subfield_slug ] : false ;
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

		return $value; 
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
		return $value; 
	}		

	protected function has_dynamic_values( $value ){
		if ( is_string( $value ) ){
			return $this->is_JSON( $value ) ; 
		} else if ( $value ){
			$has_dynamic_data = false; 
			array_walk_recursive( $value , array( $this, 'check_for_json' ), &$has_dynamic_data ); 
			return $has_dynamic_data ; 
		} 
		return false; 
	}
	protected function check_for_json( $item, $key, &$has_json ){
		if ( $this->is_JSON( $item ) ){
			$has_json = true; 
		}
	}
	protected function is_JSON( $string ){
		json_decode($string);
		return (json_last_error() == JSON_ERROR_NONE);
	}
	public function convert_dynamic_data( $value , $path_to_option, $is_metabox = false ){	
		if ($this->has_dynamic_values( $value ) ){
			if ( is_string( $value ) ){
				

				$json_array = json_decode( $value, true ) ;
				if ( $json_array && is_array( $json_array ) ){
					// grab top_level page array
					if ( $is_metabox ){
						$array_spec = $this->metaboxes[ array_shift( $path_to_option ) ] ;
						$spec_key_names = array( 'fields', 'subfields' ); 
					} else {
						
						foreach( $this->pages as $top_level_slug => $top_level_page ){
							foreach( $top_level_page[ 'subpages'] as $subpage_slug => $subpage ){
								if ( $subpage_slug == $path_to_option['subpages'] ){
									$array_spec = $subpage ; 
									array_shift( $path_to_option); 
									break 2; 
								}
							}
						}
						$spec_key_names = array( 'sections', 'fields', 'subfields' ); 											
					}                   
					foreach( $path_to_option as $spec_key => $key_name ){
						if ( ! is_numeric( $spec_key ) ){
							if ( $spec_key ){
								$array_spec = is_array( $array_spec[$spec_key] ) && isset( $array_spec[$spec_key][$key_name] ) ? $array_spec[$spec_key][$key_name] : $array_spec ;	
							} else {
								$array_spec = is_array( $array_spec ) && isset( $array_spec[$key] ) ? $array_spec[$key] : $array_spec ;													
							}
						} else {
							if ( ! is_numeric( $key_name ) ){
								$still_looking = true;
								while ( $still_looking && sizeof( $spec_key_names ) > 0 ){
									$spec_key = array_shift( $spec_key_names ) ;
									if ( isset( $array_spec[$spec_key] ) &&  is_array( $array_spec[$spec_key] ) && isset( $array_spec[$spec_key][$key_name] ) ){
                                        $array_spec =  $array_spec[$spec_key][$key_name] ; 
                                        $still_looking = false; 
									}
								}
							}
						}	                      
					}
			
					
					foreach( $json_array as $field_type => $data ){
						$value = $field_type ;                         
						if ( class_exists( Cloud_Field::get_class_name( $field_type ) ) ){
							$field_class = Cloud_Field::get_class_name( $field_type ) ;
							$value = $field_class::get_option( $data, $array_spec ) ;						
						} else {
							echo 'no class by that name' ;
						}
					}
				}
				
			} else if ( is_array( $value ) ){
				foreach ( $value as $index => &$item ){
					$path_to_nested_option = $path_to_option ;
					$path_to_nested_option[] = $index ;

					$item = $this->convert_dynamic_data( $item , $path_to_nested_option, $is_metabox ) ;
				}
			}
		} 

		return $value ;
	}	
	
	public static function get_folder_url(){
		return self::$dir .'/WP'; 	
	}	
	public static function get_include_path(){
		return self::$ABS .'/WP' ; 	
	}

}

/***====================================================================================================================================
		GLOBAL FUNCTIONS
	==================================================================================================================================== ***/
function get_theme_options( $subpage_slug = false, $section_slug = false, $field_slug = false, $clone_number = false, $subfield_slug = false ){
	$Forms = Cloud_Forms_WP::get_instance(); 
	$path_to_option = array(); 
	if ( $subpage_slug ){
		$path_to_option[ 'subpages' ] = $subpage_slug ; 
		if ( $section_slug ){
			$path_to_option['sections'] = $section_slug ; 
			if ( $field_slug ){
				$path_to_option['fields'] = $field_slug ; 
				if ( $clone_number ){
					$path_to_option[] = $clone_number ; 
					if ( $subfield_slug ){
						$path_to_option['subfields'] = $subfield_slug ; 
					}
				}
			}
		} 
	}

	return $Forms->convert_dynamic_data( $Forms->get_page_data( $subpage_slug, $section_slug, $field_slug, $clone_number, $subfield_slug ), $path_to_option, false ); 
	
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
	
	$path_to_option = array( 
		$metabox_slug
	); 
    if ( $field_slug ){ $path_to_option[] = $field_slug ; }
    if ( $group_number ){ $path_to_option[] = $group_number ; }
    if ( $subfield_slug ){ $path_to_option[] = $subfield_slug ; }
	return $Forms->convert_dynamic_data( $Forms->get_metabox_data( $post_id, $metabox_slug, $field_slug, $group_number, $subfield_slug ), $path_to_option, true ); 
	
	

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
