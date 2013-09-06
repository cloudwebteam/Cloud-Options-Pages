<?php 
// Prevent loading this file directly
defined( 'Cloud_ABS' ) || exit;

class Cloud_Field_datetime extends Cloud_Field {
	public static function create_field( $args ){
		$field = new self( $args ); 
	}

	protected function get_field_html( ){
	
		$this->date_format = isset( $this->spec['date_format'] ) ?  $this->spec['date_format'] : 'mm/dd/yy' ; 
		$this->time_format = isset( $this->spec['time_format'] ) ?  $this->spec['time_format'] : 'hh:mm tt' ; 

		if ( $this->info['save_json'] ){
			$field = $this->get_dynamic_field(); 
		} else {
			$field = $this->get_regular_field(); 
		}
		return $field;
	}
	protected function get_dynamic_field(){
		$utc_field = '<input class="timestamp" type="hidden" name="'.$this->info['name'] . '" value=\'' . $this->info['value'] . '\' />';
		$value_array = json_decode($this->info['value'], true) ;
		$value = isset( $value_array['datetime'] ) ? $value_array['datetime'] : false ;
		$field = '<input data-dateformat="'.$date_format.'" data-timeformat="'.$time_format.'" type="text" id="'. $this->info['id'] . '" class="datetimepicker" size="'.$this->size.'" value="' . $value . '"'.$this->info['disabled'] .'  />';	
		return $utc_field.$field;
	}		
	protected function get_regular_field(){
		
		return $this->get_date_field() . $this->get_time_field() . '<input class="datetime" value="'.$this->info['value'] .'" name="'.$this->info['name'].'" type="hidden" />' ; 
	}		
	protected function get_date_field(){
		$value = isset( $this->info['value'] ) ? $this->info['value'] : false; 
	
		$field = '<input data-dateformat="'.$this->date_format.'" type="text" id="'.$this->info['id'] . '-date" class="datepicker"  size="'.$this->spec['size'].'" value="' . $value. '" />';	
		return $field; 
	}
	protected function get_time_field(){
		$value = isset( $this->info['value']['time'] ) ? $this->info['value']['time'] : false; 
		
        $field = '<div class="input-append bootstrap-timepicker">' ; 
        $field .= '<input id="'. $this->info['id'].'" value="'.$value.'" type="text" class="timepicker input-small" '.$this->info['disabled'] .' >' ;
        $field .= '<span class="add-on"><i class="icon-time"></i></span>' ;
        $field .= '</div>' ;	
        return $field; 
	}
	public static function enqueue_scripts_and_styles( $field_type = false ){

		self::enqueue_script( 'jquery-ui-datepicker' ) ;
		self::enqueue_script( 'bootstrap-timepicker' ) ; 

		self::enqueue_style( 'bootstrap-timepicker' ) ;
		self::enqueue_style( 'jquery-ui-lightness' ); 
		
		parent::enqueue_scripts_and_styles( $field_type  ) ; 
	}		
	public static function get_option(  $value , $spec ){
		$date_format = $spec['date_format_php'] ; 
		return date( $date_format  , $value );
	}
	
   /**
	* LAYOUTS FOR THIS FIELD
	*/
	
}