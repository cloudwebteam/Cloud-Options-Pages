<?php class Validator {
	protected $array_hierarchy = array( 'sections', 'fields', 'subfields' ) ;
	protected $validation_spec = array() ;
	protected $errors = array() ;
	protected $success = true ;
	protected static $messages = array(
		'default' 	=> 'Error with input', 
		'email'		=> 'Invalid email',
		'file' 		=> 'File name seems invalid',
		'number' 	=> 'Numbers only, please' , 
		'phone' 	=> 'Please enter a valid phone number',
		'pin'		=> 'Four numbers, please',
		'required' 	=> 'Required', 		
		'regex' 	=> 'Improper format',
		'url'		=> 'Must be a full url ( http://... )',
		'zip'		=> 'Invalid ZIP code',
		'unknown' 	=> 'Unregistered validation type: ',
		'wtf'		=> 'Not registered in class, how here?'
	); 
	public static function get_error_message( $validation_type ){
		if ( isset( self::$messages[$validation_type] ) ) { 
			return self::$messages[$validation_type] ; 
		} else {
			return self::$messages['default'];
		}
	}
	public static function validate( $form_submission_data = '' , $form_fields = '' ){
		$validation = new self();
		
		$validation->validate_form( $form_submission_data, $form_fields ); 
				
		return array( 
			'success' => $validation->success,
			'form_data' => $validation->form_data,
			'to_save' => $validation->to_save,			
			'updated_form_spec' => $validation->form_spec_with_errors ,
		); 
	}
	public static function validate_value( $type, $value ){
		$validation = new self();
		$results = array();
		if ( is_array( $type ) ){
			foreach( $type as $validation_type ){
				if ( $has_error = $validation->call_validation_function( $validation_type, $value ) ){
				
					$results[] = $validation_type ;
				}
			}
		} else {
			if ( $has_error = $validation->call_validation_function( $type, $value ) ){
				$results[] = $type ;
			}		
		}
		return sizeof( $results ) > 0 ? $results : false; 
	}
	protected $excluded_fields = array(
		'form_id', 'submit', 'MAX_FILE_SIZE'
	);
	protected function validate_form( $form_data, $form_spec ){		

		$this->form_spec = $form_spec ; 

		$this->form_data =  $this->remove_excluded_fields( $form_data ) ;

		// ->to_save gets changed in the same loop that adds errors to form_spec
		$this->to_save = false ;
 
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
		$form_data = $this->form_data;

		foreach( $validation_spec as $slug => $spec ){			
			if ( isset( $spec['disabled'] ) && $spec['disabled'] ) {
				unset( $validation_spec[ $slug ] );
				continue; 
			}

			if ( isset( $this->form_data[ $slug ] ) ){
				$post_data = $form_data[$slug] ;
			} else {
				$post_data = array() ;
			}
			
			// both post data and spec are changed in this massive loop to prevent doing it twice
			$validation_spec[ $slug ] = $this->get_field_validation_spec( $post_data , $spec, $this->array_hierarchy  ); 			
			$form_data[$slug] = $post_data ? $post_data : null; 			
		}

		if ( $this->success ){
			$this->to_save = $form_data; 		
		}

		return $validation_spec ; 		
	}
	protected function get_field_validation_spec( &$post_data, &$spec, $array_hierarchy, $errors = array() ){
		$array_level = array_shift( $array_hierarchy );	
		if ( is_array( $post_data ) ){
			$fields_to_check = isset( $spec[ $array_level ] ) ? $spec[ $array_level ] : array() ;
			if ( ! $fields_to_check ){
				$errors = $this->check_if_password( $spec, $post_data );
				if ( $errors ){
					$spec['validation_error'] = $errors; 
					$this->success = false ;
				} else {
					$post_data = $this->prepare_to_save( $spec, $post_data ); 
				}						
			} else {
				foreach( $post_data as $slug => $slug_post_data ){
					if ( ! is_numeric( $slug ) ){
						if ( isset( $spec[ $array_level ][ $slug ] ) ){
							$field_spec =& $spec[ $array_level ][ $slug ] ;
							unset( $fields_to_check[ $slug ] );
							
							if ( $errors = $this->check_if_password( $field_spec, $slug_post_data ) ){
							
								if ( $errors ){
									$spec[ $array_level ][ $slug ]['validation_error'] = $errors; 
									$this->success = false ;
								} else {
									$post_data[$slug] = $this->prepare_to_save( $field_spec, $slug_post_data ); 
								}						
							} else {			
								$spec[ $array_level ][ $slug ] = $this->get_field_validation_spec( $slug_post_data, $field_spec, $array_hierarchy, $errors ) ; 
							}
						} 
					} else {
						// is group 
						
						if ( is_array( $slug_post_data ) ){
							foreach( $slug_post_data as $subfield_slug => $subfield_value ){
								$spec['validation_error'][ $slug ][ $subfield_slug ] = array();
								if ( isset( $spec[ $array_level ][ $subfield_slug ] ) ){
									$field_spec = $spec[ $array_level ][ $subfield_slug ] ;
									unset( $fields_to_check[ $subfield_slug ] );
									if ( $errors = $this->validate_field( $field_spec, $subfield_value ) ){
										$spec['validation_error'][ $slug ][ $subfield_slug ][] = $errors ; 
										$this->success = false ;
									} else {
										$slug_post_data[$subfield_slug] = $this->prepare_to_save( $field_spec, $subfield_value ); 
									}
								} 
							}
						// is simple cloneable
						} else {
							if ( $field_error = $this->validate_field( $spec, $slug_post_data ) ){
								$spec['validation_error'][$slug] = $field_error ;
								$this->success = false ;
							} else {
								foreach( $slug_post_data as $index => $clone_value ){
									$slug_post_data[$subfield_slug][ $index ] = $this->prepare_to_save( $field_spec, $clone_value );
								}	
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
			}
		} else {
			if ( $field_error = $this->validate_field( $spec, $post_data ) ){
				$spec['validation_error'] = $field_error ;
				$this->success = false ;
			} else {
				$post_data = $this->prepare_to_save( $spec, $post_data );
			}
		}
		return $spec; 
	}
	protected function check_if_password( $field_spec , $slug_post_data ){	
		if ( isset( $field_spec['type'] ) && $field_spec['type'] === 'password' ){
		// check fields that require special treatment....
		// @todo MAKE BETTER
			$errors = array();
			// check password
			if ( $field_spec['required'] && !$this->value_has_been_input( $slug_post_data['password'] ) ){
				$errors[] = 'required' ; 
			} else if ( $error = $this->call_validation_function( $field_spec['validate'], $slug_post_data['password'] ) ){
				$errors[] = $field_spec['validate'] ; 
			}  	
			
			// checks the confirmation field
			if ( $field_spec['confirm'] ){
			
				if(  $this->value_has_been_input( $slug_post_data['password'] ) ){									
					if ( ! $this->value_has_been_input( $slug_post_data['confirm'] ) ){
						$errors[] = 'confirm-empty'; 
					} else {													
						if ( $error = $this->call_validation_function( 'password_confirmation', $slug_post_data ) ){
							$errors[] = 'confirm-error'; 
						}
					}
				}
			}
			return $errors; 
		} else {
			return false; 
		}	
	}
	protected function validate_field( $field_spec , $field_value = '' ){
		$errors = array(); 
		if ( isset( $field_spec['required'] ) && $field_spec['required'] && ! $this->value_has_been_input( $field_value ) ){
			$errors[] = 'required'  ; 
		} else if ( $this->value_has_been_input( $field_value ) ){
			if ( isset( $field_spec['validate'] ) && $field_spec['validate'] ){
				if( is_array( $field_spec['validate'] ) ){
					foreach( $field_spec['validate'] as $validation_method ){
						if ( $has_error = $this->call_validation_function( $field_spec['validate'], $field_value ) ){	
							$errors[] = $field_spec['validate'] ;  
						} 
					}
				} else {
					if ( $has_error = $this->call_validation_function( $field_spec['validate'], $field_value ) ){ 	
						$errors[] = $field_spec['validate'] ; 					
					}
				}
			}
		}
		return $errors ;
	}
	protected function call_validation_function( $validation_type, $value ){
		if( is_callable( array( $this, $validation_type ) ) ){
			// returns the validator-generated error, if there is an error
			$validator_error = $this->{ $validation_type }( $value ); 
			if ( $validator_error ){
				// if there is a custom error message specified, use that, otherwise the normal validator one.					
				return true ; 
			} else {
				return false; 
			}				
		// did they provide a regex? Starts and ends with forward slash? ( ex. /[A-Z]+/ ); 
		} else {
			if( strpos( $validation_type, '/' ) === 0 && substr( $validation_type, -1 ) === '/' ){
				$pattern = $validation_type ; 
				$validator_error = $this->regex( $pattern, $value ); 
				return $validator_error; 
				if ( $validator_error ){
					return true; 
				} else {
					return false; 
				}
			}
		}
		return self::get_error_message( 'unknown' ) . ' ' . $validation_type ; 
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
	protected function prepare_to_save( $spec, $value ){
		switch( $spec['type'] ){
			case 'password' : 
				return $value['password']; 
			 	break;
		}
		return $value;
	}

	
/***====================================================================================================================================
		VALIDATION METHODS 
	==================================================================================================================================== ***/	
	protected function email( $field_value = '' ){
		if ( $field_value ){
			if ( ! filter_var($field_value, FILTER_VALIDATE_EMAIL) ) {
				return true ;
			}
		} 
		return false; 
	}	
	protected function file( $field_value = '' ){
		if ( $field_value ){
			if ( strlen( $field_value ) < 3 || ! strpos( $field_value, '.' )  ) {
				return true ;
			}
		} 
		return false; 
	}	
		
	protected function number( $field_value = '' ){
		if ( $field_value ){
			if ( !is_numeric( $field_value ) ){
				return true ;
			}
		}
		return false ;		
	}	
	protected function password_confirmation( $field_value = '' ){
	
		if ( is_array( $field_value ) && !empty( $field_value['password'] ) ){
			if ( (  $field_value['confirm'] !== $field_value['password'] ) ){
				return true;
			}
		}
		return false; 
	
	}	
	protected function phone( $field_value = '' ){
		if ( $field_value ){
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
		}
		return false; 
	}
	protected function pin( $field_value = '' ){
		if ( $field_value ){
			if ( ! preg_match( '/^\d{4}$/', $field_value ) ){	
				return true ;
			}
		}
		return false ;				
	}	
	protected function regex( $pattern , $field_value = '' ){	
		if ( $field_value ){
			if ( ! preg_match( $pattern, $field_value) ){			
				return true ;
			}
		}	
		return false ;
	}
	protected function url( $field_value = '' ){
		if ( $field_value ){
			if ( ! preg_match( '/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/', $field_value) ){
				return true ;
			}
		}
		return false ;		
	}
	protected function zip( $field_value = '' ){
		if ( $field_value ){
			if ( ! preg_match( '/^[\d]{5}$/', $field_value) ){
				return true ;
			}
		}
		return false ;		
	}	
}