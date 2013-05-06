<?php 
	class Layout_Form extends Layout {
		private static function get_layout_info( $form_slug, $spec ){
			$layout_vars = array(); 
			$layout_vars['form_slug'] = $form_slug;
			$layout_vars['spec'] = $spec ;

			
			$classes = self::get_form_classes( $form_slug, $spec );
			
			$layout_vars['classes'] = implode ( ' ', $classes ); 		

			$layout_vars['header'] = self::get_form_header( $form_slug, $spec ); 			
			$layout_vars['footer'] = self::get_form_footer( $form_slug, $spec ); 
			// get sections' html 
			foreach( $spec['sections'] as $section_slug => $section_spec ){
				$layout = Layout_Section::get_layout_function( $section_spec['layout'] );
				$layout_vars['sections'][] = Layout_Section::$layout( $section_slug, $section_spec, $spec ) ;
			}
			return $layout_vars; 		
		}
		public static function standard( $form_slug = '' , $spec = '' ){
			// make variables available and easy to use by extracting them
			extract( self::get_layout_info( $form_slug, $spec ), EXTR_OVERWRITE );
			ob_start(); 
			?>
			<div id="form-<?php echo $form_slug; ?>" class="<?php echo $classes; ?>">
				<form data-id="<?php echo $form_slug; ?>" action="" method="post">
					<?php echo $header ; ?>
				    <?php foreach ( $sections as $section ) { ?>
				    	<?php echo $section; ?>
				    <?php } ?>		
				    <?php echo $footer; ?>
			    </form>
			</div>
			<?php
			return ob_get_clean();
		}	
		public static function tabs( $form_slug = '' , $spec = '' ){
			// make variables available and easy to use by extracting them
			extract( self::get_layout_info( $form_slug, $spec ), EXTR_OVERWRITE );
			ob_start(); 
			?>
			<div id="form-<?php echo $form_slug; ?>" class="<?php echo $classes; ?>">
				<form data-id="<?php echo $form_slug; ?>" action="" method="post">
					<?php echo $header ; ?>			
				    <?php foreach ( $sections as $section ) { ?>
				    	<?php echo $section; ?>
				    <?php } ?>		
				    <?php echo $footer; ?>
			    </form>
			</div>
			<?php
			return ob_get_clean();			
		}
		
	}
?>