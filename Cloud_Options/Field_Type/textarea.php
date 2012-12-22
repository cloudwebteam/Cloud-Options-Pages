<?php 
// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

class Cloud_Field_textarea extends Field_Type {

	public static function create_field( $args ){
		$field_type = __CLASS__;
		$field = new $field_type( $args ); 
	}

	protected function __construct( $args ){		
		parent::__construct( __CLASS__, $args ); 	
	}

	protected function get_field_html( $args ){
		
		$this->rows = isset( $args['info']['rows'] ) ? $args['info']['rows'] : $this->rows; 
		$this->cols = isset( $args['info']['cols'] ) ? $args['info']['cols'] : $this->cols; 

		$field = '<textarea id="'.$this->info['prefix'] . $this->info['id'] . '" name="'.$this->info['name'] . '" rows="'.$this->rows.'" cols="'.$this->cols.'" >' . $this->info['value'] . '</textarea>';
		return $field;
	}
	
	
   /**
	* LAYOUTS FOR THIS FIELD
	*/		
	
}