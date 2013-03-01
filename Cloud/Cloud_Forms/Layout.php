<?php
	abstract class Layout {
		
		private static $default_layout; 

		protected static $constructor_class ;
		public static function set_constructor_class( $class_instance ){
			self::$constructor_class = $class_instance ;
		}
		public static function get_layout_function( $layout = null ){
		
			self::$default_layout = 'standard'; 
			if ( get_called_class() && method_exists( get_called_class() , $layout) ){
				return $layout;
			} else {
				return self::$default_layout;
			}		
		}
	}