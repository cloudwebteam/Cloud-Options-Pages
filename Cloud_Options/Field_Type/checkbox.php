<?php 
// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

class Cloud_Field_checkbox extends Field_Type {

	public static function create_field( $args ){
		$field_type = __CLASS__;
		$field = new $field_type( $args ); 
	}

	protected function __construct( $args ){		
		parent::__construct( __CLASS__, $args ); 	
	}

	protected function get_field_html( $args ){
		$checked = $this->info['value'] == '1' ? 'checked' : '';
		$field = '<input type="checkbox" id="'.$this->info['prefix'] . $this->info['id'] . '" name="'.$this->info['name'] . '" value="1"' . $checked . '/>';	
		return $field;
	}
	
   /**
	* LAYOUTS FOR THIS FIELD
	*/
}