<?php 
// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

class Cloud_Field_text extends Cloud_Field {
	protected $size = 50; 

	public static function create_field( $args ){
		$field = new self( $args ); 
	}

	protected function get_field_html( ){
		$this->size = isset( $args['info']['size'] ) ? $args['info']['size'] : $this->size; 	
		$field = '<input type="text" id="'.$this->info['prefix'] . $this->info['id'] . '" name="'.$this->info['name'] . '" size="'.$this->size.'" type="text" value="' . $this->info['value'] . '" />';	
		return $field;
	}
	
   /**
	* LAYOUTS FOR THIS FIELD
	*/	
}