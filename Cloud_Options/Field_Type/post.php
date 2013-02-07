<?php
// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

class Cloud_Field_post extends Field_Type {
	public static $wp_link_dialog_id = 'wp_link_popup' ; 
	
	protected $accepted_properties = array( 'ID', 'id', 'title', 'post_title', 'content', 'post_content', 'excerpt', 'post_excerpt', 'thumbnail', 'post_thumbnail', 'image', 'url', 'permalink' ); 
	
	public static function create_field( $args ){
		$field_type = __CLASS__;
		$field = new $field_type( $args ); 
	}

	protected function __construct( $args ){		
		parent::__construct( __CLASS__, $args ); 
	}

	protected function get_field_html( $args ){
		$this->size = isset( $args['info']['size'] ) ? $args['info']['size'] : ''; 	
		$this->property_to_get = isset( $args['info']['get'] ) ? $this->property_to_get( $args['info']['get'] ) : 'ID' ; 
		$image_size = isset( $args['info']['image_size'] ) ? $args['info']['image_size'] : 'thumbnail' ; 
		
		$field = '<input class="target-field" data-image_size="'.$image_size.'" data-to_get="'.$this->property_to_get. '" type="hidden" id="'.$this->info['prefix'] . $this->info['id'] . '" name="'.$this->info['name'] . '" size="'.$this->size.'" type="text" value=\'' . $this->info['value'] . '\' />';	
		$current_data = $this->get_current_data( ) ;

		return $field . $current_data;
	}
	protected function property_to_get( $property = '' ){ 
		switch ( $property ){
			case 'ID' : 
			case 'id' : 
				return 'ID'; 
				break; 
			case 'post' : 
				return 'post' ;
				break; 
			case 'title' : 
			case 'post_title' : 
				return 'post_title' ;
				break ; 
			case 'content' :
			case 'post_content' : 
				return 'post_content' ;
				break; 
			case 'excerpt' : 
			case 'post_excerpt' : 
				return 'post_excerpt' ;
				break; 
			case 'image' : 
			case 'thumbnail' : 
			case 'post_thumbnail' : 
				return 'post_thumbnail' ;
				break; 
			case 'image_url' : 
			case 'thumbnail_url' : 
			case 'post_thumbnail_url' :
				return 'post_thumbnail' ;
				break; 		
			case 'image_id' :
			case 'thumbnail_id' : 
			case 'post_thumbnail_id' : 
				return 'post_thumbnail_id' ;
				break; 
			case 'url' : 
			case 'permalink' :
				return 'url' ; 
				break; 
			default : 
				return str_replace( array( '"', "'", ' ' ), '', $property ) ; 
				
		}
	} 
	protected function get_current_data( ){
		$saved_data = json_decode( $this->info['value'], true ) ; 
		if ( $saved_data ){
			$post_id = $saved_data['post'] ;
			$post = get_post( $post_id ) ;
		}
		$current_data = '<p class="current-data"><a class="select-post" href="#">Select a post</a> <span class="current">Selected post: <b class="post-title">'.$post->post_title.'</b> (set to grab <span class="post-property">'. $this->property_to_get .'</span>) </p>' ;
		if ( $this->context == 'metabox' ){	
			$current_data .= '<div class="preview"><div class="inner">'.get_metabox_options( $_GET['post'], $this->args['metabox'], $this->args['field'] ).'</div></div>' ;
		} else {
			get_metabox_options( $args['top_level'], $args['subpage'], $args['section'], $args['field'] ) ;
		}	
		return $current_data ;
	}
	public function enqueue_field_scripts_and_styles(){
		$type = substr( __CLASS__, strlen( Field_Type::$class_prefix ) );
		// if they exist, enqueues css and js files with this fields name
		parent::register_scripts_and_styles( __CLASS__ ); 
		wp_localize_script( __CLASS__, 'link_popup_id' , self::$wp_link_dialog_id ); 
		wp_register_script( 'fancybox' , Cloud_Options::get_folder_url() .'/__inc/fancybox2.1.3/jquery.fancybox.js', array( 'jquery' ) );
		wp_enqueue_script( 'fancybox' );
		wp_register_style( 'fancybox' , Cloud_Options::get_folder_url() .'/__inc/fancybox2.1.3/jquery.fancybox.css' );		
		wp_enqueue_style( 'fancybox' );

		add_action('admin_footer', array( __CLASS__, 'options_page_link_popup' ) ); 
	}	
	public static function options_page_link_popup( $args ){ ?>
		<div id="<?php echo self::$wp_link_dialog_id; ?>" style="display: none;">
			<form id="wp-link-dialog" tabindex="-1">
				<?php wp_nonce_field( 'internal-linking', '_ajax_linking_nonce', false ); ?>
				<div class="link-search-wrapper cf">
					<label for='link-search-field'>Search Site</label>
					<input type="text" id="link-search-field" class="link-search-field" tabindex="60" autocomplete="off" />
				</div>
				<div class="query-results">
					<h3 class="title"><span>Results</span></h3>
					<div class="loading">
						<img class="waiting" src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" alt="" />
					</div>						
					<ul class="list"></ul>
					<div class="none-found">
						<p>No content found!</p>
					</div>

				</div>
			</form>
		</div>
		<?php
	}
	public function ajax_options_internal_search(){
		check_ajax_referer( 'internal-linking', '_ajax_linking_nonce' );

		$args = array();
		$property_to_retrieve = isset( $_POST['to_get'] ) ? $_POST['to_get'] : 'ID' ; 
		$image_size = isset( $_POST['image_size'] ) ? $_POST['image_size'] : false ;
		
		if ( isset( $_POST['search'] ) )
			$args['s'] = stripslashes( $_POST['search'] );
		$args['pagenum'] = ! empty( $_POST['page'] ) ? absint( $_POST['page'] ) : 1;
	
		require(ABSPATH . WPINC . '/class-wp-editor.php');
		$results = _WP_Editors::wp_link_query( $args );
		if ( isset( $results ) && is_array( $results ) && sizeof( $results ) > 0 ){
			foreach( $results as $result ){  
				$to_insert = self::get_option( $result['ID'], $property_to_retrieve ); 
				if ( $to_insert ){ ?>
					<li>
						<a data-post_id="<?php echo $result['ID']; ?>" href="<?php echo get_permalink($result['ID'] ); ?>">
							<span class="title"><?php echo $result['title']; ?></span>
							<span class='type'><?php echo $result['info'] ; ?></span>
						</a>
						<div class="to_insert"><?php echo $to_insert ; ?></div>
					</li>
				<?php }
			}
		}
		wp_die();
	}
	public static function get_option( $post_id, $spec ){
		if ( is_string( $spec ) ){

			$prop_to_get = $spec ;
		} else {
			$prop_to_get = self::property_to_get( $spec['get'] ); 
		} 
		$value = '' ;
		switch ( $prop_to_get ){
			case 'ID' : 
				return $post_id ;
				break; 
			case 'post' : 
				$value = get_post( $post_id ) ;
				break;
			case 'post_title' : 
				$post = get_post( $post_id ) ;
				return $post->post_title ; 
				break; 
			case 'post_content' : 
				$post = get_post( $post_id ) ;
				return apply_filters( 'the_content', $post->post_content ) ; 
				break; 
			case 'post_excerpt' : 
				$post = get_post( $post_id ) ;
				return apply_filters( 'the_excerpt', $post->post_excerpt ) ; 				
				break; 
			case 'post_thumbnail' : 
				$image_size = isset( $spec['image_size'] ) ? $spec['image_size'] : 'full' ;
				return get_the_post_thumbnail( $post_id, 'thumbnail'  ) ;
				break;
			case 'post_thumbnail_id' : 
				$attachment_id = get_post_thumbnail_id( $post_id ) ;
				return $attachment_id ;
				break; 					
			case 'post_thumbnail_url' :
				$attachment_id = get_post_thumbnail_id( $post_id ) ;
				$image_size = isset( $spec['image_size'] ) ? $spec['image_size'] : 'full' ;							 			
				$image_info = wp_get_attachment_image_src( $attachment_id, $image_size ) ;				
				return $image_info[0] ;
				break; 
			case 'url' :
				return get_permalink( $post_id );
				break ;				
		}
	}	
	
   /**
	* LAYOUTS FOR THIS FIELD
	*/
	
}
add_action('wp_ajax_options-page-link-popup', array( 'Cloud_Field_post' , 'ajax_options_internal_search' ) );
