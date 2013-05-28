<?php 
	class Layout_WP_Metabox extends Layout {
		public static function get_layout_function( $layout = null ){
			$layout_function = parent::get_layout_function( $layout  );

			// handle where a page layout necessitates a certain section layout
			switch ( $parent_layout ){	
			}
					
			return $layout_function; 
			
		}	
		private static function get_layout_info( $args ){
			// get all of the information stored in the master options array regarding this section and page
			$metabox_slug = $args['metabox_slug'] ; 
			$spec = $args['spec']; 
			$layout_vars = array();
			
			$layout_vars['id'] = $metabox_slug ; 
			
			//set up section classes
			$classes = array(); 
			$classes[] = 'metabox';
			
			$form_classes = self::get_form_classes( $metabox_slug, $spec ); 		
			$classes = array_merge( $classes, $form_classes ); 
			$layout_vars['header'] = self::get_form_header( $metabox_slug, $spec ); 			
			$layout_vars['footer'] = false; 
	
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
		public static function standard( $post, $metabox_info ){
			// make variables available and easy to use by extracting them
			extract( self::get_layout_info( $metabox_info['args'] ) );	?>
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
			return true; 
			
		}			
		public static function table( $args ){
			// make variables available and easy to use by extracting them
			extract( self::get_layout_info( $args ) );	?>
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
			return true; 
			
		}				
	}
?>