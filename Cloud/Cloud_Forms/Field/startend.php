<?php 
// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

class Cloud_Field_startend extends Cloud_Field {
	public static function create_field( $args ){
		$field_type = __CLASS__;
		$field = new $field_type( $args ); 
	}

	protected function __construct( $args ){		
		parent::__construct( __CLASS__, $args ); 	
	}

	protected function get_field_html( ){
		$this->size = isset( $this->spec['size'] ) ? $this->spec['size'] : ''; 	

		$field_type = isset( $this->spec['field_type'] ) ? $this->spec['field_type'] : 'date' ;
		$this->date_format = isset( $this->spec['date_format'] ) ?  $this->spec['date_format'] : 'mm/dd/yy'  ;
		$this->time_format = isset( $this->spec['time_format'] ) ?  $this->spec['time_format'] : 'hh:mm tt' ; 

		$this->start_value = isset( $this->info['value']['start'] ) ? $this->info['value']['start'] : '' ;
		$this->start_utc = $this->parse_dynamic_value( $this->start_value );
		$this->end_value = isset( $this->info['value']['end'] ) ? $this->info['value']['end'] : '' ;	
		$this->end_utc = $this->parse_dynamic_value( $this->end_value ) ;	
		
		$this->start_utc_field = '<input class="timestamp" type="hidden" name="'.$this->info['name'] . '[start]" value=\'' . $this->start_value . '\' />';
		$this->end_utc_field = '<input class="timestamp" type="hidden" name="'.$this->info['name'] . '[end]" value=\'' . $this->end_value . '\' />';
	
		switch( $field_type ){
		
			case 'time' : 
				$fields = $this->get_time_fields( ) ;
				break; 
			case 'date' : 
			default: 
				$fields = $this->get_date_fields( ) ;			
				break; 								
		}
		return $fields;
	}
	protected function parse_dynamic_value( $value ){
		$json_array = json_decode( $value , true) ; 
		return isset( $json_array['datetime'] ) ? $json_array['datetime'] : false ;	
	}
	protected function get_date_fields( ){
	
		$fields = '' ;
		$fields .= '<span class="selector"><span class="start-label">Start</span>'.$this->start_utc_field.'<input data-dateformat="'.$this->date_format.'" type="text" id="' . $this->info['id'] . '" class="datepicker start" size="'.$this->size.'" value="' .  $this->start_utc. '" /></span>' ;
		$fields .= '<span class="selector"><span class="end-label">End</span>'.$this->end_utc_field.'<input data-dateformat="'.$this->date_format.'" type="text" id="' . $this->info['id'] . '-end" class="datepicker end" size="'.$this->size.'" value="' .  $this->end_utc . '" /></span>';	

		return $fields; 
	}
	protected function get_time_fields( ){

		$fields = '' ;
        $fields .= '<span class="start-label">Start</span>';
        $fields .= '<div class="input input-append bootstrap-timepicker">' ; 
        $fields .= '<input id="'. $this->info['id'].'" name="'.$this->info['name'] . '[start]" type="text" value="'.$this->start_value.'" class="timepicker start input-small">' ;
        $fields .= '<span class="add-on"><i class="icon-time"></i></span>' ;
        $fields .= '</div>' ;	
        $fields .= '<span class="end-label">End</span>';
        $fields .= '<div class="input input-append bootstrap-timepicker">' ; 
        $fields .= '<input id="'. $this->info['id'].'-end" name="'.$this->info['name'] . '[end]" type="text" value="'.$this->end_value.'" class="timepicker end input-small">' ;
        $fields .= '<span class="add-on"><i class="icon-time"></i></span>' ;
        $fields .= '</div>' ;	        	
	//	$fields .= '<span class="selector"><span class="start-label">Start</span>'.$this->start_utc_field.'<input data-dateformat="'.$this->date_format.'" type="text" id="'.$this->info['prefix'] . $this->info['id'] . '" class="timepicker start" size="'.$this->size.'" value="' .  $this->start_utc. '" /></span>' ;
	//	$fields .= '<span class="selector"><span class="end-label">End</span>'.$this->end_utc_field.'<input data-dateformat="'.$this->date_format.'" type="text" id="'.$this->info['prefix'] . $this->info['id'] . '-end" class="timepicker end" size="'.$this->size.'" value="' .  $this->end_utc . '" /></span>';	

		return $fields; 
	}		
	public function enqueue_field_scripts_and_styles(){

		$this->enqueue_script( 'jquery-ui-datepicker' );
		$this->enqueue_script( 'jquery-ui-slider' );
		$this->enqueue_script( 'bootstrap-timepicker' );
		
		$this->enqueue_style( 'bootstrap-timepicker' );

		// if they exist, enqueues css and js files with this fields name
		parent::register_scripts_and_styles( __CLASS__ ); 
	}	
	
   /**
	* LAYOUTS FOR THIS FIELD
	*/
	
}