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
		$data_to_retrieve = $this->figure_out_data_to_retrieve( $args ); 
	
		$this->url_button = '<input class="upload_button btn btn-mini" type="button" name="upload_button" value="Find Media" /><span class="storing">Storing as: '.$data_to_retrieve.'</span>';
		$this->size = isset( $args['info']['size'] ) ? $args['info']['size'] : $this->size; 	

		if ( $args['info']['use_image'] ){
			$this->image = $this->get_image();	
			$input_type = 'hidden' ;
		} else {
			$this->image = '';
			$input_type = 'text' ;
		}
		
		
		$field = '<input data-to_insert="'.$data_to_retrieve.'" class="url_field" type="'.$input_type.'" id="'.$this->info['prefix'] . $this->info['id'] . '" name="'.$this->info['name'] . '" size="'.$this->size.'" type="text" value="' . $this->info['value'] . '" />';
		
		return $this->url_button .$field . $this->image;
	}
	protected function figure_out_data_to_retrieve( $args ){
		if ( isset( $args['info']['get'] ) ){

			switch ( $args['info']['get'] ){
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
		wp_enqueue_script('media-upload');
		wp_enqueue_script('thickbox');	
		
		wp_enqueue_style('thickbox');
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
  
  
  
   /**
	* LAYOUTS FOR THIS FIELD
	*/

	
}