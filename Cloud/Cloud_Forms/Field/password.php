<?php 
// Prevent loading this file directly
defined( 'Cloud_ABS' ) || exit;

class Cloud_Field_password extends Cloud_Field {

	public static function create_field( $args ){
		$field = new self( $args ); 
	}

	protected function get_field_html( ){

		$error = isset( $this->spec['validation_error'] ) ? $this->spec['validation_error'] : array();
		if ( $error ){
			$error_messages = $error;
			$password = isset( $this->info['value']['password'] ) ? $this->info['value']['password'] : false; 	
			$confirm = false;
				
		} else {
			$error_messages = $this->spec['error'] ; 
			$password = isset( $this->info['value']['password'] ) ? $this->info['value']['password'] : false; 		
			$confirm = isset( $this->info['value']['confirm'] ) ? $this->info['value']['confirm'] : false; 		
		}		
		if ( empty( $error_messages['password'] ) ){
			$error_messages['password'] = 'Improper password format'; 
		} 
		if ( empty( $error_messages['confirm'] ) ){
			$error_messages['confirm'] = 'Fields do not match.'; 
		} 		
		if ( $this->spec['confirm'] ){
			$field = '<div class="password-container">' ; 
			$field .= '<label for="'.$this->info['id'] . '">'. $this->spec['password_label'] .'</label>' ; 
			$field .= '<input class="password-field" type="password" id="' . $this->info['id'] . '" name="'.$this->info['name'] . '[password]" size="'.$this->spec['size'].'" value="' . $password . '" '.$this->info['disabled'] .' />';	
			$field .= '</div>'; 		
			$field .= '<div class="password-confirm-container">' ; 		
			$field .= '<label for="'.$this->info['id'] . '">'. $this->spec['confirm_label'] .'</label>' ; 
			$field .= '<input class="password-confirm-field special-field" type="password" id="' . $this->info['id'] . '-confirm" name="'.$this->info['name'] . '[confirm]" size="'.$this->spec['size'].'" value="' . $confirm . '" '.$this->info['disabled'] .' />';	
			$active_class = in_array( 'confirm-empty', $error ) ? 'active' : false;
			$field .= '<div class="error-message-special '.$active_class. '" data-validation="confirm_empty">'. $this->spec['confirm_error']['empty'].'</div>'; 			
			$active_class = in_array( 'confirm-error', $error ) ? 'active' : false;
			$field .= '<div class="error-message-special '.$active_class.'" data-validation="confirm_error">'. $this->spec['confirm_error']['error'].'</div>'; 			
			$field .= '</div>'; 
		} else {
			$field = '<input class="password-field" type="password" id="' . $this->info['id'] . '" name="'.$this->info['name'] . '" size="'.$this->spec['size'].'" value="' . $password . '" '.$this->info['disabled'] .' />';	
		
		}
		
		return $field;
	}
   /**
	* LAYOUTS FOR THIS FIELD
	*/	
}