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
		} else {
			$multiple = '';
		}
		if ( $this->options_list ){
			$field = '<select id="'. $this->info['id'] . '" name="'.$this->info['name'] . '" '.$multiple.' value="' . $this->info['value'] . '" '.$this->info['disabled'] .' >'. $this->options_list.'</select>';
		} else {
			$field = 'No options available.';
		}
		return $field ;
	}
	private function get_options_list( ){
		$html = '';
		if ( $this->spec['first_option'] ){
			if ( is_array( $this->spec['first_option'] ) ){
				$value = isset( $this->spec['first_option']['value'] ) ?  $this->spec['first_option']['value'] : false ;
				$text = isset( $this->spec['first_option']['text'] ) ?  $this->spec['first_option']['text'] : false ;
			} else {
				$value = false; 
				$text = $this->spec['first_option'] ; 
			}
			$html .= '<option value="'.$value.'">'.$text.'</option>' ; 
		}
		if ( $this->spec['use_query'] == true ){
			$query_args = wp_parse_args( array( 
				'numberposts' => -1
			), $args['info']['options'] ); 
			$posts = get_posts( $query_args );
			if ( sizeof( $posts ) > 0 ){
				if ( $this->multiple ){
					foreach( $posts as $post ){ 
						if ( is_array( $this->info['value'] ) && in_array( $post->ID, $this->info['value'] )){
							$selected = 'selected';								
						} else if ( is_array( $this->info['default'] ) && in_array( $post->ID, $this->info['default'] ) ) {
							$selected = 'selected';			
						} else if ( $this->info['default'] == $post->ID ){			
							$selected = 'selected';										
						} else {
							$selected = '';
						}					
						$html .= '<option '.$selected .' value="'.$post->ID.'">'.$post->post_title.'</option>' ; 
					}
				} else {
					foreach( $posts as $post ){ 
						if ( $this->info['value'] == $post->ID ){
							$selected = 'selected';
						} else if (  $this->info['default'] == $post->ID ){			
							$selected = 'selected';									
						} else {
							$selected = '';
						}					
						$html .= '<option '.$selected .' value="'.$post->ID.'">'.$post->post_title.'</option>' ; 
					}						
				}
			}
			return $html; 
		}
		if ( isset( $this->spec[ 'options' ] ) ){
			$options = $this->spec[ 'options' ] ; 

			if ( is_array( $options ) ){
				if ( sizeof( $options ) == 0 ){
					return false ;
				}
				if ( $this->multiple ){
					foreach( $options as $value => $option ){ 
						if ( is_array( $this->info['value'] ) && in_array( $value, $this->info['value'] )){
							$selected = 'selected';
						} else if ( is_array( $this->info['default'] ) && in_array( $value, $this->info['default'] ) ) {
							$selected = 'selected';			
						} else if ( $this->info['default'] == $value){			
							$selected = 'selected';									
						} else {
							$selected = '';
						}					
						$html .= '<option '.$selected .' value="'.$value.'">'.$option.'</option>' ; 
					}				
				} else { 					
					$is_associative_array = $this->is_assoc( $options ) ;
					$selected_found = false;					
					$selected = '';
					foreach( $options as $value => $option ){ 
						if ( ! $is_associative_array ){
							$value = $option;
						}
						if ( ! $selected_found ){
							if ( $this->info['value'] == $value ){
								$selected_found = true ;
								$selected = 'selected';
							} else if ( $this->info['default'] == $value ){			
								$selected = 'selected';
								$selected_found = true ;								
							}
						} else {
							$selected = '';
						}
						$html .= '<option '.$selected.' value="'.$value.'">'.$option.'</option>' ; 
					}
				}
			} else {
				if ( post_type_exists( $options ) ){
					$posts = get_posts( array(
						'numberposts' => -1, 
						'post_type'		=> $options
					) );
					if ( sizeof( $posts ) > 0 ){
						if ( $this->multiple ){
							foreach( $posts as $post ){ 
								if ( is_array( $this->info['value'] ) && in_array( $post->ID, $this->info['value'] )){
									$selected = 'selected';								
								} else if ( is_array( $this->info['default'] ) && in_array( $post->ID, $this->info['default'] ) ) {
									$selected = 'selected';			
								} else if ( $this->info['default'] == $post->ID ){			
									$selected = 'selected';										
								} else {
									$selected = '';
								}					
								$html .= '<option '.$selected .' value="'.$post->ID.'">'.$post->post_title.'</option>' ; 
							}
						} else {
							foreach( $posts as $post ){ 
								if ( $this->info['value'] == $post->ID ){
									$selected = 'selected';
								} else if (  $this->info['default'] == $post->ID ){			
									$selected = 'selected';									
								} else {
									$selected = '';
								}					
								$html .= '<option '.$selected .' value="'.$post->ID.'">'.$post->post_title.'</option>' ; 
							}						
						}
					}
				}
			}
		}
		return $html;
	}
	private function is_assoc($arr) {
	    return array_keys($arr) !== range(0, count($arr) - 1);
	}
	
   /**
	* LAYOUTS FOR THIS FIELD
	*/	

	
}