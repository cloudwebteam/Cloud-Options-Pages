<?php 
	class Layout_Form extends Layout1 {
		private static function get_layout_info( $form_slug, $spec ){
			$layout_vars = array(); 
			$layout_vars['form_slug'] = $form_slug;
			$layout_vars['spec'] = $spec ;
			// setup title and description
			if ( isset( $spec['title'] ) && $spec['title'] ){
				$layout_vars['title'] = '<h2 class="title">'.$spec['title'] .'</h2>';
			} else { 
				$layout_vars['title'] = '';
			}
			if ( isset( $spec['description'] ) && $spec['description'] ){
				$layout_vars['description'] = '<span class="description">'.$spec['description'] .'</span>';
			} else { 
				$layout_vars['description'] = '';
			}			
			//set up classes
			$classes = array(); 
			$classes[] = 'cloud'; //necessary to keep Bootstrap from interfering
			$classes[] = 'cloud-form';
			$classes[] = $spec['layout'] ;
			if ( $spec['ajax'] ){
				$classes[] = 'ajax' ;
			}
				
			$layout_vars['classes'] = implode ( ' ', $classes ); 		
			
			// set up a hidden input that identifies the form in the $_POST request
			$layout_vars[ 'form_id_field' ] = '<input type="hidden" name="form_id" value="' . $form_slug . '" />' ;
		
			// set up submit button html 
			$layout_vars['submit_button' ] = '<p class="submit"><input type="submit" class="button-primary" value="Save Changes" /></p>';

			$layout_vars[ 'json_spec' ] = $spec['ajax'] ? '<div id="json_spec_'.$form_slug.'" style="display:none !important;">'.json_encode( $spec ).'</div>' : '' ;
			
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
				<?php echo $title; ?>
				<?php echo $description; ?>
				<form data-id="<?php echo $form_slug; ?>" action="" method="post">
				    <?php foreach ( $sections as $section ) { ?>
				    	<?php echo $section; ?>
				    <?php } ?>		
				    <?php echo $json_spec; ?>    
					<?php echo $form_id_field; ?>				    
				    <?php echo $submit_button; ?>
			    </form>
			</div>
			<?php
			return ob_get_clean();
		}	
		public static function grid(){
			// make variables available and easy to use by extracting them
			extract( self::get_layout_info( ) );
			?>
			<div id="form-<?php echo $subpage_slug; ?>" class="<?php echo $classes; ?>"> 
				<?php echo $icon; ?> 
				<?php echo $title; ?>
				<?php echo $description; ?>			    
				<form action="options.php" class="container" method="post">
				    <?php settings_fields( $subpage_slug ); ?>
					<div class="row-fluid">
				    <?php foreach ( $sections as $section) { ?>
					   <?php echo $section['html']; ?>
				    <?php } ?>
				    </div>
					<?php echo $form_id_field; ?>				    				    
				    <?php echo $submit_button; ?>
				    <?php echo $set_defaults; ?>				    
			    </form>
			</div>			
			<?php		
		}
		public static function tab(){
			// make variables available and easy to use by extracting them
			extract( self::get_layout_info( ) );
			?>
			<div id="page-<?php echo $subpage_slug; ?>" class="<?php echo $classes; ?>"> 
				<?php echo $icon; ?> 
				<?php echo $title; ?>
				<?php echo $description; ?>	
				<form action="options.php" method="post">
				    <?php settings_fields( $subpage_slug ); ?>
					<div id="page-tabs" class="tabbable">
					    <?php if ( $sections ){ ?>
						<ul class="nav nav-tabs">
						    <?php foreach ( $sections as $section_id => $section ) { ?>
						    <li class=""><a href="#<?php echo $section_id; ?>" data-toggle="tab" ><?php echo  $section['info']['title']; ?></a></li>
							<?php } ?>
						</ul>
						<div class="tab-content">				
						    <?php foreach ( $sections as $section_id => $section ) { ?>
						    	<div class="tab-pane fade" id="<?php echo $section_id; ?>">
							    <?php echo $section['html']; ?>
						    	</div>
						    <?php } ?>
						</div>
						<?php } ?>
					</div>
					<?php echo $form_id_field; ?>				    					
				    <?php echo $submit_button; ?>
				    <?php echo $set_defaults; ?>				    
			    </form>
			</div>			
			<?php
		}
		public static function tab_left(){
			// make variables available and easy to use by extracting them
			extract( self::get_layout_info( ) );
			?>
			<div id="page-<?php echo $subpage_slug; ?>" class="<?php echo $classes; ?>"> 
				<?php echo $icon; ?> 
				<?php echo $title; ?>
				<?php echo $description; ?>	
				<form action="options.php" method="post">
				    <?php settings_fields( $subpage_slug ); ?>
					<div id="page-tabs" class="tabbable tabs-left">
					    <?php if ( $sections ){ ?>
						<ul class="nav nav-tabs">
						    <?php foreach ( $sections as $section_id => $section ) { ?>
						    <li class=""><a href="#<?php echo $section_id; ?>" data-toggle="tab" ><?php echo  $section['info']['title']; ?></a></li>
							<?php } ?>
						</ul>
						<div class="tab-content">				
						    <?php foreach ( $sections as $section_id => $section ) { ?>
						    	<div class="tab-pane fade" id="<?php echo $section_id; ?>">
							    <?php echo $section['html']; ?>
						    	</div>
						    <?php } ?>
						</div>
						<?php } ?>
					</div>
					<?php echo $form_id_field; ?>				    					
				    <?php echo $submit_button; ?>
				    <?php echo $set_defaults; ?>				    
			    </form>
			</div>				
			<?php		
		}
		public static function tab_right(){
			// make variables available and easy to use by extracting them
			extract( self::get_layout_info( ) );
			?>
			<div id="page-<?php echo $subpage_slug; ?>" class="<?php echo $classes; ?>"> 
				<?php echo $icon; ?> 
				<?php echo $title; ?>
				<?php echo $description; ?>	
				<form action="options.php" method="post">
				    <?php settings_fields( $subpage_slug ); ?>
					<div id="page-tabs" class="tabbable tabs-right">
					    <?php if ( $sections ){ ?>
						<ul class="nav nav-tabs">
						    <?php foreach ( $sections as $section_id => $section ) { ?>
						    <li class=""><a href="#<?php echo $section_id; ?>" data-toggle="tab" ><?php echo  $section['info']['title']; ?></a></li>
							<?php } ?>
						</ul>
						<div class="tab-content">				
						    <?php foreach ( $sections as $section_id => $section ) { ?>
						    	<div class="tab-pane fade" id="<?php echo $section_id; ?>">
							    <?php echo $section['html']; ?>
						    	</div>
						    <?php } ?>
						</div>
						<?php } ?>
					</div>
					<?php echo $form_id_field; ?>				    					
				    <?php echo $submit_button; ?>
				    <?php echo $set_defaults; ?>				    
			    </form>
			</div>		
			<?php		
		}								
		public static function scroll(){
			// make variables available and easy to use by extracting them
			extract( self::get_layout_info( ) );
			?>
			<script>
			jQuery( 'body' ).attr( 'data-spy', 'scroll' ).attr( 'data-target', '#scroll-nav' );
			</script>
			<div id="page-<?php echo $subpage_slug; ?>" class="<?php echo $classes; ?>"> 
				<?php echo $icon; ?> 
				<?php echo $title; ?>
				<?php echo $description; ?>	
				<form action="options.php" method="post">
				    <?php settings_fields( $subpage_slug ); ?>
					<div id="scroll-nav" class="affix">
						<ul class="nav nav-list ">
					    <?php foreach ( $sections as $section_id => $section ) { ?>
					    	<li><a href="#<?php echo $section_id; ?>"><?php echo $section['info']['title']; ?></a></li>
					    <?php } ?>					
						</ul>
					</div>
			    	<div id="scroll-content">				
					    <?php foreach ( $sections as $section_id => $section ) { ?>
					    	<div id="<?php echo $section_id; ?>">
						    <?php echo $section['html']; ?>
					    	</div>
					    <?php } ?>
					</div>
					<?php echo $form_id_field; ?>				    					
				    <?php echo $submit_button; ?>
				    <?php echo $set_defaults; ?>				    
			   </form>
			</div>			
			<?php		
		}																	
	}
?>