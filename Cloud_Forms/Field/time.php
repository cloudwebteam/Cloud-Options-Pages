<?php 
// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

class Cloud_Field_time extends Cloud_Field {
	protected $info ;
	protected $size = 20;
	public static function create_field( $args ){
		$field_type = __CLASS__;
		$field = new $field_type( $args ); 
	}

	protected function __construct( $args ){		
		parent::__construct( __CLASS__, $args ); 	
	}

	protected function get_field_html( $args ){
		$this->size = isset( $args['info']['size'] ) ? $args['info']['size'] : $this->size; 	
		
		$time_format = isset( $args['info']['time_format'] ) ?  $args['info']['time_format'] : 'hh:mm tt' ; 
		
		$field = '<input data-timeformat="'.$time_format.'" type="text" id="'.$this->info['prefix'] . $this->info['id'] . '" class="timepicker" name="'.$this->info['name'] . '" size="'.$this->size.'" type="text" value="' . $this->info['value'] . '" />';	
		return $field;
	}
	
	public function enqueue_field_scripts_and_styles(){

		wp_register_script( 'jquery-ui-timepicker-addon', Cloud_Options::get_folder_url() . '/__inc/js/jquery-ui-timepicker-addon/jquery-ui-timepicker-addon.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-datepicker') ); 
		wp_enqueue_script( 'jquery-ui-slider' );
		wp_enqueue_script( 'jquery-ui-timepicker-addon' );
		
		$theme_name = 'dot-luv'; 
		wp_register_style( 'jquery-ui', parent::get_folder_url(). '/_css/jquery-ui/'.$theme_name.'/jquery-ui.css' );
		wp_register_style( 'jquery-ui-core', parent::get_folder_url(). '/_css/jquery-ui/'.$theme_name.'/jquery.ui.core.css' );
		wp_enqueue_style( 'jquery-ui-slider', parent::get_folder_url(). '/_css/jquery-ui/'.$theme_name.'/jquery.ui.slider.css', array( 'jquery-ui', 'jquery-ui-core') );

		// if they exist, enqueues css and js files with this fields name
		parent::register_scripts_and_styles( __CLASS__ ); 
	}	
	
   /**
	* LAYOUTS FOR THIS FIELD
	*/

	
}