<?php 
	class Layout_Section extends Layout {
		public static function get_layout_function( $layout = null ){
			$layout_function = parent::get_layout_function( $layout  );

			// handle where a page layout necessitates a certain section layout
			switch ( $parent_layout ){	
			}
					
			return $layout_function; 
			
		}	
		private static function get_layout_info( $section_slug, $spec, $page_spec ){
			// get all of the information stored in the master options array regarding this section and page
			
			$layout_vars = array();
			
			// place items with particular importance for every layout here. 
			$layout_vars['id'] = $section_slug ; 
			
			// if available, set up title and description
			if (  isset( $spec['title'] ) && $page_spec['layout'] !== 'tab' ) {
				$layout_vars['title'] = '<h3 class="title">'.$spec['title'] .'</h3>' ;
			} else {
				$layout_vars['title'] = ''; 
			}
			
			if ( isset( $spec['description'] ) && $spec['description'] ){
				$layout_vars['description'] = '<span class="description">'.$spec['description'] .'</span>';
			} else {
				$layout_vars['description'] = '';
			}
			
			//set up section classes
			$classes = array(); 
			$classes[] = 'section';
			$classes[] = $spec['layout'] ; 
			
			// if the page is a grid layout, give section a width (specified 1-12, default 12) 
			if( $page_spec['layout'] === 'grid' ){
				$classes[] =  isset( $spec['width'] ) ? 'span'.$spec['width'] : 'span12';
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
		public static function standard( $slug, $spec, $page_spec ){
			// make variables available and easy to use by extracting them
			extract( self::get_layout_info( $slug, $spec, $page_spec ) );

			ob_start();	?>
			<div class='<?php echo $classes; ?>'>
				<div class="header">			    
			    	<?php echo $title; ?>
			    	<?php echo $description; ?>
				</div>
			    <table class="form-table">
				    <?php foreach ( $fields as $field ) { ?>
				    	<?php echo $field; ?>
				    <?php } ?>				    
				</table>
				
			</div>
			<?php 
			$output = ob_get_clean();
			return $output;
			
		}

		public static function grid( $slug, $spec, $page_spec  ){
			// make variables available and easy to use by extracting them
			extract( self::get_layout_info( $slug, $spec, $page_spec ) );
			ob_start();
			?>	
			    <div class="<?php echo $classes; ?>">
				<?php if ( $title || $description ){ ?>
					<div class="header">			    
				    	<?php echo $title; ?>
				    	<?php echo $description; ?>
					</div>
				<?php } ?>
					<div class="row-fluid">
				    <?php foreach ( $fields as $field ) { ?>
				    	<?php echo $field; ?>
				    <?php } ?>	
					</div>
				</div>
			<?php	
			$output = ob_get_clean();
			return $output;
			
		}	
								
	}
?>