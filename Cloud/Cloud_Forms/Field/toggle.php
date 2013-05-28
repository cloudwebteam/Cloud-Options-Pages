<?php 
// Prevent loading this file directly
defined( 'Cloud_ABS' ) || exit;

class Cloud_Field_toggle extends Cloud_Field {

	public static function create_field( $args ){
		$field = new self( $args ); 
	}

	protected function get_field_html( ){
		$checked = $this->info['value'] == $this->spec['checkbox_value'] ? ' checked ' : '';
		$fields_to_show = $this->spec['show'] ? $this->spec['show'] : array() ; 
		$fields_to_hide = $this->spec['hide'] ? $this->spec['hide'] : array() ; 
		$data = '';
		if ( $fields_to_show ){
			$data .= ' data-show=\''.json_encode($fields_to_show).'\' ' ;
		}
		if ( $fields_to_hide ){
			$data .= ' data-hide=\''.json_encode($fields_to_hide).'\' ' ;
		}		
		$field = '<input type="checkbox" id="'.$this->info['id'] . '" name="'.$this->info['name'] . '" value="'.$this->spec['checkbox_value'].'"' . $checked . $data . '/>';	
		return $field;
	}	
   /**
	* LAYOUTS FOR THIS FIELD
	*/
}