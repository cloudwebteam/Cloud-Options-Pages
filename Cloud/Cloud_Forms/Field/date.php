<?php 
// Prevent loading this file directly
defined( 'Cloud_ABS' ) || exit;

class Cloud_Field_date extends Cloud_Field {
	
	public static function create_field( $args ){
		$field = new self( $args ); 
	}

	protected function get_field_html( ){
		$this->size = isset( $this->spec['size'] ) ? $this->spec['size'] : ''; 	
		
		$this->date_format = isset( $this->spec['date_format'] ) ?  $this->spec['date_format'] : 'mm/dd/yy' ; 
		
		if ( $this->info['save_json'] ){
			$field = $this->get_dynamic_field(); 
		} else {
			$field = $this->get_regular_field(); 
		}
		return $field ;
		
	}
	protected function get_dynamic_field(){
		$utc_field = '<input class="timestamp" type="hidden" name="'.$this->info['name'] . '" value=\'' . $this->info['value'] . '\' />';		
		$value_array = json_decode( stripslashes( $this->info['value'] ), true) ;
		$value = isset( $value_array['datetime'] ) ? $value_array['datetime'] : false ;
		$field = '<input data-dateformat="'.$this->date_format.'" type="text" id="'.$this->info['id'] . '" class="datepicker saves-json"  size="'.$this->size.'" value="' . $value . '" '.$this->info['disabled'] .' />';	
		return $utc_field.$field;	
	}	
	protected function get_regular_field(){
		$data = array(); 
		$data[ 'dateformat' ] = $this->date_format; 
		if ( $this->spec['min_date'] !== false ){
			$data[ 'minDate' ] =  'data-mindate="'.$this->spec['min_date'].'"';
		}
		if ( $this->spec['max_date'] !== false ){
			$data[ 'maxDate' ] =  'data-maxdate="'.$this->spec['max_date'].'"';
		}		 		
		
		$data_atts = implode( ' ', $data ); 
		$field = '<input data-dateformat="'.$this->date_format.'" name="'.$this->info['name'] . '" '.$data_atts. ' type="text" id="'.$this->info['id'] . '" class="datepicker"  size="'.$this->size.'" value="' . $this->info['value'] . '" '.$this->info['disabled'] .' />';	
		return $field; 
	}
	public static function enqueue_scripts_and_styles( $field_type = false ){

		self::enqueue_script( 'jquery-ui-datepicker' );

		parent::enqueue_scripts_and_styles( $field_type ); 
	}	
	
   /**
	* LAYOUTS FOR THIS FIELD
	*/

	
}