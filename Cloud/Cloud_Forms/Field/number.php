<?php 
// Prevent loading this file directly
defined( 'Cloud_ABS' ) || exit;

class Cloud_Field_number extends Cloud_Field {
	protected $size = 50; 

	public static function create_field( $args ){
		$field = new self( $args ); 
	}

	protected function get_field_html( ){
		$min = is_numeric( $this->spec['min'] ) ? ' min="'.$this->spec['min'] .'"' : ''; 
		$max = is_numeric( $this->spec['max'] ) ? ' max="'.$this->spec['max'] .'"' : ''; 		
		$field = '<input type="number" id="' . $this->info['id'] . '" name="'.$this->info['name'] . '" number="'.$this->spec['size'].'" '. $this->info['disabled'] .' value="' . $this->info['value'] . '" '.$min.$max.' />';	
		return $field;
	}
	
   /**
	* LAYOUTS FOR THIS FIELD
	*/	
}