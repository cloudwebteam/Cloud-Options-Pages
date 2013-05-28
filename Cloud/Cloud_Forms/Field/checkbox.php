<?php 
// Prevent loading this file directly
defined( 'Cloud_ABS' ) || exit;

class Cloud_Field_checkbox extends Cloud_Field {

	public static function create_field( $args ){
		$field = new self( $args ); 
	}

	protected function get_field_html( ){
		$options = $this->spec['options'] ; 
		if ( $options ){
			$fields = '<div class="multiple-options">' ;

			foreach( $options as $option_key => $option_info ){
				if ( is_array( $option_info )){
					$option_title = $option_info['title'] ; 
					$option_value = $option_info['checkbox_value'];
				} else {
					$option_title = $option_info ; 
					$option_value = $option_key ;
				}
				$option_id = $this->info['id'] .$option_key ; 
				$option_name = $this->info['name'].'['.$option_key.']' ;
				
				$checked = isset( $this->info['value'][ $option_key ] ) &&   $this->info['value'][ $option_key ] == $option_value  ? 'checked' : '';				
				$field = '<input type="checkbox" id="'.$option_id. '" name="'.$option_name . '" value="'.$option_value.'"' . $checked . '/>';	
				$fields .= '<label for="'.$option_id.'">'.$field. $option_title.'</label>' ;

			}
			$fields .= '</div>';
			$field = $fields ;
		} else {
			$checked = $this->info['value'] == $this->spec['checkbox_value'] ? 'checked' : '';
			$field = '<input type="checkbox" id="'. $this->info['id'] . '" name="'.$this->info['name'] . '" value="'.$this->spec['checkbox_value'].'"' . $checked . '/>';	
		}
		return $field;
	}
	protected function get_field(){
	
	}
	
   /**
	* LAYOUTS FOR THIS FIELD
	*/
}