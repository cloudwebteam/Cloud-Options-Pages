<?php 
// Prevent loading this file directly
defined( 'Cloud_ABS' ) || exit;

class Cloud_Field_color extends Cloud_Field {
	protected $size = 45; 

	public static function create_field( $args ){
		$field = new self( $args ); 
	}

	protected function get_field_html( ){	
		$default_swab = '' ;
		$this->enabler = '';
		$hidden = '';

		$field = '<div class="option">'.$default_swab.'<div class="color-toggle" '.$hidden.' ><input size="5" class="color-picker-input miniColors" id="'. $this->info['id'] . '" name="'.$this->info['name'].'" type="text" value="'.$this->info['value'].'" /></div></div>';


		return $field . $this->enabler;
	}
	public static function enqueue_scripts_and_styles( $field_type = false ){
		// if they exist, enqueues css and js files with this fields name
		
		self::enqueue_script( 'miniColors', Cloud_Forms::get_folder_url(). '/__inc/jquery.miniColors/jquery.minicolors.js', array( 'jquery' ) ); 
		self::enqueue_style( 'miniColors', Cloud_Forms::get_folder_url(). '/__inc/jquery.miniColors/jquery.minicolors.css' ); 
		parent::enqueue_scripts_and_styles( $field_type ); 		

	}
   /**
	* LAYOUTS FOR THIS FIELD
	*/


	
	
}

