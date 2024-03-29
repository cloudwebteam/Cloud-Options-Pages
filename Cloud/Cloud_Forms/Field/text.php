<?php 
// Prevent loading this file directly
defined( 'Cloud_ABS' ) || exit;

class Cloud_Field_text extends Cloud_Field {
	protected $size = 50; 

	public static function create_field( $args ){
		$field = new self( $args ); 
	}

	protected function get_field_html( ){
		$field = '<input type="text" id="' . $this->info['id'] . '" name="'.$this->info['name'] . '" size="'.$this->spec['size'].'" '. $this->info['disabled'] .' value="' . $this->info['value'] . '" />';	
		return $field;
	}
	
   /**
	* LAYOUTS FOR THIS FIELD
	*/	
}