<?php 
// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

class Cloud_Field_checkbox extends Cloud_Field {

	public static function create_field( $args ){
		$field_type = __CLASS__;
		$field = new $field_type( $args ); 
	}

	protected function __construct( $args ){		
		parent::__construct( __CLASS__, $args ); 	
	}

	protected function get_field_html( $args ){
		$multiple = $args['info']['multiple'] ; 
		if ( $multiple ){
			$options = $args['info']['options'] ; 
			$fields = '<div class="multiple-options">' ;
			foreach( $options as $option_key => $option_info ){
				if ( is_array( $option_info )){
					$option_title = $option_info['title'] ; 
					$option_value = $option_info['checkbox_value'];
				} else {
					$option_title = $option_info ; 
					$option_value = $option_key ;
				}
				$option_id = $this->info['prefix'] . $this->info['id'] .$option_key ; 
				$option_name = $this->info['name'].'['.$option_key.']' ;
				
				$checked = isset( $this->info['value'][ $option_key ] ) &&   $this->info['value'][ $option_key ] == $option_value  ? 'checked' : '';				
				$field = '<input type="checkbox" id="'.$option_id. '" name="'.$option_name . '" value="'.$option_value.'"' . $checked . '/>';	
				$fields .= '<label for="'.$option_id.'">'.$field. $option_title.'</label>' ;

			}
			$fields .= '</div>';
			$field = $fields ;
		} else {
			$checked = $this->info['value'] == $args['info']['checkbox_value'] ? 'checked' : '';
			$field = '<input type="checkbox" id="'.$this->info['prefix'] . $this->info['id'] . '" name="'.$this->info['name'] . '" value="'.$args['info']['checkbox_value'].'"' . $checked . '/>';	
		}
		return $field;
	}
	protected function get_field(){
	
	}
	
   /**
	* LAYOUTS FOR THIS FIELD
	*/
}