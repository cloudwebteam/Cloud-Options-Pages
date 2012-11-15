<?php 
	class Section_Layout extends Layout {
		public static function get_layout_function( $layout = null , $sub_classname = null , $parent_layout = null ){
			$layout_function = parent::get_layout_function( $layout , $sub_classname );

			// handle where a page layout necessitates a certain section layout
			switch ( $parent_layout ){
			// currently not needed
			/*	case 'standard' : 
					$layout_function = 'standard'; 
					break;
			*/		
			}
					
			return $layout_function; 
			
		}	
		private static function get_layout_info( $section ){
			$Options_Page = Cloud_Options_Pages::get_instance();
						
			// get all of the information stored in the master options array regarding this section and page
			$section_spec_array = $Options_Page->get_options_array_info( $_GET['page'], $section['id'] );
			$parent_page_spec_array = $Options_Page->get_options_array_info( $_GET['page'] ); 
			
			$section_info = array();
			
			// place items with particular importance for every layout here. 
			$section_info['id']		= $section['id'] ; 
			$section_info['subpage_slug'] = $_GET['page'];
			$section_info['parent_layout'] = $parent_page_spec_array['callback'];
			
			// if available, set up title and description
			if (  isset( $section['title'] ) ) {
				$section_info['title'] = '<h3 class="title">'.$section['title'] .'</h3>' ;
			} else {
				$section_info['title'] = ''; 
			}
			
			if ( isset( $section_spec_array['description'] ) && $section_spec_array['description'] ){
				$section_info['description'] = '<span class="description">'.$section_spec_array['description'] .'</span>';
			} else {
				$section_info['description'] = '';
			}
			
			//set up section classes
			$classes = array(); 
			$classes[] = 'section';
			$classes[] = $section_spec_array['layout'] ; 
			
			// if the page is a grid layout, give section a width (specified 1-12, default 12) 
			if( $section_info['parent_layout'] === 'grid' ){
				$classes[] =  isset( $section_spec_array['width'] ) ? 'span'.$section_spec_array['width'] : 'span12';
			}
			$section_info['classes'] = implode ( ' ', $classes ); 
			
			return $section_info; 		
		}
		public function standard( $section ){
			// make variables available and easy to use by extracting them
			extract( self::get_layout_info( $section ) );
			
			ob_start();	?>
			<div class='<?php echo $classes; ?>'>
				<div class="header">			    
			    	<?php echo $title; ?>
			    	<?php echo $description; ?>
				</div>
			    <table class="form-table">
				    <?php Cloud_Options_Pages::do_settings_fields( $subpage_slug, $section ); ?>
				</table>
				
			</div>
			<?php 
			$output = ob_get_clean();
			return $output;
			
		}

		public function grid( $section ){
			// make variables available and easy to use by extracting them
			extract( self::get_layout_info( $section ) );
			ob_start();
			?>	
			    <div class="<?php echo $classes; ?>">
					<div class="header">			    
				    	<?php echo $title; ?>
				    	<?php echo $description; ?>
					</div>
					<div class="row">
				    <?php Cloud_Options_Pages::do_settings_fields( $subpage_slug, $section ); ?>
					</div>
				</div>
			<?php	
			$output = ob_get_clean();
			return $output;
			
		}	
								
	}
?>