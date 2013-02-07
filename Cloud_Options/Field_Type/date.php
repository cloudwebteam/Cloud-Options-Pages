<?php 
// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

class Cloud_Field_date extends Field_Type {

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
		
		$utc_field = '<input class="timestamp" type="hidden" name="'.$this->info['name'] . '" value=\'' . $this->info['value'] . '\' />';		
		$value_array = json_decode($this->info['value'], true) ;
		$value = isset( $value_array['datetime'] ) ? $value_array['datetime'] : false ;
		
		$field = '<input data-dateformat="'.$date_format.'" type="text" id="'.$this->info['prefix'] . $this->info['id'] . '" class="datepicker"  size="'.$this->size.'" value="' . $value . '" />';	
		return $utc_field.$field;
	}
	
	public function enqueue_field_scripts_and_styles(){

		wp_enqueue_script( 'jquery-ui-datepicker' );
		
		$theme_name = 'dot-luv'; 
		wp_register_style( 'jquery-ui', parent::get_folder_url(). '/_css/jquery-ui/'.$theme_name.'/jquery-ui.css' );
		wp_register_style( 'jquery-ui-core', parent::get_folder_url(). '/_css/jquery-ui/'.$theme_name.'/jquery.ui.core.css' );
		wp_enqueue_style( 'jquery-ui-date-picker', parent::get_folder_url(). '/_css/jquery-ui/'.$theme_name.'/jquery.ui.datepicker.css', array( 'jquery-ui', 'jquery-ui-core') );

		// if they exist, enqueues css and js files with this fields name
		parent::register_scripts_and_styles( __CLASS__ ); 
	}	
	
   /**
	* LAYOUTS FOR THIS FIELD
	*/

	
}