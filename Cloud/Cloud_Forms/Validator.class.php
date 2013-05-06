<?php class Validator {
	protected $array_hierarchy = array( 'sections', 'fields', 'subfields' ) ;
	protected $validation_spec = array() ;
	protected $errors = array() ;
	protected $success = true ;
	protected static $messages = array(
		'default' 	=> 'Error with input', 
		'email'		=> 'Invalid email',
		'number' 	=> 'Numbers only, please' , 
		'phone' 	=> 'Please enter a valid phone number',
		'pin'		=> 'Four numbers, please',
		'required' 	=> 'Required', 		
		'url'		=> 'Must be a full url ( http://... )',
		'zip'		=> 'Invalid ZIP code',
		'wtf'		=> 'Not registered in class, how here?'
	); 
	public static function validate( $form_submission_data = '' , $form_fields = '' ){
		$validation = new self( $form_submission_data, $form_fields ); 

		return array( 
			'success' => $validation->success,
			'updated_form_spec' => $validation->form_spec_with_errors ,
		); 
	}
	protected $excluded_fields = array(
		'form_id', 'submit', 'MAX_FILE_SIZE'
	);
	protected function __construct( $form_data, $form_spec ){		
		$this->form_spec = $form_spec ; 

		$this->form_data =  $this->remove_excluded_fields( $form_data ) ;
		$this->form_spec_with_errors = $this->add_errors_to_form_spec(); 	
	}
	protected function remove_excluded_fields( $form_data ){
		foreach( $this->excluded_fields as $field_name ){
			if ( isset( $form_data[ $field_name ] ) ){
				unset( $form_data[ $field_name ] ); 
			}
		}
		return $form_data; 
	}
	protected function add_errors_to_form_spec(){

		$starting_array_level = array_shift( $this->array_hierarchy ) ;
		while( ! isset( $this->form_spec[ $starting_array_level ] ) && sizeof( $this->array_hierarchy ) > 0 ){
			$starting_array_level = array_shift( $this->array_hierarchy ) ;
		}	
			
		$validation_spec = $this->form_spec[ $starting_array_level ] ;
		foreach( $validation_spec as $slug => $spec ){			

			$post_data = isset( $this->form_data[ $slug ] ) ? $this->form_data[ $slug ] : array() ;
			$validation_spec[ $slug ] = $this->get_field_validation_spec( $post_data , &$spec, $this->array_hierarchy  ); 			
		}

		
		return $validation_spec ; 		
	}
	protected function get_field_validation_spec( $post_data, &$spec, $array_hierarchy, $errors = array() ){
		$array_level = array_shift( $array_hierarchy );	
		if ( is_array( $post_data ) ){
			$fields_to_check = isset( $spec[ $array_level ] ) ? $spec[ $array_level ] : array() ;
			foreach( $post_data as $slug => $slug_post_data ){

				if ( ! is_numeric( $slug ) ){
					if ( isset( $spec[ $array_level ][ $slug ] ) ){
						$child_spec = $spec[ $array_level ][ $slug ] ;
						unset( $fields_to_check[ $slug ] );
						$spec[ $array_level ][ $slug ] = $this->get_field_validation_spec( $slug_post_data, &$child_spec, $array_hierarchy, $errors ) ; 
					} 
				} else {
					// is group 
					if ( is_array( $slug_post_data ) ){
						foreach( $slug_post_data as $subfield_slug => $subfield_value ){
							if ( isset( $spec[ $array_level ][ $subfield_slug ] ) ){
								$field_spec = $spec[ $array_level ][ $subfield_slug ] ;
								unset( $fields_to_check[ $subfield_slug ] );
								if ( $field_error = $this->validate_field( $field_spec, $subfield_value ) ){
									$spec[ $array_level ][ $subfield_slug ][$slug]['validation_error'] = $field_error ;
									$this->success = false ;
								}
							} 
						}
					// is simple cloneable
					} else {
						if ( $field_error = $this->validate_field( $spec, $slug_post_data ) ){
							$spec['validation_error'][$slug] = $field_error ;
							$this->success = false ;
						}								
					}
				}
			}			
			foreach( $fields_to_check as $subfield_slug => $subfield_spec ){
				// no value was submitted 
				$subfield_value = '' ;
				if ( $field_error = $this->validate_field( $subfield_spec, $subfield_value ) ){

					$spec[ $array_level ][ $subfield_slug ]['validation_error'] = $field_error ;
					$this->success = false ;
				}
			}
		} else {
			if ( $field_error = $this->validate_field( $spec, $post_data ) ){
				$spec['validation_error'] = $field_error ;
				$this->success = false ;
			}
		
		}
		return $spec; 
	}
	protected function validate_field( $field_spec , $field_value = '' ){
		if ( isset( $field_spec['required'] ) && $field_spec['required'] && ! $this->value_has_been_input( $field_value ) ){
			return is_string( $field_spec['required'] ) ? $field_spec['required'] : self::$messages['required'] ; 
		} else if ( $this->value_has_been_input( $field_value ) ){
			if ( isset( $field_spec['validate'] ) && is_callable( array( $this, $field_spec['validate'] ) ) ){
				// returns the validator-generated error, if there is an error
				$validator_error = $this->{ $field_spec['validate'] }( $field_value ); 
				if ( $validator_error ){
					// if there is a custom error message specified, use that, otherwise the normal validator one.					
					return !empty( $field_spec['error'] ) ? $field_spec['error'] : self::get_error_message( $field_spec['validate'] ) ; 
				}
			}		
		}
		return false ;
	}
	protected function get_error_message( $validation_type ){
		if ( isset( self::$messages[$validation_type] ) ) { 
			return self::$messages[$validation_type] ; 
		} else {
			return self::$messages['default'];
		}
	}
	//checks both arrays and strings
	protected function value_has_been_input( $field_value = null ){
		if ( $field_value !== null && $field_value !== '' ){
			if ( is_array( $field_value ) && sizeof( $field_value ) > 0 ){
				foreach( $field_value as $key => $value){
				    if( ! empty($value) ){
					    return true; 
				    }
				}
			} else if ( !$field_value ){
				return false; 
			} else {
				return true; 
			}
		} else {
			return false; 
		}
	}
	protected function prepared_to_save(){
		foreach( $this->form_data as $col_name => $col_value ){
			// phone number preparation
			if ( is_array( $col_value ) ){
				$this->form_data[$col_name] = implode('-', $col_value);
			}
		}
		return $this->form_data ;
	}

	
/***====================================================================================================================================
		VALIDATION METHODS 
	==================================================================================================================================== ***/	
	protected function email( $field_value = '' ){
		if ( ! filter_var($field_value, FILTER_VALIDATE_EMAIL) ) {
			return true ;
		} else {
			return false ;
		}
	}	
	
	protected function number( $field_value = '' ){
		if ( !is_numeric( $field_value ) ){
			return true ;
		} else {
			return false ;
		}
	}	
	protected function password( $field_value = '' ){
		if ( strlen( $field_value ) < 6 || ! preg_match( '/\d/', $field_value )) {
			return true ;
		} else {
			return false ;
		}
	}	
	protected function phone( $field_value = '' ){
		if ( is_array( $field_value ) ){
			if ( preg_match( '/\d{3}/', $field_value[0] ) && preg_match( '/\d{3}/', $field_value[1] ) &&  preg_match( '/\d{4}/', $field_value[2] ) ){
				return false; 
			} else {
				return true; 
			}
		} else {
			if ( strlen( $field_value ) < 9 || ! preg_match( '/^[\d\.\-\(\)x]+$/' , $field_value ) ){
				return true;
			}
		}	
		return false; 
	}
	protected function pin( $field_value = '' ){

		if ( ! preg_match( '/^\d{4}$/', $field_value ) ){	
			return true ;
		} else {
			return false ;
		}
		
	}	

	protected function url( $field_value = '' ){
		if ( ! preg_match( '/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/', $field_value) ){
			return true ;
		} else { 
			return false ;
		}
	}
	protected function zip( $field_value = '' ){
		if ( ! preg_match( '/^[\d]{5}$/', $field_value) ){
			return true ;
		} else { 
			return false ;
		}
	}	
}