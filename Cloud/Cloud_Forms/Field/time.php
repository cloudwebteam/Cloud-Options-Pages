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

        $field = '<div class="input-append bootstrap-timepicker">' ; 
        $field .= '<input id="'. $this->info['id'].'" name="'.$this->info['name'].'" value="'.$this->info['value'].'" type="text" class="timepicker input-small">' ;
        $field .= '<span class="add-on"><i class="icon-time"></i></span>' ;
        $field .= '</div>' ;
		return $field;
	}
	
	public function enqueue_scripts_and_styles( ){
		$this->enqueue_script( 'bootstrap-timepicker' ); 
		$this->enqueue_style( 'bootstrap-timepicker' );

		// if they exist, enqueues css and js files with this fields name
		parent::enqueue_scripts_and_styles( ); 
	}	
	
   /**
	* LAYOUTS FOR THIS FIELD
	*/

	
}