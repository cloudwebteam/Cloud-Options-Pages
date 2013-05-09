<?php 
	class Layout_WP_Page extends Layout {
		private static function get_layout_info(  ){
			extract( self::get_page_spec( ) );

			$layout_vars = array();
			
			$layout_vars['form_slug'] = $form_slug;
			$layout_vars['spec'] = $spec ;

			
			$classes = self::get_form_classes( $form_slug, $spec );
			$classes[] = 'wrap' ; 
			$layout_vars['classes'] = implode ( ' ', $classes ); 		

			$layout_vars['header'] = self::get_form_header( $form_slug, $spec ); 		
			ob_start();
				settings_fields( $form_slug );
			$layout_vars['header'] .= ob_get_clean();	
			ob_start(); 
				submit_button(); 
			$submit_button = ob_get_clean(); 
			$layout_vars['footer'] = '<footer class="cloud-form-footer">'.$submit_button.'</footer>'; 
			
			
			// get sections' html 
			foreach( $spec['sections'] as $section_slug => $section_spec ){
				$layout = Layout_Section::get_layout_function( $section_spec['layout'] );
				$layout_vars['sections'][ $section_slug ] = array( 
					'html' => Layout_Section::$layout( $section_slug, $section_spec, $spec ),
					'title' => $section_spec['title'], 
					'description' => $section_spec['description']
				);
			}
			return $layout_vars; 		
		}
		protected static function get_page_spec( ){
			if ( strpos( $_GET['page'], '.' ) !== false ){
				$parts = explode( '.', $_GET['page'] ); 
				$top_level = $parts[0] ; 
				$subpage = $parts[1] ; 
			} else {
				$top_level = $_GET['page'] ; 
				$subpage = $top_level; 
			}	
			$Forms = Cloud_Forms_WP::get_instance(); 
			$form_slug = $subpage ; 
			$form_spec = $Forms->get_spec( $top_level, $subpage ) ; 
			return array( 
				'top_level_slug' => $top_level,
				'form_slug' => $form_slug, 
				'spec' => $form_spec
			);
		
		}
		public static function standard( ){
			
			// make variables available and easy to use by extracting them
			extract( self::get_layout_info( ), EXTR_OVERWRITE );
			?>
			<div id="form-<?php echo $form_slug; ?>" class="<?php echo $classes; ?>">
				<form data-id="<?php echo $form_slug; ?>" action="options.php" method="post">
					<?php echo $header ; ?>
				    <?php foreach ( $sections as $section_slug => $section ) { ?>
				    	<?php echo $section['html']; ?>
				    <?php } ?>		
				    <?php echo $footer; ?>
			    </form>
			</div>
			<?php
		}	
		public static function tabs( ){
			// make variables available and easy to use by extracting them
			extract( self::get_layout_info( ), EXTR_OVERWRITE );
			?>
			<div id="form-<?php echo $form_slug; ?>" class="<?php echo $classes; ?>">
				<form data-id="<?php echo $form_slug; ?>" action="options.php" method="post">
					<?php echo $header ; ?>		
			    	<ul class="tabs cf">
			    	<?php foreach ( $sections as $section_slug => $section ) { ?>			    	
				    	<li class="section-<?php echo $section_slug; ?>-tab" ><a title="<?php echo $section['description']; ?>" href="#<?php echo $form_slug; ?>_<?php echo $section_slug; ?>"><?php echo $section['title']; ?></a></li>
					    <?php } ?>
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
		}
		public static function tabs_animated( ){
			self::tabs( ); 
		}
		
	}
?>