<?php 
// Prevent loading this file directly
defined( 'Cloud_ABS' ) || exit;

class Cloud_Field_media extends Cloud_Field {

    public static function create_field( $args ){
		$field = new self( $args ); 
	}

	protected function get_field_html( ){
		$data_to_display = self::property_to_get( $this->spec ); 
	
		$url_button = '<div class="selector">';
		$url_button .= '<input class="upload_button btn btn-mini" type="button" name="upload_button" value="Find Media" '.$this->info['disabled'] .' />';
		$url_button .= $this->spec['code_link'] ? '<span class="storing">Retrieves: '.$data_to_display.'</span>' : '' ;
		$url_button .= '</div>' ;
		

		if ( $this->spec['use_image'] ){
			$image = '<span class="image">'.$this->get_image().'<a class="remove-media">X</a></span>';	
			$displayed_value = '' ;
		} else {
			$image = '' ;	
			$displayed_value = '<span class="value">'.$this->info['value'].'</span>' ;
		}
		
		$field = '<input data-to_display="'.$data_to_display.'" class="url_field" type="hidden" id="'. $this->info['id'] . '" name="'.$this->info['name'] . '" size="'.$this->spec['size'].'" type="text" value=\'' . $this->info['value'] . '\' />';
		$field .= $displayed_value ;
		
		return $url_button . $field . $displayed_value . $image ;
	}
	protected static function property_to_get( $info ){

		switch ( $info['get'] ){
			case 'ID' : 
			case 'id' :
				return 'ID' ;
				break; 
			case 'image' : 
			case 'img' :
				return 'image' ;
				break; 
			case 'url' : 
			default: 
				return 'url' ;
				break; 
		} 
	}
	public static function enqueue_scripts_and_styles( $field_type = false ){
		// if they exist, enqueues css and js files with this fields name
		parent::enqueue_scripts_and_styles( $field_type ); 				
		
		wp_enqueue_media(); 
	}
	
	private function get_image(){
		
		if ( isset( $this->info['value'] ) && $this->info['value'] !== '' ){
			$value = json_decode( $this->info['value'], true ) ;
			if( $value && is_numeric( $value['media'] ) ){
				$attachment_id = $value['media'] ;
				$attachment = get_post( $attachment_id );
				if ( $attachment->post_mime_type === "image/svg+xml" ){
					$url =  wp_get_attachment_url( $attachment_id ) ;
				} else {
					$image_info = wp_get_attachment_image_src( $attachment_id, 'medium', true ) ; 
					$url = $image_info[0] ;
				}
				if ( $url ){
					return '<img class="preview-image img-polaroid" src="'.$url.'" title="'.$url.'" />';			
				}
			} 
		}

		return '<img class="hidden preview-image img-polaroid" title="No image" />';	
	}	
	public static function get_option( $attachment_id, $spec ){
		$prop_to_get = self::property_to_get( $spec ); 
		$value = '' ;
		switch ( $prop_to_get ){
			case 'url' : 
				$size = isset( $spec['image_size'] ) ? $spec['image_size'] : 'full' ; 
				$image_info = wp_get_attachment_image_src( $attachment_id, $size ) ;
				if ( $image_info ){
					$value = $image_info[0] ; // returns url
				} else {
					$value = wp_get_attachment_url( $attachment_id );
				}
				break; 
			case 'image' : 
				$size = isset( $spec['image_size'] ) ? $spec['image_size'] : 'full' ; 
				$value  = wp_get_attachment_image( $attachment_id, $size ) ;
				break; 
			case 'ID' :
				$value = $attachment_id ; 
				break ;				
		}
		return $value ;
	}
  
  
   /**
	* LAYOUTS FOR THIS FIELD
	*/

	
}