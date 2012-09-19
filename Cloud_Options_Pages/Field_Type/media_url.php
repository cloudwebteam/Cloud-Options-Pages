<?php 
class media_url extends Field_Type {
	private $info ;
	private $size = 45; 
	private $field ;
	private $label ;
	private $url_button ; 
	private $attributes = array() ;

	public static function create_field( $args ){
		$field_type = __CLASS__;
		$field = new $field_type( $args ); 
	}	
	private function __construct( $args ){
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_stuff' ) ); 
		$this->info = parent::get_field_info($args);

		$this->size = isset( $this->info['size'] ) ? $this->info['size'] : $this->size; 
		$this->field = '<input type="hidden" id="'.$this->info['prefix'] . $this->info['id'] . '" name="'.$this->info['name'] . '" size="'.$this->size.'" type="text" value="' . $this->info['value'] . '" />';
		$this->url_button = '<input id="upload_button" class="upload_button btn btn-mini" type="button" name="upload_button" value="Find or Upload file" />';
		$this->image = $this->get_image();
		$this->label = parent::get_label( $this->info ); 
		$this->description = isset( $this->info['description']) && $this->info['description'] !== '' ? '<p class="description">'.$this->info['description'] . '</p>' : '';

		$layout = parent::get_layout( __CLASS__, $this->info );
		$this->$layout( $args ); 
	}

	public function enqueue_field_scripts_and_styles(){
		// if they exist, enqueues css and js files with this fields name
		parent::register_scripts_and_styles( __CLASS__ ); 

		wp_enqueue_script('media-upload');
		wp_enqueue_script('thickbox');	
		
		wp_enqueue_style('thickbox');
	}
	
	private function get_image(){
		if ( isset( $this->info['value'] ) && $this->info['value'] !== '' ){
			return '<img class="preview-image img-polaroid" src="'.$this->info['value'].'" title="'.$this->info['value'].'" />';	
		} else {
			return '<img class="hidden preview-image img-polaroid" title="'.$this->info['value'].'" />';	
		}
	}	
	/* LAYOUTS */
	
	public function standard ( $args ){
		?>
		<tr valign="top">
			<th scope="row"><?php echo $this->label; ?></th>
			<td <?php echo $this->attributes; ?>>
				<?php echo $this->field; ?><?php echo $this->url_button; ?><?php echo $this->image; ?>
				<?php echo $this->description; ?>
			</td>
		</tr>
		<?php
	}
	public function expandable( $args ){
		$field_info = parent::get_field_info($args);
			
	}
	public function custom( $args ){
		$layout_details = $this->info['layout']; 
		
	}
	
}