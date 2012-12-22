<?php 
// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

class Cloud_Field_media_url extends Field_Type {
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
		$this->url_button = '<input class="upload_button btn btn-mini" type="button" name="upload_button" value="Find or Upload file" />';
		$this->size = isset( $args['info']['size'] ) ? $args['info']['size'] : $this->size; 	

		if ( $args['info']['use_image'] ){
			$this->image = $this->get_image();	
			$input_type = 'hidden' ;
		} else {
			$this->image = '';
			$input_type = 'text' ;
		}
		$field = '<input class="url_field" type="'.$input_type.'" id="'.$this->info['prefix'] . $this->info['id'] . '" name="'.$this->info['name'] . '" size="'.$this->size.'" type="text" value="' . $this->info['value'] . '" />';
		
		return $this->url_button .$field . $this->image;
	}
	protected function get_field_components( $args ){
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

	
}