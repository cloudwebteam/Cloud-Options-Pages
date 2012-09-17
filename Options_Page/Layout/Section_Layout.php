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
			$Options_Page = Options_Page::get_instance();
		
			$parent_page = $Options_Page->get_options_array_info( $_GET['page'] ); 

			$info = array(); 
			$info['subpage_slug'] = $_GET['page'];
			$info['parent_layout'] = $parent_page['callback'];

			$info['info'] = $Options_Page->get_options_array_info( $_GET['page'], $section['id'] );
			
			$classes = array(); 
			$classes[] = $info['parent_layout'] === 'grid' ? 'span'.$info['info']['width'] : '';
			$classes[] = 'section'; 
			$info['classes'] = implode ( ' ', $classes ); 
			
			return $info; 		
		}
		public function standard( $section ){
			$info = self::get_layout_info( $section );	
			ob_start();		
			?>
			<div class='<?php echo $info['classes'] ?>'>		
			<?php if ( $section['title'] ){ ?>
		    	<h3><?php echo $section['title']; ?></h3>
		    <?php } ?>
			    <table class="form-table">
				    <?php Options_Page::do_settings_fields( $info['subpage_slug'], $section ); ?>
				</table>
			</div>
			<?php 
			$output = ob_get_clean();
			
			return $output;
		}
		public function grid( $section ){
			$info = self::get_layout_info( $section );
			$section_width = $info['info']['width'] ? $info['info']['width'] : 12 ; 
			?>	
			    <div class="span<?php echo $section_width; ?>">
			    	<h3>GRID SECTION (<?php echo $section_width; ?>): <?php echo $section['title']; ?></h3>
			    
				    <?php Options_Page::do_settings_fields( $info['subpage_slug'], $section ); ?>
				</div>
			<?php		
		}	
		public function tab( $section ){
			$info = self::get_layout_info();
			?>
		    	<h3><?php echo $section['title']; ?></h3>
			    <table class="form-table">
				    <?php Options_Page::do_settings_fields( $info['subpage_slug'], $section ); ?>
				</table>
			<?php		
		}
								
	}
?>