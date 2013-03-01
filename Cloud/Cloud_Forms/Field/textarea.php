<?php 
// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

class Cloud_Field_textarea extends Cloud_Field {

	public static function create_field( $args ){
		$field_type = __CLASS__;
		$field = new $field_type( $args ); 
	}

	protected function __construct( $args ){		
		parent::__construct( __CLASS__, $args ); 	
	}

	protected function get_field_html( ){
		
		$this->rows = isset( $this->spec['rows'] ) ? $this->spec['rows'] : 3 ; 
		$this->cols = isset( $this->spec['cols'] ) ? $this->spec['cols'] : '' ; 

		$field = '<textarea id="'.$this->info['prefix'] . $this->info['id'] . '" name="'.$this->info['name'] . '" rows="'.$this->rows.'" cols="'.$this->cols.'" >' . $this->info['value'] . '</textarea>';
		return $field;
	}
	
	
   /**
	* LAYOUTS FOR THIS FIELD
	*/		
	
}