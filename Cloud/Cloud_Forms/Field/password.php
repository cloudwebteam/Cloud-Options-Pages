<?php 
// Prevent loading this file directly
defined( 'Cloud_ABS' ) || exit;

class Cloud_Field_password extends Cloud_Field {

	public static function create_field( $args ){
		$field = new self( $args ); 
	}

	protected function get_field_html( ){

		$error = isset( $this->spec['validation_error'] ) ? $this->spec['validation_error'] : false;
		if ( $error ){
			$error_messages = $error;
			$password = false; 
		} else {
			$error_messages = $this->spec['error'] ; 
			$password = isset( $this->info['value']['password'] ) ? $this->info['value']['password'] : false; 		
		}		
		if ( empty( $error_messages['password'] ) ){
			$error_messages['password'] = 'Improper password format'; 
		} 
		if ( empty( $error_messages['confirm'] ) ){
			$error_messages['confirm'] = 'Fields do not match.'; 
		} 		
		$field = '<div class="password-container">' ; 
		$field .= '<label for="'.$this->info['id'] . '">'. $this->spec['password_label'] .'</label>' ; 
		$field .= '<input class="password-field" type="password" id="' . $this->info['id'] . '" name="'.$this->info['name'] . '[password]" size="'.$this->spec['size'].'" value="' . $password . '" />';	
		$visible_class = isset( $error['password'] ) ? 'visible' : false ;		
		$field .= '<div class="cloud-error '.$visible_class.'"><span class="error-inner">'.$error_messages['password'].'</span></div>' ; 
		$field .= '</div>'; 
		$field .= '<div class="password-confirm-container">' ; 		
		$field .= '<label for="'.$this->info['id'] . '">'. $this->spec['confirm_label'] .'</label>' ; 
		$field .= '<input class="password-confirm-field" type="password" id="' . $this->info['id'] . '-confirm" name="'.$this->info['name'] . '[confirm]" size="'.$this->spec['size'].'" value="' . $password . '" />';	
		$visible_class = isset( $error['confirm'] ) ? 'visible' : false ;		
		$field .= '<div class="cloud-error '.$visible_class.'"><span class="error-inner">'.$error_messages['confirm'].'</span></div>' ; 
		$field .= '</div>'; 
		
		return $field;
	}
	
   /**
	* LAYOUTS FOR THIS FIELD
	*/	
}