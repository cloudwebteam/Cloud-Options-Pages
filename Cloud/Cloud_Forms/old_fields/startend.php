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
		$this->size = isset( $args['info']['size'] ) ? $args['info']['size'] : ''; 	

		$field_type = isset( $args['info']['field_type'] ) ? $args['info']['field_type'] : 'date' ;
		$this->date_format = isset( $args['info']['date_format'] ) ?  $args['info']['date_format'] : 'mm/dd/yy'  ;
		$this->time_format = isset( $args['info']['time_format'] ) ?  $args['info']['time_format'] : 'hh:mm tt' ; 

		$this->start_value = isset( $this->info['value']['start'] ) ? $this->info['value']['start'] : '' ;
		$this->start_utc = $this->parse_dynamic_value( $this->start_value );
		$this->end_value = isset( $this->info['value']['end'] ) ? $this->info['value']['end'] : '' ;	
		$this->end_utc = $this->parse_dynamic_value( $this->end_value ) ;	
		
		$this->start_utc_field = '<input class="timestamp" type="hidden" name="'.$this->info['name'] . '[start]" value=\'' . $this->start_value . '\' />';
		$this->end_utc_field = '<input class="timestamp" type="hidden" name="'.$this->info['name'] . '[end]" value=\'' . $this->end_value . '\' />';
	
		switch( $field_type ){
		
			case 'time' : 
				$fields = $this->get_time_fields( $args ) ;
				break; 
			case 'datetime' : 
				$fields = $this->get_datetime_fields( $args ) ;
				break; 
			case 'date' : 
			default: 
				$fields = $this->get_date_fields( $args ) ;			
				break; 								
		}
		return $fields;
	}
	protected function parse_dynamic_value( $value ){
		$json_array = json_decode( $value , true) ; 
		return isset( $json_array['datetime'] ) ? $json_array['datetime'] : false ;	
	}
	protected function get_date_fields( $args ){
	
		$fields = '' ;
		$fields .= '<span class="selector"><span class="start-label">Start</span>'.$this->start_utc_field.'<input data-dateformat="'.$this->date_format.'" type="text" id="'.$this->info['prefix'] . $this->info['id'] . '" class="datepicker start" size="'.$this->size.'" value="' .  $this->start_utc. '" /></span>' ;
		$fields .= '<span class="selector"><span class="end-label">End</span>'.$this->end_utc_field.'<input data-dateformat="'.$this->date_format.'" type="text" id="'.$this->info['prefix'] . $this->info['id'] . '-end" class="datepicker end" size="'.$this->size.'" value="' .  $this->end_utc . '" /></span>';	

		return $fields; 
	}
	protected function get_datetime_fields( $args ){
		$fields = '' ;		
		$fields .= '<span class="selector"><span class="start-label">Start</span>'.$this->start_utc_field.'<input data-dateformat="'.$this->date_format.'" data-timeformat="'.$this->time_format.'"  type="text" id="'.$this->info['prefix'] . $this->info['id'] . '" class="datetimepicker start" size="'.$this->size.'" value="' . $this->start_utc. '" /></span>';	
		$fields .= '<span class="selector"><span class="end-label">End</span>'.$this->end_utc_field.'<input data-dateformat="'.$this->date_format.'" data-timeformat="'.$this->time_format.'"  type="text" id="'.$this->info['prefix'] . $this->info['id'] . '-end" class="datetimepicker end" size="'.$this->size.'"value="' .  $this->end_utc . '" /></span>';	

		return $fields; 
	}	
	protected function get_time_fields( $args ){

		$fields = '' ;
		$fields .= '<span class="selector"><span class="start-label">Start</span>'.$this->start_utc_field.'<input data-dateformat="'.$this->date_format.'" type="text" id="'.$this->info['prefix'] . $this->info['id'] . '" class="timepicker start" size="'.$this->size.'" value="' .  $this->start_utc. '" /></span>' ;
		$fields .= '<span class="selector"><span class="end-label">End</span>'.$this->end_utc_field.'<input data-dateformat="'.$this->date_format.'" type="text" id="'.$this->info['prefix'] . $this->info['id'] . '-end" class="timepicker end" size="'.$this->size.'" value="' .  $this->end_utc . '" /></span>';	

		return $fields; 
	}		
	public function enqueue_field_scripts_and_styles(){

		wp_register_script( 'jquery-ui-timepicker-addon', Cloud_Options::get_folder_url() . '/__inc/js/jquery-ui-timepicker-addon/jquery-ui-timepicker-addon.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-datepicker') ); 
		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_script( 'jquery-ui-slider' );
		wp_enqueue_script( 'jquery-ui-timepicker-addon' );
		
		$theme_name = 'dot-luv'; 
		wp_register_style( 'jquery-ui', parent::get_folder_url(). '/_css/jquery-ui/'.$theme_name.'/jquery-ui.css' );
		wp_register_style( 'jquery-ui-core', parent::get_folder_url(). '/_css/jquery-ui/'.$theme_name.'/jquery.ui.core.css' );
		wp_enqueue_style( 'jquery-ui-date-picker', parent::get_folder_url(). '/_css/jquery-ui/'.$theme_name.'/jquery.ui.datepicker.css', array( 'jquery-ui', 'jquery-ui-core') );
		wp_enqueue_style( 'jquery-ui-slider', parent::get_folder_url(). '/_css/jquery-ui/'.$theme_name.'/jquery.ui.slider.css', array( 'jquery-ui', 'jquery-ui-core') );

		// if they exist, enqueues css and js files with this fields name
		parent::register_scripts_and_styles( __CLASS__ ); 
	}	
	
   /**
	* LAYOUTS FOR THIS FIELD
	*/
	
}