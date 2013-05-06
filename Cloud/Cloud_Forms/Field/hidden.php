<?php 
// Prevent loading this file directly
defined( 'Cloud_ABS' ) || exit;

class Cloud_Field_hidden extends Cloud_Field {

	public static function create_field( $args ){
		$field = new self( $args ); 
	}
	
	protected function get_field_html( ){
		$field = '<input type="hidden" id="'. $this->info['id'] . '" name="'.$this->info['name'] . '" value="' . $this->info['value'] . '" />';	
		return $field;
	}
	
   /**
	* LAYOUTS FOR THIS FIELD
	*/
}