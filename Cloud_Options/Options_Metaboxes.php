<?php 
class Cloud_Metaboxes {
	private static $instance ; 
	// what are the values stored in the database?
	protected $values ;
	// whatever the user passes in
	protected $passed_in_metaboxes = array();
	protected $valid_metaboxes = array(); 
	// after merging with defaults
	public static $metaboxes = array();
	
	public static function init( $user_metaboxes ){
		if ( sizeof( $user_metaboxes ) > 0 ){
			if ( ! self::$instance ){
				self::$instance = new self( $user_metaboxes ); 
			}
			return self::$instance ; 
		} else {
			return false; 
		}
	}
	public static function get_instance(){
		return self::$instance; 
	}
	private function __construct( $user_metaboxes = array() ){
		$this->passed_in_metaboxes = $user_metaboxes ;
		// create metaboxes
		add_action( 'admin_init', array( $this, 'set_metaboxes' ) ) ;

		add_action( 'save_post', array( $this, 'save_metaboxes' ) );
	
	}
	public function set_metaboxes( ){
		$valid_array = array();
		foreach( $this->passed_in_metaboxes as $passed_in ){
			if( self::valid_on_current_page( $passed_in[ 'add_to' ] ) ){
				foreach ( $passed_in['user_array'] as $key => $array ){
					if ( $passed_in['context'] && ! isset( $array['context'] ) ){
						$array['context'] =  $passed_in['context']  ; 
					} else {
					}
					if ( $passed_in['priority'] && ! isset( $array['priority'] ) ){
						$array['priority'] = $passed_in['priority'] ; 
					}				
					$valid_array[$key] = $array;
				}	
			}
		}
		$this->valid_metaboxes = $valid_array ;
		self::$metaboxes = $this->merge_with_defaults();
			
		if ( sizeof( self::$metaboxes ) > 0 ){
			$this->enqueue_metabox_scripts(); 
			add_action('add_meta_boxes', array( $this, 'create_metaboxes' ) );
		}		
	}
	protected function valid_on_current_page( $add_to ){

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
	protected function merge_with_defaults(){
		$defaults = Cloud_Options::$defaults; 
		$user_array = $this->valid_metaboxes; 

		$_master = array();					
		foreach ($user_array as $metabox_slug => $metabox){
			$_master[ $metabox_slug ] = array();  
			$_master[ $metabox_slug ] = Cloud_Options::merge_with_defaults( 'metaboxes', $metabox ); 
		}
		return $_master;
	}		
	protected function enqueue_metabox_scripts(){
		//enqueue necessary css/js
		$Options = Cloud_Options::get_instance();
		$Options->load_styles_and_scripts();	
		if ( is_array( self::$metaboxes ) && sizeof( self::$metaboxes ) > 0 ){
			foreach( self::$metaboxes as $metabox ){
				if ( isset( $metabox['fields'] ) &&  is_array( $metabox['fields'] ) && sizeof( $metabox['fields'] ) > 0 ){				
					foreach( $metabox['fields'] as $field ){
						$field_type = Field_Type::get_class_name( $field['type'] );
						add_action( 'admin_enqueue_scripts', array( $field_type, 'enqueue_field_scripts_and_styles' ) ); 		
					}
				}
			}
		}
	}
	public function create_metaboxes(){
		foreach ( self::$metaboxes as $metabox_slug => $metabox ){
			$id 			= $metabox_slug;
			$title 			= $metabox['title']; 
			$callback 		= $this->get_metabox_layout_function( $metabox['layout'] ); 

			add_meta_box( $id, $title, $callback, '', $metabox['context'], $metabox['priority'], $metabox );		

		}
	}
	private function get_metabox_layout_function( $layout = null ){
	
		$layout_function = Metabox_Layout::get_layout_function($layout, 'Metabox_Layout'); 
		return array('Metabox_Layout', $layout_function );
	}
	public function save_metaboxes( $post_id ){
		if ( !wp_is_post_revision( $post_id ) ){
			foreach ( self::$metaboxes as $metabox_id => $metabox ){
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
	public function get_info( $metabox_id ){
		return self::$user_metabox_array[ $metabox_id ] ;
	}			
	
	protected function get_option_default( $metabox_slug, $field_slug ){
		$post_id = '';
		if ( isset( $_GET['post'] ) ){
			$post_id = $_GET['post'] ; 
		} else if ( isset( $_POST['post_id'] ) ){
			$post_id = $_POST['post_id'] ; 
		}
		if ( $post_id ){
			return $this->get_option( $post_id, $metabox_slug, 'enabled', $field_slug );
		}
	}

	public static function get_fields( $metabox_id, $metabox ){
		ob_start() ;
	
		foreach( $metabox['fields'] as $field_id => $field ){
			$function = Cloud_Options::get_field_layout_function( $field );
			$args = array(); 
			$args[0]['info'] = $field ;
			$args[0]['context'] = 'metabox' ;
			$args[0]['metabox'] = $metabox_id ;
			$args[0]['field'] = $field_id ;

			call_user_func_array( $function, $args );
		}
		$fields_html = ob_get_clean();
		return $fields_html ;
	}
	public function get_option( $post_id, $metabox_slug, $field_slug ){
		$metabox_values =  get_post_meta( $post_id, $metabox_slug, true ) ;
		if ( isset( $metabox_values[ $field_slug ] ) ){
			return $metabox_values[ $field_slug ] ;
		} else {
			return false;
		}
	}
	public static function get_options( $post_id, $metabox_slug, $field_slug = null , $group_number = null, $subfield_slug = null ){
		if ( isset( $metabox_slug ) && $metabox_slug ){
			$metabox_values =  get_post_meta( $post_id , $metabox_slug, true) ; 
			if ( $metabox_values ) {
			
				if ( isset( $field_slug ) && isset( $group_number ) && isset( $subfield_slug )  ){
					if ( isset( $metabox_values[ $field_slug ][ $group_number ][ $subfield_slug ] ) ){
						return $metabox_values[ $field_slug ][ $group_number ][ $subfield_slug ] ;
					} else {
					 	return '';
					 }
				} else if ( $field_slug && $group_number ){
					if ( isset( $metabox_values[ $field_slug ][ $group_number ] ) ){
						return $metabox_values[ $field_slug ][ $group_number ] ;
					} else {
					 	return '';
					 }					
				} else if ( $field_slug ){
					if ( isset( $metabox_values[ $field_slug ] ) ){
						return $metabox_values[ $field_slug ];
					} else {
					 	return '';
					 }
				} else {
					return $metabox_values ;
				}
			}
			return false; 	
		} 
	}
	public function is_option_enabled( $post_id, $metabox_slug, $field_slug, $group_number = null, $subfield_slug = null ){
		$enabled_options = $this->get_option( $post_id, $metabox_slug, 'enabled' );
		if ( isset( $group_number ) && isset( $subfield_slug ) ){
			return isset( $enabled_options[$field_slug][ $group_number ][ $subfield_slug] ); 
		} else if ( isset ( $group_number ) ){
			return isset( $enabled_options[$field_slug][$group_number] ); 
		} else {
			return isset( $enabled_options[$field_slug] ); 
		}
	}	

}

/***====================================================================================================================================
		HELPERS
	==================================================================================================================================== ***/
function is_array_empty($InputVariable){
   $Result = true;

   if (is_array($InputVariable) && count($InputVariable) > 0)
   {
      foreach ($InputVariable as $Value)
      {
         $Result = $Result && is_array_empty($Value);
      }
   }
   else
   {
      $Result = empty($InputVariable);
   }

   return $Result;
}