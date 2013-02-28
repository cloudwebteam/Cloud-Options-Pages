<?php 
// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

class Cloud_Field_toggle extends Cloud_Field {

	public static function create_field( $args ){
		$field_type = __CLASS__;
		$field = new $field_type( $args ); 
	}

	protected function __construct( $args ){		
		parent::__construct( __CLASS__, $args ); 	
	}

	protected function get_field_html( ){
		$checked = $this->info['value'] == $args['info']['checkbox_value'] ? ' checked ' : '';
		$fields_to_show = $this->args['info']['show'] ? $this->args['info']['show'] : array() ; 
		$fields_to_hide = $this->args['info']['hide'] ? $this->args['info']['hide'] : array() ; 
		$data = '';
		if ( $fields_to_show ){
			$data .= ' data-show=\''.json_encode($fields_to_show).'\' ' ;
		}
		if ( $fields_to_hide ){
			$data .= ' data-hide=\''.json_encode($fields_to_hide).'\' ' ;
		}		
		$field = '<input type="checkbox" id="'.$this->info['prefix'] . $this->info['id'] . '" name="'.$this->info['name'] . '" value="'.$args['info']['checkbox_value'].'"' . $checked . $data . '/>';	
		return $field;
	}	
   /**
	* LAYOUTS FOR THIS FIELD
	*/
}