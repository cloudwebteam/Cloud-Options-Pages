<?php 
	class Page_Layout extends Layout {
		private static function get_layout_info(){
			$Options_Page = Cloud_Options_Pages::get_instance();
			$page_spec_array = $Options_Page->get_options_array_info( $_GET['page'] ); 
		
			$page_info = array(); 
			$page_info['subpage_slug'] = $_GET['page'];
			$page_info['page_spec_array'] = $page_spec_array ;
			// setup title and description
			if ( isset( $page_spec_array['title'] ) && $page_spec_array['title'] ){
				$page_info['title'] = '<h2 class="title">'.$page_spec_array['title'] .'</h2>';
			} else { 
				$page_info['title'] = '';
			}
			if ( isset( $page_spec_array['description'] ) && $page_spec_array['description'] ){
				$page_info['description'] = '<span class="description">'.$page_spec_array['description'] .'</span>';
			} else { 
				$page_info['description'] = '';
			}			
			
			//set up classes
			$classes = array(); 
			$classes[] = 'wrap'; // a typical WP class
			$classes[] = 'options-page';
			$classes[] = $page_spec_array['layout'] ; 
			
			$page_info['classes'] = implode ( ' ', $classes ); 		
				
			// set up icon html 
			$page_info['icon'] = '<div class="options-page-icon" id="icon-options-'.$page_info['subpage_slug'] .'" ></div>';
			
			// set up submit button html 
			$page_info['submit_button' ] = '<p class="submit"><input type="submit" class="button-primary" value="Save Changes" /></p>';
			
			// get sections' html 
			$page_info['sections'] = Cloud_Options_Pages::get_settings_sections( $page_info['subpage_slug']  ,$page_info );
			
			return $page_info; 		
		}
		public function standard(){
			// make variables available and easy to use by extracting them
			extract( self::get_layout_info( ), EXTR_OVERWRITE );
			?>
			<div id="page-<?php echo $subpage_slug; ?>" class="<?php echo $classes; ?>"> 
				<?php echo $icon; ?> 
				<?php echo $title; ?>
				<?php echo $description; ?>
				<form action="options.php" method="post">
				    <?php settings_fields( $subpage_slug ); ?>
				    <?php foreach ( $sections as $section ) { ?>
				    	<?php echo $section['html']; ?>
				    <?php } ?>		    
				    <?php echo $submit_button; ?>
			    </form>
			</div>
			<?php
		}
		public function grid(){
			// make variables available and easy to use by extracting them
			extract( self::get_layout_info( ) );
			?>
			<div id="page-<?php echo $subpage_slug; ?>" class="<?php echo $classes; ?>"> 
				<?php echo $icon; ?> 
				<?php echo $title; ?>
				<?php echo $description; ?>			    
				<form action="options.php" class="container" method="post">
				    <?php settings_fields( $subpage_slug ); ?>
					<div class="row">
				    <?php foreach ( $sections as $section) { ?>
					   <?php echo $section['html']; ?>
				    <?php } ?>
				    </div>
				    <?php echo $submit_button; ?>
			    </form>
			</div>			
			<?php		
		}
		public function tab(){
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
				    <?php echo $submit_button; ?>
			    </form>
			</div>			
			<?php
		}
		public function tab_left(){
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
				    <?php echo $submit_button; ?>
			    </form>
			</div>				
			<?php		
		}
		public function tab_right(){
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
				    <?php echo $submit_button; ?>
			    </form>
			</div>		
			<?php		
		}								
		public function scroll(){
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
				    <?php echo $submit_button; ?>
			   </form>
			</div>			
			<?php		
		}																	
	}
?>