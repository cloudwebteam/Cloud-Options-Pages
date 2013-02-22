<?php 
// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

class Cloud_Field_datetime extends Cloud_Field {
	public static function create_field( $args ){
		$field_type = __CLASS__;
		$field = new $field_type( $args ); 
	}

	protected function __construct( $args ){		
		parent::__construct( __CLASS__, $args ); 	
	}

	protected function get_field_html( $args ){
		$this->size = isset( $args['info']['size'] ) ? $args['info']['size'] : ''; 	

		$date_format = isset( $args['info']['date_format'] ) ?  $args['info']['date_format'] : 'mm/dd/yy' ; 
		$time_format = isset( $args['info']['time_format'] ) ?  $args['info']['time_format'] : 'hh:mm tt' ; 

		$utc_field = '<input class="timestamp" type="hidden" name="'.$this->info['name'] . '" value=\'' . $this->info['value'] . '\' />';
		$value_array = json_decode($this->info['value'], true) ;
		$value = isset( $value_array['datetime'] ) ? $value_array['datetime'] : false ;
		$field = '<input data-dateformat="'.$date_format.'" data-timeformat="'.$time_format.'" type="text" id="'.$this->info['prefix'] . $this->info['id'] . '" class="datetimepicker" size="'.$this->size.'" value="' . $value . '" />';	
		return $utc_field.$field;
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
	public static function get_option(  $value , $spec ){
		$date_format = $spec['date_format_php'] ; 
		return date( $date_format  , $value );
	}
	
   /**
	* LAYOUTS FOR THIS FIELD
	*/
	
}