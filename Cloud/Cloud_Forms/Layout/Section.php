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
			// if the page is a grid layout, give section a width (specified 1-12, default 12) 
				if(  $page_spec['layout'] === 'grid' ){
					$classes[] =  isset( $spec['width'] ) ? 'span'.$spec['width'] : 'span12';
				}	
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
				$layout_vars['fields'][] = ob_get_clean() ;
			}				
			return $layout_vars; 		
		}

		public static function standAlone( $slug, $spec, $page_spec = false ){
			// make variables available and easy to use by extracting them
			extract( self::get_layout_info( $slug, $spec, $page_spec ) );
			ob_start();	?>
			<div id="form-<?php echo $slug; ?>" class="<?php echo $classes; ?>">
				<form data-id="<?php echo $slug; ?>" action="<?php bloginfo( 'url' ); ?>" method="post">
					<?php echo $header; ?>
					<div class="fields">
				    <?php foreach ( $fields as $field ) { ?>
				    	<?php echo $field; ?>
				    <?php } ?>
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

			    <div class="form-fields">
				    <?php foreach ( $fields as $field ) { ?>
				    	<?php echo $field; ?>
				    <?php } ?>				    
				</div>
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
	}
?>