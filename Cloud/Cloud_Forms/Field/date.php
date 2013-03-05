<?php 
// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

class Cloud_Field_date extends Cloud_Field {
	
	public static function create_field( $args ){
		$field = new self( $args ); 
	}

	protected function get_field_html( ){
		$this->size = isset( $this->spec['size'] ) ? $this->spec['size'] : ''; 	
		
		$date_format = isset( $this->spec['date_format'] ) ?  $this->spec['date_format'] : 'mm/dd/yy' ; 
		
		$utc_field = '<input class="timestamp" type="hidden" name="'.$this->info['name'] . '" value=\'' . $this->info['value'] . '\' />';		
		$value_array = json_decode($this->info['value'], true) ;
		$value = isset( $value_array['datetime'] ) ? $value_array['datetime'] : false ;
		
		$field = '<input data-dateformat="'.$date_format.'" type="text" id="'.$this->info['id'] . '" class="datepicker"  size="'.$this->size.'" value="' . $value . '" />';	
		return $utc_field.$field;
	}
	
	public function enqueue_scripts_and_styles( ){

		$this->enqueue_script( 'jquery-ui-datepicker' );

		parent::enqueue_scripts_and_styles( ); 
	}	
	
   /**
	* LAYOUTS FOR THIS FIELD
	*/

	
}