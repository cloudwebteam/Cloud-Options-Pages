<?php
	abstract class Layout {
		
		private static $default_layout; 
		
		public static function get_layout_function( $layout = null ){
		
			self::$default_layout = 'standard'; 
			if ( get_called_class() && method_exists( get_called_class() , $layout) ){
				return $layout;
			} else {
				return self::$default_layout;
			}		
		}
	}