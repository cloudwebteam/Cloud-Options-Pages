<?php 
// Prevent loading this file directly
defined( 'Cloud_ABS' ) || exit;

class Cloud_Field_toggle extends Cloud_Field {

	public static function create_field( $args ){
		$field = new self( $args ); 
	}

	protected function get_field_html( ){
		$this->spec['show'] = $this->spec['show'] ? $this->spec['show'] : array() ; 
		$this->spec['hide'] = $this->spec['hide'] ? $this->spec['hide'] : array() ; 

		if ( is_array( $this->spec['options'] ) ){
			switch( $this->spec['toggle_type'] ){
				case 'radio' : 
					$field = $this->get_multiple_radio( ); 
					break; 
				case 'select' : 
					$field = $this->get_multiple_select( ); 				
					break; 
				case 'checkbox' : 
				default: 
					$field = $this->get_multiple_checkbox( ); 
					break; 
			}
		} else {	
			$data = '';
			if ( $this->spec['show'] ){
				$data .= ' data-show=\''.json_encode($this->spec['show']).'\' ' ;
			}
			if ( $this->spec['hide'] ){
				$data .= ' data-hide=\''.json_encode($this->spec['hide']).'\' ' ;
			}
		
			$checked = $this->info['value'] == $this->spec['checkbox_value'] ? ' checked ' : '';
			$field = '<input type="checkbox" id="'.$this->info['id'] . '" name="'.$this->info['name'] . '" value="'.$this->spec['checkbox_value'].'"' . $checked . $data . ' '.$this->info['disabled'] .' />';		
		}
		return $field;
	}
	protected function get_show_data( $data ){
		if ( is_array( $data ) ){
			return ' data-show=\''.json_encode($data).'\'' ; 
		} else {
			return ' data-show='.json_encode($data) ; 		
		}
	}
	protected function get_hide_data( $data ){
		if ( is_array( $data ) ){
			return ' data-hide=\''.json_encode($data).'\'' ; 
		} else {
			return ' data-hide='.json_encode($data) ; 		
		}
	}	
	protected function get_multiple_radio( ){
		$field = '' ; 
		foreach( $this->spec['options'] as $value => $text ){
			$checked = $this->info['value'] == $value ? ' checked ' : '';		
			$data = isset( $this->spec['show'][ $value ] ) ? $this->get_show_data( $this->spec['show'][ $value ] ) : '';
			$data .= isset( $this->spec['hide'][ $value ] ) ? $this->get_hide_data( $this->spec['hide'][ $value ] ) : '';
			$field .= '<div class="radio-group">'; 
			$field .= '<input type="radio" id="'.$this->info['id'] . '-'.$value.'" name="'.$this->info['name'] . '" value="'.$value.'"' .$data. $checked .' />';		
			$field .= '<label for="'.$this->info['id'] . '-' . $value .'">'.$text.'</label>' ; 
			$field .= '</div>'; 
		}
		return $field; 
	}
	protected function get_multiple_select( ){
	
		$field = '<select id="'.$this->info['id'] . '" name="'.$this->info['name'] . '" value="'.$this->info['value'].'" '.$this->info['disabled'] .' />';		
		$i = 0; 
		foreach( $this->spec['options'] as $value => $text ){
			$data = isset( $this->spec['show'][ $value ] ) ? $this->get_show_data( $this->spec['show'][ $value ] ) : '';
			$data .= isset( $this->spec['hide'][ $value ] ) ? $this->get_hide_data( $this->spec['hide'][ $value ] ) : '';		
			$selected = $this->info['value'] == $value ? ' selected ' : '';		
			$field .= '<option id="'.$this->info['id'] . '-'.$i.'" value="'.$value.'"' . $selected .$data .' >'.$text.'</option>';		
			$i++; 
		}	
		$field .= "</select>";
		return $field; 
	}
	protected function get_multiple_checkbox( ){
		$field = '' ; 

		foreach( $this->spec['options'] as $value => $text ){
			$checked = is_array( $this->info['value'] ) && in_array( $value, $this->info['value'] ) ? ' checked ' : '';		
			$field .= '<div class="checkbox-group">'; 
			$field .= '<input type="checkbox" id="'.$this->info['id'] . '-'.$value.'" name="'.$this->info['name'] . '[]" value="'.$value.'"' . $checked .' />';		
			$field .= '<label for="'.$this->info['id'] . '-' . $value .'">'.$text.'</label>' ; 
			$field .= '</div>'; 
		}
		return $field; 	
	}		
   /**
	* LAYOUTS FOR THIS FIELD
	*/
}