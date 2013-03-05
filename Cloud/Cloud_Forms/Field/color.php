<?php 
// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

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
	public function enqueue_scripts_and_styles( ){
		// if they exist, enqueues css and js files with this fields name

		$this->enqueue_script( 'miniColors', Cloud_Forms::get_folder_url(). '/__inc/jquery.miniColors/jquery.minicolors.js', array( 'jquery' ) ); 
		$this->enqueue_style( 'miniColors', Cloud_Forms::get_folder_url(). '/__inc/jquery.miniColors/jquery.minicolors.css' ); 
		parent::enqueue_scripts_and_styles( ); 		

	}
   /**
	* LAYOUTS FOR THIS FIELD
	*/


	
	
}

