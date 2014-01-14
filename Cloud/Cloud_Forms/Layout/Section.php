<?php 
	class Layout_Section extends Layout {
		public static function get_layout_function( $layout = null ){
			$layout_function = parent::get_layout_function( $layout  );

			// handle where a page layout necessitates a certain section layout
			switch ( $parent_layout ){	
			}
					
			return $layout_function; 
			
		}	
		private static function get_layout_info( $form_slug, $spec, $page_spec = false ){
			// get all of the information stored in the master options array regarding this section and page
			
			$layout_vars = array();
			
			$layout_vars['id'] = $form_slug ; 
			$layout_vars['success_message'] = self::get_success_message( $form_slug, $spec );
			
			//set up section classes
			$classes = array(); 
			$classes[] = 'section';
						
			// if its a simple, one section form
			if ( !$page_spec ){
				$form_classes = self::get_form_classes( $form_slug, $spec ); 		
				$classes = array_merge( $classes, $form_classes ); 
				$layout_vars['header'] = self::get_form_header( $form_slug, $spec ); 			
				$layout_vars['footer'] = self::get_form_footer( $form_slug, $spec ); 
			} else {
				$classes[] = 'section_slug-'.$form_slug;
				if ( isset( $spec['validation_error'] ) && $spec['validation_error'] ){
					$classes[] = 'has-error' ; 
				}								
				if ( $page_spec['layout'] === 'tab'	){
					$layout_vars['title'] = '' ;
				}
				$title = isset( $spec['title'] ) ? '<h3 class="title">'.$spec['title'] .'</h3>' : '' ;
				$description  = isset( $spec['description'] ) ? '<span class="description">'.$spec['description'] .'</span>' : '' ;		
				if ( $title || $description ){
					$layout_vars['header'] = '<header class="cloud-section-header">'. $title. $description .'</header>'; 
				}		
				$layout_vars['footer'] = false; 
			}
			$layout_vars['classes'] = implode ( ' ', $classes ); 
			
			// get sections' html 
			foreach( $spec['fields'] as $field_slug => $field_spec ){
				$field_type = Cloud_Field::get_class_name( $field_spec['type'] );
				ob_start();
					$field_type::create_field( $field_spec ) ;
				$layout_vars['fields'][ $field_slug ] = ob_get_clean() ;
			}				
			return $layout_vars; 		
		}
		public static function success( $form_slug = '', $spec = '' ){
			extract( self::get_layout_info( $form_slug, $spec ), EXTR_OVERWRITE );
			ob_start(); 
			?>
			<div class="<?php echo $classes; ?>">
				<?php echo $success_message; ?>
			</div>
		<?php 
			return ob_get_clean();		
		}
		public static function standAlone( $form_slug, $spec, $page_spec = false ){
			extract( self::get_layout_info( $form_slug, $spec, $page_spec ) );
			if ( $spec['layout'] === 'table' ){
				// make variables available and easy to use by extracting them
				ob_start();	?>
				<div class="<?php echo $classes; ?>">
					<form data-id="<?php echo $form_slug; ?>" action="" method="post">
						<?php echo $header; ?>
						<table class="form-fields">
					    	<?php foreach ( $fields as $field ) {
								echo $field;
							} ?>
						</table>
						<?php echo $footer; ?>
					</form>						
				</div>
				<?php 
				$output = ob_get_clean();
				return $output;
			} 
			$layout = Layout_Form::get_layout_function( $spec['layout'] );	
			if ( $layout === 'custom' ){
				$layout = $spec['layout']; 
				foreach ( $fields as $slug => $field ) {
					$layout = preg_replace( '/\[ ?'.$slug.' ?\]/', $field, $layout );
				} 						
				$fields_layout = $layout ; 
			} else {
				$fields_layout = '';
				foreach ( $fields as $field ) {
					$fields_layout .= $field;
				}
			}
			// make variables available and easy to use by extracting them
			ob_start();	?>
			<div class="<?php echo $classes; ?>">
				<form data-id="<?php echo $form_slug; ?>" action="" method="post">
					<?php echo $header; ?>
					<div class="form-fields">
				    	<?php echo $fields_layout; ?>
					</div>
					<?php echo $footer; ?>
				</form>						
			</div>
			<?php 
			$output = ob_get_clean();
			return $output;						
			
		}		
		public static function standard( $slug, $spec, $page_spec = false ){
			
			// make variables available and easy to use by extracting them
			extract( self::get_layout_info( $slug, $spec, $page_spec ) );
			ob_start();	?>
			<div class='<?php echo $classes; ?>'>
				<?php echo $header; ?>

			    <table class="form-fields form-table">
				    <?php foreach ( $fields as $field ) { ?>
				    <?php echo $field; ?>
				    <?php } ?>				    
				</table>
				<?php echo $footer; ?>
				
			</div>
			<?php 
			$output = ob_get_clean();
			return $output;
			
		}			
		public static function table( $slug, $spec, $page_spec = false ){
			// make variables available and easy to use by extracting them
			extract( self::get_layout_info( $slug, $spec, $page_spec ) );
			ob_start();	?>
			<div class='<?php echo $classes; ?>'>
				<?php echo $header; ?>

			    <table class="form-table">
				    <?php foreach ( $fields as $field ) { ?>
				    	<?php echo $field; ?>
				    <?php } ?>				    
				</table>
				<?php echo $footer; ?>
				
			</div>
			<?php 
			$output = ob_get_clean();
			return $output;
			
		}
		public static function custom( $slug, $spec, $page_spec = false ){
			// make variables available and easy to use by extracting them
			extract( self::get_layout_info( $slug, $spec, $page_spec ) );
			$layout = $spec['layout']; 
			foreach ( $fields as $slug => $field ) {
				$layout = preg_replace( '/\[ ?'.$slug.' ?\]/', $field, $layout );
			} 						
			ob_start();	?>
			<div class='<?php echo $classes; ?>'>
				<?php echo $header; ?>
			    <div class="form-fields">
			    	<?php echo $layout; ?>				    
				</div>
				<?php echo $footer; ?>
			</div>
			<?php 
			$output = ob_get_clean();
			return $output;		
		}
	}
?>