<?php 
class url extends Field_Type {
	protected $info ;
	protected $size = 53; 
	protected $field ;
	protected $label ;
	protected $url_button ; 
	protected $attributes = 'class="url"' ;

	public static function create_field( $args ){
		$field_type = __CLASS__;
		$field = new $field_type( $args ); 
	}	

	protected function __construct( $args ){		
		parent::__construct( __CLASS__, $args ); 	
	}

	protected function get_field_html( $args ){
		$this->size = isset( $args['info']['size'] ) ? $args['info']['size'] : $this->size; 	
		$field = '<input class="url_field" type="text" id="'.$this->info['prefix'] . $this->info['id'] . '" name="'.$this->info['name'] . '" size="'.$this->size.'" type="text" value="' . $this->info['value'] . '" />';
		return $field;
	}
	protected function get_field_components( $args ){
		$this->url_button = '<input class="url_button upload_button btn btn-mini" type="button" name="upload_button" value="Find or Upload file" />';
	}
	public function enqueue_field_scripts_and_styles(){
		// if they exist, enqueues css and js files with this fields name
		parent::register_scripts_and_styles( __CLASS__ ); 

		wp_enqueue_script('media-upload');
		wp_enqueue_script('thickbox');	
		
		wp_enqueue_style('thickbox');
	}
	
   /**
	* LAYOUTS FOR THIS FIELD
	*/
	public function standard ( $args ){
		?>
		<tr valign="top">
			<th scope="row"><?php echo $this->label; ?></th>
			<td <?php echo $this->attributes; ?>>
				<?php echo $this->field; ?><?php echo $this->url_button; ?>
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
		?>
			<div <?php echo $this->attributes; ?>>
				<p><?php echo $this->label; ?></p>
				<p><?php echo $this->field; ?><?php echo $this->url_button; ?></p>
				<p><?php echo $this->description; ?></p>
			</div>		
		<?php
	}
	
}