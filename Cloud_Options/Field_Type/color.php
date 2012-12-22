<?php 
// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

class Cloud_Field_color extends Field_Type {
	protected $info ;
	protected $size = 45; 
	protected $field ;
	protected $label ;
	protected $url_button ; 
	protected $attributes = 'class="color-picker"' ;

	public static function create_field( $args ){
		$field_type = __CLASS__;
		$field = new $field_type( $args ); 
	}
	protected function __construct( $args ){		
		parent::__construct( __CLASS__, $args ); 	
	}	

	protected function get_field_html( $args ){	
		if ( $this->info['settable_defaults'] ){
			$default_swab = '<div class="default-swab-container"><span class="default-swab" title="Click for default color" style="background:'.$this->info['default'] .'">df</span></div>' ;
			$checked = $this->info['enabled'] ? 'checked="checked"' : '' ;
			$this->enabler = '<span class="enable-override"><input type="checkbox" value="true" name="'. $this->info['enabled_name'].'" id="'.$this->info['enabled_name'].'" class="option_enabler color" '.$checked. ' /><label for="'.$this->info['enabled_name'].'">Override default</span></label>';
			$hidden = $this->info['enabled'] ? '' : 'style="display: none; "' ; 			
		} else { 
			$default_swab = '<div class="default-swab-container"><span class="default-swab" title="Click for default color" style="background:'.$this->info['default'] .'">df</span></div>' ;
			$this->enabler = '';
			$hidden = '';
		}				

		$field = '<div class="option">'.$default_swab.'<div class="color-toggle" '.$hidden.' ><input size="5" class="color-picker-input miniColors" id="'.$this->info['prefix'] . $this->info['id'] . '" name="'.$this->info['name'].'" type="text" value="'.$this->info['value'].'" /></div></div>';


		return $field . $this->enabler;
	}
	public function enqueue_field_scripts_and_styles(){
		// if they exist, enqueues css and js files with this fields name
		parent::register_scripts_and_styles( __CLASS__ ); 
		wp_enqueue_script( 'miniColors', parent::get_folder_url(). '/_js/jquery.miniColors.min.js', array( 'jquery' ) ); 
		wp_enqueue_style( 'miniColors', parent::get_folder_url(). '/_css/jquery.miniColors.css' ); 
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-dialog' ); 

	}
	
	private function get_image(){
		if ( isset( $this->info['value'] ) && $this->info['value'] !== '' ){
			return '<img class="preview-image img-polaroid" src="'.$this->info['value'].'" title="'.$this->info['value'].'" />';	
		} else {
			return '<img class="hidden preview-image img-polaroid" title="'.$this->info['value'].'" />';	
		}
	}	
	private function make_layout(){
		$layout_details = $this->info['layout']; 
		if ( is_array( $layout_details ) && sizeof( $layout_details ) > 0 ){
			foreach( $layout_details as $row ){ 
				echo 'row<br />'; 
				if ( is_array( $row ) && sizeof( $row ) > 0 ){
					foreach( $row as $row_item ){
						echo $row_item ; 
					}
				} else {
					echo $row.'<br />';
				}
				echo '<br />'; 
			}
		}
	}
  
   /**
	* LAYOUTS FOR THIS FIELD
	*/


	
	
}

