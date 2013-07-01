<?php 
// Prevent loading this file directly
defined( 'Cloud_ABS' ) || exit;

class Cloud_Field_range_slider extends Cloud_Field {
	
	public static function create_field( $args ){
		$field = new self( $args ); 
	}

	protected function get_field_html( ){		
		$data = '';
		
		if ( $this->spec['min'] ){
			$data .= ' data-min="'.$this->spec['min'].'"' ; 
		} 
		if ( $this->spec['max'] ){
			$data .= ' data-max="'.$this->spec['max'].'"' ; 
		} 		
		if ( $this->spec['step'] ){
			$data .= ' data-step="'.$this->spec['step'].'"' ; 		
		}
		
		$min_value = isset( $this->info['value']['min'] ) ? $this->info['value']['min'] : false; 
		$max_value = isset( $this->info['value']['max'] ) ? $this->info['value']['max'] : false; 
		
		$field = '<div id="'.$this->info['id'].'" class="range-slider" '.$data .' ></div>'; 
		$field .= '<input type="hidden" id="'.$this->info['id'] .'-min" class="range-slider-min" name="'.$this->info['name'].'[min]" value="'.$min_value.'" />'; 
		$field .= '<input type="hidden" id="'.$this->info['id'] .'-max" class="range-slider-max" name="'.$this->info['name'].'[max]" value="'.$max_value.'" />'; 		
		return $field ;
		
	}
	public static function enqueue_scripts_and_styles( $field_type ){

		self::enqueue_script( 'jquery-ui-slider' );
		parent::enqueue_scripts_and_styles( $field_type ); 
	}	
	
   /**
	* LAYOUTS FOR THIS FIELD
	*/

	
}