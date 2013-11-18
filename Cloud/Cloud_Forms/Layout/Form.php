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
			
			$layout_vars['success_message'] = self::get_success_message( $form_slug, $spec );
			// get sections' html 
			foreach( $spec['sections'] as $section_slug => $section_spec ){
			
				$layout = Layout_Section::get_layout_function( $section_spec['layout'] );
				$layout_vars['sections'][ $section_slug ] = array( 
					'html' => Layout_Section::$layout( $section_slug, $section_spec, $spec ),
					'title' => $section_spec['title'], 
					'description' => $section_spec['description']
				);
				$has_error_class = isset( $section_spec['validation_error'] ) && $section_spec['validation_error'] ? 'has-error' : ''  ; 
				ob_start(); 
				
				?>
					<li class="section-<?php echo $section_slug; ?>-tab <?php echo $has_error_class ; ?>" ><a title="<?php echo $section_spec['description']; ?>" href="#<?php echo $form_slug; ?>_<?php echo $section_slug; ?>"><?php echo $section_spec['title']; ?></a></li>				
				<?php $layout_vars['tabs'][] = ob_get_clean(); 
				
			}
			return $layout_vars; 		
		}
		public static function success( $form_slug = '', $spec = '' ){
			extract( self::get_layout_info( $form_slug, $spec ), EXTR_OVERWRITE );
			ob_start(); 
			?>
			<div class="<?php echo $classes; ?>">
				<div class="success-message"><?php echo $success_message; ?></div>
			</div>
		<?php 
			return ob_get_clean();		
		}
		public static function standard( $form_slug = '' , $spec = '' ){
			// make variables available and easy to use by extracting them
			extract( self::get_layout_info( $form_slug, $spec ), EXTR_OVERWRITE );
			ob_start(); 
			?>
			<div class="<?php echo $classes; ?>">
				<form data-id="<?php echo $form_slug; ?>" action="" method="post">
					<?php echo $header ; ?>
				    <?php foreach ( $sections as $section_slug => $section ) { ?>
				    	<?php echo $section['html']; ?>
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
			<div class="<?php echo $classes; ?>">
				<form data-id="<?php echo $form_slug; ?>" action="" method="post">
					<?php echo $header ; ?>			
			    	<ul class="tabs cf">
			    	<?php foreach ( $tabs as $tab ) {
			    		echo $tab; 
					} ?>
			    	</ul>
			    	<div class="tabs-content">
				    <?php foreach ( $sections as $section_slug => $section ) { ?>
			    		<div id="<?php echo $form_slug; ?>_<?php echo $section_slug; ?>">
				    		<?php echo $section['html']; ?>
			    		</div>

				    <?php } ?>		
				    </div>				    
				    <?php echo $footer; ?>
			    </form>
			</div>
			<?php
			return ob_get_clean();			
		}
		public static function tabs_animated( $form_slug = '' , $spec = '' ){
			return self::tabs( $form_slug , $spec ); 
		}
		public static function custom( $form_slug = '' , $spec = '' ){		
			// make variables available and easy to use by extracting them			
			extract( self::get_layout_info( $form_slug, $spec ), EXTR_OVERWRITE );		
		
			$layout = $spec['layout']; 
			foreach ( $sections as $slug => $section ) {
				$layout = preg_replace( '/\[ ?'.$slug.' ?\]/', $section['html'], $layout );
			} 						

			ob_start(); 
			?>
			<div class="<?php echo $classes; ?>">
				<form data-id="<?php echo $form_slug; ?>" action="" method="post">
					<?php echo $header ; ?>
					<?php echo $layout; ?>	
				    <?php echo $footer; ?>
			    </form>
			</div>
			<?php
			return ob_get_clean();	
		}
		
	}
?>