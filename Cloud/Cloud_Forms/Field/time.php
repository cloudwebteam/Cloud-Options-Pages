<?php 
// Prevent loading this file directly
defined( 'Cloud_ABS' ) || exit;

class Cloud_Field_time extends Cloud_Field {

	public static function create_field( $args ){
		$field = new self( $args ); 
	}

	protected function get_field_html( ){
		$this->size = isset( $this->spec['size'] ) ? $this->spec['size'] : $this->size; 	
		
		$time_format = isset( $this->spec['time_format'] ) ?  $this->spec['time_format'] : 'hh:mm tt' ; 
		$value = $this->info['value'] ? $this->info['value'] : '1:00 AM' ; 
        $field = '<div class="input-append bootstrap-timepicker">' ; 
        $field .= '<input id="'. $this->info['id'].'" name="'.$this->info['name'].'" value="'.$value.'" type="text" class="timepicker input-small" '.$this->info['disabled'] .' >' ;
        $field .= '<span class="add-on"><i class="icon-time"></i></span>' ;
        $field .= '</div>' ;
		return $field;
	}
	
	public static function enqueue_scripts_and_styles( $field_type ){
		self::enqueue_script( 'bootstrap-timepicker' ); 
		self::enqueue_style( 'bootstrap-timepicker' );

		// if they exist, enqueues css and js files with this fields name
		parent::enqueue_scripts_and_styles( $field_type ); 
	}	
	
   /**
	* LAYOUTS FOR THIS FIELD
	*/

	
}