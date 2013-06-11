<?php 
require '../Validator.class.php' ;
ini_set('display_errors',1);
error_reporting(E_ALL);
$action = isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : false; 
switch( $action ){
	case 'form_validate' : 
		$form_id = isset( $_REQUEST['form_id'] ) ? $_REQUEST['form_id'] : false; 
		$form_data = isset( $_REQUEST['form_data'] ) ? $_REQUEST['form_data']  : false; 
		parse_str( $form_data, $form_data );
		$form_spec = isset( $_REQUEST['form_spec'] ) ? $_REQUEST['form_spec'] : false ;
		
		function convert_true_false_to_booleans( &$item, $key ){
			if ( $item === 'true' ){
				$item = true; 
			} 
			if ( $item === 'false' ){
				$item = false; 
			}
		}
		array_walk_recursive( $form_spec, 'convert_true_false_to_booleans' ); 
		
		if ( $form_id ){
			$validation_results = Validator::validate( $form_data, $form_spec ); 
			$response = $validation_results; 
		
		} else {
			$response = 'No form by that ID';
		}	
		echo json_encode($response); 
		die; 
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