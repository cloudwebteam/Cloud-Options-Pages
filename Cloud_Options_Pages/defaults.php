<?php

// fallback defaults for every possible option should be here. 

global $options_pages_defaults; 
$options_pages_defaults = array (

	'top_level' => array (
		'image'		=> 'IMAGE_URL', 
		'priority'	=> 52,
		'subpages'	=> array (
		)
	), 
	
	'subpages'	=> array (
		'title'			=> 'Default Page Title',
		'menu_title'	=> 'Default',
		'capability'	=> 'create_users',
		'layout'		=> 'standard',
		'style'			=> 'standard',
		'description' 	=> null,
		'sections'		=> array (
		)	
	), 
	
	'sections'	=> array (
		'title'			=> 'Default Section Title',
		'layout'		=> 'standard',
		'width'			=> 6,
		'description'	=> null,
		'fields'		=> array ( 
		)		
	),
	
	'fields'	=> array (
		'title'			=> 'Default Field Title',
		'layout'		=> array (
			'layout'		=> 'standard',
			'label'			=> 'left',
			'description'	=> 'bottom'
		),
		'type'			=> 'text', 
		'clone'			=> false,
		'width'			=> 6,
		'size'			=> 55,
		'description'	=> null,
		'editor_list'	=> false,
		'fields'		=> null
	)
); 