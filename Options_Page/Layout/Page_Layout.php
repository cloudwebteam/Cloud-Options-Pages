<?php 
	class Page_Layout extends Layout {
		private static function get_layout_info(){
			$Options_Page = Options_Page::get_instance();
		
			$info = array(); 
			$info['subpage_slug'] = $_GET['page'];
			$info['page_info'] = $Options_Page->get_options_array_info( $_GET['page'] ); 

			$info['title'] = $info['page_info']['title'];

			return $info; 		
		}
		public function standard(){
			$info = self::get_layout_info();
			?>
			<div id="theme-options-wrap" class="wrap standard"> 
				<div class="icon32" id="icon-options-general"> <br /> </div>	 
				<h3><?php echo $info['title']; ?></h3>															
				<form action="options.php" method="post">
				    <?php settings_fields( $info['subpage_slug'] ); ?>
				    <?php $sections = Options_Page::get_settings_sections( $info['subpage_slug'] , $info['page_info'] ); ?>
				    <?php foreach ( $sections as $section ) { ?>
				    	<?php echo $section['html']; ?>
				    <?php } ?>				    
				    <p class="submit">
				    	<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
				    </p>
			
			    </form>
			</div>
			<?php
		}
		public function grid(){
			$info = self::get_layout_info();
			?>
			<div id="theme-options-wrap" class="wrap grid"> 
				<div class="icon32" id="icon-options-general"> <br /> </div>	 
				<h3><?php echo $info['title']; ?></h3>
			    
				<form action="options.php" class="container" method="post">
				    <?php settings_fields( $info['subpage_slug'] ); ?>

					<div class="row">
			
				    <?php $sections = Options_Page::get_settings_sections( $info['subpage_slug'] , $info['page_info'] ); ?>
					    <?php foreach ( $sections as $section_html ) { ?>
						    <?php echo $section_html; ?>
					    <?php } ?>
				    </div>
				    <p class="submit">
				    	<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
				    </p>
		
			    </form>
			</div>			
			<?php		
		}
		public function tab(){
			$info = self::get_layout_info();
			?>
			<div id="theme-options-wrap" class="wrap tab"> 
				<div class="icon32" id="icon-tools"> <br /> </div>	 
				<h3><?php echo $info['title']; ?></h3>
			
				<form action="options.php" method="post">
				    <?php settings_fields( $info['subpage_slug'] ); ?>
					<div id="page-tabs" class="tabbable">
					    <?php $sections = Options_Page::get_settings_sections( $info['subpage_slug'] , $info['page_info'] ); ?>
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
				    <p class="submit">
				    	<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
				    </p>
			
			    </form>
			</div>			
			<?php
		}
		public function tab_top(){
			$info = self::get_layout_info();
			?>
			<div id="theme-options-wrap" class="wrap tab"> 
				<div class="icon32" id="icon-tools"> <br /> </div>	 
				<h3><?php echo $info['title']; ?></h3>
			
				<form action="options.php" method="post">
				    <?php settings_fields( $info['subpage_slug'] ); ?>
					<div id="page-tabs" class="tabbable">
					    <?php $sections = Options_Page::get_settings_sections( $info['subpage_slug'] , $info['page_info'] ); ?>
					
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
					</div>
				    <p class="submit">
				    	<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
				    </p>
			
			    </form>
			</div>			
			<?php	
		}
		public function tab_left(){
			$info = self::get_layout_info();
			?>
			<div id="theme-options-wrap" class="wrap tab"> 
				<div class="icon32" id="icon-tools"> <br /> </div>	 
				<h3><?php echo $info['title']; ?></h3>
			
				<form action="options.php" method="post">
				    <?php settings_fields( $info['subpage_slug'] ); ?>
					<div id="page-tabs" class="tabbable tabs-left">
					    <?php $sections = Options_Page::get_settings_sections( $info['subpage_slug'] , $info['page_info'] ); ?>
					
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
					</div>
				    <p class="submit">
				    	<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
				    </p>
			
			    </form>
			</div>			
			<?php		
		}
		public function tab_right(){
			$info = self::get_layout_info();
			?>
			<div id="theme-options-wrap" class="wrap tab"> 
				<div class="icon32" id="icon-tools"> <br /> </div>	 
				<h3><?php echo $info['title']; ?></h3>
			
				<form action="options.php" method="post">
				    <?php settings_fields( $info['subpage_slug'] ); ?>
					<div id="page-tabs" class="tabbable tabs-right">
					    <?php $sections = Options_Page::get_settings_sections( $info['subpage_slug'] , $info['page_info'] ); ?>
					
						<ul class="nav nav-tabs fade">
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
					</div>
				    <p class="submit">
				    	<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
				    </p>
			
			    </form>
			</div>			
			<?php		
		}								
		public function scroll(){
			$info = self::get_layout_info();
			?>
			<script>
			jQuery( 'body' ).attr( 'data-spy', 'scroll' ).attr( 'data-target', '#scroll-nav' );
			</script>
			<div id="theme-options-wrap" class="wrap scroll" > 
				<div class="icon32" id="icon-tools"> <br /> </div>	 
				<h3><?php echo $info['title']; ?></h3>

				<form action="options.php" method="post">
				    <?php settings_fields( $info['subpage_slug'] ); ?>
				    <?php $sections = Options_Page::get_settings_sections( $info['subpage_slug'] , $info['page_info'] ); ?>
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
				    <p class="submit">
				    	<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
				    </p>
			   </form>
			</div>			
			<?php		
		}						
		public function scroll_top(){
			$info = self::get_layout_info();
			?>
			<div id="theme-options-wrap" class="wrap"> 
				<div class="icon32" id="icon-tools"> <br /> </div>	 
				<h3><?php echo $info['title']; ?></h3>
			
				<form action="options.php" method="post">
				    <?php settings_fields( $info['subpage_slug'] ); ?>
				    <?php $sections = Options_Page::get_settings_sections( $info['subpage_slug'] , $info['page_info'] ); ?>
				    
				    <p class="submit">
				    	<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
				    </p>
			
			    </form>
			</div>			
			<?php	
		}						
		public function scroll_left(){
			$info = self::get_layout_info();
			?>
			<div id="theme-options-wrap" class="wrap"> 
				<div class="icon32" id="icon-tools"> <br /> </div>	 
				<h3><?php echo $info['title']; ?></h3>
			
				<form action="options.php" method="post">
				    <?php settings_fields( $info['subpage_slug'] ); ?>
				    <?php $sections = Options_Page::get_settings_sections( $info['subpage_slug'] , $info['page_info'] ); ?>
				    
				    <p class="submit">
				    	<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
				    </p>
			
			    </form>
			</div>			
			<?php		
		}												
	}
?>