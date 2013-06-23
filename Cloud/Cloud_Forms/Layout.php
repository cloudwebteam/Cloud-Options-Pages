<?php
	abstract class Layout {
		
		private static $default_layout; 

		protected static $constructor_class ;
		public static function set_constructor_class( $class_instance ){
			self::$constructor_class = $class_instance ;
		}
		public static function get_layout_function( $layout = null ){
			self::$default_layout = 'standard'; 		
			if ( $layout && get_called_class() && method_exists( get_called_class() , $layout) ){
				return $layout;
			} else {
				return self::$default_layout;
			}		
		}
		protected static function get_form_classes( $form_slug, $spec ){
			//set up classes
			$classes = array(); 
			$classes[] = 'cloud'; //necessary to keep Bootstrap from interfering
			$classes[] = 'cloud-form';
			$classes[] = $spec['layout'] . '-layout';
			if ( isset( $spec['ajax'] ) && $spec['ajax'] ){
				$classes[] = 'ajax' ;
			}	
			if ( isset( $spec['validation_error'] ) && $spec['validation_error'] ){
				$classes[] = 'has-error' ; 
			}			
			return $classes; 
		}
		protected static function get_form_footer( $form_slug, $spec ){
			// set up a hidden input that identifies the form in the $_POST request
			$form_id_field = '<input type="hidden" name="form_id" value="' . $form_slug . '" />' ;
			// set up submit button html 
			$submit_button = '<p class="submit"><input type="submit" class="button-primary" value="'.$spec['submit_text'].'" /></p>';
			// if ajax is enabled, include spec in a hidden div			
			return '<footer class="form">'.$form_id_field  . $submit_button .'</footer>' ; 
		}		
		protected static function get_form_header( $form_slug, $spec ){
			// setup title and description
			$title = !empty( $spec['title'] ) ? '<h2 class="title">'.$spec['title'] .'</h2>' : false; 
			$description = !empty( $spec['description'] ) ? '<h4 class="description">'.$spec['description'] .'</h4>' : false; 			
			if ( $title || $description ){
				$header = '<header class="cloud-form-header">'.$title .$description.'</header>' ; 
			} else {
				$header = false;
			}
			return $header; 
		}
	}