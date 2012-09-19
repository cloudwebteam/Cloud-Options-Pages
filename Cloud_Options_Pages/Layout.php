<?php
	abstract class Layout {
		private function __construct( ){ 
		}
		
		private static $default_layout; 
		
		public static function get_layout_function( $layout = null , $sub_classname = null ){
			self::$default_layout = 'standard'; 
						
			if ( $sub_classname && method_exists( $sub_classname , $layout) ){
				return $layout;
			} else {
				return self::$default_layout;
			}		
		}

		public function standard(){
			$subpage_slug = $_GET['page'];
			$Options_Page = Options_Page::get_instance();
			$page_info = $Options_Page->get_options_page_info($subpage_slug); 
			$title = $page_info['title'];
			?>
			Please create a default layout!
			<?php
		}	
	}