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
		$property_to_get = isset( $args['info']['get'] ) ? $this->property_to_get( $args['info']['get'] ) : 'ID' ; 

		$field = '<input data-to_get="'.$property_to_get. '" type="text" id="'.$this->info['prefix'] . $this->info['id'] . '" name="'.$this->info['name'] . '" size="'.$this->size.'" type="text" value="' . $this->info['value'] . '" />';	

		return $field;
	}
	protected function property_to_get( $property = '' ){ 
		switch ( $property ){
			case 'ID' : 
			case 'id' : 
				return 'ID'; 
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
			case 'thumbnail' :
			case 'image' :
			case 'post_thumbnail' : 
				return 'thumbnail' ;
				break; 
			case 'url' : 
			case 'permalink' :
				return 'url' ; 
				break; 
			default : 
				return str_replace( array( '"', "'", ' ' ), '', $property ) ; 
				
		}
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
		if ( isset( $_POST['search'] ) )
			$args['s'] = stripslashes( $_POST['search'] );
		$args['pagenum'] = ! empty( $_POST['page'] ) ? absint( $_POST['page'] ) : 1;
	
		require(ABSPATH . WPINC . '/class-wp-editor.php');
		$results = _WP_Editors::wp_link_query( $args );
		if ( isset( $results ) && is_array( $results ) && sizeof( $results ) > 0 ){
			foreach( $results as $result ){  
				switch( $property_to_retrieve ){
					case 'post_content': 
					case 'ID' :
					case 'post_excerpt' : 
					case 'post_title' : 
						$post = get_post( $result['ID'] ); 
						$to_insert = $post->$property_to_retrieve ; 
						break; 
					case 'post_thumbnail' : 
						$to_insert = get_post_thumbnail_id( $result['ID'] ) ;
						break; 
					case 'url' : 
						$to_insert = get_permalink( $result['ID'] ) ; 
						break;
					default : 
						$metabox_array = preg_split( '/[\"\']?,[\t ]*[\'\"]?/', $property_to_retrieve ); 
						$metabox = isset( $metabox_array[0] ) ? $metabox_array[0] : null;
						$field = isset( $metabox_array[1] ) ? $metabox_array[1] : null;
						$group = isset( $metabox_array[2] ) ? $metabox_array[2] : null;
						$subfield = isset( $metabox_array[3] ) ? $metabox_array[3] : null;		
						$metabox_data = get_metabox_options( $result['ID'], $metabox , $field, $group, $subfield ) ;
						if ( is_string( $metabox_data ) ){
							$to_insert = $metabox_data; 
						} else {
							$to_insert = false;
						}
						break; 
					}
				if ( $to_insert ){ ?>
					<li><a data-to_insert="<?php echo $to_insert; ?>" title="<?php echo $property_to_retrieve; ?>: <?php echo $to_insert; ?>" href="<?php echo get_permalink($result['ID'] ); ?>"><?php echo $result['title']; ?><span class='type'><?php echo $result['info'] ; ?></span></a></li>
				<?php }
			}
		}
		wp_die();
	}
	
   /**
	* LAYOUTS FOR THIS FIELD
	*/
	
}
add_action('wp_ajax_options-page-link-popup', array( 'Cloud_Field_post' , 'ajax_options_internal_search' ) );
