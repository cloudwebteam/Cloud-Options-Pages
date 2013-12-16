<?php 
// Prevent loading this file directly
defined( 'Cloud_ABS' ) || exit;

class Cloud_Field_radio extends Cloud_Field {

	public static function create_field( $args ){
		$field = new self( $args ); 
	}

	protected function get_field_html( ){
		$field = '' ; 
		foreach( $this->spec['options'] as $value => $text ){
			$checked = $this->info['value'] == $value ? ' checked ' : '';		
			$field .= '<div class="radio-group cf">'; 
			$field .= '<input type="radio" id="'.$this->info['id'] . '-'.$value.'" name="'.$this->info['name'] . '" value="'.$value.'"' . $checked .' />';		
			$field .= '<label for="'.$this->info['id'] . '-' . $value .'">'.$text.'</label>' ; 
			$field .= '</div>'; 
		}
		return $field; 
	}
	
   /**
	* LAYOUTS FOR THIS FIELD
	*/	
}

