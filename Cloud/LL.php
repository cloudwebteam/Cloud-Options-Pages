<?php class LL {
	private static $instance; 
	public static function init(){
		if ( ! self::$instance ){
			self::$instance = new self();
		}
		return self::$instance;
	}
	private function __construct(){
		add_filter( 'posts_search', array( $this, 'search_post_excerpt_filter' ) ); 


	}	

	public function search_post_excerpt_filter( $args ){
		$adjusted_query = preg_replace( '/\(wp_posts.post_content LIKE \'%?([A-Za-z0-9]+)%?\'\)/', '( MATCH( wp_posts.post_excerpt ) AGAINST ( \'\1\' IN BOOLEAN MODE ) )', $args ); 
		return $adjusted_query; 
	}	
	static function get_perks_query( $args ){	
		global $User;
		$defaults = array( 
			'orderby' => 'recent', 
			'order' => 'ASC', 
			'numberposts' => -1,	
			'in_wallet' => false,
			'in_club' => false,
			'club' => false,
			'clubs' => false,			
			'category' => false,
			'categories' => false,
			'location' => false,
			'locations' => false,	
			'keyword' => false,
			'post_status' => 'publish'	
		) ;
		$options = wp_parse_args( $args, $defaults ); 
		$query_args['post_type'] = 'll_perk' ; 
		
		switch( $options['orderby'] ){
			case 'recent' : 
				$orderby = 'date' ;
				$options['order'] = 'DESC';
				break;
			case 'alphabetical' : $orderby = 'title' ; 
				break; 
			default : $orderby = $options['orderby'];
				break;
		}
		$query_args[ 'post_status' ] = $options['post_status']; 
		$query_args[ 'orderby' ] = $orderby; 
		$query_args[ 'order' ] = $options['order']; 
		$query_args[ 'numberposts' ] = $options['numberposts']; 
		$query_args[ 's' ] = $options['keyword'] ;
		
		if ( $options['in_wallet'] ){
			if ( is_array( $User->wallet ) ){
				$query_args['post__in'] = $User->wallet ;
			} else {
				$query_args['post__in'] = array( 0 ) ; // force empty result 
			}
		}		
		/***====================================================================================================================================
				LET CLUBS HANDLE TAXONOMIES/CLUB ID selection
			==================================================================================================================================== ***/
		$club_ids = false; 		
		$clubs_query = false; 
		$club_tax_queries = array();
		if ( $options['club'] ){
			$club_ids = array( $options['club'] ) ;
		} else if ( $options['clubs'] || $options['in_club'] || $options['category'] || $options['categories'] || $options['location'] || $options['locations'] ){
			$clubs = LL::get_clubs( array( 
				'in_club' => $options['in_club'], 
				'category' => $options['category'],
				'categories' => $options['categories'], 
				'clubs' => $options['clubs'],
				'location' => $options['location'],
				'locations' => $options['locations']
			) ) ;

			$club_ids = array();
			foreach( $clubs as $club ){
				$club_ids[] = $club->ID ;
			}				
		}
		if ( is_array( $club_ids ) ){
			if ( sizeof( $club_ids ) == 0 ){
				$query_args['post__in'] = array( 0 ) ; // force empty result 
			} else {
				$query_args['meta_query'][] = array(
					'key' => 'club',
					'value' => $club_ids, 
					'compare' => 'IN'
				);			
			}
		}

		global $Theme ; 
		$Theme::mark_perks_expired() ;			
		return $query_args ;		
	}
	static function get_perks( $ll_query_args = array() ){
		$wp_query_args = self::get_perks_query( $ll_query_args ) ;
		$perks =  get_posts( $wp_query_args ); 
		foreach( $perks as $index => $perk ){
			$meta = get_post_custom( $perk->ID ); 
			$perks[$index]->club = $meta['club'][0] ;
			$perks[$index]->perk_type = $meta['type'][0]; 
		}
		return $perks ; 
	}
	static function get_clubs_query( $args ){
		global $User;
		$defaults = array( 
			'orderby' => 'title', 
			'order' => 'ASC', 
			'numberposts' => -1,	
			'in_club' => false,
			'has_perks' => false,		
			'clubs' => false,			
			'category' => false,
			'categories' => false,			
			'location' => false,
			'locations' => false,
			'keyword' => false,
			'post_status' => 'publish'									
		) ;
		$options = wp_parse_args( $args, $defaults ); 

		$query_args = array();
		switch( $options['orderby'] ){
			case 'recent' : 
				$orderby = 'date' ;
				$options['order'] = 'DESC';
				break;
			case 'alphabetical' : $orderby = 'title' ; 
				break; 
			default : $orderby = $options['orderby'];
				break;
		}
		$query_args['post_type'] = 'll_business' ; 
		$query_args[ 'post_status' ] = $options['post_status']; 		
		$query_args[ 'orderby' ] = $orderby; 
		$query_args[ 'order' ] = $options['order']; 
		$query_args[ 'numberposts' ] = $options['numberposts']; 
		$query_args[ 's' ] = $options['keyword'] ;
		$club_ids = false; 		
		if ( is_array( $options['clubs'] ) ){
			$club_ids = $options['clubs'] ;
		}
		if ( $options['in_club'] && $User->logged_in ){
			if (  is_array( $User->fan_club_ids )){
				$club_ids = is_array( $club_ids ) ? array_intersect( $club_ids, $User->fan_club_ids ) : $User->fan_club_ids ;
			} else {
				$club_ids = array();
			}
		}
		if ( $options['has_perks'] ){
			$clubs_with_perks = get_ids_of_clubs_with_perks();

			if ( is_array( $clubs_with_perks ) ){
				$club_ids = is_array( $club_ids ) ? array_intersect( $club_ids, $clubs_with_perks ) : $clubs_with_perks ;
			}
		}
		if ( is_array( $club_ids ) ){
			$query_args['post__in'] = $club_ids ;
			if( sizeof( $club_ids ) == 0 ){
				$query_args['post__in'] = array( 0 ) ; // force empty result 
			}	
		}
		/***====================================================================================================================================
				FILTER BY TAXONOMIES
			==================================================================================================================================== ***/
		$club_tax_queries = array();			
		// filter by category
		$category_terms = false; 
		if ( $options['category'] ){
			$category_field = is_numeric( $options['category'] ) ? 'id' : 'slug' ; 					
			$category_terms = $options['category'];		
		} else if( is_array( $options['categories'] ) ){
			$category_field = is_numeric( $options['categories'][0] ) ? 'id' : 'slug' ; 			
			$category_terms = $options['categories'] ;
		}
		if ( $category_terms ){
			$club_tax_queries['relation'] = 'AND' ;
			$club_tax_queries[] = array( 
				'taxonomy' => 'll_category',
				'field' => $category_field,
				'terms' => $category_terms, 
				'operator' => 'IN'
			);		
		}		
		// filter by location
		$location_terms = false; 		
		if ( $options['location'] ){
			$location_field = is_numeric( $options['location'] ) ? 'id' : 'slug' ; 					
			$location_terms = $options['location'] ;		
		} else if( is_array( $options['locations'] ) ){
			$location_field = is_numeric( $options['locations'][0] ) ? 'id' : 'slug' ; 			
			$location_terms = $options['locations'] ;
		}
		if ( $location_terms ){
			$club_tax_queries['relation'] = 'AND' ;
			$club_tax_queries[] = array( 
				'taxonomy' => 'll_location',
				'field' => $location_field,
				'terms' => $location_terms, 
				'operator' => 'IN'
			);		
		}			
		if ( sizeof( $club_tax_queries ) > 0 ){
			$query_args[ 'tax_query' ] = $club_tax_queries ;
		}	
		return $query_args;		
	}
	static function get_clubs( $ll_query_args = array() ){
		$wp_query_args = self::get_clubs_query( $ll_query_args ) ;
		return get_posts( $wp_query_args ); 
	}	
	public function get_categories( $args ){
		$defaults = array( 
			'orderby' => 'title', 
			'order' => 'ASC', 
			'numberposts' => -1,
			'in_club' => false,
			'has_perks' => false,		
			'clubs' => false,			
			'categories' => false,			
			'location' => false,
			'locations' => false,
			'keyword' => false,
		) ;	
		$options = wp_parse_args( $args, $defaults ); 
		$query_args['orderby'] = $defaults['orderby'] ;	
		$query_args['order'] = $defaults['order'] ;
		$query_args['search'] = $options['keyword'] ;	
		$query_args['hide_empty'] = true;
			
		$clubs = LL::get_clubs( array( 
			'in_club' => $options['in_club'], 
			'has_perks' => $options['has_perks'], 
			'clubs' => $options['clubs'],
			'categories' => $options['categories'], 
			'location' => $options['location'],
			'locations' => $options['locations'], 
		) ) ;
		if ( !$clubs || sizeof( $clubs ) == 0 ){
			$query_args['include'] = array( 0 ) ; // oops, no clubs, hence, no terms
		} else {
			$clubs_terms = get_posts_terms( $clubs, 'll_category' ); 
			$term_ids = array(); 
			foreach( $clubs_terms as $term ){
				$term_ids[] = $term->term_id; 
			}
			$query_args['include'] = $term_ids ; 
		}
		$ordered_and_queried_terms = get_terms( 'll_category', $query_args );		
		return $ordered_and_queried_terms ;
	}		
	public function get_terms_html( $terms, $layout = 'inline' ){
		$output = '' ;
		if ( is_array($terms) && sizeof( $terms ) > 0 ){
			$first_term = array_shift( $terms );
			switch( $layout ){
				case 'inline' : 
					
					$output .= '<ul class="'.$first_term->taxonomy.' term-list layout-'.$layout.'">' ;
					array_unshift( $terms, $first_term );
					foreach( $terms as $term ){
						$output .= '<li class="label label-ll"><a href="'.get_term_link( $term ).'">'.$term->name.'</a></li>' ;
					}
					$output .= '</ul>' ; 
					break;
			}
		}
		return $output; 
	}
	static function get_paginated_item_results( $items, $args = array() ){
		$default_page = isset( $_POST['page'] ) && intval( $_POST['page'] ) ? intval( $_POST['page'] ) : 1 ;
		
		$defaults = array( 
			'empty_message' => 'No results',
			'layout' => 'listing',
			'type' => 'club', 
			'page' => $default_page,
			'per_page' => false, // set below
			'result_count' => sizeof( $items ),
			'return_pagination' => false
		) ;
		$options = wp_parse_args( $args , $defaults);
	
		// switch the default per page amount depending on the the layout type
		if ( ! $options['per_page'] ){
			$per_page = false;
			switch ( $options['layout'] ){
				case 'card' : 
					switch ( $options['type'] ){
						case 'perk' : $per_page = 8 ; break; 
						case 'club' : $per_page = 5 ; break; 
					}
					break;
				case 'card-small' : 					
					switch ( $options['type'] ){
						case 'perk' : $per_page = 15 ; break; 
						case 'club' : $per_page = 12 ; break; 
					}
					break;
				case 'list' : 
					switch ( $options['type'] ){
						case 'perk' : $per_page = 20 ; break; 
						case 'club' : $per_page = 15 ; break; 
					}
					break;				
			}
			$options['per_page'] = $per_page ? $per_page : 10;
		}
		
		$current_page = $options['page'] ? $options['page'] : 1 ;
		$per_page = $options['per_page'];		 
		$result_count = $options['result_count'];
		if ( sizeof ( $items ) > 0 ){
			global $post ;
			global ${ $options['type'] }; 
 				
			global $Theme ;
			if ( $result_count > $per_page ){
				$items_to_return = array_slice( $items, ($current_page-1) *$per_page, $per_page );
			} else {
				$items_to_return = $items ; 
			}			
		
			$html = '<div class="ll-overlay"></div><ul class="'.$options['layout'].'-container '.$options['type'].' row-fluid">'; 		
			ob_start();	
			foreach( $items_to_return as $item ){ 
				$post = $item ;
				setup_postdata( $post );			
				$Theme->component( $options['layout'] .'-'.$options['type'] );
			}


			$html .= ob_get_clean();		
			$html .= '</ul>'; 				
			wp_reset_postdata();
		} else {
			$html = '<p class="empty">'.stripslashes( $options['empty_message'] ).'</p>' ;
		}
		if ( $options['return_pagination'] && $result_count > $per_page ){
			$current_page = 1;
			$total_pages = $result_count / $per_page ;
			$pagination = '<ul class="pagination">' ;
			for( $i = 0; $i < $total_pages ; $i++ ){
				$pagination .= '<li class="page-number"><a data-number="'.($i+1).'" >'.($i+1).'</a></li>' ;
			}
			$pagination .= '</ul>' ;
		} else {
			$pagination = false ;
		}
		
		$response = array( 
			'options' => $options,
			'html' => $html, 
			'current_page' => $current_page, 
			'total_records' => $result_count, 
			'pagination' => $pagination
		);		
	
		return $response ;
	}
	public function get_map_items( $query, $view ){
		$query_type	= $view['type']; 
		$clubs = LL::get_clubs( $query ); 
		
		$map_items = array(); 

		foreach( $clubs as $club ){
			$addresses = get_metabox_options( $club->ID, "general" , "locations" ) ;					
			if ( is_array( $addresses ) && sizeof( $addresses ) > 0 ){
	
				$type = get_club_ll_type( $club->ID ); 
						
				switch( $type ){
					case 'Recreation' : 
						$image = 'marker-recreation.png' ;
						break; 
					case 'Restaurants' : 
						$image = 'marker-restaurants.png' ; 
						break; 
					case 'Services' : 
						$image = 'marker-services.png' ; 
						break;
					case 'Shopping' :
						$image = 'marker-shopping.png' ;
						break;
					default : 
						$image = 'marker-services.png' ;
						break; 
				}
				$permalink = get_permalink( $club->ID ); 
				
				$content = array() ;
				$featured_image = get_club_image( $club, 'thumbnail' ) ; 				
				$content['title'] = '<h4 class="title"><a href="'.$permalink.'">'.$club->post_title.'</a><a class="alignright" href="'.$permalink.'" >'.$featured_image.'</a></h4>';
				
				if ( $query_type == 'club' ){
					$post_content = strlen( $club->post_content ) < 400 ? $club->post_content : substr( $club->post_content, 0, 400 ) . '...' ; 
					$content['content'] = apply_filters( 'the_content', $post_content ) ; 
				} else {
					$club_perks = LL::get_perks(array(
						'club' => $club->ID
					)); 
					if ( is_array( $club_perks ) && sizeof( $club_perks ) > 0 ){
					
						$content['content'] = '<ul class="perk-list"><b>Perks</b>'; 
						foreach( $club_perks as $perk ){
							$content['content'] .= '<li><b><a onclick="launch_perk_fancybox('.$perk->ID.');" data-perk_id="'.$perk->ID.'" class="perk-fancybox">'.$perk->post_title.'</a></li>' ;
						}
						$content['content'] .= '</ul>' ;
						
					} else {
						continue; 
					}
				}			
				foreach( $addresses as $address ){
					if ( $address['street'] && ( $address['city'] || $address['zip'] ) ){
						$address_string = $address['street'] . ' ' .$address['city'].', '.$address['state'] . ' ' .$address['zip'] ;
						
						$final_content = '<article class="'.$query_type.'">' . $content['title'] . $address_string . $content['content'] .'</article>';  
						$map_items[] = array( 
							'data' => $club->post_title,
							'address' => $address_string ,
							'options' => array( 
								'icon' => get_bloginfo( 'template_directory' ).'/images/'.$image
							), 
							'content' => $final_content
						); 
					}
				}
			}
		}
		$map_items = sizeof( $map_items ) > 0 ? $map_items : false ;		
		
		global $Theme; 
		ob_start(); 
			$Theme->component( 'map' ); 
		$map_html = ob_get_clean();		
		$response = array( 
			'html' => $map_html, 
			'map_items' => $map_items,
			'current_page' => false, 
			'total_records' => sizeof( $map_items ), 
			'pagination' => false
		);		
		return $response; 	
	}
	public function get_clubs_html( $query, $view ){
		$items = LL::get_clubs( $query );
		if( $view['layout'] !== 'map' ){
			$response = LL::get_paginated_item_results( $items, $view );	
		} else {
			$response = LL::get_map_items( $query, $view );	
		}
		return $response;		
	}
	public function get_perks_html( $query, $view ){
		$items = LL::get_perks( $query );
		if( $view['layout'] !== 'map' ){	
			$response = LL::get_paginated_item_results( $items, $view );
		} else {
			$response = LL::get_map_items( $query, $view );		
		}
		$response['query'] = $query ;
		return $response;	
	}	
	public function get_categories_html( $query, $view ){
		$categories = LL::get_categories( $query );
			
		if ( $categories ){
			$response_html = LL::get_terms_html( $categories , $view['layout'] );
			$result_count = sizeof( $categories );
		} else {
			$response_html = '<p class="empty">'.$view['empty_message'].'</p>' ;
			$result_count = 0;
		}
		$response = array( 
			'query' => $query,
			'html' => $response_html, 
			'total_records' => $result_count, 
			'pagination' => false
		);		
		return $response;		
	}		
	
}


LL::init();