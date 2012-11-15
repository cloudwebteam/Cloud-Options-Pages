<?php 
class media_url extends Field_Type {
	protected $info ;
	protected $size = 45; 
	protected $field ;
	protected $label ;
	protected $url_button ; 
	protected $attributes = 'class="media-url"' ;

	public static function create_field( $args ){
		$field_type = __CLASS__;
		$field = new $field_type( $args ); 
	}
	protected function __construct( $args ){		
		parent::__construct( __CLASS__, $args ); 	
	}	

	protected function get_field_html( $args ){
		$this->size = isset( $args['info']['size'] ) ? $args['info']['size'] : $this->size; 	
		$field = '<input type="hidden" id="'.$this->info['prefix'] . $this->info['id'] . '" name="'.$this->info['name'] . '" size="'.$this->size.'" type="text" value="' . $this->info['value'] . '" />';
		return $field;
	}
	protected function get_field_components( $args ){
		$this->url_button = '<input class="upload_button btn btn-mini" type="button" name="upload_button" value="Find or Upload file" />';
		$this->image = $this->get_image();	
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
  
  
  
   /**
	* LAYOUTS FOR THIS FIELD
	*/
	public function standard ( $args ){
		?>
		<tr valign="top" <?php echo $this->attributes; ?>>
			<th scope="row"><?php echo $this->label; ?></th>
			<td <?php echo $this->attributes; ?>>
				<p><?php echo $this->field; ?><?php echo $this->url_button; ?><?php echo $this->image; ?></p>
				<p><?php echo $this->description; ?></p>
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
				<p><?php echo $this->field; ?><?php echo $this->url_button; ?><?php echo $this->image; ?></p>
				<p><?php echo $this->description; ?></p>
			</div>		
		<?php
	}
	
}