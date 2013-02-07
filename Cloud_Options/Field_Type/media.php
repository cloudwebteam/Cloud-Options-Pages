<?php 
// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

class Cloud_Field_media extends Field_Type {
	protected $info ;
	protected $size = 45; 
	protected $field ;
	protected $label ;
	protected $url_button ; 

	public static function create_field( $args ){
		$field_type = __CLASS__;	
		$field = new $field_type( $args ); 
	}
	protected function __construct( $args ){		
		parent::__construct( __CLASS__, $args ); 	
	}	

	protected function get_field_html( $args ){
		$data_to_display = self::figure_out_data_to_display( $args['info'] ); 
	
		$url_button = '<div class="selector"><input class="upload_button btn btn-mini" type="button" name="upload_button" value="Find Media" /><span class="storing">Display: '.$data_to_display.'</span></div>';
		$this->size = isset( $args['info']['size'] ) ? $args['info']['size'] : $this->size; 	

		if ( $args['info']['use_image'] ){
			$image = '<span class="image">'.$this->get_image().'</span>';	
			$displayed_value = '' ;
		} else {
			$image = '' ;	
			$displayed_value = '<span class="value">'.$this->info['value'].'</span>' ;
		}
		
		$field = '<input data-to_display="'.$data_to_display.'" class="url_field" type="hidden" id="'.$this->info['prefix'] . $this->info['id'] . '" name="'.$this->info['name'] . '" size="'.$this->size.'" type="text" value=\'' . $this->info['value'] . '\' />';
		$field .= $displayed_value ;
		
		return $url_button . $field . $displayed_value . $image ;
	}
	protected static function figure_out_data_to_display( $info ){
		if ( isset( $info['get'] ) ){

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
	}
	public function enqueue_field_scripts_and_styles(){
		// if they exist, enqueues css and js files with this fields name
		parent::register_scripts_and_styles( __CLASS__ ); 
		wp_enqueue_media(); 
	}
	
	private function get_image(){
		
		if ( isset( $this->info['value'] ) && $this->info['value'] !== '' ){
			if( is_numeric( $this->info['value'] ) ){
				$attachment_id = $this->info['value'] ; 
				$image_info = wp_get_attachment_image_src( $attachment_id, 'thumb', true ) ; 
				$url = $image_info[0] ;
			} else if ( strpos( $this->info['value'], '<img' ) == 0 ){
				preg_match( '/http:[\/\/[\w:\.-]+/', $this->info['value'], $matches ) ;
				$url = isset( $matches[ 0 ] ) ? $matches[ 0 ] : '' ; 
			} else {
				$url = $this->info['value'] ; 
			}
			if ( $url ){
				return '<img class="preview-image img-polaroid" src="'.$url.'" title="'.$url.'" />';			
			}
		}

		return '<img class="hidden preview-image img-polaroid" title="No image" />';	
	}	
	public function get_option( $attachment_id, $spec ){
		$data_to_display = self::figure_out_data_to_display( $spec ); 
		$value = '' ;
		switch ( $data_to_display ){
			case 'url' : 
				$size = isset( $spec['image_size'] ) ? $spec['image_size'] : 'full' ; 
				$image_info = wp_get_attachment_image_src( $attachment_id, $size ) ;
				$value = $image_info[0] ; // returns url
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