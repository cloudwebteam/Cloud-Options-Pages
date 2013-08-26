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
			} else if ( preg_match( '/\[[^\]]+\]/', $layout ) ){
				return 'custom' ; 
			} else {
				return self::$default_layout;
			}		
		}
		protected static function get_form_classes( $form_slug, $spec ){
			//set up classes
			$classes = array(); 
			$classes[] = 'cloud'; //necessary to keep Bootstrap from interfering
			$classes[] = 'cloud-form';
			$classes[] = 'form-'.$form_slug; 
			$classes[] = isset( $spec['layout'] ) && strpos(  $spec['layout'], '[' ) === false ? $spec['layout'] . '-layout' : 'custom-layout';
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
			$success_function_js = isset( $spec['success_function_js'] ) ? ' data-on_success="'.$spec['success_function_js'].'"' : ''; 
			$submit_button = '<input type="submit" class="button-primary submit-button" '. $success_function_js . ' value="'.$spec['submit_text'].'" />';

			if ( $spec['footer_layout'] ){
				$layout = $spec['footer_layout']; 			
				$layout = preg_replace( '/\[ ?submit ?\]/', $submit_button, $layout ); 						
				
				if ( $layout ){
					$footer = '<footer class="cloud-form-footer cf">' ; 
					$footer .= $form_id_field; 
					$footer .= $layout;
					$footer .= '</footer>' ; 
					return $footer; 
				} 	
			}	
			return '<footer class="cloud-form-footer cf">'.$form_id_field  . '<p class="submit">'.$submit_button .'</p></footer>' ; 
		}		
		protected static function get_form_header( $form_slug, $spec ){
		
	
			// setup title and description
			if ( $spec['header_layout'] ){
				$layout = $spec['header_layout']; 			
				$title = empty( $spec['title'] ) ? '' : $spec['title']; 
				$description = empty( $spec['description'] ) ? '' : $spec['description']; 				
				$layout = preg_replace( '/\[ ?title ?\]/', $title, $layout ); 						
				$layout = preg_replace( '/\[ ?description ?\]/', $description, $layout ); 										
				
				if ( $layout ){
					$header = '<header class="cloud-form-header">' ; 
					$header .= $layout;
					$header .= '</header>' ; 
					return $header; 
				} 
			} else {		
				if ( !empty( $spec['title'] ) ||  !empty( $spec['description'] ) ){
					$title = !empty( $spec['title'] ) ? '<h2 class="title">'.$spec['title'] .'</h2>' : false; 
					$description = !empty( $spec['description'] ) ? '<h4 class="description">'.$spec['description'] .'</h4>' : false; 				
					return '<header class="cloud-form-header">'.$title .$description.'</header>' ; 
				}
			}
			return false; 
		}
	}