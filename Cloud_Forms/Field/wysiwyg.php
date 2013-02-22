<?php 
// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

class Cloud_Field_wysiwyg extends Cloud_Field {

	public static function create_field( $args ){
		$field_type = __CLASS__;
		$field = new $field_type( $args ); 
	}
	protected function __construct( $args ){		
		parent::__construct( __CLASS__, $args ); 	
	}	

	protected function get_field_html( $args ){
		$settings = array();
		$settings['textarea_name']	= $this->info['name']; 
		$settings['textarea_rows']	= $args['info']['rows']; 
		ob_start();
			wp_editor( $this->info['value'], $this->info['prefix'] . $this->info['id'], $settings );
		$field = ob_get_clean() ; 
		return $field;
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