<?php 
// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

class Cloud_Field_info extends Cloud_Field {
	protected $info ;
	protected $size = 45; 
	protected $field ;
	protected $label ;
	protected $attributes = array() ;

	public static function create_field( $args ){
		$field_type = __CLASS__;
		$field = new $field_type( $args ); 
	}

	protected function __construct( $args ){		
		parent::__construct( __CLASS__, $args ); 	
	}

	protected function get_field_html( ){
		$field = '';
		return $field;
	}

   /**
	* LAYOUTS FOR THIS FIELD
	*/
}