<?php 
	class Layout_WP_Page extends Layout {
		private static function get_layout_info(  ){
			extract( self::get_page_spec( ) );

			$layout_vars = array();
			
			$layout_vars['form_slug'] = $form_slug;
			$layout_vars['spec'] = $spec ;

			
			$classes = self::get_form_classes( $form_slug, $spec );
			$classes[] = 'wrap' ; 
			if ( ! isset( $spec['sections'] ) ){
				$classes[] = 'section';
			}

			$layout_vars['classes'] = implode ( ' ', $classes ); 		

			$layout_vars['header'] = self::get_form_header( $form_slug, $spec ); 		
			ob_start();
				settings_fields( $form_slug );
			$layout_vars['header'] .= ob_get_clean();	
			ob_start(); 
				submit_button(); 
			$submit_button = ob_get_clean(); 
			$layout_vars['footer'] = self::get_form_footer( $form_slug, $spec );
			
			
			// if sections, get sections' html 
			if ( isset( $spec['sections'] ) ){
				foreach( $spec['sections'] as $section_slug => $section_spec ){
					$layout = Layout_Section::get_layout_function( $section_spec['layout'] );
					$layout_vars['sections'][ $section_slug ] = array( 
						'html' => Layout_Section::$layout( $section_slug, $section_spec, $spec ),
						'title' => $section_spec['title'], 
						'layout' => $section_spec['layout'],
						'description' => $section_spec['description']
					);
				}
			// otherwise, get fields' html 
			} else {
				foreach( $spec['fields'] as $field_slug => $field_spec ){
					$field_type = Cloud_Field::get_class_name( $field_spec['type'] );
					ob_start();
						$field_type::create_field( $field_spec ) ;
					$layout_vars['fields'][ $field_slug ] = ob_get_clean() ;
				}								
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
			$form_spec = $Forms->get_page_spec( $top_level, $subpage ) ; 
			return array( 
				'top_level_slug' => $top_level,
				'form_slug' => $form_slug, 
				'spec' => $form_spec
			);
		
		}
		public static function noSections(){
			extract( self::get_layout_info( ) );

			if ( ( $spec['layout'] === 'table' && ! empty( $spec['form_slug'] ) ) || $spec['layout'] === 'standard' ){			
				// make variables available and easy to use by extracting them
				ob_start();	?>
				<div class="<?php echo $classes; ?>">
					<form data-id="<?php echo $form_slug; ?>" action="options.php" method="post">
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
				echo $output;
				return;
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
				<form data-id="<?php echo $form_slug; ?>" action="options.php" method="post">
					<?php echo $header; ?>
					<div class="form-fields">
				    	<?php echo $fields_layout; ?>
					</div>
					<?php echo $footer; ?>
				</form>						
			</div>
			<?php 
			$output = ob_get_clean();
			echo $output;		
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
			    	<?php foreach ( $sections as $section_slug => $section ) { 
						if ( $section['layout'] !== 'hidden' ){	?>
				    	<li class="section-<?php echo $section_slug; ?>-tab" ><a title="<?php echo $section['description']; ?>" href="#<?php echo $form_slug; ?>_<?php echo $section_slug; ?>"><?php echo $section['title']; ?></a></li>
					    <?php } ?>
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
		public static function custom( $form_slug = '' , $spec = '' ){		
			// make variables available and easy to use by extracting them			
			extract( self::get_layout_info( $form_slug, $spec ), EXTR_OVERWRITE );		
		
			$layout = $spec['layout']; 
			
			foreach ( $sections as $slug => $section ) {
				$layout = preg_replace( '/\[ ?'.$slug.' ?\]/', $section['html'], $layout );
			} 			
			?>
			<div id="form-<?php echo $form_slug; ?>" class="<?php echo $classes; ?>">
				<form data-id="<?php echo $form_slug; ?>" action="options.php" method="post">
					<?php echo $header ; ?>
					<?php echo $layout; ?>	
				    <?php echo $footer; ?>
			    </form>
			</div>
			<?php
		}		
		
	}
?>