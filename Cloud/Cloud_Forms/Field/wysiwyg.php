<?php 
// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

class Cloud_Field_wysiwyg extends Cloud_Field {

	public static function create_field( $args ){
		$field = new self( $args ); 
	}

	protected function get_field_html( ){
		$settings = array();
		$settings['textarea_name']	= $this->info['name']; 
		$settings['textarea_rows']	= $this->spec['rows']; 
		ob_start();
			wp_editor( $this->info['value'],  $this->info['id'], $settings );
		$field = ob_get_clean() ; 
		return $field;
	}
	protected function get_field_components( $args ){
	}
	public function enqueue_field_scripts_and_styles( $field_type){
		// if they exist, enqueues css and js files with this fields name
		parent::register_scripts_and_styles( $field_type ); 
		self::enqueue_script('media-upload');
		self::enqueue_script('thickbox');	
		
		self::enqueue_script('thickbox');
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