<?php 
// Prevent loading this file directly
defined( 'Cloud_ABS' ) || exit;

class Cloud_Field_map extends Cloud_Field {
	
	public static function create_field( $args ){
		$field = new self( $args ); 
	}

	protected function get_field_html( ){
		ob_start(); ?>
		<?php $value = $this->info['value'] ; 
			$latitude = isset( $value['latitude'] ) ? $value['latitude'] : false; 
			$longitude = isset( $value['longitude'] ) ? $value['longitude'] : false; 
			$zoom = isset( $value['zoom'] ) ? $value['zoom'] : false; ?>
		<input type="hidden" name="<?php echo $this->info['name']; ?>[latitude]" class="latitude" value="<?php echo $latitude; ?>" />
		<input type="hidden" name="<?php echo $this->info['name']; ?>[longitude]" class="longitude" value="<?php echo $longitude; ?>" />		
		<input type="hidden" name="<?php echo $this->info['name']; ?>[zoom]" class="zoom" value="<?php echo $zoom; ?>" />				
		<?php $width = strpos( $this->spec['width'], '%' ) === false && strpos( $this->spec['width'], 'px' ) === false  ? $this->spec['width'] . 'px' : $this->spec['width'] ; ?> 
		<?php $height = strpos( $this->spec['height'], '%' ) === false  && strpos( $this->spec['height'], 'px' ) === false ? $this->spec['height'] . 'px' : $this->spec['height'] ; ?>		
		<div class="map-container" style="width: <?php echo $width ; ?>; height: <?php echo $height ; ?>" >
		</div>
		<?php $field = ob_get_clean(); 
		return $field ;
		
	}
	public static function enqueue_scripts_and_styles( $field_type = false ){
		global $google_maps_key; 
		self::enqueue_script( 'gmaps-api', 'https://maps.googleapis.com/maps/api/js?key='.$google_maps_key. '&sensor=true' );
		parent::enqueue_scripts_and_styles( $field_type ); 
	}	
	
   /**
	* LAYOUTS FOR THIS FIELD
	*/

	
}