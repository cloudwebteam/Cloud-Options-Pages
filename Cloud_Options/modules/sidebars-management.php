<?php 
/***====================================================================================================================================
		SIDEBARS
	==================================================================================================================================== ***/
function add_sidebars_management(){
	$sidebars_page = array (
		// top level slug first (what will these pages be listed under?)
		'sidebars' => array (
			'image' 	=> Cloud_Options::get_folder_url().'/modules/images/layout-select-sidebar.png',
			'priority'  => 51,
			'defaults' => array(
				'fields' => array(
					'editor_list' => false
				)
			),
			'subpages'		=> array (
			
				'sidebars' => array (
					'title' 		=> 'Manage Sidebars',
					'menu_title' 	=> 'Sidebars',
					'layout'		=> 'standard',
					'sections'		=> array (
						'sidebars'	=> array(
							'title' 	=> 'Sidebars',
							'description' 	=> 'Sidebars created here will be available under the widgets page, and selectable on enabled post types.',
							'fields'		=> array(
								'sidebar' => array(
									'type' => 'group',
									'title' => 'Available Sidebars',
									'subfields' => array(
										'name' => array(
											'title' => 'Name of sidebar'
										),
										'description' => array(
											'title' => 'Description (optional)',
											'description' => 'Will show on widgets page'
										)
									)
								)
							)
						)
					)
				), 
				'widgets' => array(
					'title' => 'Widgets', 
					'layout' => 'standard',
					'menu_title' => 'Widgets', 
					'sections' => array(
						'widgets-section' => array(
							'title' => 'Widgets',
							'fields'		=> array(
								'notice' => array(
									'type' => 'info',
									'title' => 'You are seeing this?',
									'description' => 'You should not be, the page should have redirected!',
								)
							)
						)
					)
				)
			)
		)
	);
	Cloud_Options::add_pages( $sidebars_page );
	if ( $saved_sidebars = Custom_Theme::get_sidebars() ) {
		if( is_array( $saved_sidebars ) ){
			$sidebars = array() ;
			foreach( $saved_sidebars as $key => $sidebar ){
				$sidebars[ $sidebar['id'] ] = $sidebar['name'] ;
			}
		}
	}
	$metaboxes = array(
		'sidebars'	=> array(
			'title' 	=> 'Sidebar',
			'fields'		=> array(
				'sidebar' => array(
					'title' => 'Select a Sidebar',
					'type' => 'select', 
					'defaults' => 'default',
					'options' => $sidebars,
					'description' => 'If a sidebar is selected, it will appear on this page. Otherwise, content will be full-width.'
				)
			)
		)
	);
	Cloud_Options::add_metaboxes( array( 'post_type' => 'page' ), $metaboxes, 'side','low' );
	
	add_action( 'admin_menu', 'move_widgets_page' );
}
add_sidebars_management() ;

function move_widgets_page(){
	remove_submenu_page( 'themes.php', 'widgets.php' ) ;
	add_action( 'load-sidebars_page_widgets', 'load_widgets_page' );
		
}
function load_widgets_page(){
	global $wp_registered_sidebars ;
	include 'widgets.php' ;
	exit; 
}
