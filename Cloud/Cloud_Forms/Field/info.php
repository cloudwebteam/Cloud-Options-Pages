<?php 
// Prevent loading this file directly
defined( 'Cloud_ABS' ) || exit;

class Cloud_Field_info extends Cloud_Field {
	protected $info ;
	protected $size = 45; 
	protected $field ;
	protected $label ;
	protected $attributes = array() ;

	public static function create_field( $args ){
		$field = new self( $args ); 
	}

	protected function get_field_html( ){
		$field = '';
		return $field;
	}

   /**
	* LAYOUTS FOR THIS FIELD
	*/
}