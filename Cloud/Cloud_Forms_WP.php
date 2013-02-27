<?php 
class Cloud_Forms_WP extends Cloud_Forms {
	// singleton get method
	public static function get_instance(){
		if ( !self::$instance ){
			self::$instance = new self(); 
		}
		return self::$instance; 
	}
	
}