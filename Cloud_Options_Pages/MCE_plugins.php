<?php
class MCE_Plugins {
	private static $instance ; 
	public static $options_list_info ;
	public static function init(){
		if ( ! self::$instance ){
			self::$instance = new self; 
		} 
		return self::$instance; 
	}
	private function __construct(){
		$this->add_editor_list(); 
	}
	private function add_editor_list(){
		self::$options_list_info = self::get_mce_options_list_info(); 
		if ( self::$options_list_info ){
			// Don't bother doing this stuff if the current user lacks permissions
			if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') ) {
				return;
			}
		   // Add only in Rich Editor mode
			if ( get_user_option('rich_editing') == 'true') {
				add_filter("mce_external_plugins", array( $this, "add_options_list_to_mce" ) );
				add_filter('mce_buttons', array ( $this, 'register_editor_mce_buttons' ) );
			}	
			add_action( 'wp_ajax_mce_get_options_list', array( $this, 'mce_get_options_list') ); 
		}
	}
	public function mce_get_options_list(){
		$array = array( 'a','b','c'); 
		if ( $array ){
			echo json_encode($array);
		} else { 
			echo 0;
		}
	}
	public function add_options_list_to_mce( $plugin_array ){
		$plugin_array['options_list'] = Cloud_Options_Pages::get_include_path() . '/Cloud_Options_Pages/_js/mce_plugins/options_list.js';
		return $plugin_array; 
	
	}
	public function register_editor_mce_buttons($buttons){
		array_push($buttons, "separator", "options_list");
		return $buttons;
	}
	public static function get_mce_options_list_info(){
		$shortcodes = array(); 
		foreach ( Cloud_Options_Pages::$options_pages as $top_level ){
			foreach ( $top_level['subpages'] as $subpage_slug => $subpage ){
				$shortcodes[$subpage_slug] = array();
				$shortcodes[$subpage_slug]['title'] = $subpage['title'];
				$shortcodes[$subpage_slug]['sections'] = array();
				foreach ( $subpage['sections'] as $section_slug => $section ){	
					$shortcodes[$subpage_slug]['sections'][$section_slug] = array();
					$shortcodes[$subpage_slug]['sections'][$section_slug]['title'] = $section['title'];
					foreach ( $section['fields'] as $field_slug => $field ){
					
						if ( $field['type'] === 'group' && sizeof( $field['fields'] ) > 0  ){
							
						} else if ($field['editor_list'] === true ){
							$shortcodes[$subpage_slug]['sections'][$section_slug]['fields'][$field_slug] = array(
								'field_title' => $field['title'],
								'section_title' => $section['title'],
								'subpage_title' => $subpage['title'],
								'shortcode'		=> "[info p='".$subpage_slug."' s='".$section_slug."' f='".$field_slug."' ][/info]"
							);
						}
						
					}
					if (!isset( $shortcodes[$subpage_slug]['sections'][$section_slug]['fields']	)) {
						unset( $shortcodes[$subpage_slug]['sections'][$section_slug]);
					}
					
				}
				if (!isset( $shortcodes[$subpage_slug]['sections']	)) {
					unset( $shortcodes[$subpage_slug]);
				}				
			}
			if ( sizeof( $shortcodes[$subpage_slug]['sections']	) === 0 ) {
				unset( $shortcodes[$subpage_slug]);
			}			
		}
		return $shortcodes;
	}	
}