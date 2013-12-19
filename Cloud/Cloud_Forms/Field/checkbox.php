<?php 
// Prevent loading this file directly
defined( 'Cloud_ABS' ) || exit;

class Cloud_Field_checkbox extends Cloud_Field {

	public static function create_field( $args ){
		$field = new self( $args ); 
	}

	protected function get_field_html( ){
		if ( !empty( $this->spec['options'] ) ){
			$this->multiple = true;
			$options = $this->get_select_choices(); 
			$fields = '<div class="multiple-options">' ;
			foreach( $options as $option_key => $option_info ){
				$title = $option_info['title'];
				if ( is_array( $title )){
					$option_title = $title['title'] ; 
					$option_value = $title['checkbox_value'];
				} else {
					$option_title = $title ; 
					$option_value = $option_key ;
				}
				$option_id = $this->info['id'] .$option_key ; 
				$option_name = $this->info['name'].'['.$option_key.']' ;
				
				$checked = $option_info['selected']  ? 'checked' : '';				
				$field = '<input type="checkbox" id="'.$option_id. '" name="'.$option_name . '" value="'.$option_value.'"' . $checked . ' '.$this->info['disabled'] .' />';	
				$fields .= '<label for="'.$option_id.'">'.$field. $option_title.'</label>' ;

			}
			$fields .= '</div>';
			$field = $fields ;
		} else {
			$checked = $this->info['value'] == $this->spec['checkbox_value'] ? 'checked' : '';
			$field = '<input type="checkbox" id="'. $this->info['id'] . '" name="'.$this->info['name'] . '" value="'.$this->spec['checkbox_value'].'"' . $checked . ' '.$this->info['disabled'] .' />';	
		}
		return $field;
	}
	protected function get_field(){
	
	}
	
   /**
	* LAYOUTS FOR THIS FIELD
	*/
}