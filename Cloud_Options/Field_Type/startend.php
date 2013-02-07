<?php 
// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

class Cloud_Field_startend extends Field_Type {
	public static function create_field( $args ){
		$field_type = __CLASS__;
		$field = new $field_type( $args ); 
	}

	protected function __construct( $args ){		
		parent::__construct( __CLASS__, $args ); 	
	}

	protected function get_field_html( $args ){
		$this->size = isset( $args['info']['size'] ) ? $args['info']['size'] : ''; 	

		$field_type = isset( $args['info']['field_type'] ) ? $args['info']['field_type'] : 'date' ;
		
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
	protected function get_date_fields( $args ){
		$date_format = isset( $args['info']['date_format'] ) ?  $args['info']['date_format'] : 'mm/dd/yy' ; 

		$start_value = isset( $this->info['value']['start'] ) ? $this->info['value']['start'] : '' ;
		$end_value = isset( $this->info['value']['end'] ) ? $this->info['value']['end'] : '' ;		
		
		$fields = '' ;
		$fields .= '<span class="selector"><span class="start-label">Start</span><input data-dateformat="'.$date_format.'" type="text" id="'.$this->info['prefix'] . $this->info['id'] . '" class="datepicker start" name="'.$this->info['name'] . '[start]" size="'.$this->size.'" type="text" value="' . $start_value. '" /></span>' ;
		$fields .= '<span class="selector"><span class="end-label">End</span><input data-dateformat="'.$date_format.'" type="text" id="'.$this->info['prefix'] . $this->info['id'] . '-end" class="datepicker end" name="'.$this->info['name'] . '[end]" size="'.$this->size.'" type="text" value="' . $end_value . '" /></span>';	

		return $fields; 
	}
	protected function get_datetime_fields( $args ){
		$date_format = isset( $args['info']['date_format'] ) ?  $args['info']['date_format'] : 'mm/dd/yy' ; 
		$time_format = isset( $args['info']['time_format'] ) ?  $args['info']['time_format'] : 'hh:mm tt' ; 
		$start_value = isset( $this->info['value']['start'] ) ? $this->info['value']['start'] : '' ;
		$end_value = isset( $this->info['value']['end'] ) ? $this->info['value']['end'] : '' ;		
		
		
		$start_utc_field = '<input class="timestamp" type="hidden" name="'.$this->info['name'] . '[start]" value=\'' . $start_value . '\' />';
		$end_utc_field = '<input class="timestamp" type="hidden" name="'.$this->info['name'] . '[end]" value=\'' . $end_value . '\' />';
		
		$start_value_array = json_decode( $start_value , true) ; 
		$start_value_utc = isset( $start_value_array['datetime'] ) ? $start_value_array['datetime'] : false ;
		$end_value_array = json_decode( $end_value , true ) ; 
		$end_value_utc = isset( $end_value_array['datetime'] ) ? $end_value_array['datetime'] : false ;		
		$fields = '' ;		
		$fields .= '<span class="selector"><span class="start-label">Start</span>'.$start_utc_field.'<input data-dateformat="'.$date_format.'" data-timeformat="'.$time_format.'"  type="text" id="'.$this->info['prefix'] . $this->info['id'] . '" class="datetimepicker start" size="'.$this->size.'" type="text" value="' . $start_value_utc. '" /></span>';	
		$fields .= '<span class="selector"><span class="end-label">End</span>'.$end_utc_field.'<input data-dateformat="'.$date_format.'" data-timeformat="'.$time_format.'"  type="text" id="'.$this->info['prefix'] . $this->info['id'] . '-end" class="datetimepicker end" size="'.$this->size.'" type="text" value="' . $end_value_utc . '" /></span>';	

		return $fields; 
	}	
	protected function get_time_fields( $args ){
		$time_format = isset( $args['info']['time_format'] ) ?  $args['info']['time_format'] : 'hh:mm tt' ; 

		$start_value = isset( $this->info['value']['start'] ) ? $this->info['value']['start'] : '' ;
		$end_value = isset( $this->info['value']['end'] ) ? $this->info['value']['end'] : '' ;		
		
		$fields = '' ;
		$fields .= '<span class="selector"><span class="start-label">Start</span><input data-timeformat="'.$time_format.'" type="text" id="'.$this->info['prefix'] . $this->info['id'] . '" class="timepicker start" name="'.$this->info['name'] . '[start]" size="'.$this->size.'" type="text" value="' . $start_value. '" /></span>';	
		$fields .= '<span class="selector"><span class="end-label">End</span><input data-timeformat="'.$time_format.'" type="text" id="'.$this->info['prefix'] . $this->info['id'] . '-end" class="timepicker end" name="'.$this->info['name'] . '[end]" size="'.$this->size.'" type="text" value="' . $end_value . '" /></span>';	

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