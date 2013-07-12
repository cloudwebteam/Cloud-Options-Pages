<?php 
class LL_Ajax {
	protected static $instance;
	public function get_instance(){
		if ( ! self::$instance ){
			self::$instance = new self(); 
		}
		return self::$instance; 
	}
	protected function __construct(){
		$this->add_own_actions(); 
	}
	public function add_action( $action_name, $function_to_call ){
		add_action( 'wp_ajax_'.$action_name, $function_to_call ); 	
		add_action( 'wp_ajax_nopriv_'.$action_name, $function_to_call ); 
	}	
	protected function add_own_actions(){
		$actions = array( 
			'get_posts', 
			'get_items', 
			'get_item', 
			'get_map_items', 
			'get_location_name_list'
			
		); 
		foreach( $actions as $action ){
			$this->add_action( $action, array( $this, $action ) ); 
		}
	}
	
	protected function response( $response ){
		echo json_encode( $response ); 
		die; 
	}
	/***====================================================================================================================================
			OWN ACTIONS
		==================================================================================================================================== ***/
	public function get_posts(){
		$args = isset( $_POST['args'] ) ? $_POST['args'] : false; 
		$this->response( get_posts( $args ) ); 
	}
	public function get_items(){
		$args = isset( $_POST['args'] ) ? $_POST['args'] : array(); 
		
		foreach( $args as $key => $value ){
			if ( $value == 'true' ){
				$args[ $key ] = true; 
			} else if ( $value == 'false'){
				$args[ $key ] = false;
			}
		}			
		if ( ! isset( $args['type'] ) ){
			$args['type'] = 'club' ;
		}
		switch ( $args['type'] ){
			case 'club' : 
				$response = LL::get_clubs( $args ); 
				foreach( $response as $index => $club ){
					$locations = wp_get_post_terms( $club->ID, 'll_location' ) ; 
					$location_ids = array(); 
					foreach( $locations as $location ){
						$location_ids[] = $location->term_id; 
					}
					$response[ $index ]->images = get_attachment_image_src_in_all_sizes( get_post_thumbnail_id ( $club->ID ) ); 
					$response[ $index ]->mainCategories = get_club_top_categories( $club->ID, 'label label-ll' ); 					
					$response[ $index ]->categories = get_club_categories( $club->ID );
					$response[ $index ]->locations = $location_ids;
					$response[ $index ]->perks = LL::get_perks( array( 'clubs' => array( $club->ID) )) ; 
				}
				break;
			case 'perk' : 
				$response = LL::get_perks( $args ); 
				break;
			case 'category' : 
				$response = LL::get_categories( $args ); 			
				break;
		}				
			
		$this->response( $response );		
	}
	public function get_item(){
		$post_id = $_POST['post_id']; 
		$layout = $_POST['layout']; 
		$type = $_POST['type'] ; 

		global $Theme;
		global $post; 		
		$post = get_post( $post_id );
		setup_postdata( $post ); 					 
		ob_start(); 
			$Theme->component( $layout .'-'.$type );
		$response = ob_get_clean( );
		wp_reset_postdata(); 	
		
		$this->response( $response );		
			
	}	
	public function get_map_items(){
		$query = isset( $_POST['query'] ) ? $_POST['query'] : array(); 
		$view = isset( $_POST['view'] ) ? $_POST['view'] : array(); 
		foreach( $query as $key => $value ){
			if ( $value == 'true' ){
				$query[ $key ] = true; 
			} else if ( $value == 'false'){
				$query[ $key ] = false;
			}
		}	
			
		foreach( $view as $key => $value ){
			if ( $value == 'true' ){
				$view[ $key ] = true; 
			} else if ( $value == 'false'){
				$view[ $key ] = false;
			}
		}
		$view['type'] = $query['type'] ;
		$response = LL::get_map_items( $query, $view ); 
	
		$this->response( $response );		

	}	
	public function get_location_name_list(){
		$locations = $_POST['locations'] ;
		$location_level_names = array(); 
		foreach( $locations as $index => $locations_level  ){
			$term_names = array(); 
			foreach( $locations_level as $term_id ){
				$term_id = intval($term_id) ;
				$term = get_term( $term_id, 'll_location' ); 
				$term_names[] = '<span class="term-name">'.$term->name.'</span>'; 
			}
			$location_level_names[] = '<span class="locations-'.$index.'" >'.implode( ' + ', $term_names ).'</span>'; 
		}
		$response = implode( ', ', $location_level_names ); 

		$this->response( $response );				
	}		
}
