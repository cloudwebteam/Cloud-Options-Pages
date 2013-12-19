<?php 
// Prevent loading this file directly
defined( 'Cloud_ABS' ) || exit;

class Cloud_Field_select extends Cloud_Field {
	public static function create_field( $args ){
		$field = new self( $args ); 
	}

	protected function get_field_html( ){
		$this->multiple = isset( $this->spec['multiple'] ) ? $this->spec['multiple'] : false ; 
		$this->options_list = $this->get_options_list( );
		if ( $this->multiple ){
			$multiple = ' multiple="multiple"' ; 
			$this->info['name'] = $this->info['name'] . '[]'; 
			$this->spec['first_option'] = false;
		} else {
			$multiple = '';
		}
		if ( $this->options_list ){
			$value = is_array( $this->info['value'] ) ? implode( ', ', $this->info['value'] ) : '';
			$field = '<select id="'. $this->info['id'] . '" name="'.$this->info['name'] . '" '.$multiple.' value="' . $value . '" '.$this->info['disabled'] .' >'. $this->options_list.'</select>';
		} else {
			$field = 'No options available.';
		}
		return $field ;
	}
	private function get_options_list( ){
		$choices = $this->get_select_choices();
		$html = '';
		if ( $choices ){
			foreach( $choices as $value => $choice){
				$selected = $choice['selected'] ? 'selected' : '';
				$html .= '<option '.$selected.' value="'.$value.'">'.$choice['title'].'</option>';
			}
		}
		return $html;
	}
	
   /**
	* LAYOUTS FOR THIS FIELD
	*/	

	
}