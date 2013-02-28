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

	protected function get_field_html( $args ){
		$checked = $this->info['value'] == $args['info']['checkbox_value'] ? ' checked ' : '';
		 
		$options = $args['info']['options'] ;
		$fields_to_show = $this->args['info']['show'] ? $this->args['info']['show'] : array() ; 
		$fields_to_hide = $this->args['info']['hide'] ? $this->args['info']['hide'] : array() ; 		
		if ( ! is_array( $options ) ){
			$data = '';
			if ( $fields_to_show ){
				$data .= ' data-show=\''.json_encode($fields_to_show).'\' ' ;
			}
			if ( $fields_to_hide ){
				$data .= ' data-hide=\''.json_encode($fields_to_hide).'\' ' ;
			}		
			$field = '<input type="checkbox" id="'.$this->info['prefix'] . $this->info['id'] . '" name="'.$this->info['name'] . '" value="'.$args['info']['checkbox_value'].'"' . $checked . $data . '/>';	
		} else {
			ob_start();
		
			$field_type = in_array( $args['info']['field'] , array( 'checkbox', 'radio', 'select' ) ) ? $args['info']['field'] : 'checkbox' ;
			
			if ( $field_type == 'select' ){ ?>
			<select name="<?php echo $this->info['name']; ?>" id="<?php echo $this->info['id']; ?>" value="<?php echo $this->info['value']; ?>">
			<?php
				foreach ( $options as $slug => $option ){
					$option_value = is_string( $slug ) ? ' value="'. $slug .'"' : '';  
					$selected = $this->info['value'] === $option_value ? 'selected="selected"' : '' ; 
					$data = '';
					if ( isset( $fields_to_show[ $slug ] ) ){
						$data .= ' data-show=\''.json_encode($fields_to_show[ $slug ]).'\' ' ;
					}
					if ( isset( $fields_to_hide[ $slug ] ) ){
						$data .= ' data-hide=\''.json_encode($fields_to_hide[ $slug ]).'\' ' ;
					}												
					?>
					<option class="toggle-option" <?php echo $data; ?><?php echo $selected; ?><?php echo $option_value; ?> ><?php echo $option ; ?></option>
					<?php
				}		
			?>
			</select>
			<?php		
			} else {
				foreach ( $options as $slug => $option ){
					$option_value = is_string( $slug ) ? $slug : $option ; 
					$checked = $this->info['value'] === $option_value ? 'checked="checked"' : '' ; 		
					$option_id = $this->info['id'] . '-' . $slug ; 
					$data = '';
					if ( isset( $fields_to_show[ $slug ] ) ){
						$data .= ' data-show=\''.json_encode($fields_to_show[ $slug ]).'\' ' ;
					}
					if ( isset( $fields_to_hide[ $slug ] ) ){
						$data .= ' data-hide=\''.json_encode($fields_to_hide[ $slug ]).'\' ' ;
					}						
					?>
					<label class="toggle-option" for="<?php echo $option_id; ?>"><input id="<?php echo $option_id ; ?>" type="<?php echo $field_type; ?>" value="<?php echo $option_value; ?>" name="<?php echo $this->info['name'] ; ?>" <?php echo $data; ?> <?php echo $checked; ?> ><?php echo $option; ?></label>
					<?php
				}							
			}	
			$field = ob_get_clean();
				
		}
		return $field;
	}	
   /**
	* LAYOUTS FOR THIS FIELD
	*/
}