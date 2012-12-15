<?php
// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

class Cloud_Field_content_url extends Field_Type {
	public static $wp_link_dialog_id = 'wp_link_popup' ; 
	
	public static function create_field( $args ){
		$field_type = __CLASS__;
		$field = new $field_type( $args ); 
	}

	protected function __construct( $args ){		
		parent::__construct( __CLASS__, $args ); 
	}

	protected function get_field_html( $args ){
		$this->size = isset( $args['info']['size'] ) ? $args['info']['size'] : ''; 	
		$field = '<input type="text" id="'.$this->info['prefix'] . $this->info['id'] . '" name="'.$this->info['name'] . '" size="'.$this->size.'" type="text" value="' . $this->info['value'] . '" />';	
		return $field;
	}

	public function enqueue_field_scripts_and_styles(){
		$type = substr( __CLASS__, strlen( Field_Type::$class_prefix ) );
		// if they exist, enqueues css and js files with this fields name
		parent::register_scripts_and_styles( __CLASS__ ); 
		wp_localize_script( $type, 'link_popup_id' , self::$wp_link_dialog_id ); 
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
	
		if ( isset( $_POST['search'] ) )
			$args['s'] = stripslashes( $_POST['search'] );
		$args['pagenum'] = ! empty( $_POST['page'] ) ? absint( $_POST['page'] ) : 1;
	
		require(ABSPATH . WPINC . '/class-wp-editor.php');
		$results = _WP_Editors::wp_link_query( $args );
		if ( isset( $results ) && is_array( $results ) && sizeof( $results ) > 0 ){
			foreach( $results as $result ){  ?>
				<li><a data-id="<?php echo $result['ID']; ?>" href="<?php echo get_permalink($result['ID'] ); ?>"><?php echo $result['title']; ?><span class='type'><?php echo $result['info'] ; ?></span></a></li>
			<?php
			}
		}
		wp_die();
	}
   /**
	* LAYOUTS FOR THIS FIELD
	*/
	public function standard ( $args ){
	
		?>
		<tr valign="top" <?php echo $this->attributes; ?>>
			<th scope="row"><?php echo $this->label; ?></th>
			<td <?php echo $this->attributes; ?>>			
				<?php echo $this->field; ?>
				<?php echo $this->description; ?>
			</td>
		</tr>
		<?php
	}
	public function expandable( $args ){
		$field_info = parent::get_field_info($args);
			
	}
	public function custom( $args ){
		$layout_details = $this->info['layout']; ?>
		<div <?php echo $this->attributes; ?>>
		<?php
		switch ( $layout_details['label'] ){
			case 'left' : ?>
				<?php echo $this->label; ?><?php echo $this->field; ?></p>
				<?php echo $this->description; ?>
				
				<?php 
				break;
				
			case 'right' : ?>
				<p><?php echo $this->label; ?><?php echo $this->field; ?></p>
				<?php echo $this->description; ?>

				<?php 
				break;

			case 'top' : ?>
				<p><?php echo $this->label; ?></p>
				<p><?php echo $this->field; ?></p>
				<?php echo $this->description; ?>

				<?php 
				break;	
			default : ?>
				<p><?php echo $this->label; ?></p>
				<p><?php echo $this->field; ?></p>
				<?php echo $this->description; ?>

				<?php 
				break; 
		} ?>
		</div>
		<?php
	}
	
}
add_action('wp_ajax_options-page-link-popup', array( 'Cloud_Field_content_url' , 'ajax_options_internal_search' ) );
