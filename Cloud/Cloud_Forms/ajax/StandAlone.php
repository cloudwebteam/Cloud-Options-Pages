<?php 
require '../Validator.class.php' ;
ini_set('display_errors',1);
error_reporting(E_ALL);
$action = isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : false; 
switch( $action ){
	case 'form_validate' :

		$form_id = isset( $_REQUEST['form_id'] ) ? $_REQUEST['form_id'] : false; 
		$form_data = isset( $_REQUEST['form_data'] ) ? $_REQUEST['form_data']  : false; 
			
		function convert_true_false_to_booleans( &$item, $key ){
			if ( $item === 'true' ){
				$item = true; 
			} 
			if ( $item === 'false' ){
				$item = false; 
			}
		}
		array_walk_recursive( $form_data, 'convert_true_false_to_booleans' ); 
		$response = array() ; 
			
		if ( $form_id && $form_data ){
			foreach( $form_data as $index => $field_data ){
			
				if ( $field_data['required'] && ! $field_data['value'] ){
					$response[ $field_data['name'] ] = 'required' ;
				} else {
					if ( $has_error = Validator::validate_value( $field_data['validation'], $field_data['value'] )  ){
						$response[ $field_data['name'] ] = $field_data['validation'] ; 
					}
				}
			}
		} else {
			$response = 'No form by that ID';
		}	
		if ( $response ){
			echo json_encode($response); 
		} else {
			echo json_encode( 'success' );
		}
		break; 
	case 'input_validate' : 
		$validation_type = isset( $_REQUEST['validation'] ) ? $_REQUEST['validation'] : false;
		$value = isset( $_REQUEST['value'] ) ? $_REQUEST['value'] : false; 
		if ( $results = Validator::validate_value( $validation_type, $value ) ){
			echo json_encode( $results ); 
			die; 
		} else {
			echo json_encode( 0 ); 
			die; 
		} 
		break; 
}